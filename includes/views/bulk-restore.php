<?php
/**
 * Bulk restore page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$omtc_settings = get_option('omtc_settings', array());
$omtc_is_configured = !empty($omtc_settings['provider']) && !empty($omtc_settings['bucket']);
?>

<div class="wrap omtc-bulk-offload-wrap">
    <h1><?php esc_html_e('Restore Local Files', 'Offload-Media-to-Cloud'); ?></h1>

    <div class="omtc-bulk-container">
        <?php if (!$omtc_is_configured): ?>
            <div class="notice notice-warning">
                <p><strong><?php esc_html_e('Configuration Required', 'Offload-Media-to-Cloud'); ?></strong></p>
                <p><?php esc_html_e('Please configure your cloud storage settings first.', 'Offload-Media-to-Cloud'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=offload-media-to-cloud')); ?>" style="font-weight: 600;"><?php esc_html_e('Go to Settings', 'Offload-Media-to-Cloud'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="omtc-bulk-info">
                <p><?php esc_html_e('This tool downloads cloud-stored media files back to your local server. Use this before deactivating the plugin if you enabled "Remove Local Files".', 'Offload-Media-to-Cloud'); ?></p>
                <p><strong><?php esc_html_e('Note:', 'Offload-Media-to-Cloud'); ?></strong> <?php esc_html_e('Only files missing locally will be downloaded. Files already on the server are skipped.', 'Offload-Media-to-Cloud'); ?></p>
            </div>

            <div id="bulk-restore-stats" style="margin: 20px 0;">
                <p><strong><?php esc_html_e('Files to restore:', 'Offload-Media-to-Cloud'); ?></strong> <span id="restore-count">...</span></p>
            </div>

            <div id="bulk-restore-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="restore-progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="restore-progress-text"><?php esc_html_e('Restoring...', 'Offload-Media-to-Cloud'); ?></span>
                    <span id="restore-progress-percentage">0%</span>
                </p>
            </div>

            <div id="bulk-restore-errors" style="display:none; margin: 20px 0;">
                <h3><?php esc_html_e('Errors', 'Offload-Media-to-Cloud'); ?></h3>
                <ul id="restore-error-list"></ul>
            </div>

            <p class="submit">
                <button type="button" id="start-bulk-restore" class="button button-primary button-hero">
                    <?php esc_html_e('Start Restore', 'Offload-Media-to-Cloud'); ?>
                </button>
            </p>

            <div id="bulk-restore-complete" style="display:none; margin: 20px 0;">
                <div class="notice notice-success">
                    <p><?php esc_html_e('All files have been restored to local storage!', 'Offload-Media-to-Cloud'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
