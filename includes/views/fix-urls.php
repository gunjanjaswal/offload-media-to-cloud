<?php
/**
 * Fix URL Mismatch page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$g33ki_settings = get_option('g33ki_settings', array());
$G33KI_is_configured = !empty($g33ki_settings['provider']) && !empty($g33ki_settings['bucket']);
?>

<div class="wrap omtc-bulk-offload-wrap">
    <h1><?php esc_html_e('Fix URL Mismatch', 'g33ki-cloud-storage-for-media-library'); ?></h1>

    <div class="omtc-bulk-container">
        <?php if (!$G33KI_is_configured): ?>
            <div class="notice notice-warning">
                <p><strong><?php esc_html_e('Configuration Required', 'g33ki-cloud-storage-for-media-library'); ?></strong></p>
                <p><?php esc_html_e('Please configure your cloud storage settings first.', 'g33ki-cloud-storage-for-media-library'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=g33ki-cloud-storage-for-media-library')); ?>"><?php esc_html_e('Go to Settings', 'g33ki-cloud-storage-for-media-library'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="omtc-bulk-info">
                <p><?php esc_html_e('Detects when your CDN URL, bucket, or region settings changed but stored media URLs still point to the old location. Fixes URLs without re-uploading.', 'g33ki-cloud-storage-for-media-library'); ?></p>
                <p><strong><?php esc_html_e('Step 1:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <?php esc_html_e('Scan to find mismatched URLs.', 'g33ki-cloud-storage-for-media-library'); ?></p>
                <p><strong><?php esc_html_e('Step 2:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <?php esc_html_e('Fix URLs to match current settings (no re-upload needed).', 'g33ki-cloud-storage-for-media-library'); ?></p>
            </div>

            <div id="fix-scan-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="scan-progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="scan-progress-text"><?php esc_html_e('Scanning...', 'g33ki-cloud-storage-for-media-library'); ?></span>
                    <span id="scan-progress-percentage">0%</span>
                </p>
            </div>

            <div id="fix-scan-results" style="display:none; margin: 20px 0;">
                <div id="scan-results-ok" style="display:none;" class="notice notice-success">
                    <p><?php esc_html_e('All offloaded URLs match current settings. No fixes needed!', 'g33ki-cloud-storage-for-media-library'); ?></p>
                </div>
                <div id="scan-results-mismatched" style="display:none;">
                    <div class="notice notice-error" style="display:none;">
                        <p><strong id="mismatched-count-text"></strong></p>
                    </div>
                    <table class="widefat striped" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('ID', 'g33ki-cloud-storage-for-media-library'); ?></th>
                                <th><?php esc_html_e('Title', 'g33ki-cloud-storage-for-media-library'); ?></th>
                                <th><?php esc_html_e('Current URL', 'g33ki-cloud-storage-for-media-library'); ?></th>
                                <th><?php esc_html_e('Expected URL', 'g33ki-cloud-storage-for-media-library'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="mismatched-files-list"></tbody>
                    </table>
                </div>
            </div>

            <div id="fix-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="fix-progress-bar" style="background: #46b450; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="fix-progress-text"><?php esc_html_e('Fixing...', 'g33ki-cloud-storage-for-media-library'); ?></span>
                </p>
            </div>

            <div id="fix-complete" style="display:none; margin: 20px 0;" class="notice notice-success">
                <p id="fix-complete-text"></p>
            </div>

            <div id="fix-errors" style="display:none; margin: 20px 0;">
                <h3><?php esc_html_e('Fix Errors', 'g33ki-cloud-storage-for-media-library'); ?></h3>
                <ul id="fix-error-list"></ul>
            </div>

            </p>
        <?php endif; ?>
    </div>
</div>


