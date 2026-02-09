<?php
/**
 * Bulk offload page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('oijc_settings', array());
$is_configured = !empty($settings['provider']) && !empty($settings['bucket']);
?>

<div class="wrap oijc-bulk-offload-wrap">
    <h1>📦 <?php _e('Bulk Offload Existing Media', 'offload-images-js-css'); ?></h1>
    
    <div class="oijc-bulk-container">
        <?php if (!$is_configured): ?>
            <div class="notice notice-warning">
                <p><strong>⚠️ <?php _e('Configuration Required', 'offload-images-js-css'); ?></strong></p>
                <p><?php _e('Please configure your cloud storage settings before using bulk offload.', 'offload-images-js-css'); ?> 
                <a href="<?php echo admin_url('admin.php?page=offload-images-js-css'); ?>" style="font-weight: 600;"><?php _e('→ Go to Settings', 'offload-images-js-css'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="oijc-bulk-info">
                <p><?php _e('This tool will offload all existing media files that have not yet been uploaded to your cloud storage. New uploads are automatically offloaded.', 'offload-images-js-css'); ?></p>
                <p><strong><?php _e('Note:', 'offload-images-js-css'); ?></strong> <?php _e('This process may take some time depending on the number of files. Please do not close this page until the process is complete.', 'offload-images-js-css'); ?></p>
            </div>
            
            <div id="bulk-offload-stats" style="margin: 20px 0;">
                <p><strong><?php _e('Media files to offload:', 'offload-images-js-css'); ?></strong> <span id="media-count">...</span></p>
            </div>
            
            <div id="bulk-offload-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="progress-text"><?php _e('Processing...', 'offload-images-js-css'); ?></span>
                    <span id="progress-percentage">0%</span>
                </p>
            </div>
            
            <div id="bulk-offload-errors" style="display:none; margin: 20px 0;">
                <h3><?php _e('Errors', 'offload-images-js-css'); ?></h3>
                <ul id="error-list"></ul>
            </div>
            
            <p class="submit">
                <button type="button" id="start-bulk-offload" class="button button-primary button-hero">
                    <?php _e('Start Bulk Offload', 'offload-images-js-css'); ?>
                </button>
            </p>
            
            <div id="bulk-offload-complete" style="display:none; margin: 20px 0;">
                <div class="notice notice-success">
                    <p><?php _e('Bulk offload completed successfully!', 'offload-images-js-css'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="oijc-info-box" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-left: 4px solid #0073aa;">
        <h3><?php _e('Support the Developer', 'offload-images-js-css'); ?></h3>
        <p><?php _e('If you find this plugin helpful, consider supporting its development:', 'offload-images-js-css'); ?></p>
        <p>
            <a href="https://buymeacoffee.com/gunjanjaswal" target="_blank" class="button button-secondary">☕ Buy Me a Coffee</a>
        </p>
        <p>
            <strong><?php _e('Developer:', 'offload-images-js-css'); ?></strong> Gunjan Jaswal<br>
            <strong><?php _e('Email:', 'offload-images-js-css'); ?></strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a><br>
            <strong><?php _e('Website:', 'offload-images-js-css'); ?></strong> <a href="https://www.gunjanjaswal.me" target="_blank">www.gunjanjaswal.me</a>
        </p>
    </div>
</div>
