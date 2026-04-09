<?php
/**
 * Fix Permissions class - scans for private/inaccessible cloud files and re-uploads with public-read ACL
 */

if (!defined('ABSPATH')) {
    exit;
}

class G33KI_Fix_Permissions {

    public function __construct() {
        add_action('wp_ajax_g33ki_scan_permissions', array($this, 'scan_permissions_ajax'));
        add_action('wp_ajax_g33ki_fix_permissions', array($this, 'fix_permissions_ajax'));
    }

    /**
     * Scan for offloaded files that return non-200 (private/broken)
     */
    public function scan_permissions_ajax() {
        check_ajax_referer('g33ki_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'g33ki-cloud-storage-for-media-library')));
        }

        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $batch_size = 20; // Check 20 at a time (HEAD requests are fast)

        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => $batch_size,
            'offset'         => $offset,
            'fields'         => 'ids',
            // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Needed to track offload state via meta
            'meta_query'     => array(
                array(
                    'key'     => 'g33ki_remote_url',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $query = new WP_Query($args);
        $broken = array();
        $checked = 0;

        foreach ($query->posts as $attachment_id) {
            $remote_url = get_post_meta($attachment_id, 'g33ki_remote_url', true);
            if (empty($remote_url)) {
                $remote_url = get_post_meta($attachment_id, 'omtc_remote_url', true);
            }
            if (empty($remote_url)) {
                continue;
            }

            $checked++;
            $response = wp_remote_head($remote_url, array('timeout' => 10));

            if (is_wp_error($response)) {
                $broken[] = array(
                    'id'    => $attachment_id,
                    'title' => get_the_title($attachment_id),
                    'url'   => $remote_url,
                    'error' => $response->get_error_message(),
                );
                continue;
            }

            $code = wp_remote_retrieve_response_code($response);
            if ($code === 403 || $code === 404 || $code >= 500) {
                $broken[] = array(
                    'id'    => $attachment_id,
                    'title' => get_the_title($attachment_id),
                    'url'   => $remote_url,
                    'error' => "HTTP {$code}",
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
     * Fix a batch of broken files by re-uploading with public-read ACL
     */
    public function fix_permissions_ajax() {
        check_ajax_referer('g33ki_ajax_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'g33ki-cloud-storage-for-media-library')));
        }

        $ids = isset($_POST['ids']) ? array_map('intval', (array) $_POST['ids']) : array();

        if (empty($ids)) {
            wp_send_json_error(array('message' => __('No files to fix', 'g33ki-cloud-storage-for-media-library')));
        }

        $settings = get_option('g33ki_settings', array());
        $provider = $this->get_provider($settings);

        if (!$provider) {
            wp_send_json_error(array('message' => __('Provider not configured', 'g33ki-cloud-storage-for-media-library')));
        }

        $fixed = 0;
        $errors = array();

        foreach ($ids as $attachment_id) {
            $file_path = get_attached_file($attachment_id);
            $remote_path = get_post_meta($attachment_id, 'g33ki_remote_path', true);
            if (empty($remote_path)) {
                $remote_path = get_post_meta($attachment_id, 'omtc_remote_path', true);
            }

            if (empty($remote_path)) {
                $errors[] = array('id' => $attachment_id, 'error' => 'No remote path');
                continue;
            }

            // Try set_public first (faster, no re-upload needed)
            if ($provider->set_public($remote_path)) {
                $fixed++;

                // Also fix thumbnails
                $metadata = wp_get_attachment_metadata($attachment_id);
                if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                    foreach ($metadata['sizes'] as $size => $size_data) {
                        $thumb_remote_path = get_post_meta($attachment_id, 'g33ki_remote_url_' . $size, true);
                        if (empty($thumb_remote_path)) {
                            $thumb_remote_path = get_post_meta($attachment_id, 'omtc_remote_url_' . $size, true);
                        }
                        if (!empty($thumb_remote_path)) {
                            $upload_dir = wp_upload_dir();
                            $base_dir = dirname($file_path);
                            $thumb_path = $base_dir . '/' . $size_data['file'];
                            $thumb_remote = $this->get_remote_path($thumb_path, $settings);
                            $provider->set_public($thumb_remote);
                        }
                    }
                }
                continue;
            }

            // Fallback: re-upload the file if set_public fails
            if (file_exists($file_path)) {
                $result = $provider->upload_file($file_path, $remote_path);
                if ($result['success']) {
                    $fixed++;

                    // Re-upload thumbnails too
                    $metadata = wp_get_attachment_metadata($attachment_id);
                    if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                        $base_dir = dirname($file_path);
                        foreach ($metadata['sizes'] as $size => $size_data) {
                            $thumb_path = $base_dir . '/' . $size_data['file'];
                            if (file_exists($thumb_path)) {
                                $thumb_remote = $this->get_remote_path($thumb_path, $settings);
                                $provider->upload_file($thumb_path, $thumb_remote);
                            }
                        }
                    }
                } else {
                    $errors[] = array('id' => $attachment_id, 'error' => $result['message']);
                }
            } else {
                $errors[] = array('id' => $attachment_id, 'error' => 'Local file not found for re-upload');
            }
        }

        wp_send_json_success(array(
            'fixed'  => $fixed,
            'errors' => $errors,
        ));
    }

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

    private function get_remote_path($file_path, $settings) {
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);

        $prefix = !empty($settings['path_prefix']) ? trailingslashit($settings['path_prefix']) : '';
        return $prefix . $relative_path;
    }
}

new G33KI_Fix_Permissions();


