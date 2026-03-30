<?php
/**
 * Fix URL Mismatch class - scans for stored URLs that don't match current settings and updates them
 */

if (!defined('ABSPATH')) {
    exit;
}

class OMTC_Fix_Urls {

    public function __construct() {
        add_action('wp_ajax_omtc_scan_urls', array($this, 'scan_urls_ajax'));
        add_action('wp_ajax_omtc_fix_urls', array($this, 'fix_urls_ajax'));
    }

    /**
     * Scan for offloaded files whose stored URL doesn't match current settings
     */
    public function scan_urls_ajax() {
        check_ajax_referer('omtc_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }

        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $batch_size = 50; // No HTTP requests, just string comparison, so fast

        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => $batch_size,
            'offset'         => $offset,
            'fields'         => 'ids',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Needed to track offload state via meta
            'meta_query'     => array(
                array(
                    'key'     => 'omtc_remote_url',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $query = new WP_Query($args);
        $mismatched = array();
        $checked = 0;

        $settings = get_option('omtc_settings', array());

        foreach ($query->posts as $attachment_id) {
            $remote_url = get_post_meta($attachment_id, 'omtc_remote_url', true);
            $remote_path = get_post_meta($attachment_id, 'omtc_remote_path', true);

            if (empty($remote_url) || empty($remote_path)) {
                continue;
            }

            $checked++;
            $expected_url = $this->build_url_from_path($remote_path, $settings);

            if ($remote_url !== $expected_url) {
                $mismatched[] = array(
                    'id'           => $attachment_id,
                    'title'        => get_the_title($attachment_id),
                    'old_url'      => $remote_url,
                    'expected_url' => $expected_url,
                );
            }
        }

        $total = $query->found_posts;
        $scanned = $offset + $checked;

        wp_send_json_success(array(
            'mismatched' => $mismatched,
            'checked'    => $checked,
            'scanned'    => $scanned,
            'total'      => $total,
            'complete'   => ($scanned >= $total),
        ));
    }

    /**
     * Fix a batch of mismatched URLs by recalculating from current settings
     */
    public function fix_urls_ajax() {
        check_ajax_referer('omtc_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }

        $ids = isset($_POST['ids']) ? array_map('intval', (array) $_POST['ids']) : array();

        if (empty($ids)) {
            wp_send_json_error(array('message' => __('No files to fix', 'offload-media-to-cloud')));
        }

        // Process in batches of 20
        $ids = array_slice($ids, 0, 20);

        $settings = get_option('omtc_settings', array());
        $fixed = 0;
        $errors = array();

        foreach ($ids as $attachment_id) {
            $remote_path = get_post_meta($attachment_id, 'omtc_remote_path', true);

            if (empty($remote_path)) {
                $errors[] = array('id' => $attachment_id, 'error' => 'No remote path stored');
                continue;
            }

            // Update main URL
            $new_url = $this->build_url_from_path($remote_path, $settings);
            update_post_meta($attachment_id, 'omtc_remote_url', $new_url);

            // Update thumbnail URLs
            $metadata = wp_get_attachment_metadata($attachment_id);
            if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                $path_dir = dirname($remote_path);

                foreach ($metadata['sizes'] as $size => $size_data) {
                    $old_thumb_url = get_post_meta($attachment_id, 'omtc_remote_url_' . $size, true);
                    if (!empty($old_thumb_url)) {
                        $thumb_remote_path = $path_dir . '/' . $size_data['file'];
                        $new_thumb_url = $this->build_url_from_path($thumb_remote_path, $settings);
                        update_post_meta($attachment_id, 'omtc_remote_url_' . $size, $new_thumb_url);
                    }
                }
            }

            $fixed++;
        }

        wp_send_json_success(array(
            'fixed'  => $fixed,
            'errors' => $errors,
        ));
    }

    /**
     * Build the correct URL from a remote path and current settings
     *
     * @param string $remote_path The remote path (e.g. "wp-content/uploads/2024/01/image.jpg")
     * @param array  $settings    The plugin settings
     * @return string The full URL
     */
    private function build_url_from_path($remote_path, $settings) {
        $base_url = $this->get_cloud_base_url($settings);
        return $base_url . $remote_path;
    }

    /**
     * Get cloud base URL (CDN or origin) - mirrors logic from main plugin class
     */
    private function get_cloud_base_url($settings) {
        if (!empty($settings['cdn_url'])) {
            return trailingslashit($settings['cdn_url']);
        }

        $bucket = isset($settings['bucket']) ? $settings['bucket'] : '';
        $region = isset($settings['region']) ? $settings['region'] : '';
        $provider = isset($settings['provider']) ? $settings['provider'] : '';

        if ($provider === 'spaces') {
            return "https://{$bucket}.{$region}.digitaloceanspaces.com/";
        } elseif ($provider === 'gcs') {
            return "https://storage.googleapis.com/{$bucket}/";
        } else {
            if ($region === 'us-east-1') {
                return "https://{$bucket}.s3.amazonaws.com/";
            }
            return "https://{$bucket}.s3.{$region}.amazonaws.com/";
        }
    }
}

new OMTC_Fix_Urls();
