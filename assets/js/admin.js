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
            url: oijc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'oijc_test_connection',
                nonce: oijc_ajax.nonce,
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
    
    // Bulk offload functionality
    if ($('#start-bulk-offload').length) {
        
        // Get media count on page load
        $.ajax({
            url: oijc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'oijc_get_media_count',
                nonce: oijc_ajax.nonce
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
            
            var offset = 0;
            var processed = 0;
            var errors = [];
            
            function processBatch() {
                $.ajax({
                    url: oijc_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'oijc_bulk_offload',
                        nonce: oijc_ajax.nonce,
                        offset: offset
                    },
                    success: function(response) {
                        if (response.success) {
                            processed += response.data.processed;
                            offset += response.data.processed;
                            
                            if (response.data.errors.length > 0) {
                                errors = errors.concat(response.data.errors);
                            }
                            
                            var percentage = Math.round((processed / totalCount) * 100);
                            $('#progress-bar').css('width', percentage + '%');
                            $('#progress-percentage').text(percentage + '%');
                            $('#progress-text').text('Processed ' + processed + ' of ' + totalCount + ' files');
                            
                            if (response.data.complete) {
                                // Complete
                                $('#bulk-offload-complete').show();
                                $('#progress-text').text('Complete! Processed ' + processed + ' files.');
                                
                                if (errors.length > 0) {
                                    $('#bulk-offload-errors').show();
                                    errors.forEach(function(error) {
                                        $('#error-list').append('<li><strong>' + error.title + '</strong>: ' + error.error + '</li>');
                                    });
                                }
                                
                                // Update count
                                $('#media-count').text('0 (All media files are now offloaded!)');
                            } else {
                                // Continue with next batch
                                processBatch();
                            }
                        } else {
                            alert('Error: ' + response.data.message);
                            $button.prop('disabled', false).removeClass('disabled');
                        }
                    },
                    error: function() {
                        alert('An error occurred during bulk offload.');
                        $button.prop('disabled', false).removeClass('disabled');
                    }
                });
            }
            
            processBatch();
        });
    }
});
