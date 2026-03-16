<?php
/**
 * Bulk offload page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$omtc_settings = get_option('omtc_settings', array());
$omtc_is_configured = !empty($omtc_settings['provider']) && !empty($omtc_settings['bucket']);
?>

<div class="wrap omtc-bulk-offload-wrap">
    <h1>📦 <?php esc_html_e('Bulk Offload Existing Media', 'Offload-Media-to-Cloud'); ?></h1>
    
    <div class="omtc-bulk-container">
        <?php if (!$omtc_is_configured): ?>
            <div class="notice notice-warning">
                <p><strong>⚠️ <?php esc_html_e('Configuration Required', 'Offload-Media-to-Cloud'); ?></strong></p>
                <p><?php esc_html_e('Please configure your cloud storage settings before using bulk offload.', 'Offload-Media-to-Cloud'); ?> 
                <a href="<?php echo esc_url(admin_url('admin.php?page=offload-media-to-cloud')); ?>" style="font-weight: 600;"><?php esc_html_e('→ Go to Settings', 'Offload-Media-to-Cloud'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="omtc-bulk-info">
                <p><?php esc_html_e('This tool will offload all existing media files that have not yet been uploaded to your cloud storage. New uploads are automatically offloaded.', 'Offload-Media-to-Cloud'); ?></p>
                <p><strong><?php esc_html_e('Note:', 'Offload-Media-to-Cloud'); ?></strong> <?php esc_html_e('This process may take some time depending on the number of files. Please do not close this page until the process is complete.', 'Offload-Media-to-Cloud'); ?></p>
            </div>
            
            <div id="bulk-offload-stats" style="margin: 20px 0;">
                <p><strong><?php esc_html_e('Media files to offload:', 'Offload-Media-to-Cloud'); ?></strong> <span id="media-count">...</span></p>
            </div>
            
            <div id="bulk-offload-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="progress-text"><?php esc_html_e('Processing...', 'Offload-Media-to-Cloud'); ?></span>
                    <span id="progress-percentage">0%</span>
                </p>
            </div>
            
            <div id="bulk-offload-errors" style="display:none; margin: 20px 0;">
                <h3><?php esc_html_e('Errors', 'Offload-Media-to-Cloud'); ?></h3>
                <ul id="error-list"></ul>
            </div>
            
            <p class="submit">
                <button type="button" id="start-bulk-offload" class="button button-primary button-hero">
                    <?php esc_html_e('Start Bulk Offload', 'Offload-Media-to-Cloud'); ?>
                </button>
            </p>
            
            <div id="bulk-offload-complete" style="display:none; margin: 20px 0;">
                <div class="notice notice-success">
                    <p><?php esc_html_e('Bulk offload completed successfully!', 'Offload-Media-to-Cloud'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="omtc-info-box" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-left: 4px solid #0073aa;">
        <h3><?php esc_html_e('Support the Developer', 'Offload-Media-to-Cloud'); ?></h3>
        <p><?php esc_html_e('If you find this plugin helpful, consider supporting its development:', 'Offload-Media-to-Cloud'); ?></p>
        <p>
            <a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" class="button button-secondary">☕ Buy Me a Coffee</a>
        </p>
        <p>
            <strong><?php esc_html_e('Developer:', 'Offload-Media-to-Cloud'); ?></strong> Gunjan Jaswal<br>
            <strong><?php esc_html_e('Email:', 'Offload-Media-to-Cloud'); ?></strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a><br>
            <strong><?php esc_html_e('Website:', 'Offload-Media-to-Cloud'); ?></strong> <a href="https://www.gunjanjaswal.me" target="_blank">www.gunjanjaswal.me</a>
        </p>
    </div>
</div>
