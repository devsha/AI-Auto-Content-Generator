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
         * Export History CSV
         */
        $('.aiacg-export-history-csv').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var originalText = $button.text();

            $button.prop('disabled', true).text('Exporting...');

            // Create form and submit
            var form = $('<form>', {
                'method': 'POST',
                'action': aiacgAdmin.ajaxUrl
            });

            form.append($('<input>', {
                'type': 'hidden',
                'name': 'action',
                'value': 'aiacg_export_history_csv'
            }));

            form.append($('<input>', {
                'type': 'hidden',
                'name': 'nonce',
                'value': aiacgAdmin.nonce
            }));

            $('body').append(form);
            form.submit();
            form.remove();

            // Re-enable button
            setTimeout(function() {
                $button.prop('disabled', false).text(originalText);
                showNotice('success', 'CSV export initiated. Download should start automatically.');
            }, 1000);
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

        /**
         * Initialize Dashboard Charts (v1.1.0)
         */
        if (typeof Chart !== 'undefined') {
            initDashboardCharts();
        } else {
            // Load Chart.js from CDN if not available
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
            script.onload = function() {
                initDashboardCharts();
            };
            document.head.appendChild(script);
        }

    });

    /**
     * Initialize Dashboard Charts
     */
    function initDashboardCharts() {
        // Trend Chart (7-day generation trend)
        var trendCanvas = document.getElementById('aiacg-trend-chart');
        if (trendCanvas) {
            var trendData = JSON.parse(trendCanvas.dataset.stats || '[]');

            var trendLabels = trendData.map(function(item) { return item.date; });
            var trendValues = trendData.map(function(item) { return item.count; });

            new Chart(trendCanvas, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Posts Generated',
                        data: trendValues,
                        borderColor: '#0073aa',
                        backgroundColor: 'rgba(0, 115, 170, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#0073aa',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    return 'Generated: ' + context.parsed.y + ' posts';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // API Usage Distribution Chart (Pie/Doughnut)
        var apiCanvas = document.getElementById('aiacg-api-chart');
        if (apiCanvas) {
            var apiData = JSON.parse(apiCanvas.dataset.stats || '[]');

            var apiLabels = apiData.map(function(item) { return item.label; });
            var apiValues = apiData.map(function(item) { return item.value; });

            var apiColors = [
                '#0073aa', // Gemini - Blue
                '#46b450', // DeepSeek - Green
                '#ffb900', // OpenAI - Yellow
                '#826eb4', // Others - Purple
                '#dc3232'  // Fallback - Red
            ];

            new Chart(apiCanvas, {
                type: 'doughnut',
                data: {
                    labels: apiLabels,
                    datasets: [{
                        data: apiValues,
                        backgroundColor: apiColors.slice(0, apiLabels.length),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12 },
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                    var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Helper: Get admin URL
    var adminUrl = window.location.origin + '/wp-admin/';

    // ========================================================================
    // Cron Job Management (v1.1.1)
    // ========================================================================

    // Toggle cron job (enable/disable)
    $(document).on('click', '.aiacg-toggle-cron', function() {
        var $button = $(this);
        var action = $button.data('action');
        var actionText = action === 'enable' ? 'enable' : 'disable';

        if (!confirm(action === 'enable'
            ? 'Are you sure you want to enable automatic generation?'
            : 'Are you sure you want to disable automatic generation?')) {
            return;
        }

        $button.prop('disabled', true);

        $.ajax({
            url: aiacgAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aiacg_toggle_cron',
                cron_action: actionText,
                nonce: aiacgAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    // Reload page to update UI
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showNotice('error', response.data.message || 'Failed to toggle cron job');
                    $button.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                showNotice('error', 'AJAX Error: ' + error);
                $button.prop('disabled', false);
            }
        });
    });

    // Refresh cron status
    $(document).on('click', '.aiacg-refresh-cron-status', function() {
        var $button = $(this);
        var $card = $('#aiacg-cron-status-card');

        $button.prop('disabled', true).find('.dashicons').addClass('spin');

        $.ajax({
            url: aiacgAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aiacg_get_cron_status',
                nonce: aiacgAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', 'Status refreshed');
                    // Reload page to show updated status
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    showNotice('error', response.data.message || 'Failed to refresh status');
                }
            },
            error: function(xhr, status, error) {
                showNotice('error', 'AJAX Error: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false).find('.dashicons').removeClass('spin');
            }
        });
    });

    // Manually trigger cron job
    $(document).on('click', '.aiacg-trigger-cron', function() {
        var $button = $(this);

        if (!confirm('This will immediately start generating posts. Continue?')) {
            return;
        }

        $button.prop('disabled', true);
        var originalText = $button.html();
        $button.html('<span class="dashicons dashicons-update spin"></span> Generating...');

        $.ajax({
            url: aiacgAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aiacg_trigger_cron_manual',
                nonce: aiacgAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', response.data.message);
                    // Wait a bit then refresh to see new posts
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    showNotice('error', response.data.message || 'Failed to trigger generation');
                    $button.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                showNotice('error', 'AJAX Error: ' + error);
                $button.prop('disabled', false).html(originalText);
            }
        });
    });

    // Add spin animation for loading states
    $('<style>')
        .text('.spin { animation: spin 1s linear infinite; } @keyframes spin { 100% { transform: rotate(360deg); } }')
        .appendTo('head');

    // ========================================================================
    // System Diagnostics (v1.1.2)
    // ========================================================================

    // Run system diagnostics
    $(document).on('click', '.aiacg-run-diagnostics', function() {
        var $button = $(this);
        var $result = $('#aiacg-diagnostics-result');
        var $content = $('#aiacg-diagnostics-content');

        $button.prop('disabled', true);
        var originalText = $button.html();
        $button.html('<span class="dashicons dashicons-update spin"></span> Running diagnostics...');

        $result.show();
        $content.html('<p style="text-align:center;"><span class="dashicons dashicons-update spin"></span> Analyzing system...</p>');

        $.ajax({
            url: aiacgAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aiacg_run_diagnostics',
                nonce: aiacgAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $content.html(response.data.html_report);

                    // Show summary notification
                    var summary = response.data.summary;
                    if (summary.overall_status === 'good') {
                        showNotice('success', 'System health check completed. All systems operational!');
                    } else if (summary.overall_status === 'warning') {
                        showNotice('warning', 'System health check completed with ' + summary.total_warnings + ' warnings.');
                    } else {
                        showNotice('error', 'System health check found ' + summary.total_issues + ' issues that need attention.');
                    }
                } else {
                    $content.html('<div class="notice notice-error"><p>' + (response.data.message || 'Failed to run diagnostics') + '</p></div>');
                }
            },
            error: function(xhr, status, error) {
                $content.html('<div class="notice notice-error"><p>AJAX Error: ' + error + '</p></div>');
            },
            complete: function() {
                $button.prop('disabled', false).html(originalText);
            }
        });
    });

    // ========================================================================
    // API Health Monitoring (v1.1.1)
    // ========================================================================

    // Refresh API health
    $(document).on('click', '.aiacg-refresh-api-health', function() {
        var $button = $(this);

        $button.prop('disabled', true).find('.dashicons').addClass('spin');

        $.ajax({
            url: aiacgAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aiacg_check_api_health',
                nonce: aiacgAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('success', 'API health check completed');
                    // Reload page to show updated status
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    showNotice('error', response.data.message || 'Failed to check API health');
                }
            },
            error: function(xhr, status, error) {
                showNotice('error', 'AJAX Error: ' + error);
            },
            complete: function() {
                $button.prop('disabled', false).find('.dashicons').removeClass('spin');
            }
        });
    });

})(jQuery);
