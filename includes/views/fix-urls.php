<?php
/**
 * Fix URL Mismatch page view
 */

if (!defined('ABSPATH')) {
    exit;
}

$omtc_settings = get_option('omtc_settings', array());
$omtc_is_configured = !empty($omtc_settings['provider']) && !empty($omtc_settings['bucket']);
?>

<div class="wrap omtc-bulk-offload-wrap">
    <h1><?php esc_html_e('Fix URL Mismatch', 'offload-media-to-cloud'); ?></h1>

    <div class="omtc-bulk-container">
        <?php if (!$omtc_is_configured): ?>
            <div class="notice notice-warning">
                <p><strong><?php esc_html_e('Configuration Required', 'offload-media-to-cloud'); ?></strong></p>
                <p><?php esc_html_e('Please configure your cloud storage settings first.', 'offload-media-to-cloud'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=offload-media-to-cloud')); ?>"><?php esc_html_e('Go to Settings', 'offload-media-to-cloud'); ?></a></p>
            </div>
        <?php else: ?>
            <div class="omtc-bulk-info">
                <p><?php esc_html_e('Detects when your CDN URL, bucket, or region settings changed but stored media URLs still point to the old location. Fixes URLs without re-uploading.', 'offload-media-to-cloud'); ?></p>
                <p><strong><?php esc_html_e('Step 1:', 'offload-media-to-cloud'); ?></strong> <?php esc_html_e('Scan to find mismatched URLs.', 'offload-media-to-cloud'); ?></p>
                <p><strong><?php esc_html_e('Step 2:', 'offload-media-to-cloud'); ?></strong> <?php esc_html_e('Fix URLs to match current settings (no re-upload needed).', 'offload-media-to-cloud'); ?></p>
            </div>

            <div id="fix-scan-progress" style="display:none; margin: 20px 0;">
                <div style="background: #f0f0f0; height: 30px; border-radius: 5px; overflow: hidden;">
                    <div id="scan-progress-bar" style="background: #0073aa; height: 100%; width: 0%; transition: width 0.3s;"></div>
                </div>
                <p style="margin-top: 10px;">
                    <span id="scan-progress-text"><?php esc_html_e('Scanning...', 'offload-media-to-cloud'); ?></span>
                    <span id="scan-progress-percentage">0%</span>
                </p>
            </div>

            <div id="fix-scan-results" style="display:none; margin: 20px 0;">
                <div id="scan-results-ok" style="display:none;" class="notice notice-success">
                    <p><?php esc_html_e('All offloaded URLs match current settings. No fixes needed!', 'offload-media-to-cloud'); ?></p>
                </div>
                <div id="scan-results-mismatched" style="display:none;">
                    <div class="notice notice-error">
                        <p><strong id="mismatched-count-text"></strong></p>
                    </div>
                    <table class="widefat striped" style="margin-top: 10px;">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('ID', 'offload-media-to-cloud'); ?></th>
                                <th><?php esc_html_e('Title', 'offload-media-to-cloud'); ?></th>
                                <th><?php esc_html_e('Current URL', 'offload-media-to-cloud'); ?></th>
                                <th><?php esc_html_e('Expected URL', 'offload-media-to-cloud'); ?></th>
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
                    <span id="fix-progress-text"><?php esc_html_e('Fixing...', 'offload-media-to-cloud'); ?></span>
                </p>
            </div>

            <div id="fix-complete" style="display:none; margin: 20px 0;" class="notice notice-success">
                <p id="fix-complete-text"></p>
            </div>

            <div id="fix-errors" style="display:none; margin: 20px 0;">
                <h3><?php esc_html_e('Fix Errors', 'offload-media-to-cloud'); ?></h3>
                <ul id="fix-error-list"></ul>
            </div>

            <p class="submit">
                <button type="button" id="start-url-scan" class="button button-primary button-hero">
                    <?php esc_html_e('Scan URLs', 'offload-media-to-cloud'); ?>
                </button>
                <button type="button" id="start-url-fix" class="button button-hero" style="display:none; background: #46b450; color: #fff; border-color: #46b450;">
                    <?php esc_html_e('Fix All Mismatched URLs', 'offload-media-to-cloud'); ?>
                </button>
            </p>

            <script type="text/javascript">
            jQuery(document).ready(function($) {
                var mismatchedItems = [];

                function truncateUrl(url, maxLen) {
                    maxLen = maxLen || 60;
                    if (url.length <= maxLen) return url;
                    return url.substring(0, 30) + '...' + url.substring(url.length - 27);
                }

                // Scan URLs
                $('#start-url-scan').on('click', function() {
                    var $btn = $(this);
                    $btn.prop('disabled', true);
                    $('#start-url-fix').hide();
                    $('#fix-scan-results').hide();
                    $('#fix-complete').hide();
                    $('#fix-errors').hide();
                    $('#fix-scan-progress').show();
                    $('#mismatched-files-list').empty();
                    mismatchedItems = [];

                    function scanBatch(offset) {
                        $.post(omtc_ajax.ajax_url, {
                            action: 'omtc_scan_urls',
                            nonce: omtc_ajax.nonce,
                            offset: offset
                        }, function(response) {
                            if (!response.success) {
                                alert(response.data.message || 'Scan failed');
                                $btn.prop('disabled', false);
                                return;
                            }

                            var data = response.data;

                            // Collect mismatched items
                            if (data.mismatched && data.mismatched.length) {
                                $.each(data.mismatched, function(i, item) {
                                    mismatchedItems.push(item);
                                    $('#mismatched-files-list').append(
                                        '<tr>' +
                                            '<td>' + item.id + '</td>' +
                                            '<td>' + $('<span>').text(item.title).html() + '</td>' +
                                            '<td title="' + $('<span>').text(item.old_url).html() + '">' + $('<span>').text(truncateUrl(item.old_url)).html() + '</td>' +
                                            '<td title="' + $('<span>').text(item.expected_url).html() + '">' + $('<span>').text(truncateUrl(item.expected_url)).html() + '</td>' +
                                        '</tr>'
                                    );
                                });
                            }

                            // Update progress
                            var pct = data.total > 0 ? Math.round((data.scanned / data.total) * 100) : 100;
                            $('#scan-progress-bar').css('width', pct + '%');
                            $('#scan-progress-percentage').text(pct + '%');
                            $('#scan-progress-text').text(
                                '<?php echo esc_js(__('Scanned', 'offload-media-to-cloud')); ?> ' + data.scanned + ' / ' + data.total
                            );

                            if (data.complete) {
                                $('#fix-scan-progress').hide();
                                $('#fix-scan-results').show();

                                if (mismatchedItems.length === 0) {
                                    $('#scan-results-ok').show();
                                    $('#scan-results-mismatched').hide();
                                } else {
                                    $('#scan-results-ok').hide();
                                    $('#scan-results-mismatched').show();
                                    $('#mismatched-count-text').text(
                                        mismatchedItems.length + ' <?php echo esc_js(__('file(s) with mismatched URLs found.', 'offload-media-to-cloud')); ?>'
                                    );
                                    $('#start-url-fix').show();
                                }

                                $btn.prop('disabled', false);
                            } else {
                                scanBatch(data.scanned);
                            }
                        }).fail(function() {
                            alert('<?php echo esc_js(__('AJAX request failed', 'offload-media-to-cloud')); ?>');
                            $btn.prop('disabled', false);
                        });
                    }

                    scanBatch(0);
                });

                // Fix URLs
                $('#start-url-fix').on('click', function() {
                    var $btn = $(this);
                    $btn.prop('disabled', true);
                    $('#start-url-scan').prop('disabled', true);
                    $('#fix-progress').show();
                    $('#fix-complete').hide();
                    $('#fix-errors').hide();
                    $('#fix-error-list').empty();

                    var allIds = mismatchedItems.map(function(item) { return item.id; });
                    var totalToFix = allIds.length;
                    var totalFixed = 0;
                    var allErrors = [];

                    function fixBatch(ids) {
                        var batch = ids.slice(0, 20);
                        var remaining = ids.slice(20);

                        $.post(omtc_ajax.ajax_url, {
                            action: 'omtc_fix_urls',
                            nonce: omtc_ajax.nonce,
                            ids: batch
                        }, function(response) {
                            if (!response.success) {
                                alert(response.data.message || 'Fix failed');
                                $btn.prop('disabled', false);
                                $('#start-url-scan').prop('disabled', false);
                                return;
                            }

                            var data = response.data;
                            totalFixed += data.fixed;

                            if (data.errors && data.errors.length) {
                                allErrors = allErrors.concat(data.errors);
                            }

                            var pct = Math.round((totalFixed / totalToFix) * 100);
                            $('#fix-progress-bar').css('width', pct + '%');
                            $('#fix-progress-text').text(
                                '<?php echo esc_js(__('Fixed', 'offload-media-to-cloud')); ?> ' + totalFixed + ' / ' + totalToFix
                            );

                            if (remaining.length > 0) {
                                fixBatch(remaining);
                            } else {
                                $('#fix-progress').hide();
                                $('#fix-complete').show();
                                $('#fix-complete-text').text(
                                    totalFixed + ' <?php echo esc_js(__('file(s) updated successfully.', 'offload-media-to-cloud')); ?>'
                                );

                                if (allErrors.length > 0) {
                                    $('#fix-errors').show();
                                    $.each(allErrors, function(i, err) {
                                        $('#fix-error-list').append(
                                            '<li>' + '<?php echo esc_js(__('Attachment', 'offload-media-to-cloud')); ?> #' + err.id + ': ' + $('<span>').text(err.error).html() + '</li>'
                                        );
                                    });
                                }

                                $btn.hide();
                                $('#start-url-scan').prop('disabled', false);
                            }
                        }).fail(function() {
                            alert('<?php echo esc_js(__('AJAX request failed', 'offload-media-to-cloud')); ?>');
                            $btn.prop('disabled', false);
                            $('#start-url-scan').prop('disabled', false);
                        });
                    }

                    fixBatch(allIds);
                });
            });
            </script>
        <?php endif; ?>
    </div>
</div>
