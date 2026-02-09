<?php
/**
 * Plugin Name: Offload Images JS CSS
 * Plugin URI: https://github.com/gunjanjaswal/Offload-Images-JS-CSS
 * Description: Seamlessly transfer and serve your WordPress media files from Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. Automatically sync new uploads and optionally migrate existing media with one-click bulk offload.
 * Version: 1.0.0
 * Author: Gunjan Jaswal
 * Author URI: https://www.gunjanjaswal.me
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: offload-images-js-css
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('OIJC_VERSION', '1.0.0');
define('OIJC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OIJC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('OIJC_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Load Composer autoloader if available
if (file_exists(OIJC_PLUGIN_DIR . 'vendor/autoload.php')) {
    require_once OIJC_PLUGIN_DIR . 'vendor/autoload.php';
}

// Require the main plugin class
require_once OIJC_PLUGIN_DIR . 'includes/class-offload-images-js-css.php';

// Initialize the plugin
function oijc_init() {
    return Offload_Images_JS_CSS::instance();
}

// Start the plugin
add_action('plugins_loaded', 'oijc_init');

// Activation hook
register_activation_hook(__FILE__, array('Offload_Images_JS_CSS', 'activate'));

// Deactivation hook
register_deactivation_hook(__FILE__, array('Offload_Images_JS_CSS', 'deactivate'));
