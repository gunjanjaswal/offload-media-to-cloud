<?php
/**
 * Bulk Restore class - downloads cloud files back to local storage
 */

if (!defined('ABSPATH')) {
    exit;
}

class OMTC_Bulk_Restore {

    public function __construct() {
        add_action('wp_ajax_omtc_get_restore_count', array($this, 'get_restore_count_ajax'));
        add_action('wp_ajax_omtc_bulk_restore', array($this, 'bulk_restore_ajax'));
    }

    /**
     * Get count of media files that are offloaded but missing locally
     */
    public function get_restore_count_ajax() {
        check_ajax_referer('omtc_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }

        $count = $this->get_restore_count();
        wp_send_json_success(array('count' => $count));
    }

    /**
     * Get the number of attachments that need restoring
     */
    public function get_restore_count() {
        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => 'omtc_remote_url',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $query = new WP_Query($args);
        $count = 0;

        foreach ($query->posts as $attachment_id) {
            $file_path = get_attached_file($attachment_id);
            if (!file_exists($file_path)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Bulk restore media files from cloud
     */
    public function bulk_restore_ajax() {
        check_ajax_referer('omtc_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }

        $batch_size = 3; // Small batches to avoid Cloudflare/server timeouts

        // Find offloaded attachments where local file is missing
        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => $batch_size,
            'meta_query'     => array(
                array(
                    'key'     => 'omtc_remote_url',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $query = new WP_Query($args);
        $processed = 0;
        $skipped = 0;
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
                $file_path = get_attached_file($attachment_id);

                // Skip if local file already exists
                if (file_exists($file_path)) {
                    $skipped++;
                    continue;
                }

                $result = $this->restore_single_attachment($attachment_id, $provider, $settings);
                if ($result['success']) {
                    $processed++;
                } else {
                    $errors[] = array(
                        'id'    => $attachment_id,
                        'title' => get_the_title(),
                        'error' => $result['message'],
                    );
                }
            }
            wp_reset_postdata();
        }

        // Check how many still need restoring
        $remaining = $this->get_restore_count();

        wp_send_json_success(array(
            'processed' => $processed,
            'skipped'   => $skipped,
            'remaining' => $remaining,
            'errors'    => $errors,
            'complete'  => ($remaining <= 0),
        ));
    }

    /**
     * Restore a single attachment from cloud to local
     */
    private function restore_single_attachment($attachment_id, $provider, $settings) {
        $file_path = get_attached_file($attachment_id);
        $remote_path = get_post_meta($attachment_id, 'omtc_remote_path', true);

        if (empty($remote_path)) {
            return array('success' => false, 'message' => __('No remote path stored', 'offload-media-to-cloud'));
        }

        // Ensure local directory exists
        $dir = dirname($file_path);
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }

        // Download main file
        $result = $provider->download_file($remote_path);
        if (!$result['success']) {
            return $result;
        }

        if (file_put_contents($file_path, $result['body']) === false) {
            return array('success' => false, 'message' => __('Failed to write local file', 'offload-media-to-cloud'));
        }

        // Download thumbnails
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $base_dir = dirname($file_path);

            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumb_path = $base_dir . '/' . $size_data['file'];

                if (file_exists($thumb_path)) {
                    continue;
                }

                $thumb_remote_url = get_post_meta($attachment_id, 'omtc_remote_url_' . $size, true);
                if (empty($thumb_remote_url)) {
                    continue;
                }

                $thumb_remote_path = $this->get_remote_path($thumb_path, $settings);
                $thumb_result = $provider->download_file($thumb_remote_path);

                if ($thumb_result['success']) {
                    file_put_contents($thumb_path, $thumb_result['body']);
                }
            }
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
}

new OMTC_Bulk_Restore();
