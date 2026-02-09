<?php
/**
 * Settings class
 */

if (!defined('ABSPATH')) {
    exit;
}

class OIJC_Settings {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('oijc_settings', array());
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_ajax_oijc_save_settings', array($this, 'save_settings_ajax'));
        add_action('wp_ajax_oijc_test_connection', array($this, 'test_connection_ajax'));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('oijc_settings_group', 'oijc_settings', array($this, 'sanitize_settings'));
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
        require_once OIJC_PLUGIN_DIR . 'includes/views/settings.php';
    }
    
    /**
     * Save settings via AJAX
     */
    public function save_settings_ajax() {
        check_ajax_referer('oijc_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-images-js-css')));
        }
        
        $settings = $_POST['settings'];
        $sanitized = $this->sanitize_settings($settings);
        update_option('oijc_settings', $sanitized);
        
        wp_send_json_success(array('message' => __('Settings saved successfully', 'offload-images-js-css')));
    }
    
    /**
     * Test connection via AJAX
     */
    public function test_connection_ajax() {
        check_ajax_referer('oijc_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'offload-images-js-css')));
        }
        
        $provider = sanitize_text_field($_POST['provider']);
        $credentials = $_POST['credentials'];
        
        // Get provider instance
        $provider_class = 'OIJC_' . ucfirst($provider) . '_Provider';
        if (!class_exists($provider_class)) {
            wp_send_json_error(array('message' => __('Invalid provider', 'offload-images-js-css')));
        }
        
        $provider_instance = new $provider_class($credentials);
        $result = $provider_instance->test_connection();
        
        if ($result['success']) {
            wp_send_json_success(array('message' => __('Connection successful!', 'offload-images-js-css')));
        } else {
            wp_send_json_error(array('message' => $result['message']));
        }
    }
}
