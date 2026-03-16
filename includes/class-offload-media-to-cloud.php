<?php
/**
 * Main plugin class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Offload_Media_To_Cloud {
    
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
        require_once OMTC_PLUGIN_DIR . 'includes/class-dependency-checker.php';
        require_once OMTC_PLUGIN_DIR . 'includes/class-settings.php';
        require_once OMTC_PLUGIN_DIR . 'includes/class-uploader.php';
        require_once OMTC_PLUGIN_DIR . 'includes/class-bulk-offload.php';
        require_once OMTC_PLUGIN_DIR . 'includes/providers/class-provider-base.php';
        require_once OMTC_PLUGIN_DIR . 'includes/providers/class-s3-provider.php';
        require_once OMTC_PLUGIN_DIR . 'includes/providers/class-spaces-provider.php';
        require_once OMTC_PLUGIN_DIR . 'includes/providers/class-gcs-provider.php';
        
        $this->settings = new OMTC_Settings();
        $this->uploader = new OMTC_Uploader();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_filter('wp_get_attachment_url', array($this, 'filter_attachment_url'), 10, 2);
        add_filter('wp_get_attachment_image_src', array($this, 'filter_attachment_image_src'), 10, 4);
        add_filter('plugin_action_links_' . OMTC_PLUGIN_BASENAME, array($this, 'plugin_action_links'));
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
    }
    
    /**
     * Add admin menu
     */
    public function admin_menu() {
        add_menu_page(
            __('Offload Media', 'offload-media-to-cloud'),
            __('Offload Media', 'offload-media-to-cloud'),
            'manage_options',
            'offload-media-to-cloud',
            array($this->settings, 'render_settings_page'),
            'dashicons-cloud-upload',
            80
        );
        
        add_submenu_page(
            'offload-media-to-cloud',
            __('Settings', 'offload-media-to-cloud'),
            __('Settings', 'offload-media-to-cloud'),
            'manage_options',
            'offload-media-to-cloud',
            array($this->settings, 'render_settings_page')
        );
        
        add_submenu_page(
            'offload-media-to-cloud',
            __('Bulk Offload', 'offload-media-to-cloud'),
            __('Bulk Offload', 'offload-media-to-cloud'),
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
        
        wp_enqueue_style('omtc-admin', OMTC_PLUGIN_URL . 'assets/css/admin.css', array(), OMTC_VERSION);
        wp_enqueue_script('omtc-admin', OMTC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), OMTC_VERSION, true);
        
        wp_localize_script('omtc-admin', 'omtc_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('omtc_ajax_nonce')
        ));
    }
    
    /**
     * Filter attachment URL
     */
    public function filter_attachment_url($url, $post_id) {
        $remote_url = get_post_meta($post_id, 'omtc_remote_url', true);
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
            $remote_url = get_post_meta($attachment_id, 'omtc_remote_url_' . $size, true);
            if (!$remote_url) {
                $remote_url = get_post_meta($attachment_id, 'omtc_remote_url', true);
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
        require_once OMTC_PLUGIN_DIR . 'includes/views/bulk-offload.php';
    }
    
    /**
     * Add action links on Plugins page
     */
    public function plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=offload-media-to-cloud') . '">' . __('Settings', 'offload-media-to-cloud') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add meta links on Plugins page (row meta)
     */
    public function plugin_row_meta($links, $file) {
        if ($file === OMTC_PLUGIN_BASENAME) {
            $links[] = '<a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" style="color: #ff813f; font-weight: 600;">&#9749; Buy Me a Coffee</a>';
        }
        return $links;
    }

    /**
     * Activation hook
     */
    public static function activate() {
        // Create options table if needed
        add_option('omtc_settings', array());
        flush_rewrite_rules();
    }
    
    /**
     * Deactivation hook
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
