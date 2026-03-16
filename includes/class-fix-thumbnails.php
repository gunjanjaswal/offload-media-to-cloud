<?php
/**
 * Fix Missing Thumbnails class - scans for offloaded media with missing thumbnail URLs and re-uploads them
 */

if (!defined('ABSPATH')) {
    exit;
}

class OMTC_Fix_Thumbnails {

    public function __construct() {
        add_action('wp_ajax_omtc_scan_thumbnails', array($this, 'scan_thumbnails_ajax'));
        add_action('wp_ajax_omtc_fix_thumbnails', array($this, 'fix_thumbnails_ajax'));
    }

    /**
     * Scan for offloaded attachments with missing thumbnail URLs
     */
    public function scan_thumbnails_ajax() {
        check_ajax_referer('omtc_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }

        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $batch_size = 20;

        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => $batch_size,
            'offset'         => $offset,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => 'omtc_remote_url',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $query = new WP_Query($args);
        $broken = array();
        $checked = 0;

        foreach ($query->posts as $attachment_id) {
            $checked++;

            $metadata = wp_get_attachment_metadata($attachment_id);
            if (!isset($metadata['sizes']) || !is_array($metadata['sizes'])) {
                continue;
            }

            $missing_sizes = array();

            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumb_url = get_post_meta($attachment_id, 'omtc_remote_url_' . $size, true);
                if (empty($thumb_url)) {
                    $missing_sizes[] = $size;
                }
            }

            if (!empty($missing_sizes)) {
                $broken[] = array(
                    'id'            => $attachment_id,
                    'title'         => get_the_title($attachment_id),
                    'missing_sizes' => $missing_sizes,
                );
            }
        }

        $total = $query->found_posts;
        $scanned = $offset + $checked;

        wp_send_json_success(array(
            'broken'   => $broken,
            'checked'  => $checked,
            'scanned'  => $scanned,
            'total'    => $total,
            'complete' => ($scanned >= $total),
        ));
    }

    /**
     * Fix missing thumbnails by uploading them to cloud storage
     */
    public function fix_thumbnails_ajax() {
        check_ajax_referer('omtc_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-media-to-cloud')));
        }

        $ids = isset($_POST['ids']) ? array_map('intval', (array) $_POST['ids']) : array();

        if (empty($ids)) {
            wp_send_json_error(array('message' => __('No files to fix', 'offload-media-to-cloud')));
        }

        $settings = get_option('omtc_settings', array());
        $provider = $this->get_provider($settings);

        if (!$provider) {
            wp_send_json_error(array('message' => __('Provider not configured', 'offload-media-to-cloud')));
        }

        $fixed = 0;
        $errors = array();

        foreach ($ids as $attachment_id) {
            $file_path = get_attached_file($attachment_id);
            $metadata = wp_get_attachment_metadata($attachment_id);

            if (!isset($metadata['sizes']) || !is_array($metadata['sizes'])) {
                $errors[] = array('id' => $attachment_id, 'error' => __('No thumbnail sizes in metadata', 'offload-media-to-cloud'));
                continue;
            }

            $base_dir = dirname($file_path);
            $attachment_fixed = false;

            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumb_url = get_post_meta($attachment_id, 'omtc_remote_url_' . $size, true);
                if (!empty($thumb_url)) {
                    continue;
                }

                $thumb_path = $base_dir . '/' . $size_data['file'];

                // If local thumbnail doesn't exist, try to regenerate
                if (!file_exists($thumb_path)) {
                    if (file_exists($file_path) && function_exists('wp_create_image_subsizes')) {
                        $new_metadata = wp_create_image_subsizes($file_path, $attachment_id);
                        if (!is_wp_error($new_metadata) && isset($new_metadata['sizes'])) {
                            wp_update_attachment_metadata($attachment_id, $new_metadata);
                            $metadata = $new_metadata;
                            if (isset($metadata['sizes'][$size])) {
                                $thumb_path = $base_dir . '/' . $metadata['sizes'][$size]['file'];
                            }
                        }
                    }
                }

                if (!file_exists($thumb_path)) {
                    $errors[] = array('id' => $attachment_id, 'error' => sprintf(__('Local file missing for size: %s', 'offload-media-to-cloud'), $size));
                    continue;
                }

                $thumb_remote_path = $this->get_remote_path($thumb_path, $settings);

                if ($provider->remote_file_exists($thumb_remote_path)) {
                    $provider->set_public($thumb_remote_path);
                    $thumb_url_result = $provider->get_file_url($thumb_remote_path);
                } else {
                    $result = $provider->upload_file($thumb_path, $thumb_remote_path);
                    $thumb_url_result = $result['success'] ? $result['url'] : '';

                    if (!$result['success']) {
                        $errors[] = array('id' => $attachment_id, 'error' => sprintf(__('Upload failed for size %s: %s', 'offload-media-to-cloud'), $size, $result['message']));
                        continue;
                    }
                }

                if ($thumb_url_result) {
                    update_post_meta($attachment_id, 'omtc_remote_url_' . $size, $thumb_url_result);
                    $attachment_fixed = true;
                }
            }

            if ($attachment_fixed) {
                $fixed++;
            }
        }

        wp_send_json_success(array(
            'fixed'  => $fixed,
            'errors' => $errors,
        ));
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

new OMTC_Fix_Thumbnails();
