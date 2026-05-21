<?php
/**
 * Plugin Name: G33ki Cloud Storage For Media Library
 * Plugin URI: https://github.com/gunjanjaswal/g33ki-cloud-storage-for-media-library
 * Description: Seamlessly offload your WordPress media library to Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. Effortlessly move media to cloud for better performance and CDN delivery.
 * Version: 1.2.4
 * Author: Gunjan Jaswal
 * Author URI: https://gunjanjaswal.me
 * Requires at least: 5.0
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: g33ki-cloud-storage-for-media-library
 * Domain Path:
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('G33KI_VERSION', '1.2.4');
define('G33KI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('G33KI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('G33KI_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Settings migration logic (OMTC to G33KI)
function g33ki_migrate_settings() {
    $current = get_option('g33ki_settings');
    $old_settings = get_option('omtc_settings');
    
    if ((empty($current) || empty($current['provider'])) && !empty($old_settings)) {
        update_option('g33ki_settings', $old_settings);
    }
}
g33ki_migrate_settings();

// Require the S3 signing utility
require_once G33KI_PLUGIN_DIR . 'includes/class-s3-signing.php';

// Require the main plugin class
require_once G33KI_PLUGIN_DIR . 'includes/class-g33ki-cloud-storage-for-media-library.php';

// Initialize the plugin
function g33ki_init() {
    return G33ki_Cloud_Storage_For_Media_Library::instance();
}

// Start the plugin
add_action('plugins_loaded', 'g33ki_init');

// Activation hook
register_activation_hook(__FILE__, array('G33ki_Cloud_Storage_For_Media_Library', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('G33ki_Cloud_Storage_For_Media_Library', 'deactivate'));

/* -------------------------------------------------------------------------
 * WordPress 7.0 Connectors API integration
 *
 * WordPress 7.0 ships a Connectors API + central Connections screen. Plugins
 * register on the `wp_connectors_init` action by calling
 * `$registry->register( $id, $args )`. The API's `api_key` authentication
 * method handles a SINGLE setting value (a key string, optionally backed by
 * a PHP constant or environment variable).
 *
 * This plugin needs multiple credentials per provider (access_key,
 * secret_key, bucket, region, …) all stored inside one array option
 * `g33ki_settings`. That shape does not fit the Connectors API's single
 * setting_name model, so a clean central UI is not possible without
 * splitting the option (a bigger refactor planned for a later release).
 *
 * Two integration points are still useful today:
 *   1. Register an informational connector per provider with `method: none`
 *      so the Connections screen links users to this plugin's settings page
 *      for credential management.
 *   2. Fire the `g33ki_register_connectors` action so third-party code can
 *      register a richer mapping when the core API evolves to support
 *      multi-field credentials.
 * ---------------------------------------------------------------------- */

function g33ki_register_connectors($registry) {
    if (!is_object($registry) || !method_exists($registry, 'register')) {
        do_action('g33ki_register_connectors', false, null);
        return;
    }

    $settings_url = admin_url('admin.php?page=g33ki-cloud-storage-for-media-library');
    $providers = array(
        'g33ki-amazon-s3' => array(
            'name'        => __('Amazon S3 (G33ki Cloud Storage)', 'g33ki-cloud-storage-for-media-library'),
            'description' => __('Offload media to Amazon S3. Manage credentials on the G33ki Cloud Storage settings screen.', 'g33ki-cloud-storage-for-media-library'),
        ),
        'g33ki-digitalocean-spaces' => array(
            'name'        => __('DigitalOcean Spaces (G33ki Cloud Storage)', 'g33ki-cloud-storage-for-media-library'),
            'description' => __('Offload media to DigitalOcean Spaces. Manage credentials on the G33ki Cloud Storage settings screen.', 'g33ki-cloud-storage-for-media-library'),
        ),
        'g33ki-google-cloud-storage' => array(
            'name'        => __('Google Cloud Storage (G33ki Cloud Storage)', 'g33ki-cloud-storage-for-media-library'),
            'description' => __('Offload media to Google Cloud Storage (via HMAC keys). Manage credentials on the G33ki Cloud Storage settings screen.', 'g33ki-cloud-storage-for-media-library'),
        ),
    );

    foreach ($providers as $id => $meta) {
        $registry->register(
            $id,
            array(
                'name'           => $meta['name'],
                'description'    => $meta['description'],
                'type'           => 'cloud_storage',
                'authentication' => array(
                    'method'          => 'none',
                    'credentials_url' => $settings_url,
                ),
                'plugin'         => array(
                    'file'      => G33KI_PLUGIN_BASENAME,
                    'is_active' => function () {
                        return defined('G33KI_VERSION');
                    },
                ),
            )
        );
    }

    /**
     * Fires after the plugin registers its cloud-storage connectors.
     *
     * Third-party code can use this to register richer connectors against
     * the same providers once the core API supports multi-field credentials.
     *
     * @param bool                  $registered True if registration ran.
     * @param WP_Connector_Registry $registry   Core connector registry.
     */
    do_action('g33ki_register_connectors', true, $registry);
}
add_action('wp_connectors_init', 'g33ki_register_connectors');


