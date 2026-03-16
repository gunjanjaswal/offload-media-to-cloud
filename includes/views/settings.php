<?php
/**
 * Settings page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$omtc_settings = get_option('omtc_settings', array());
$omtc_provider = isset($omtc_settings['provider']) ? $omtc_settings['provider'] : '';
?>

<div class="wrap omtc-settings-wrap">
    <h1>⚡ <?php esc_html_e('Offload Media Settings', 'Offload-Media-to-Cloud'); ?></h1>
    
    <div class="omtc-settings-container">
        <form id="omtc-settings-form" method="post" action="options.php">
            <?php settings_fields('omtc_settings_group'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="provider">
                            <span style="font-size: 1.1em;">☁️</span> <?php esc_html_e('Storage Provider', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <select name="omtc_settings[provider]" id="provider" class="regular-text">
                            <option value=""><?php esc_html_e('— Select Your Cloud Provider —', 'Offload-Media-to-Cloud'); ?></option>
                            <option value="s3" <?php selected($omtc_provider, 's3'); ?>>🔶 Amazon S3</option>
                            <option value="spaces" <?php selected($omtc_provider, 'spaces'); ?>>🌊 DigitalOcean Spaces</option>
                            <option value="gcs" <?php selected($omtc_provider, 'gcs'); ?>>☁️ Google Cloud Storage</option>
                        </select>
                        <p class="description"><?php esc_html_e('Choose your preferred cloud storage provider for media offloading', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="access_key">
                            <span style="font-size: 1.1em;">🔑</span> <?php esc_html_e('Access Key', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[access_key]" id="access_key" value="<?php echo esc_attr(isset($omtc_settings['access_key']) ? $omtc_settings['access_key'] : ''); ?>" class="regular-text" placeholder="AKIAIOSFODNN7EXAMPLE">
                        <p class="description"><?php esc_html_e('Your cloud storage access key. For GCS, use an HMAC access key (Cloud Storage > Settings > Interoperability).', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="secret_key">
                            <span style="font-size: 1.1em;">🔐</span> <?php esc_html_e('Secret Key', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="password" name="omtc_settings[secret_key]" id="secret_key" value="<?php echo esc_attr(isset($omtc_settings['secret_key']) ? $omtc_settings['secret_key'] : ''); ?>" class="regular-text" placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
                        <p class="description"><?php esc_html_e('Your cloud storage secret key. For GCS, use the HMAC secret key.', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="bucket">
                            <span style="font-size: 1.1em;">🗂️</span> <?php esc_html_e('Bucket Name', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[bucket]" id="bucket" value="<?php echo esc_attr(isset($omtc_settings['bucket']) ? $omtc_settings['bucket'] : ''); ?>" class="regular-text" placeholder="my-media-bucket">
                        <p class="description"><?php esc_html_e('The name of your storage bucket or space', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="region">
                            <span style="font-size: 1.1em;">🌍</span> <?php esc_html_e('Region', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[region]" id="region" value="<?php echo esc_attr(isset($omtc_settings['region']) ? $omtc_settings['region'] : ''); ?>" class="regular-text" placeholder="us-east-1">
                        <p class="description"><?php esc_html_e('Region code: us-east-1 (S3), nyc3 (Spaces), us-central1 (GCS)', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="cdn_url">
                            <span style="font-size: 1.1em;">🚀</span> <?php esc_html_e('CDN URL', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="url" name="omtc_settings[cdn_url]" id="cdn_url" value="<?php echo esc_attr(isset($omtc_settings['cdn_url']) ? $omtc_settings['cdn_url'] : ''); ?>" class="regular-text" placeholder="https://cdn.example.com">
                        <p class="description"><?php esc_html_e('Optional: Custom domain or CloudFront URL for faster delivery (no trailing slash)', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="path_prefix">
                            <span style="font-size: 1.1em;">📁</span> <?php esc_html_e('Path Prefix', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="omtc_settings[path_prefix]" id="path_prefix" value="<?php echo esc_attr(isset($omtc_settings['path_prefix']) ? $omtc_settings['path_prefix'] : ''); ?>" class="regular-text" placeholder="wp-content/uploads">
                        <p class="description"><?php esc_html_e('Optional: Add a folder prefix to organize your cloud files', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="remove_local_files">
                            <span style="font-size: 1.1em;">🗑️</span> <?php esc_html_e('Local Files', 'Offload-Media-to-Cloud'); ?>
                        </label>
                    </th>
                    <td>
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="omtc_settings[remove_local_files]" id="remove_local_files" value="1" <?php checked(isset($omtc_settings['remove_local_files']) ? $omtc_settings['remove_local_files'] : false, true); ?>>
                            <span><?php esc_html_e('Automatically remove files from server after successful cloud upload', 'Offload-Media-to-Cloud'); ?></span>
                        </label>
                        <p class="description" style="margin-top: 0.75rem;"><?php esc_html_e('⚠️ Warning: Files will only exist in cloud storage. Ensure backups are in place.', 'Offload-Media-to-Cloud'); ?></p>
                    </td>
                </tr>
            </table>
            
            <p class="submit" style="display: flex; gap: 1rem; align-items: center;">
                <button type="button" id="test-connection" class="button button-secondary">
                    <span style="font-size: 1.1em;">🔌</span> <?php esc_html_e('Test Connection', 'Offload-Media-to-Cloud'); ?>
                </button>
                <?php submit_button(__('💾 Save Settings', 'Offload-Media-to-Cloud'), 'primary', 'submit', false); ?>
            </p>
        </form>
        
        <div id="connection-status" style="display:none; margin-top: 20px;"></div>
    </div>
    
    <div class="omtc-support-box">
        <div class="omtc-support-content">
            <h3><?php esc_html_e('Enjoying this plugin?', 'Offload-Media-to-Cloud'); ?></h3>
            <p><?php esc_html_e('Your support helps keep it free, updated, and actively maintained for everyone.', 'Offload-Media-to-Cloud'); ?></p>
            <a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" class="omtc-bmc-button">
                <img src="https://cdn.buymeacoffee.com/buttons/bmc-new-btn-logo.svg" alt="Coffee icon" width="20" height="20">
                <span><?php esc_html_e('Buy me a coffee', 'Offload-Media-to-Cloud'); ?></span>
            </a>
        </div>
        <div class="omtc-developer-info">
            <div><strong><?php esc_html_e('Developer:', 'Offload-Media-to-Cloud'); ?></strong> Gunjan Jaswal</div>
            <div><strong><?php esc_html_e('Email:', 'Offload-Media-to-Cloud'); ?></strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a></div>
            <div><strong><?php esc_html_e('Website:', 'Offload-Media-to-Cloud'); ?></strong> <a href="https://www.gunjanjaswal.me" target="_blank">www.gunjanjaswal.me</a></div>
        </div>
    </div>
</div>
