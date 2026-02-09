<?php
/**
 * Amazon S3 provider
 */

if (!defined('ABSPATH')) {
    exit;
}

class OIJC_S3_Provider extends OIJC_Provider_Base {
    
    private $client;
    
    public function __construct($settings) {
        parent::__construct($settings);
        
        // Initialize AWS SDK if available
        if (class_exists('Aws\S3\S3Client')) {
            try {
                $this->client = new Aws\S3\S3Client([
                    'version' => 'latest',
                    'region' => $this->settings['region'],
                    'credentials' => [
                        'key' => $this->settings['access_key'],
                        'secret' => $this->settings['secret_key']
                    ]
                ]);
            } catch (Exception $e) {
                error_log('OIJC S3 Error: ' . $e->getMessage());
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
        
        // Use S3 URL
        $region = $this->settings['region'];
        $bucket = $this->settings['bucket'];
        
        if ($region === 'us-east-1') {
            return "https://{$bucket}.s3.amazonaws.com/{$remote_path}";
        } else {
            return "https://{$bucket}.s3.{$region}.amazonaws.com/{$remote_path}";
        }
    }
}
