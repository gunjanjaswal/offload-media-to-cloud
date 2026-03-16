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
    <h1>📦 <?php esc_html_e('Bulk Offload Existing Media', 'offload-media-to-cloud'); ?></h1>
    
    <div class="omtc-bulk-container">
        <?php if (!$omtc_is_configured): ?>
            <div class="notice notice-warning">
                <p><strong>⚠️ <?php esc_html_e('Configuration Required', 'offload-media-to-cloud'); ?></strong></p>
                <p><?php esc_html_e('Please configure your cloud storage settings before using bulk offload.', 'offload-media-to-cloud'); ?> 
                <a href="<?php echo esc_url(admin_url('admin.php?page=offload-media-to-cloud')); ?>" style="font-weight: 600;"><?php esc_html_e('→ Go to Settings', 'offload-media-to-cloud'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="omtc-bulk-info">
                <p><?php esc_html_e('This tool will offload all existing media files that have not yet been uploaded to your cloud storage. New uploads are automatically offloaded.', 'offload-media-to-cloud'); ?></p>
                <p><strong><?php esc_html_e('Note:', 'offload-media-to-cloud'); ?></strong> <?php esc_html_e('This process may take some time depending on the number of files. Please do not close this page until the process is complete.', 'offload-media-to-cloud'); ?></p>
            </div>
            
            <div id="bulk-offload-stats" style="margin: 20px 0;">
                <p><strong><?php esc_html_e('Media files to offload:', 'offload-media-to-cloud'); ?></strong> <span id="media-count">...</span></p>
            </div>
            
            <div id="bulk-offload-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="progress-text"><?php esc_html_e('Processing...', 'offload-media-to-cloud'); ?></span>
                    <span id="progress-percentage">0%</span>
                </p>
            </div>
            
            <div id="bulk-offload-errors" style="display:none; margin: 20px 0;">
                <h3><?php esc_html_e('Errors', 'offload-media-to-cloud'); ?></h3>
                <ul id="error-list"></ul>
            </div>
            
            <p class="submit">
                <button type="button" id="start-bulk-offload" class="button button-primary button-hero">
                    <?php esc_html_e('Start Bulk Offload', 'offload-media-to-cloud'); ?>
                </button>
            </p>
            
            <div id="bulk-offload-complete" style="display:none; margin: 20px 0;">
                <div class="notice notice-success" style="display:none;">
                    <p><?php esc_html_e('Bulk offload completed successfully!', 'offload-media-to-cloud'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="omtc-info-box" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-left: 4px solid #0073aa;">
        <h3><?php esc_html_e('Support the Developer', 'offload-media-to-cloud'); ?></h3>
        <p><?php esc_html_e('If you find this plugin helpful, consider supporting its development:', 'offload-media-to-cloud'); ?></p>
        <p>
            <a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" class="button button-secondary">☕ Buy Me a Coffee</a>
        </p>
        <p>
            <strong><?php esc_html_e('Developer:', 'offload-media-to-cloud'); ?></strong> Gunjan Jaswal<br>
            <strong><?php esc_html_e('Email:', 'offload-media-to-cloud'); ?></strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a><br>
            <strong><?php esc_html_e('Website:', 'offload-media-to-cloud'); ?></strong> <a href="https://www.gunjanjaswal.me" target="_blank">www.gunjanjaswal.me</a>
        </p>
    </div>
</div>
