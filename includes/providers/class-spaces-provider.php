<?php
/**
 * DigitalOcean Spaces provider
 */

if (!defined('ABSPATH')) {
    exit;
}

class OIJC_Spaces_Provider extends OIJC_Provider_Base {
    
    private $client;
    
    public function __construct($settings) {
        parent::__construct($settings);
        
        // DigitalOcean Spaces uses S3-compatible API
        if (class_exists('Aws\S3\S3Client')) {
            try {
                $endpoint = "https://{$this->settings['region']}.digitaloceanspaces.com";
                
                $this->client = new Aws\S3\S3Client([
                    'version' => 'latest',
                    'region' => $this->settings['region'],
                    'endpoint' => $endpoint,
                    'credentials' => [
                        'key' => $this->settings['access_key'],
                        'secret' => $this->settings['secret_key']
                    ]
                ]);
            } catch (Exception $e) {
                error_log('OIJC Spaces Error: ' . $e->getMessage());
            }
        }
    }
    
    public function upload_file($file_path, $remote_path) {
        if (!$this->client) {
            return array('success' => false, 'message' => __('AWS SDK not available', 'offload-images-js-css'));
        }
        
        try {
            $result = $this->client->putObject([
                'Bucket' => $this->settings['bucket'],
                'Key' => $remote_path,
                'SourceFile' => $file_path,
                'ACL' => 'public-read',
                'ContentType' => $this->get_mime_type($file_path)
            ]);
            
            $url = $this->get_file_url($remote_path);
            
            return array('success' => true, 'url' => $url);
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    public function delete_file($remote_path) {
        if (!$this->client) {
            return array('success' => false, 'message' => __('AWS SDK not available', 'offload-images-js-css'));
        }
        
        try {
            $this->client->deleteObject([
                'Bucket' => $this->settings['bucket'],
                'Key' => $remote_path
            ]);
            
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    public function test_connection() {
        if (!$this->client) {
            return array('success' => false, 'message' => __('AWS SDK not available. Please install the AWS SDK for PHP.', 'offload-images-js-css'));
        }
        
        try {
            $this->client->headBucket([
                'Bucket' => $this->settings['bucket']
            ]);
            
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    public function get_file_url($remote_path) {
        // Use CDN URL if configured
        if (!empty($this->settings['cdn_url'])) {
            return trailingslashit($this->settings['cdn_url']) . $remote_path;
        }
        
        // Use Spaces URL
        $region = $this->settings['region'];
        $bucket = $this->settings['bucket'];
        
        return "https://{$bucket}.{$region}.digitaloceanspaces.com/{$remote_path}";
    }
}
