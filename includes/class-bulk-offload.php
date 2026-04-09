<?php
/**
 * Bulk Offload class - handles bulk migration of existing media
 */

if (!defined('ABSPATH')) {
    exit;
}

class G33KI_Bulk_Offload {
    
    public function __construct() {
        add_action('wp_ajax_g33ki_get_media_count', array($this, 'get_media_count_ajax'));
        add_action('wp_ajax_g33ki_bulk_offload', array($this, 'bulk_offload_ajax'));
    }
    
    /**
     * Get count of media files not yet offloaded
     */
    public function get_media_count_ajax() {
        check_ajax_referer('g33ki_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'g33ki-cloud-storage-for-media-library')));
        }
        
        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Needed to track offload state via meta
            'meta_query' => array(
                array(
                    'key' => 'g33ki_remote_url',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        $query = new WP_Query($args);
        $count = $query->found_posts;
        
        wp_send_json_success(array('count' => $count));
    }
    
    /**
     * Bulk offload media files
     */
    public function bulk_offload_ajax() {
        check_ajax_referer('g33ki_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'g33ki-cloud-storage-for-media-library')));
        }
        
        $batch_size = 3; // Small batches to avoid Cloudflare/server timeouts

        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => $batch_size,
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Needed to track offload state via meta
            'meta_query' => array(
                array(
                    'key' => 'g33ki_remote_url',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        $query = new WP_Query($args);
        $processed = 0;
        $errors = array();
        
        if ($query->have_posts()) {
            $settings = get_option('g33ki_settings', array());
            $provider = $this->get_provider($settings);
            
            if (!$provider) {
                wp_send_json_error(array('message' => __('Provider not configured', 'g33ki-cloud-storage-for-media-library')));
            }
            
            while ($query->have_posts()) {
                $query->the_post();
                $attachment_id = get_the_ID();
                
                $result = $this->offload_single_attachment($attachment_id, $provider, $settings);
                if ($result['success']) {
                    $processed++;
                } else {
                    $errors[] = array(
                        'id' => $attachment_id,
                        'title' => get_the_title(),
                        'error' => $result['message']
                    );
                }
            }
            wp_reset_postdata();
        }
        
        $remaining = $query->found_posts - $processed;
        
        wp_send_json_success(array(
            'processed' => $processed,
            'remaining' => max(0, $remaining),
            'errors' => $errors,
            'complete' => ($remaining <= 0)
        ));
    }
    
    /**
     * Offload single attachment
     */
    private function offload_single_attachment($attachment_id, $provider, $settings) {
        $file_path = get_attached_file($attachment_id);
        
        if (!file_exists($file_path)) {
            return array('success' => false, 'message' => __('File not found', 'g33ki-cloud-storage-for-media-library'));
        }
        
        // Check if file already exists in cloud (e.g. after restore + reactivate)
        $remote_path = $this->get_remote_path($file_path, $settings);
        $already_exists = $provider->remote_file_exists($remote_path);

        if ($already_exists) {
            // File already in cloud — ensure public-read ACL and link the URL
            $provider->set_public($remote_path);
            $url = $provider->get_file_url($remote_path);
        } else {
            // Upload main file
            $result = $provider->upload_file($file_path, $remote_path);

            if (!$result['success']) {
                return $result;
            }

            $url = $result['url'];
        }

        update_post_meta($attachment_id, 'g33ki_remote_url', $url);
        update_post_meta($attachment_id, 'g33ki_remote_path', $remote_path);

        // Upload thumbnails
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $base_dir = dirname($file_path);

            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumb_path = $base_dir . '/' . $size_data['file'];
                if (file_exists($thumb_path)) {
                    $thumb_remote_path = $this->get_remote_path($thumb_path, $settings);

                    if ($provider->remote_file_exists($thumb_remote_path)) {
                        $provider->set_public($thumb_remote_path);
                        $thumb_url = $provider->get_file_url($thumb_remote_path);
                    } else {
                        $thumb_result = $provider->upload_file($thumb_path, $thumb_remote_path);
                        $thumb_url = $thumb_result['success'] ? $thumb_result['url'] : '';
                    }

                    if ($thumb_url) {
                        update_post_meta($attachment_id, 'g33ki_remote_url_' . $size, $thumb_url);
                    }
                }
            }
        }
        
        // Remove local files if option is enabled
        if (!empty($settings['remove_local_files'])) {
            $this->remove_local_file($file_path, $metadata);
        }
        
        return array('success' => true);
    }
    
    /**
     * Get provider instance
     */
    private function get_provider($settings) {
        if (empty($settings['provider'])) {
            return null;
        }
        
        $provider_class = 'G33KI_' . ucfirst($settings['provider']) . '_Provider';
        if (!class_exists($provider_class)) {
            return null;
        }
        
        return new $provider_class($settings);
    }
    
    /**
     * Get remote path for file
     */
    private function get_remote_path($file_path, $settings) {
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);
        
        $prefix = !empty($settings['path_prefix']) ? trailingslashit($settings['path_prefix']) : '';
        return $prefix . $relative_path;
    }
    
    /**
     * Remove local file
     */
    private function remove_local_file($file_path, $metadata) {
        if (file_exists($file_path)) {
            wp_delete_file($file_path);
        }
        
        // Remove thumbnails
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $base_dir = dirname($file_path);
            foreach ($metadata['sizes'] as $size_data) {
                $thumb_path = $base_dir . '/' . $size_data['file'];
                if (file_exists($thumb_path)) {
                    wp_delete_file($thumb_path);
                }
            }
        }
    }
}

// Initialize
new G33KI_Bulk_Offload();


