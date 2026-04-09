<?php
/**
 * Settings class
 */

if (!defined('ABSPATH')) {
    exit;
}

class g33ki_settings {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('g33ki_settings', array());
        add_action('wp_ajax_g33ki_save_settings', array($this, 'save_settings_ajax'));
        add_action('wp_ajax_g33ki_test_connection', array($this, 'test_connection_ajax'));
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        if (isset($input['provider'])) {
            $sanitized['provider'] = sanitize_text_field($input['provider']);
        }
        
        if (isset($input['access_key'])) {
            $sanitized['access_key'] = sanitize_text_field($input['access_key']);
        }
        
        if (isset($input['secret_key'])) {
            $sanitized['secret_key'] = sanitize_text_field($input['secret_key']);
        }
        
        if (isset($input['bucket'])) {
            $sanitized['bucket'] = sanitize_text_field($input['bucket']);
        }
        
        if (isset($input['region'])) {
            $sanitized['region'] = sanitize_text_field($input['region']);
        }
        
        if (isset($input['cdn_url'])) {
            $sanitized['cdn_url'] = esc_url_raw($input['cdn_url']);
        }
        
        if (isset($input['remove_local_files'])) {
            $sanitized['remove_local_files'] = (bool) $input['remove_local_files'];
        }
        
        if (isset($input['path_prefix'])) {
            $sanitized['path_prefix'] = sanitize_text_field($input['path_prefix']);
        }
        
        return $sanitized;
    }
    
    /**
     * Get option
     */
    public function get_option($key, $default = '') {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        require_once G33KI_PLUGIN_DIR . 'includes/views/settings.php';
    }
    
    /**
     * Save settings via AJAX
     */
    public function save_settings_ajax() {
        check_ajax_referer('g33ki_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'g33ki-cloud-storage-for-media-library')));
        }
        
        $settings = isset($_POST['settings']) ? array_map('sanitize_text_field', wp_unslash($_POST['settings'])) : array();
        $sanitized = $this->sanitize_settings($settings);
        update_option('g33ki_settings', $sanitized);
        
        wp_send_json_success(array('message' => __('Settings saved successfully', 'g33ki-cloud-storage-for-media-library')));
    }
    
    /**
     * Test connection via AJAX
     */
    public function test_connection_ajax() {
        check_ajax_referer('g33ki_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'g33ki-cloud-storage-for-media-library')));
        }
        
        $provider = isset($_POST['provider']) ? sanitize_text_field(wp_unslash($_POST['provider'])) : '';
        $credentials = isset($_POST['credentials']) ? array_map('sanitize_text_field', wp_unslash($_POST['credentials'])) : array();
        
        // Get provider instance
        $provider_class = 'G33KI_' . ucfirst($provider) . '_Provider';
        if (!class_exists($provider_class)) {
            wp_send_json_error(array('message' => __('Invalid provider', 'g33ki-cloud-storage-for-media-library')));
        }
        
        $provider_instance = new $provider_class($credentials);
        $result = $provider_instance->test_connection();
        
        if ($result['success']) {
            wp_send_json_success(array('message' => __('Connection successful!', 'g33ki-cloud-storage-for-media-library')));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
}


