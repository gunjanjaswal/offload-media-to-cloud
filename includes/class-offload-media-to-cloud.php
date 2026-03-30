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
        require_once OMTC_PLUGIN_DIR . 'includes/class-bulk-restore.php';
        require_once OMTC_PLUGIN_DIR . 'includes/class-fix-permissions.php';
        require_once OMTC_PLUGIN_DIR . 'includes/class-fix-thumbnails.php';
        require_once OMTC_PLUGIN_DIR . 'includes/class-fix-urls.php';
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
        add_filter('wp_calculate_image_srcset', array($this, 'filter_image_srcset'), 10, 5);
        add_filter('the_content', array($this, 'filter_content_urls'), 99);
        add_filter('post_thumbnail_html', array($this, 'filter_content_urls'), 99);
        add_filter('widget_text', array($this, 'filter_content_urls'), 99);
        add_filter('get_custom_logo', array($this, 'filter_content_urls'), 99);
        add_filter('wp_get_attachment_image', array($this, 'filter_content_urls'), 99);
        add_filter('get_header_image_tag', array($this, 'filter_content_urls'), 99);
        add_filter('plugin_action_links_' . OMTC_PLUGIN_BASENAME, array($this, 'plugin_action_links'));
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2);
        add_action('admin_notices', array($this, 'deactivation_warning_notice'));

        // Output buffer to catch theme-hardcoded upload URLs (header, footer, etc.)
        if (!is_admin()) {
            add_filter('wp_template_enhancement_output_buffer', array($this, 'filter_output_buffer'));
        }
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

        add_submenu_page(
            'offload-media-to-cloud',
            __('Restore Local', 'offload-media-to-cloud'),
            __('Restore Local', 'offload-media-to-cloud'),
            'manage_options',
            'offload-bulk-restore',
            array($this, 'render_bulk_restore_page')
        );

        add_submenu_page(
            'offload-media-to-cloud',
            __('Fix Permissions', 'offload-media-to-cloud'),
            __('Fix Permissions', 'offload-media-to-cloud'),
            'manage_options',
            'offload-fix-permissions',
            array($this, 'render_fix_permissions_page')
        );

        add_submenu_page(
            'offload-media-to-cloud',
            __('Fix Thumbnails', 'offload-media-to-cloud'),
            __('Fix Thumbnails', 'offload-media-to-cloud'),
            'manage_options',
            'offload-fix-thumbnails',
            array($this, 'render_fix_thumbnails_page')
        );

        add_submenu_page(
            'offload-media-to-cloud',
            __('Fix URLs', 'offload-media-to-cloud'),
            __('Fix URLs', 'offload-media-to-cloud'),
            'manage_options',
            'offload-fix-urls',
            array($this, 'render_fix_urls_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function admin_scripts($hook) {
        if (strpos($hook, 'offload') === false) {
            return;
        }
        
        wp_enqueue_style('omtc-admin', OMTC_PLUGIN_URL . 'assets/css/admin.css', array(), OMTC_VERSION . '.2');
        wp_enqueue_script('omtc-admin', OMTC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), OMTC_VERSION . '.2', true);
        
        wp_localize_script('omtc-admin', 'omtc_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('omtc_ajax_nonce'),
            'i18n' => array(
                'scanned' => __('Scanned', 'offload-media-to-cloud'),
                'file_mismatched_found' => __('file(s) with mismatched URLs found.', 'offload-media-to-cloud'),
                'ajax_failed' => __('AJAX request failed', 'offload-media-to-cloud'),
                'fixed' => __('Fixed', 'offload-media-to-cloud'),
                'file_updated' => __('file(s) updated successfully.', 'offload-media-to-cloud'),
                'attachment' => __('Attachment', 'offload-media-to-cloud'),
            )
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
     * Filter srcset URLs to use cloud storage
     */
    public function filter_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
        if (!is_array($sources)) {
            return $sources;
        }

        $remote_url = get_post_meta($attachment_id, 'omtc_remote_url', true);
        if (!$remote_url) {
            return $sources;
        }

        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'];
        $settings = get_option('omtc_settings', array());
        $cloud_base = $this->get_cloud_base_url($settings);

        foreach ($sources as $width => $source) {
            if (strpos($source['url'], $base_url) !== false) {
                $relative = str_replace($base_url . '/', '', $source['url']);
                $prefix = !empty($settings['path_prefix']) ? trailingslashit($settings['path_prefix']) : '';
                $sources[$width]['url'] = $cloud_base . $prefix . $relative;
            }
        }

        return $sources;
    }

    /**
     * Replace local upload URLs in post content with cloud URLs
     */
    public function filter_content_urls($content) {
        if (empty($content)) {
            return $content;
        }

        $settings = get_option('omtc_settings', array());
        if (empty($settings['provider']) || empty($settings['bucket'])) {
            return $content;
        }

        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'];
        $cloud_base = $this->get_cloud_base_url($settings);
        $prefix = !empty($settings['path_prefix']) ? trailingslashit($settings['path_prefix']) : '';
        $cloud_url = $cloud_base . $prefix;

        // Replace full URL (https)
        $content = str_replace($base_url . '/', $cloud_url, $content);

        // Replace http version if site uses https
        if (strpos($base_url, 'https://') === 0) {
            $http_base = str_replace('https://', 'http://', $base_url);
            $content = str_replace($http_base . '/', $cloud_url, $content);
        }

        // Replace relative /wp-content/uploads/ paths in src attributes
        $relative_path = wp_parse_url($base_url, PHP_URL_PATH);
        if ($relative_path) {
            $content = str_replace('"' . $relative_path . '/', '"' . $cloud_url, $content);
            $content = str_replace("'" . $relative_path . "/", "'" . $cloud_url, $content);
        }

        return $content;
    }

    /**
     * Filter the entire page output to replace upload URLs
     */
    public function filter_output_buffer($html) {
        if (empty($html)) {
            return $html;
        }
        return $this->filter_content_urls($html);
    }

    /**
     * Get cloud base URL (CDN or origin)
     */
    private function get_cloud_base_url($settings) {
        if (!empty($settings['cdn_url'])) {
            return trailingslashit($settings['cdn_url']);
        }

        $bucket = $settings['bucket'];
        $region = isset($settings['region']) ? $settings['region'] : '';
        $provider = isset($settings['provider']) ? $settings['provider'] : '';

        if ($provider === 'spaces') {
            return "https://{$bucket}.{$region}.digitaloceanspaces.com/";
        } elseif ($provider === 'gcs') {
            return "https://storage.googleapis.com/{$bucket}/";
        } else {
            if ($region === 'us-east-1') {
                return "https://{$bucket}.s3.amazonaws.com/";
            }
            return "https://{$bucket}.s3.{$region}.amazonaws.com/";
        }
    }

    /**
     * Render bulk offload page
     */
    public function render_bulk_offload_page() {
        require_once OMTC_PLUGIN_DIR . 'includes/views/bulk-offload.php';
    }

    /**
     * Render bulk restore page
     */
    public function render_bulk_restore_page() {
        require_once OMTC_PLUGIN_DIR . 'includes/views/bulk-restore.php';
    }
    
    /**
     * Render fix permissions page
     */
    public function render_fix_permissions_page() {
        require_once OMTC_PLUGIN_DIR . 'includes/views/fix-permissions.php';
    }

    /**
     * Render fix thumbnails page
     */
    public function render_fix_thumbnails_page() {
        require_once OMTC_PLUGIN_DIR . 'includes/views/fix-thumbnails.php';
    }

    /**
     * Render fix URLs page
     */
    public function render_fix_urls_page() {
        require_once OMTC_PLUGIN_DIR . 'includes/views/fix-urls.php';
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
     * Show warning on plugins page if local files are missing
     */
    public function deactivation_warning_notice() {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'plugins') {
            return;
        }

        $settings = get_option('omtc_settings', array());
        if (empty($settings['remove_local_files'])) {
            return;
        }

        // Quick check: sample a few offloaded attachments to see if local files are missing
        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => 5,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => 'omtc_remote_url',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $query = new WP_Query($args);
        $missing = false;
        foreach ($query->posts as $id) {
            if (!file_exists(get_attached_file($id))) {
                $missing = true;
                break;
            }
        }

        if (!$missing) {
            return;
        }

        $restore_url = admin_url('admin.php?page=offload-bulk-restore');
        echo '<div class="notice notice-warning">';
        echo '<p><strong>' . esc_html__('Offload Media to Cloud', 'offload-media-to-cloud') . ':</strong> ';
        echo esc_html__('Some media files exist only in cloud storage. If you deactivate this plugin, those media URLs will break.', 'offload-media-to-cloud') . ' ';
        echo '<a href="' . esc_url($restore_url) . '"><strong>' . esc_html__('Restore local files first', 'offload-media-to-cloud') . '</strong></a>';
        echo '</p></div>';
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
