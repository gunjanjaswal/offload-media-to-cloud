jQuery(document).ready(function($) {
    
    // Show/hide provider fields based on selection
    $('#provider').on('change', function() {
        var provider = $(this).val();
        if (provider) {
            $('.provider-field').addClass('active');
        } else {
            $('.provider-field').removeClass('active');
        }
    }).trigger('change');
    
    // Test connection
    $('#test-connection').on('click', function() {
        var $button = $(this);
        var $status = $('#connection-status');
        
        var provider = $('#provider').val();
        if (!provider) {
            alert('Please select a storage provider first.');
            return;
        }
        
        var credentials = {
            provider: provider,
            access_key: $('#access_key').val(),
            secret_key: $('#secret_key').val(),
            bucket: $('#bucket').val(),
            region: $('#region').val()
        };
        
        $button.prop('disabled', true).text('Testing...');
        $status.hide();
        
        $.ajax({
            url: omtc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'omtc_test_connection',
                nonce: omtc_ajax.nonce,
                provider: provider,
                credentials: credentials
            },
            success: function(response) {
                $status.removeClass('success error');
                if (response.success) {
                    $status.addClass('success').html('<strong>Success!</strong> ' + response.data.message);
                } else {
                    $status.addClass('error').html('<strong>Error:</strong> ' + response.data.message);
                }
                $status.show();
            },
            error: function() {
                $status.removeClass('success error').addClass('error');
                $status.html('<strong>Error:</strong> Failed to test connection.').show();
            },
            complete: function() {
                $button.prop('disabled', false).text('Test Connection');
            }
        });
    });
    
    // Bulk restore functionality
    if ($('#start-bulk-restore').length) {

        // Reset UI state on page load
        $('#bulk-restore-complete').hide();
        $('#bulk-restore-progress').hide();
        $('#bulk-restore-errors').hide();

        // Get restore count on page load
        $.ajax({
            url: omtc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'omtc_get_restore_count',
                nonce: omtc_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#restore-count').text(response.data.count);
                    if (response.data.count === 0) {
                        $('#restore-count').text('0 (All files are already stored locally!)');
                        $('#start-bulk-restore').prop('disabled', true);
                    }
                }
            }
        });

        // Start bulk restore
        $('#start-bulk-restore').on('click', function() {
            var $button = $(this);
            var totalCount = parseInt($('#restore-count').text());

            if (isNaN(totalCount) || totalCount === 0) {
                return;
            }

            if (!confirm('This will download ' + totalCount + ' files from cloud storage. Continue?')) {
                return;
            }

            $button.prop('disabled', true).addClass('disabled');
            $('#bulk-restore-progress').show();
            $('#bulk-restore-errors').hide();
            $('#restore-error-list').empty();

            var processed = 0;
            var errors = [];
            var retryCount = 0;
            var maxRetries = 5;

            function restoreBatch() {
                $.ajax({
                    url: omtc_ajax.ajax_url,
                    type: 'POST',
                    timeout: 120000,
                    data: {
                        action: 'omtc_bulk_restore',
                        nonce: omtc_ajax.nonce,
                        offset: 0
                    },
                    success: function(response) {
                        retryCount = 0;
                        if (response.success) {
                            processed += response.data.processed;

                            if (response.data.errors.length > 0) {
                                errors = errors.concat(response.data.errors);
                            }

                            var percentage = totalCount > 0 ? Math.min(Math.round((processed / totalCount) * 100), 100) : 100;
                            $('#restore-progress-bar').css('width', percentage + '%');
                            $('#restore-progress-percentage').text(percentage + '%');
                            $('#restore-progress-text').text('Restored ' + processed + ' of ' + totalCount + ' files');

                            if (response.data.complete) {
                                $('#bulk-restore-complete').show();
                                $('#restore-progress-text').text('Complete! Restored ' + processed + ' files.');

                                if (errors.length > 0) {
                                    $('#bulk-restore-errors').show();
                                    errors.forEach(function(error) {
                                        $('#restore-error-list').append('<li><strong>' + error.title + '</strong>: ' + error.error + '</li>');
                                    });
                                }

                                $('#restore-count').text('0 (All files are already stored locally!)');
                            } else {
                                restoreBatch();
                            }
                        } else {
                            alert('Error: ' + response.data.message);
                            $button.prop('disabled', false).removeClass('disabled');
                        }
                    },
                    error: function() {
                        retryCount++;
                        if (retryCount <= maxRetries) {
                            $('#restore-progress-text').text('Connection lost. Retrying (' + retryCount + '/' + maxRetries + ')...');
                            setTimeout(restoreBatch, 3000);
                        } else {
                            $('#restore-progress-text').text('Stopped at ' + processed + ' of ' + totalCount + ' files. Click button to resume.');
                            $button.prop('disabled', false).removeClass('disabled');
                        }
                    }
                });
            }

            restoreBatch();
        });
    }

    // Bulk offload functionality
    if ($('#start-bulk-offload').length) {

        // Reset UI state on page load
        $('#bulk-offload-complete').hide();
        $('#bulk-offload-progress').hide();
        $('#bulk-offload-errors').hide();
        
        // Get media count on page load
        $.ajax({
            url: omtc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'omtc_get_media_count',
                nonce: omtc_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#media-count').text(response.data.count);
                    if (response.data.count === 0) {
                        $('#media-count').text('0 (All media files are already offloaded!)');
                        $('#start-bulk-offload').prop('disabled', true);
                    }
                }
            }
        });
        
        // Start bulk offload
        $('#start-bulk-offload').on('click', function() {
            var $button = $(this);
            var totalCount = parseInt($('#media-count').text());

            if (isNaN(totalCount) || totalCount === 0) {
                return;
            }

            if (!confirm('This will offload ' + totalCount + ' media files. Continue?')) {
                return;
            }

            $button.prop('disabled', true).addClass('disabled');
            $('#bulk-offload-progress').show();
            $('#bulk-offload-errors').hide();
            $('#error-list').empty();

            var processed = 0;
            var errors = [];
            var retryCount = 0;
            var maxRetries = 5;

            function processBatch() {
                $.ajax({
                    url: omtc_ajax.ajax_url,
                    type: 'POST',
                    timeout: 120000,
                    data: {
                        action: 'omtc_bulk_offload',
                        nonce: omtc_ajax.nonce,
                        offset: 0
                    },
                    success: function(response) {
                        retryCount = 0;
                        if (response.success) {
                            processed += response.data.processed;

                            if (response.data.errors.length > 0) {
                                errors = errors.concat(response.data.errors);
                            }

                            var percentage = totalCount > 0 ? Math.min(Math.round((processed / totalCount) * 100), 100) : 100;
                            $('#progress-bar').css('width', percentage + '%');
                            $('#progress-percentage').text(percentage + '%');
                            $('#progress-text').text('Processed ' + processed + ' of ' + totalCount + ' files');

                            if (response.data.complete) {
                                $('#bulk-offload-complete').show();
                                $('#progress-text').text('Complete! Processed ' + processed + ' files.');

                                if (errors.length > 0) {
                                    $('#bulk-offload-errors').show();
                                    errors.forEach(function(error) {
                                        $('#error-list').append('<li><strong>' + error.title + '</strong>: ' + error.error + '</li>');
                                    });
                                }

                                $('#media-count').text('0 (All media files are now offloaded!)');
                            } else {
                                processBatch();
                            }
                        } else {
                            alert('Error: ' + response.data.message);
                            $button.prop('disabled', false).removeClass('disabled');
                        }
                    },
                    error: function() {
                        retryCount++;
                        if (retryCount <= maxRetries) {
                            $('#progress-text').text('Connection lost. Retrying (' + retryCount + '/' + maxRetries + ')...');
                            setTimeout(processBatch, 3000);
                        } else {
                            $('#progress-text').text('Stopped at ' + processed + ' of ' + totalCount + ' files. Click button to resume.');
                            $button.prop('disabled', false).removeClass('disabled');
                        }
                    }
                });
            }

            processBatch();
        });
    }
});
