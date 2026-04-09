<?php
/**
 * AWS Signature V4 signing utility
 *
 * Used by S3, DigitalOcean Spaces, and GCS (HMAC) providers
 * to sign HTTP requests without requiring the AWS SDK.
 */

if (!defined('ABSPATH')) {
    exit;
}

class G33KI_S3_Signing {

    /**
     * Make a signed S3-compatible API request
     *
     * @param string $method    HTTP method (GET, PUT, DELETE, HEAD)
     * @param string $url       Full URL to the resource
     * @param array  $headers   Additional headers (Content-Type, etc.)
     * @param string $body      Request body (file contents for PUT)
     * @param string $access_key Access key ID
     * @param string $secret_key Secret access key
     * @param string $region    Region code
     * @param string $service   Service name (s3, s3 for GCS HMAC)
     * @return array|WP_Error   Response array or WP_Error
     */
    public static function request($method, $url, $headers, $body, $access_key, $secret_key, $region, $service = 's3') {
        $parsed = wp_parse_url($url);
        $host = $parsed['host'];
        $path = isset($parsed['path']) ? $parsed['path'] : '/';
        $query = isset($parsed['query']) ? $parsed['query'] : '';

        $timestamp = gmdate('Ymd\THis\Z');
        $datestamp = gmdate('Ymd');

        // Content hash
        $payload_hash = hash('sha256', $body);

        // Build headers
        $headers['Host'] = $host;
        $headers['x-amz-date'] = $timestamp;
        $headers['x-amz-content-sha256'] = $payload_hash;

        // Canonical headers (must be sorted lowercase)
        $canonical_headers = '';
        $signed_headers_list = array();
        $lower_headers = array();
        foreach ($headers as $key => $value) {
            $lower_headers[strtolower($key)] = trim($value);
        }
        ksort($lower_headers);
        foreach ($lower_headers as $key => $value) {
            $canonical_headers .= $key . ':' . $value . "\n";
            $signed_headers_list[] = $key;
        }
        $signed_headers = implode(';', $signed_headers_list);

        // Canonical query string
        $canonical_querystring = '';
        if (!empty($query)) {
            parse_str($query, $query_params);
            ksort($query_params);
            $canonical_querystring = http_build_query($query_params, '', '&', PHP_QUERY_RFC3986);
        }

        // Canonical request
        $canonical_request = implode("\n", array(
            $method,
            self::uri_encode_path($path),
            $canonical_querystring,
            $canonical_headers,
            $signed_headers,
            $payload_hash,
        ));

        // String to sign
        $credential_scope = $datestamp . '/' . $region . '/' . $service . '/aws4_request';
        $string_to_sign = implode("\n", array(
            'AWS4-HMAC-SHA256',
            $timestamp,
            $credential_scope,
            hash('sha256', $canonical_request),
        ));

        // Signing key
        $signing_key = self::get_signing_key($secret_key, $datestamp, $region, $service);

        // Signature
        $signature = hash_hmac('sha256', $string_to_sign, $signing_key);

        // Authorization header
        $authorization = sprintf(
            'AWS4-HMAC-SHA256 Credential=%s/%s, SignedHeaders=%s, Signature=%s',
            $access_key,
            $credential_scope,
            $signed_headers,
            $signature
        );

        // Build wp_remote_request args
        $wp_headers = array();
        foreach ($headers as $key => $value) {
            $wp_headers[$key] = $value;
        }
        $wp_headers['Authorization'] = $authorization;

        $args = array(
            'method'  => $method,
            'headers' => $wp_headers,
            'body'    => $body,
            'timeout' => 60,
        );

        // For large files, increase timeout
        if (strlen($body) > 1048576) {
            $args['timeout'] = 300;
        }

        return wp_remote_request($url, $args);
    }

    /**
     * Generate the signing key
     */
    private static function get_signing_key($secret_key, $datestamp, $region, $service) {
        $k_date = hash_hmac('sha256', $datestamp, 'AWS4' . $secret_key, true);
        $k_region = hash_hmac('sha256', $region, $k_date, true);
        $k_service = hash_hmac('sha256', $service, $k_region, true);
        $k_signing = hash_hmac('sha256', 'aws4_request', $k_service, true);
        return $k_signing;
    }

    /**
     * URI-encode path segments (but not the slashes)
     */
    private static function uri_encode_path($path) {
        $segments = explode('/', $path);
        $encoded = array();
        foreach ($segments as $segment) {
            $encoded[] = rawurlencode($segment);
        }
        return implode('/', $encoded);
    }
}


