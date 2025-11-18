/**
 * AI Auto Content Generator - Admin Scripts
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Test API Connection
         */
        $('.aiacg-test-api').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var api = $button.data('api');
            var $result = $('.aiacg-test-result[data-api="' + api + '"]');

            // Show loading state
            $button.prop('disabled', true).addClass('loading');
            $button.text(aiacgAdmin.strings.testing);
            $result.removeClass('success error').hide();

            // Make AJAX request
            $.ajax({
                url: aiacgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'aiacg_test_api',
                    api: api,
                    nonce: aiacgAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result
                            .addClass('success')
                            .html('<strong>' + aiacgAdmin.strings.success + '</strong><br>' + response.data.message + '<br><small>Latency: ' + Math.round(response.data.latency) + 'ms</small>')
                            .fadeIn();
                    } else {
                        $result
                            .addClass('error')
                            .html('<strong>' + aiacgAdmin.strings.error + '</strong><br>' + response.data.message)
                            .fadeIn();
                    }
                },
                error: function(xhr, status, error) {
                    $result
                        .addClass('error')
                        .html('<strong>' + aiacgAdmin.strings.error + '</strong><br>AJAX request failed: ' + error)
                        .fadeIn();
                },
                complete: function() {
                    $button.prop('disabled', false).removeClass('loading');
                    $button.text(aiacgAdmin.strings.testing.replace('...', ''));
                }
            });
        });

        /**
         * Generate Content Now
         */
        $('.aiacg-generate-now').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var count = $button.data('count');
            var originalText = $button.text();

            // Confirm action
            if (count > 1 && !confirm('Are you sure you want to generate ' + count + ' posts now?')) {
                return;
            }

            // Show loading state
            $button.prop('disabled', true).addClass('loading');
            $button.text(aiacgAdmin.strings.generating);

            // Make AJAX request
            $.ajax({
                url: aiacgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'aiacg_generate_now',
                    count: count,
                    nonce: aiacgAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('success', response.data.message);

                        // Redirect to posts page after a delay
                        setTimeout(function() {
                            window.location.href = adminUrl + 'edit.php';
                        }, 2000);
                    } else {
                        showNotice('error', response.data.message || 'Generation failed');
                    }
                },
                error: function(xhr, status, error) {
                    showNotice('error', 'AJAX request failed: ' + error);
                },
                complete: function() {
                    $button.prop('disabled', false).removeClass('loading');
                    $button.text(originalText);
                }
            });
        });

        /**
         * Clear Logs
         */
        $('.aiacg-clear-logs').on('click', function(e) {
            e.preventDefault();

            if (!confirm(aiacgAdmin.strings.confirmClear)) {
                return;
            }

            var $button = $(this);
            var originalText = $button.text();

            $button.prop('disabled', true).text('Clearing...');

            $.ajax({
                url: aiacgAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'aiacg_clear_logs',
                    nonce: aiacgAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('success', response.data.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotice('error', 'Failed to clear logs');
                    }
                },
                error: function(xhr, status, error) {
                    showNotice('error', 'AJAX request failed: ' + error);
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        });

        /**
         * Show Notice
         */
        function showNotice(type, message) {
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');

            $('.aiacg-settings-wrap').prepend($notice);

            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);

            // Make dismissible
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            });
        }

        /**
         * Auto-save form on change (optional, with debounce)
         */
        var saveTimeout;
        $('.aiacg-tab-content input, .aiacg-tab-content select, .aiacg-tab-content textarea').on('change', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(function() {
                // Add save indicator
                var $indicator = $('<span class="aiacg-save-indicator" style="color: #46b450; margin-left: 10px;">✓ Saved</span>');
                $('.submit').append($indicator);

                setTimeout(function() {
                    $indicator.fadeOut(function() {
                        $(this).remove();
                    });
                }, 2000);
            }, 500);
        });

        /**
         * Range input value display
         */
        $('input[type="range"]').on('input', function() {
            var $output = $(this).next('output');
            if ($output.length) {
                $output.val($(this).val());
            }
        });

        /**
         * Password field toggle
         */
        $('input[type="password"]').each(function() {
            var $input = $(this);
            var $toggle = $('<button type="button" class="button" style="margin-left: 5px;">Show</button>');

            $input.after($toggle);

            $toggle.on('click', function() {
                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $toggle.text('Hide');
                } else {
                    $input.attr('type', 'password');
                    $toggle.text('Show');
                }
            });
        });

        /**
         * Confirm before leaving with unsaved changes
         */
        var formChanged = false;

        $('.aiacg-tab-content form input, .aiacg-tab-content form select, .aiacg-tab-content form textarea').on('change', function() {
            formChanged = true;
        });

        $('.aiacg-tab-content form').on('submit', function() {
            formChanged = false;
        });

        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        /**
         * Tab state persistence
         */
        $('.nav-tab-wrapper a').on('click', function() {
            var tab = $(this).attr('href').split('tab=')[1];
            if (tab) {
                localStorage.setItem('aiacg_active_tab', tab);
            }
        });

        // Restore active tab
        var savedTab = localStorage.getItem('aiacg_active_tab');
        if (savedTab && window.location.search.indexOf('tab=') === -1) {
            window.location.href = window.location.href + '&tab=' + savedTab;
        }

        /**
         * Copy to clipboard functionality
         */
        $('.aiacg-copy-btn').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var text = $button.data('copy');

            // Create temporary textarea
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();

            // Show feedback
            var originalText = $button.text();
            $button.text('Copied!');

            setTimeout(function() {
                $button.text(originalText);
            }, 2000);
        });

        /**
         * Expandable sections
         */
        $('.aiacg-expandable-header').on('click', function() {
            $(this).next('.aiacg-expandable-content').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });

        /**
         * Initialize tooltips
         */
        $('.aiacg-tooltip').each(function() {
            $(this).attr('title', $(this).data('tooltip'));
        });

    });

    // Helper: Get admin URL
    var adminUrl = window.location.origin + '/wp-admin/';

})(jQuery);
