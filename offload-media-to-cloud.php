<?php
/**
 * Plugin Name: Offload Media to Cloud
 * Plugin URI: https://github.com/gunjanjaswal/offload-media-to-cloud
 * Description: Seamlessly offload your WordPress media files to Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. Automatic sync, bulk migration, CDN support — no Composer or external SDKs required.
 * Version: 1.1.0
 * Author: Gunjan Jaswal
 * Author URI: https://gunjanjaswal.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: offload-media-to-cloud
 * Domain Path:
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('OMTC_VERSION', '1.1.0');
define('OMTC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OMTC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('OMTC_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Require the S3 signing utility
require_once OMTC_PLUGIN_DIR . 'includes/class-s3-signing.php';

// Require the main plugin class
require_once OMTC_PLUGIN_DIR . 'includes/class-offload-media-to-cloud.php';

// Initialize the plugin
function omtc_init() {
    return Offload_Media_To_Cloud::instance();
}

// Start the plugin
add_action('plugins_loaded', 'omtc_init');

// Activation hook
register_activation_hook(__FILE__, array('Offload_Media_To_Cloud', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('Offload_Media_To_Cloud', 'deactivate'));
