<?php
/**
 * Settings page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$g33ki_settings = get_option('g33ki_settings', array());
$G33KI_provider = isset($g33ki_settings['provider']) ? $g33ki_settings['provider'] : '';
?>

<div class="wrap g33ki-settings-wrap">
    <h1>⚡ <?php esc_html_e('G33ki Cloud Settings', 'g33ki-cloud-storage-for-media-library'); ?></h1>
    
    <div class="g33ki-settings-container">
        <form id="g33ki-settings-form" method="post">
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="provider">
                            <span style="font-size: 1.1em;">☁️</span> <?php esc_html_e('Storage Provider', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <select name="g33ki_settings[provider]" id="provider" class="regular-text">
                            <option value=""><?php esc_html_e('— Select Your Cloud Provider —', 'g33ki-cloud-storage-for-media-library'); ?></option>
                            <option value="s3" <?php selected($g33ki_provider, 's3'); ?>>🔶 Amazon S3</option>
                            <option value="spaces" <?php selected($g33ki_provider, 'spaces'); ?>>🌊 DigitalOcean Spaces</option>
                            <option value="gcs" <?php selected($g33ki_provider, 'gcs'); ?>>☁️ Google Cloud Storage</option>
                        </select>
                        <p class="description"><?php esc_html_e('Choose your preferred cloud storage provider for media offloading', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="access_key">
                            <span style="font-size: 1.1em;">🔑</span> <?php esc_html_e('Access Key', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="g33ki_settings[access_key]" id="access_key" value="<?php echo esc_attr(isset($g33ki_settings['access_key']) ? $g33ki_settings['access_key'] : ''); ?>" class="regular-text" placeholder="AKIAIOSFODNN7EXAMPLE">
                        <p class="description"><?php esc_html_e('Your cloud storage access key. For GCS, use an HMAC access key (Cloud Storage > Settings > Interoperability).', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="secret_key">
                            <span style="font-size: 1.1em;">🔐</span> <?php esc_html_e('Secret Key', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="password" name="g33ki_settings[secret_key]" id="secret_key" value="<?php echo esc_attr(isset($g33ki_settings['secret_key']) ? $g33ki_settings['secret_key'] : ''); ?>" class="regular-text" placeholder="wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY">
                        <p class="description"><?php esc_html_e('Your cloud storage secret key. For GCS, use the HMAC secret key.', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="bucket">
                            <span style="font-size: 1.1em;">🗂️</span> <?php esc_html_e('Bucket Name', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="g33ki_settings[bucket]" id="bucket" value="<?php echo esc_attr(isset($g33ki_settings['bucket']) ? $g33ki_settings['bucket'] : ''); ?>" class="regular-text" placeholder="my-media-bucket">
                        <p class="description"><?php esc_html_e('The name of your storage bucket or space', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="region">
                            <span style="font-size: 1.1em;">🌍</span> <?php esc_html_e('Region', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="g33ki_settings[region]" id="region" value="<?php echo esc_attr(isset($g33ki_settings['region']) ? $g33ki_settings['region'] : ''); ?>" class="regular-text" placeholder="us-east-1">
                        <p class="description"><?php esc_html_e('Region code: us-east-1 (S3), nyc3 (Spaces), us-central1 (GCS)', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="cdn_url">
                            <span style="font-size: 1.1em;">🚀</span> <?php esc_html_e('CDN URL', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="url" name="g33ki_settings[cdn_url]" id="cdn_url" value="<?php echo esc_attr(isset($g33ki_settings['cdn_url']) ? $g33ki_settings['cdn_url'] : ''); ?>" class="regular-text" placeholder="https://cdn.example.com">
                        <p class="description"><?php esc_html_e('Optional: Custom domain or CloudFront URL for faster delivery (no trailing slash)', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="path_prefix">
                            <span style="font-size: 1.1em;">📁</span> <?php esc_html_e('Path Prefix', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="g33ki_settings[path_prefix]" id="path_prefix" value="<?php echo esc_attr(isset($g33ki_settings['path_prefix']) ? $g33ki_settings['path_prefix'] : ''); ?>" class="regular-text" placeholder="wp-content/uploads">
                        <p class="description"><?php esc_html_e('Optional: Add a folder prefix to organize your cloud files', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
                
                <tr class="provider-field">
                    <th scope="row">
                        <label for="remove_local_files">
                            <span style="font-size: 1.1em;">🗑️</span> <?php esc_html_e('Local Files', 'g33ki-cloud-storage-for-media-library'); ?>
                        </label>
                    </th>
                    <td>
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="g33ki_settings[remove_local_files]" id="remove_local_files" value="1" <?php checked(isset($g33ki_settings['remove_local_files']) ? $g33ki_settings['remove_local_files'] : false, true); ?>>
                            <span><?php esc_html_e('Automatically remove files from server after successful cloud upload', 'g33ki-cloud-storage-for-media-library'); ?></span>
                        </label>
                        <p class="description" style="margin-top: 0.75rem;"><?php esc_html_e('⚠️ Warning: Files will only exist in cloud storage. Ensure backups are in place.', 'g33ki-cloud-storage-for-media-library'); ?></p>
                    </td>
                </tr>
            </table>
            
            <p class="submit" style="display: flex; gap: 1rem; align-items: center;">
                <button type="button" id="test-connection" class="button button-secondary">
                    <span style="font-size: 1.1em;">🔌</span> <?php esc_html_e('Test Connection', 'g33ki-cloud-storage-for-media-library'); ?>
                </button>
                <?php submit_button(__('💾 Save Settings', 'g33ki-cloud-storage-for-media-library'), 'primary', 'submit', false); ?>
            </p>
        </form>
        
        <div id="connection-status" style="display:none; margin-top: 20px;"></div>
    </div>
    
    <div class="g33ki-info-box" style="margin-top: 30px; padding: 20px; background: #fff; border-left: 4px solid #0073aa; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <div class="g33ki-developer-info">
            <div><strong><?php esc_html_e('Developer:', 'g33ki-cloud-storage-for-media-library'); ?></strong> Gunjan Jaswal</div>
            <div><strong><?php esc_html_e('Email:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a></div>
            <div><strong><?php esc_html_e('Website:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <a href="https://gunjanjaswal.me" target="_blank">gunjanjaswal.me</a></div>
        </div>
    </div>
</div>


