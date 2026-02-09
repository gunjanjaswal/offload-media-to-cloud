<?php
/**
 * Google Cloud Storage provider
 */

if (!defined('ABSPATH')) {
    exit;
}

class OIJC_GCS_Provider extends OIJC_Provider_Base {
    
    private $client;
    private $bucket;
    
    public function __construct($settings) {
        parent::__construct($settings);
        
        // Initialize Google Cloud Storage client if available
        if (class_exists('Google\Cloud\Storage\StorageClient')) {
            try {
                $config = array();
                
                // If key file path is provided
                if (!empty($this->settings['key_file_path'])) {
                    $config['keyFilePath'] = $this->settings['key_file_path'];
                }
                
                $storage = new Google\Cloud\Storage\StorageClient($config);
                $this->bucket = $storage->bucket($this->settings['bucket']);
            } catch (Exception $e) {
                error_log('OIJC GCS Error: ' . $e->getMessage());
            }
        }
    }
    
    public function upload_file($file_path, $remote_path) {
        if (!$this->bucket) {
            return array('success' => false, 'message' => __('Google Cloud Storage SDK not available', 'offload-images-js-css'));
        }
        
        try {
            $file_content = file_get_contents($file_path);
            
            $object = $this->bucket->upload($file_content, [
                'name' => $remote_path,
                'metadata' => [
                    'contentType' => $this->get_mime_type($file_path)
                ],
                'predefinedAcl' => 'publicRead'
            ]);
            
            $url = $this->get_file_url($remote_path);
            
            return array('success' => true, 'url' => $url);
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    public function delete_file($remote_path) {
        if (!$this->bucket) {
            return array('success' => false, 'message' => __('Google Cloud Storage SDK not available', 'offload-images-js-css'));
        }
        
        try {
            $object = $this->bucket->object($remote_path);
            $object->delete();
            
            return array('success' => true);
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    public function test_connection() {
        if (!$this->bucket) {
            return array('success' => false, 'message' => __('Google Cloud Storage SDK not available. Please install the Google Cloud Storage PHP library.', 'offload-images-js-css'));
        }
        
        try {
            $this->bucket->exists();
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
        
        // Use GCS URL
        $bucket = $this->settings['bucket'];
        return "https://storage.googleapis.com/{$bucket}/{$remote_path}";
    }
}
