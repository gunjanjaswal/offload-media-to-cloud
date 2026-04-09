<?php
/**
 * Plugin Name: G33ki Cloud Storage For Media Library
 * Plugin URI: https://github.com/gunjanjaswal/g33ki-cloud-storage-for-media-library
 * Description: Seamlessly offload your WordPress media library to Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. Effortlessly move media to cloud for better performance and CDN delivery.
 * Version: 1.2.2
 * Author: Gunjan Jaswal
 * Author URI: https://gunjanjaswal.me
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
define('G33KI_VERSION', '1.2.2');
define('G33KI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('G33KI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('G33KI_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Settings migration logic (OMTC to G33KI)
function g33ki_migrate_settings() {
    if (!get_option('g33ki_settings') && $old_settings = get_option('omtc_settings')) {
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


