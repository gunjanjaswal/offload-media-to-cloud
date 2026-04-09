<?php
/**
 * Dependency Checker - Validates PHP requirements
 */

if (!defined('ABSPATH')) {
    exit;
}

class G33KI_Dependency_Checker {

    public function __construct() {
        add_action('admin_notices', array($this, 'check_dependencies'));
    }

    /**
     * Check if required PHP functions are available
     */
    public function check_dependencies() {
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'g33ki') === false) {
            return;
        }

        // Check for hash_hmac (needed for request signing)
        if (!function_exists('hash_hmac')) {
            ?>
            <div class="notice notice-error">
                <h3><?php esc_html_e('Missing Required PHP Extension', 'g33ki-cloud-storage-for-media-library'); ?></h3>
                <p><?php esc_html_e('The <strong>hash</strong> PHP extension is required for this plugin to work. Please contact your hosting provider to enable it.', 'g33ki-cloud-storage-for-media-library'); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Check if provider requirements are met
     */
    public static function is_sdk_available($provider) {
        // No external SDKs needed — all providers use built-in HTTP + signing
        return function_exists('hash_hmac');
    }
}

// Initialize
new G33KI_Dependency_Checker();


