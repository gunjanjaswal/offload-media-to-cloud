<?php
/**
 * Amazon S3 provider - uses direct REST API calls (no SDK required)
 */

if (!defined('ABSPATH')) {
    exit;
}

class OMTC_S3_Provider extends OMTC_Provider_Base {

    private function get_endpoint($path = '') {
        $region = $this->settings['region'];
        $bucket = $this->settings['bucket'];

        if ($region === 'us-east-1') {
            return "https://{$bucket}.s3.amazonaws.com/{$path}";
        }
        return "https://{$bucket}.s3.{$region}.amazonaws.com/{$path}";
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
            return array('success' => false, 'message' => "S3 returned HTTP {$code}: {$body_response}");
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    public function download_file($remote_path) {
        try {
            $url = $this->get_endpoint($remote_path);

            $response = OMTC_S3_Signing::request(
                'GET',
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
            if ($code >= 200 && $code < 300) {
                return array('success' => true, 'body' => wp_remote_retrieve_body($response));
            }

            return array('success' => false, 'message' => "S3 returned HTTP {$code}");
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
            $region = $this->settings['region'];
            $bucket = $this->settings['bucket'];

            if ($region === 'us-east-1') {
                $url = "https://{$bucket}.s3.amazonaws.com/";
            } else {
                $url = "https://{$bucket}.s3.{$region}.amazonaws.com/";
            }

            $response = OMTC_S3_Signing::request(
                'HEAD',
                $url,
                array(),
                '',
                $this->settings['access_key'],
                $this->settings['secret_key'],
                $region
            );

            if (is_wp_error($response)) {
                return array('success' => false, 'message' => $response->get_error_message());
            }

            $code = wp_remote_retrieve_response_code($response);
            if ($code >= 200 && $code < 400) {
                return array('success' => true);
            }

            return array('success' => false, 'message' => "S3 returned HTTP {$code}");
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    public function remote_file_exists($remote_path) {
        try {
            $url = $this->get_endpoint($remote_path);

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
                return false;
            }

            $code = wp_remote_retrieve_response_code($response);
            return ($code >= 200 && $code < 300);
        } catch (Exception $e) {
            return false;
        }
    }

    public function get_file_url($remote_path) {
        if (!empty($this->settings['cdn_url'])) {
            return trailingslashit($this->settings['cdn_url']) . $remote_path;
        }

        return $this->get_endpoint($remote_path);
    }
}
