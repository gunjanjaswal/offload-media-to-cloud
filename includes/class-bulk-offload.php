<?php
/**
 * Bulk Offload class - handles bulk migration of existing media
 */

if (!defined('ABSPATH')) {
    exit;
}

class OMTC_Bulk_Offload {
    
    public function __construct() {
        add_action('wp_ajax_omtc_get_media_count', array($this, 'get_media_count_ajax'));
        add_action('wp_ajax_omtc_bulk_offload', array($this, 'bulk_offload_ajax'));
    }
    
    /**
     * Get count of media files not yet offloaded
     */
    public function get_media_count_ajax() {
        check_ajax_referer('omtc_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }
        
        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'omtc_remote_url',
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
        check_ajax_referer('omtc_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }
        
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $batch_size = 10; // Process 10 items at a time
        
        $args = array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => $batch_size,
            'offset' => $offset,
            'meta_query' => array(
                array(
                    'key' => 'omtc_remote_url',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        $query = new WP_Query($args);
        $processed = 0;
        $errors = array();
        
        if ($query->have_posts()) {
            $settings = get_option('omtc_settings', array());
            $provider = $this->get_provider($settings);
            
            if (!$provider) {
                wp_send_json_error(array('message' => __('Provider not configured', 'offload-media-to-cloud')));
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
            return array('success' => false, 'message' => __('File not found', 'offload-media-to-cloud'));
        }
        
        // Upload main file
        $remote_path = $this->get_remote_path($file_path, $settings);
        $result = $provider->upload_file($file_path, $remote_path);
        
        if (!$result['success']) {
            return $result;
        }
        
        update_post_meta($attachment_id, 'omtc_remote_url', $result['url']);
        update_post_meta($attachment_id, 'omtc_remote_path', $remote_path);
        
        // Upload thumbnails
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $base_dir = dirname($file_path);
            
            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumb_path = $base_dir . '/' . $size_data['file'];
                if (file_exists($thumb_path)) {
                    $thumb_remote_path = $this->get_remote_path($thumb_path, $settings);
                    $thumb_result = $provider->upload_file($thumb_path, $thumb_remote_path);
                    
                    if ($thumb_result['success']) {
                        update_post_meta($attachment_id, 'omtc_remote_url_' . $size, $thumb_result['url']);
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
        
        $provider_class = 'OMTC_' . ucfirst($settings['provider']) . '_Provider';
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
            @unlink($file_path);
        }
        
        // Remove thumbnails
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $base_dir = dirname($file_path);
            foreach ($metadata['sizes'] as $size_data) {
                $thumb_path = $base_dir . '/' . $size_data['file'];
                if (file_exists($thumb_path)) {
                    @unlink($thumb_path);
                }
            }
        }
    }
}

// Initialize
new OMTC_Bulk_Offload();
