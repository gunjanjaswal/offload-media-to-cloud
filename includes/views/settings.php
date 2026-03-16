<?php
/**
 * Settings page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('omtc_settings', array());
$provider = isset($settings['provider']) ? $settings['provider'] : '';
?>

<div class="wrap omtc-settings-wrap">
    <h1>⚡ <?php _e('Offload Media Settings', 'offload-media-to-cloud'); ?></h1>
    
    <div class="omtc-settings-container">
        <form id="omtc-settings-form" method="post" action="options.php">
            <?php settings_fields('omtc_settings_group'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="provider">
                            <span style="font-size: 1.1em;">☁️</span> <?php _e('Storage Provider', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <select name="omtc_settings[provider]" id="provider" class="regular-text">
                            <option value=""><?php _e('— Select Your Cloud Provider —', 'offload-media-to-cloud'); ?></option>
                            <option value="s3" <?php selected($provider, 's3'); ?>>🔶 Amazon S3</option>
                            <option value="spaces" <?php selected($provider, 'spaces'); ?>>🌊 DigitalOcean Spaces</option>
                            <option value="gcs" <?php selected($provider, 'gcs'); ?>>☁️ Google Cloud Storage</option>
                        </select>
                        <p class="description"><?php _e('Choose your preferred cloud storage provider for media offloading', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="access_key">
                            <span style="font-size: 1.1em;">🔑</span> <?php _e('Access Key', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[access_key]" id="access_key" value="<?php echo esc_attr(isset($settings['access_key']) ? $settings['access_key'] : ''); ?>" class="regular-text" placeholder="AKIAIOSFODNN7EXAMPLE">
                        <p class="description"><?php _e('Your cloud storage access key. For GCS, use an HMAC access key (Cloud Storage > Settings > Interoperability).', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="secret_key">
                            <span style="font-size: 1.1em;">🔐</span> <?php _e('Secret Key', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="password" name="omtc_settings[secret_key]" id="secret_key" value="<?php echo esc_attr(isset($settings['secret_key']) ? $settings['secret_key'] : ''); ?>" class="regular-text" placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
                        <p class="description"><?php _e('Your cloud storage secret key. For GCS, use the HMAC secret key.', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="bucket">
                            <span style="font-size: 1.1em;">🗂️</span> <?php _e('Bucket Name', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[bucket]" id="bucket" value="<?php echo esc_attr(isset($settings['bucket']) ? $settings['bucket'] : ''); ?>" class="regular-text" placeholder="my-media-bucket">
                        <p class="description"><?php _e('The name of your storage bucket or space', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="region">
                            <span style="font-size: 1.1em;">🌍</span> <?php _e('Region', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[region]" id="region" value="<?php echo esc_attr(isset($settings['region']) ? $settings['region'] : ''); ?>" class="regular-text" placeholder="us-east-1">
                        <p class="description"><?php _e('Region code: us-east-1 (S3), nyc3 (Spaces), us-central1 (GCS)', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="cdn_url">
                            <span style="font-size: 1.1em;">🚀</span> <?php _e('CDN URL', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="url" name="omtc_settings[cdn_url]" id="cdn_url" value="<?php echo esc_attr(isset($settings['cdn_url']) ? $settings['cdn_url'] : ''); ?>" class="regular-text" placeholder="https://cdn.example.com">
                        <p class="description"><?php _e('Optional: Custom domain or CloudFront URL for faster delivery (no trailing slash)', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="path_prefix">
                            <span style="font-size: 1.1em;">📁</span> <?php _e('Path Prefix', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[path_prefix]" id="path_prefix" value="<?php echo esc_attr(isset($settings['path_prefix']) ? $settings['path_prefix'] : ''); ?>" class="regular-text" placeholder="wp-content/uploads">
                        <p class="description"><?php _e('Optional: Add a folder prefix to organize your cloud files', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="remove_local_files">
                            <span style="font-size: 1.1em;">🗑️</span> <?php _e('Local Files', 'offload-media-to-cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="omtc_settings[remove_local_files]" id="remove_local_files" value="1" <?php checked(isset($settings['remove_local_files']) ? $settings['remove_local_files'] : false, true); ?>>
                            <span><?php _e('Automatically remove files from server after successful cloud upload', 'offload-media-to-cloud'); ?></span>
                        </label>
                        <p class="description" style="margin-top: 0.75rem;"><?php _e('⚠️ Warning: Files will only exist in cloud storage. Ensure backups are in place.', 'offload-media-to-cloud'); ?></p>
                    </td>
                </tr>
            </table>
            
            <p class="submit" style="display: flex; gap: 1rem; align-items: center;">
                <button type="button" id="test-connection" class="button button-secondary">
                    <span style="font-size: 1.1em;">🔌</span> <?php _e('Test Connection', 'offload-media-to-cloud'); ?>
                </button>
                <?php submit_button(__('💾 Save Settings', 'offload-media-to-cloud'), 'primary', 'submit', false); ?>
            </p>
        </form>
        
        <div id="connection-status" style="display:none; margin-top: 20px;"></div>
    </div>
    
    <div class="omtc-support-box">
        <div class="omtc-support-content">
            <h3><?php _e('Enjoying this plugin?', 'offload-media-to-cloud'); ?></h3>
            <p><?php _e('Your support helps keep it free, updated, and actively maintained for everyone.', 'offload-media-to-cloud'); ?></p>
            <a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" class="omtc-bmc-button">
                <img src="https://cdn.buymeacoffee.com/buttons/bmc-new-btn-logo.svg" alt="Coffee icon" width="20" height="20">
                <span><?php _e('Buy me a coffee', 'offload-media-to-cloud'); ?></span>
            </a>
        </div>
        <div class="omtc-developer-info">
            <div><strong><?php _e('Developer:', 'offload-media-to-cloud'); ?></strong> Gunjan Jaswal</div>
            <div><strong><?php _e('Email:', 'offload-media-to-cloud'); ?></strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a></div>
            <div><strong><?php _e('Website:', 'offload-media-to-cloud'); ?></strong> <a href="https://www.gunjanjaswal.me" target="_blank">www.gunjanjaswal.me</a></div>
        </div>
    </div>
</div>
