<?php
/**
 * Base provider class
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class OIJC_Provider_Base {
    
    protected $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
    }
    
    /**
     * Upload file to cloud storage
     * 
     * @param string $file_path Local file path
     * @param string $remote_path Remote file path
     * @return array Result with 'success' and 'url' or 'message'
     */
    abstract public function upload_file($file_path, $remote_path);
    
    /**
     * Delete file from cloud storage
     * 
     * @param string $remote_path Remote file path
     * @return array Result with 'success' and 'message'
     */
    abstract public function delete_file($remote_path);
    
    /**
     * Test connection to cloud storage
     * 
     * @return array Result with 'success' and 'message'
     */
    abstract public function test_connection();
    
    /**
     * Get file URL
     * 
     * @param string $remote_path Remote file path
     * @return string File URL
     */
    abstract public function get_file_url($remote_path);
    
    /**
     * Get MIME type of file
     */
    protected function get_mime_type($file_path) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $mime_type;
    }
}
