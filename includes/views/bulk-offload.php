<?php
/**
 * Bulk offload page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$g33ki_settings = get_option('g33ki_settings', array());
$g33ki_is_configured = !empty($g33ki_settings['provider']) && !empty($g33ki_settings['bucket']);
?>

<div class="wrap g33ki-bulk-offload-wrap">
    <h1>📦 <?php esc_html_e('Bulk Offload Existing Media', 'g33ki-cloud-storage-for-media-library'); ?></h1>
    
    <div class="g33ki-bulk-container">
        <?php if (!$g33ki_is_configured): ?>
            <div class="notice notice-warning">
                <p><strong>⚠️ <?php esc_html_e('Configuration Required', 'g33ki-cloud-storage-for-media-library'); ?></strong></p>
                <p><?php esc_html_e('Please configure your cloud storage settings before using bulk offload.', 'g33ki-cloud-storage-for-media-library'); ?> 
                <a href="<?php echo esc_url(admin_url('admin.php?page=g33ki-cloud-storage-for-media-library')); ?>" style="font-weight: 600;"><?php esc_html_e('→ Go to Settings', 'g33ki-cloud-storage-for-media-library'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="g33ki-bulk-info">
                <p><?php esc_html_e('This tool will offload all existing media files that have not yet been uploaded to your cloud storage. New uploads are automatically offloaded.', 'g33ki-cloud-storage-for-media-library'); ?></p>
                <p><strong><?php esc_html_e('Note:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <?php esc_html_e('This process may take some time depending on the number of files. Please do not close this page until the process is complete.', 'g33ki-cloud-storage-for-media-library'); ?></p>
            </div>
            
            <div id="bulk-offload-stats" style="margin: 20px 0;">
                <p><strong><?php esc_html_e('Media files to offload:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <span id="media-count">...</span></p>
            </div>
            
            <div id="bulk-offload-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="progress-text"><?php esc_html_e('Processing...', 'g33ki-cloud-storage-for-media-library'); ?></span>
                    <span id="progress-percentage">0%</span>
                </p>
            </div>
            
            <div id="bulk-offload-errors" style="display:none; margin: 20px 0;">
                <h3><?php esc_html_e('Errors', 'g33ki-cloud-storage-for-media-library'); ?></h3>
                <ul id="error-list"></ul>
            </div>
            
            <p class="submit">
                <button type="button" id="start-bulk-offload" class="button button-primary button-hero">
                    <?php esc_html_e('Start Bulk Offload', 'g33ki-cloud-storage-for-media-library'); ?>
                </button>
            </p>
            
            <div id="bulk-offload-complete" style="display:none; margin: 20px 0;">
                <div class="notice notice-success" style="display:none;">
                    <p><?php esc_html_e('Bulk offload completed successfully!', 'g33ki-cloud-storage-for-media-library'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="g33ki-info-box" style="margin-top: 30px; padding: 20px; background: #fff; border-left: 4px solid #0073aa; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
        <div class="g33ki-developer-info">
            <div><strong><?php esc_html_e('Developer:', 'g33ki-cloud-storage-for-media-library'); ?></strong> Gunjan Jaswal</div>
            <div><strong><?php esc_html_e('Email:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <a href="mailto:hello@gunjanjaswal.me">hello@gunjanjaswal.me</a></div>
            <div><strong><?php esc_html_e('Website:', 'g33ki-cloud-storage-for-media-library'); ?></strong> <a href="https://gunjanjaswal.me" target="_blank">gunjanjaswal.me</a></div>
        </div>
    </div>
</div>


