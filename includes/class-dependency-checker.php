<?php
/**
 * Dependency Checker - Displays admin notices for missing SDKs
 */

if (!defined('ABSPATH')) {
    exit;
}

class OIJC_Dependency_Checker {
    
    public function __construct() {
        add_action('admin_notices', array($this, 'check_dependencies'));
    }
    
    /**
     * Check if required SDKs are available
     */
    public function check_dependencies() {
        // Only show on plugin pages
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'offload') === false) {
            return;
        }
        
        $settings = get_option('oijc_settings', array());
        $provider = isset($settings['provider']) ? $settings['provider'] : '';
        
        if (empty($provider)) {
            return;
        }
        
        $missing_sdk = false;
        $sdk_name = '';
        $install_command = '';
        
        // Check for AWS SDK (needed for S3 and Spaces)
        if (($provider === 's3' || $provider === 'spaces') && !class_exists('Aws\S3\S3Client')) {
            $missing_sdk = true;
            $sdk_name = 'AWS SDK for PHP';
            $install_command = 'composer require aws/aws-sdk-php';
        }
        
        // Check for Google Cloud SDK
        if ($provider === 'gcs' && !class_exists('Google\Cloud\Storage\StorageClient')) {
            $missing_sdk = true;
            $sdk_name = 'Google Cloud Storage PHP Library';
            $install_command = 'composer require google/cloud-storage';
        }
        
        if ($missing_sdk) {
            ?>
            <div class="notice notice-error">
                <h3>⚠️ <?php _e('Missing Required Library', 'offload-images-js-css'); ?></h3>
                <p>
                    <strong><?php echo esc_html($sdk_name); ?></strong> <?php _e('is required for your selected provider but is not installed.', 'offload-images-js-css'); ?>
                </p>
                <p><?php _e('To install it, run this command in your WordPress root directory or plugin directory:', 'offload-images-js-css'); ?></p>
                <p>
                    <code style="background: #f0f0f0; padding: 8px 12px; display: inline-block; border-radius: 4px; font-size: 14px;">
                        <?php echo esc_html($install_command); ?>
                    </code>
                </p>
                <p>
                    <?php _e('If you don\'t have Composer installed, you can:', 'offload-images-js-css'); ?>
                </p>
                <ol>
                    <li><?php _e('Install Composer from', 'offload-images-js-css'); ?> <a href="https://getcomposer.org/" target="_blank">getcomposer.org</a></li>
                    <li><?php _e('Or download the SDK manually and place it in the plugin directory', 'offload-images-js-css'); ?></li>
                </ol>
                <p>
                    <strong><?php _e('Need help?', 'offload-images-js-css'); ?></strong> 
                    <?php _e('Contact', 'offload-images-js-css'); ?> 
                    <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Check if SDK is available for a specific provider
     */
    public static function is_sdk_available($provider) {
        if ($provider === 's3' || $provider === 'spaces') {
            return class_exists('Aws\S3\S3Client');
        }
        
        if ($provider === 'gcs') {
            return class_exists('Google\Cloud\Storage\StorageClient');
        }
        
        return false;
    }
}

// Initialize
new OIJC_Dependency_Checker();
