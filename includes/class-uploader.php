<?php
/**
 * Uploader class - handles automatic upload of new media
 */

if (!defined('ABSPATH')) {
    exit;
}

class G33KI_Uploader {
    
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('g33ki_settings', array());
        
        // Hook into WordPress upload process
        add_filter('wp_generate_attachment_metadata', array($this, 'upload_attachment'), 10, 2);
        add_action('delete_attachment', array($this, 'delete_attachment'));
    }
    
    /**
     * Upload attachment and thumbnails to cloud storage
     */
    public function upload_attachment($metadata, $attachment_id) {
        if (empty($this->settings['provider'])) {
            return $metadata;
        }
        
        $provider = $this->get_provider();
        if (!$provider) {
            return $metadata;
        }
        
        $file_path = get_attached_file($attachment_id);
        if (!file_exists($file_path)) {
            return $metadata;
        }
        
        // Upload main file
        $remote_path = $this->get_remote_path($file_path);
        $result = $provider->upload_file($file_path, $remote_path);
        
        if ($result['success']) {
            update_post_meta($attachment_id, 'g33ki_remote_url', $result['url']);
            update_post_meta($attachment_id, 'g33ki_remote_path', $remote_path);
            
            // Upload thumbnails
            if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                $upload_dir = wp_upload_dir();
                $base_dir = dirname($file_path);
                
                foreach ($metadata['sizes'] as $size => $size_data) {
                    $thumb_path = $base_dir . '/' . $size_data['file'];
                    if (file_exists($thumb_path)) {
                        $thumb_remote_path = $this->get_remote_path($thumb_path);
                        $thumb_result = $provider->upload_file($thumb_path, $thumb_remote_path);
                        
                        if ($thumb_result['success']) {
                            update_post_meta($attachment_id, 'g33ki_remote_url_' . $size, $thumb_result['url']);
                        }
                    }
                }
            }
            
            // Remove local files if option is enabled
            if (!empty($this->settings['remove_local_files'])) {
                $this->remove_local_file($attachment_id, $file_path, $metadata);
            }
        }
        
        return $metadata;
    }
    
    /**
     * Delete attachment from cloud storage
     */
    public function delete_attachment($attachment_id) {
        $remote_path = get_post_meta($attachment_id, 'g33ki_remote_path', true);
        if (!$remote_path) {
            $remote_path = get_post_meta($attachment_id, 'omtc_remote_path', true);
        }
        if (!$remote_path) {
            return;
        }
        
        $provider = $this->get_provider();
        if (!$provider) {
            return;
        }
        
        $provider->delete_file($remote_path);
        
        // Delete thumbnails
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumb_remote_url = get_post_meta($attachment_id, 'g33ki_remote_url_' . $size, true);
                if (!$thumb_remote_url) {
                    $thumb_remote_url = get_post_meta($attachment_id, 'omtc_remote_url_' . $size, true);
                }
                if ($thumb_remote_url) {
                    $thumb_remote_path = $this->get_remote_path_from_url($thumb_remote_url);
                    $provider->delete_file($thumb_remote_path);
                }
            }
        }
    }
    
    /**
     * Get provider instance
     */
    private function get_provider() {
        if (empty($this->settings['provider'])) {
            return null;
        }
        
        $provider_class = 'G33KI_' . ucfirst($this->settings['provider']) . '_Provider';
        if (!class_exists($provider_class)) {
            return null;
        }
        
        return new $provider_class($this->settings);
    }
    
    /**
     * Get remote path for file
     */
    private function get_remote_path($file_path) {
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $file_path);
        
        $prefix = !empty($this->settings['path_prefix']) ? trailingslashit($this->settings['path_prefix']) : '';
        return $prefix . $relative_path;
    }
    
    /**
     * Get remote path from URL
     */
    private function get_remote_path_from_url($url) {
        $bucket = $this->settings['bucket'];
        $parts = wp_parse_url($url);
        $path = ltrim($parts['path'], '/');
        
        // Remove bucket name if present in path
        if (strpos($path, $bucket . '/') === 0) {
            $path = substr($path, strlen($bucket) + 1);
        }
        
        return $path;
    }
    
    /**
     * Remove local file
     */
    private function remove_local_file($attachment_id, $file_path, $metadata) {
        if (file_exists($file_path)) {
            wp_delete_file($file_path);
        }
        
        // Remove thumbnails
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            $base_dir = dirname($file_path);
            foreach ($metadata['sizes'] as $size_data) {
                $thumb_path = $base_dir . '/' . $size_data['file'];
                if (file_exists($thumb_path)) {
                    wp_delete_file($thumb_path);
                }
            }
        }
    }
}


