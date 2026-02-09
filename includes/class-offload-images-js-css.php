<?php
/**
 * Main plugin class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Offload_Images_JS_CSS {
    
    /**
     * Single instance of the class
     */
    protected static $_instance = null;
    
    /**
     * Settings instance
     */
    public $settings;
    
    /**
     * Uploader instance
     */
    public $uploader;
    
    /**
     * Main instance
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once OIJC_PLUGIN_DIR . 'includes/class-dependency-checker.php';
        require_once OIJC_PLUGIN_DIR . 'includes/class-settings.php';
        require_once OIJC_PLUGIN_DIR . 'includes/class-uploader.php';
        require_once OIJC_PLUGIN_DIR . 'includes/class-bulk-offload.php';
        require_once OIJC_PLUGIN_DIR . 'includes/providers/class-provider-base.php';
        require_once OIJC_PLUGIN_DIR . 'includes/providers/class-s3-provider.php';
        require_once OIJC_PLUGIN_DIR . 'includes/providers/class-spaces-provider.php';
        require_once OIJC_PLUGIN_DIR . 'includes/providers/class-gcs-provider.php';
        
        $this->settings = new OIJC_Settings();
        $this->uploader = new OIJC_Uploader();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_filter('wp_get_attachment_url', array($this, 'filter_attachment_url'), 10, 2);
        add_filter('wp_get_attachment_image_src', array($this, 'filter_attachment_image_src'), 10, 4);
    }
    
    /**
     * Add admin menu
     */
    public function admin_menu() {
        add_menu_page(
            __('Offload Media', 'offload-images-js-css'),
            __('Offload Media', 'offload-images-js-css'),
            'manage_options',
            'offload-images-js-css',
            array($this->settings, 'render_settings_page'),
            'dashicons-cloud-upload',
            80
        );
        
        add_submenu_page(
            'offload-images-js-css',
            __('Settings', 'offload-images-js-css'),
            __('Settings', 'offload-images-js-css'),
            'manage_options',
            'offload-images-js-css',
            array($this->settings, 'render_settings_page')
        );
        
        add_submenu_page(
            'offload-images-js-css',
            __('Bulk Offload', 'offload-images-js-css'),
            __('Bulk Offload', 'offload-images-js-css'),
            'manage_options',
            'offload-bulk-offload',
            array($this, 'render_bulk_offload_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function admin_scripts($hook) {
        if (strpos($hook, 'offload') === false) {
            return;
        }
        
        wp_enqueue_style('oijc-admin', OIJC_PLUGIN_URL . 'assets/css/admin.css', array(), OIJC_VERSION);
        wp_enqueue_script('oijc-admin', OIJC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), OIJC_VERSION, true);
        
        wp_localize_script('oijc-admin', 'oijc_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('oijc_ajax_nonce')
        ));
    }
    
    /**
     * Filter attachment URL
     */
    public function filter_attachment_url($url, $post_id) {
        $remote_url = get_post_meta($post_id, 'oijc_remote_url', true);
        if ($remote_url) {
            return $remote_url;
        }
        return $url;
    }
    
    /**
     * Filter attachment image src
     */
    public function filter_attachment_image_src($image, $attachment_id, $size, $icon) {
        if ($image && isset($image[0])) {
            $remote_url = get_post_meta($attachment_id, 'oijc_remote_url_' . $size, true);
            if (!$remote_url) {
                $remote_url = get_post_meta($attachment_id, 'oijc_remote_url', true);
            }
            if ($remote_url) {
                $image[0] = $remote_url;
            }
        }
        return $image;
    }
    
    /**
     * Render bulk offload page
     */
    public function render_bulk_offload_page() {
        require_once OIJC_PLUGIN_DIR . 'includes/views/bulk-offload.php';
    }
    
    /**
     * Activation hook
     */
    public static function activate() {
        // Create options table if needed
        add_option('oijc_settings', array());
        flush_rewrite_rules();
    }
    
    /**
     * Deactivation hook
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
