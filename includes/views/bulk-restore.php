<?php
/**
 * Bulk restore page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$g33ki_settings = get_option('g33ki_settings', array());
$g33ki_is_configured = !empty($g33ki_settings['provider']) && !empty($g33ki_settings['bucket']);
?>

<div class="wrap g33ki-bulk-offload-wrap">
    <h1><?php esc_html_e('Restore Local Files', 'g33ki-cloud-storage-for-media-library'); ?></h1>

    <div class="g33ki-bulk-container">
        <?php if (!$g33ki_is_configured): ?>
            <div class="notice notice-warning">
                <p><strong><?php esc_html_e('Configuration Required', 'g33ki-cloud-storage-for-media-library'); ?></strong></p>
                <p><?php esc_html_e('Please configure your cloud storage settings first.', 'g33ki-cloud-storage-for-media-library'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=g33ki-cloud-storage-for-media-library')); ?>" style="font-weight: 600;"><?php esc_html_e('Go to Settings', 'g33ki-cloud-storage-for-media-library'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="g33ki-bulk-info">
                <p><?php esc_html_e('This tool downloads cloud-stored media files back to your local server. Use this before deactivating the plugin if you enabled "Remove Local Files".', 'g33ki-cloud-storage-for-media-library'); ?></p>
                <p><strong><?php esc_html_e('Note:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <?php esc_html_e('Only files missing locally will be downloaded. Files already on the server are skipped.', 'g33ki-cloud-storage-for-media-library'); ?></p>
            </div>

            <div id="bulk-restore-stats" style="margin: 20px 0;">
                <p><strong><?php esc_html_e('Files to restore:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <span id="restore-count">...</span></p>
            </div>

            <div id="bulk-restore-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="restore-progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="restore-progress-text"><?php esc_html_e('Restoring...', 'g33ki-cloud-storage-for-media-library'); ?></span>
                    <span id="restore-progress-percentage">0%</span>
                </p>
            </div>

            <div id="bulk-restore-errors" style="display:none; margin: 20px 0;">
                <h3><?php esc_html_e('Errors', 'g33ki-cloud-storage-for-media-library'); ?></h3>
                <ul id="restore-error-list"></ul>
            </div>

            <p class="submit">
                <button type="button" id="start-bulk-restore" class="button button-primary button-hero">
                    <?php esc_html_e('Start Restore', 'g33ki-cloud-storage-for-media-library'); ?>
                </button>
            </p>

            <div id="bulk-restore-complete" style="display:none; margin: 20px 0;">
                <div class="notice notice-success" style="display:none;">
                    <p><?php esc_html_e('All files have been restored to local storage!', 'g33ki-cloud-storage-for-media-library'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


