<?php
/**
 * DigitalOcean Spaces provider - uses direct REST API calls (no SDK required)
 */

if (!defined('ABSPATH')) {
    exit;
}

class OMTC_Spaces_Provider extends OMTC_Provider_Base {

    private function get_endpoint($path = '') {
        $region = $this->settings['region'];
        $bucket = $this->settings['bucket'];

        return "https://{$bucket}.{$region}.digitaloceanspaces.com/{$path}";
    }

    public function upload_file($file_path, $remote_path) {
        try {
            $body = file_get_contents($file_path);
            if ($body === false) {
                return array('success' => false, 'message' => __('Could not read local file', 'offload-media-to-cloud'));
            }

            $mime = $this->get_mime_type($file_path);
            $url = $this->get_endpoint($remote_path);

            $response = OMTC_S3_Signing::request(
                'PUT',
                $url,
                array(
                    'Content-Type' => $mime,
                    'x-amz-acl'    => 'public-read',
                ),
                $body,
                $this->settings['access_key'],
                $this->settings['secret_key'],
                $this->settings['region']
            );

            if (is_wp_error($response)) {
                return array('success' => false, 'message' => $response->get_error_message());
            }

            $code = wp_remote_retrieve_response_code($response);
            if ($code >= 200 && $code < 300) {
                return array('success' => true, 'url' => $this->get_file_url($remote_path));
            }

            $body_response = wp_remote_retrieve_body($response);
            return array('success' => false, 'message' => "Spaces returned HTTP {$code}: {$body_response}");
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    public function delete_file($remote_path) {
        try {
            $url = $this->get_endpoint($remote_path);

            $response = OMTC_S3_Signing::request(
                'DELETE',
                $url,
                array(),
                '',
                $this->settings['access_key'],
                $this->settings['secret_key'],
                $this->settings['region']
            );

            if (is_wp_error($response)) {
                return array('success' => false, 'message' => $response->get_error_message());
            }

            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    public function test_connection() {
        try {
            $url = $this->get_endpoint();

            $response = OMTC_S3_Signing::request(
                'HEAD',
                $url,
                array(),
                '',
                $this->settings['access_key'],
                $this->settings['secret_key'],
                $this->settings['region']
            );

            if (is_wp_error($response)) {
                return array('success' => false, 'message' => $response->get_error_message());
            }

            $code = wp_remote_retrieve_response_code($response);
            if ($code >= 200 && $code < 400) {
                return array('success' => true);
            }

            return array('success' => false, 'message' => "Spaces returned HTTP {$code}");
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    public function get_file_url($remote_path) {
        if (!empty($this->settings['cdn_url'])) {
            return trailingslashit($this->settings['cdn_url']) . $remote_path;
        }

        return $this->get_endpoint($remote_path);
    }
}
