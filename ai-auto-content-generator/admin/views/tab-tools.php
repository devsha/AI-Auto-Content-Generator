<?php
/**
 * 工具Tab - 导入导出、备份恢复
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="aiacg-tools-section">
    <h2><?php _e('System Diagnostics', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-tool-box">
        <h3><?php _e('Run System Health Check', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Perform a comprehensive diagnostic check of your WordPress environment, PHP configuration, database, plugin settings, and API connections.', 'ai-auto-content-generator'); ?></p>

        <button type="button" class="button button-primary aiacg-run-diagnostics">
            <span class="dashicons dashicons-admin-tools" style="margin-top: 3px;"></span>
            <?php _e('Run Diagnostics', 'ai-auto-content-generator'); ?>
        </button>

        <div id="aiacg-diagnostics-result" style="margin-top: 20px; display: none;">
            <div id="aiacg-diagnostics-content"></div>
        </div>
    </div>

    <hr style="margin: 30px 0;">

    <h2><?php _e('Import / Export Settings', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-tool-box">
        <h3><?php _e('Export Settings', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Export your current plugin settings as a JSON file. This does not include API keys for security reasons.', 'ai-auto-content-generator'); ?></p>
        <button type="button" class="button button-primary aiacg-export-settings">
            <span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
            <?php _e('Export Settings', 'ai-auto-content-generator'); ?>
        </button>
    </div>

    <div class="aiacg-tool-box" style="margin-top: 20px;">
        <h3><?php _e('Import Settings', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Import settings from a previously exported JSON file. This will overwrite your current settings.', 'ai-auto-content-generator'); ?></p>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('aiacg_import_settings'); ?>
            <input type="file" name="settings_file" accept=".json" required>
            <button type="submit" name="aiacg_import_settings" class="button">
                <span class="dashicons dashicons-upload" style="margin-top: 3px;"></span>
                <?php _e('Import Settings', 'ai-auto-content-generator'); ?>
            </button>
        </form>
    </div>

    <div class="aiacg-tool-box" style="margin-top: 20px;">
        <h3><?php _e('Export History Report (CSV)', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Export all generation history as a CSV file for analysis in Excel or other tools. Includes quality scores, costs, and detailed statistics.', 'ai-auto-content-generator'); ?></p>
        <button type="button" class="button button-primary aiacg-export-history-csv">
            <span class="dashicons dashicons-media-spreadsheet" style="margin-top: 3px;"></span>
            <?php _e('Export History CSV', 'ai-auto-content-generator'); ?>
        </button>
        <p class="description" style="margin-top: 10px;">
            <?php
            $total_records = AIACG_Database::get_statistics()['total_generated'];
            printf(__('Total records: %d', 'ai-auto-content-generator'), $total_records);
            ?>
        </p>
    </div>

    <hr style="margin: 30px 0;">

    <h2><?php _e('Quality Management', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-tool-box">
        <h3><?php _e('Re-evaluate Quality Scores', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Re-evaluate the quality scores for posts that don\'t have quality scores yet (generated before v1.1.0). This process evaluates posts in batches of 100.', 'ai-auto-content-generator'); ?></p>

        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . AIACG_Database::TABLE_NAME;
        $pending_count = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name}
             WHERE (quality_score = 0 OR quality_score IS NULL)
             AND status = 'completed'
             AND post_id > 0"
        );
        ?>

        <p class="description">
            <?php
            if ($pending_count > 0) {
                printf(
                    __('Posts without quality scores: <strong>%d</strong>', 'ai-auto-content-generator'),
                    $pending_count
                );
            } else {
                _e('All posts have been evaluated.', 'ai-auto-content-generator');
            }
            ?>
        </p>

        <button type="button" class="button button-primary aiacg-reevaluate-quality" <?php echo $pending_count > 0 ? '' : 'disabled'; ?>>
            <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
            <?php _e('Re-evaluate Quality Scores', 'ai-auto-content-generator'); ?>
        </button>

        <div id="aiacg-reevaluate-progress" style="margin-top: 15px; display: none;">
            <div class="progress-bar" style="width: 100%; height: 24px; background: #e0e0e0; border-radius: 12px; overflow: hidden;">
                <div class="progress-fill" style="height: 100%; width: 0%; background: linear-gradient(90deg, #46b450 0%, #0073aa 100%); transition: width 0.3s ease;"></div>
            </div>
            <p id="aiacg-reevaluate-status" style="margin-top: 10px; font-weight: 600;"></p>
        </div>
    </div>

    <hr style="margin: 30px 0;">

    <h2><?php _e('Database Management', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-tool-box">
        <h3><?php _e('Clean Old Records', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Remove history records older than the specified number of days.', 'ai-auto-content-generator'); ?></p>

        <label>
            <?php _e('Delete records older than:', 'ai-auto-content-generator'); ?>
            <input type="number" id="aiacg-cleanup-days" value="90" min="7" max="365" style="width: 80px;">
            <?php _e('days', 'ai-auto-content-generator'); ?>
        </label>
        <br><br>

        <button type="button" class="button aiacg-cleanup-old-records">
            <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
            <?php _e('Clean Old Records', 'ai-auto-content-generator'); ?>
        </button>
    </div>

    <div class="aiacg-tool-box" style="margin-top: 20px;">
        <h3><?php _e('Reset All Statistics', 'ai-auto-content-generator'); ?></h3>
        <p class="description" style="color: #d63638;">
            <?php _e('WARNING: This will reset all API usage statistics. This action cannot be undone.', 'ai-auto-content-generator'); ?>
        </p>

        <button type="button" class="button aiacg-reset-stats" style="color: #d63638; border-color: #d63638;">
            <span class="dashicons dashicons-warning" style="margin-top: 3px;"></span>
            <?php _e('Reset Statistics', 'ai-auto-content-generator'); ?>
        </button>
    </div>

    <hr style="margin: 30px 0;">

    <h2><?php _e('Batch Operations', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-tool-box">
        <h3><?php _e('Batch Update Categories & Tags', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Apply categories and tags to all generated posts in bulk.', 'ai-auto-content-generator'); ?></p>

        <div style="margin-bottom: 15px;">
            <label>
                <strong><?php _e('Date Range:', 'ai-auto-content-generator'); ?></strong><br>
                <?php _e('From:', 'ai-auto-content-generator'); ?>
                <input type="date" id="aiacg-batch-date-from" style="margin-right: 10px;">
                <?php _e('To:', 'ai-auto-content-generator'); ?>
                <input type="date" id="aiacg-batch-date-to" value="<?php echo date('Y-m-d'); ?>">
            </label>
        </div>

        <div style="margin-bottom: 15px;">
            <label>
                <strong><?php _e('Categories:', 'ai-auto-content-generator'); ?></strong><br>
                <?php
                wp_dropdown_categories(array(
                    'show_option_none' => __('— No Change —', 'ai-auto-content-generator'),
                    'option_none_value' => '',
                    'hide_empty' => 0,
                    'hierarchical' => 1,
                    'id' => 'aiacg-batch-category',
                    'name' => 'aiacg_batch_category',
                    'class' => 'regular-text',
                ));
                ?>
            </label>
        </div>

        <div style="margin-bottom: 15px;">
            <label>
                <strong><?php _e('Add Tags (comma separated):', 'ai-auto-content-generator'); ?></strong><br>
                <input type="text" id="aiacg-batch-tags" class="regular-text" placeholder="<?php esc_attr_e('tag1, tag2, tag3', 'ai-auto-content-generator'); ?>">
            </label>
        </div>

        <button type="button" class="button button-primary aiacg-batch-update-taxonomy">
            <span class="dashicons dashicons-category" style="margin-top: 3px;"></span>
            <?php _e('Apply to Generated Posts', 'ai-auto-content-generator'); ?>
        </button>

        <div id="aiacg-batch-taxonomy-progress" style="margin-top: 15px; display: none;">
            <div class="progress-bar" style="width: 100%; height: 24px; background: #e0e0e0; border-radius: 12px; overflow: hidden;">
                <div class="progress-fill" style="height: 100%; width: 0%; background: linear-gradient(90deg, #46b450 0%, #0073aa 100%); transition: width 0.3s ease;"></div>
            </div>
            <p id="aiacg-batch-taxonomy-status" style="margin-top: 10px; font-weight: 600;"></p>
        </div>
    </div>

    <div class="aiacg-tool-box" style="margin-top: 20px;">
        <h3><?php _e('Batch Update Post Status', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Change the publish status of generated posts in bulk (draft, publish, private, etc).', 'ai-auto-content-generator'); ?></p>

        <div style="margin-bottom: 15px;">
            <label>
                <strong><?php _e('Target Posts:', 'ai-auto-content-generator'); ?></strong><br>
                <select id="aiacg-batch-status-filter" class="regular-text">
                    <option value="all"><?php _e('All Generated Posts', 'ai-auto-content-generator'); ?></option>
                    <option value="publish"><?php _e('Published Posts Only', 'ai-auto-content-generator'); ?></option>
                    <option value="draft"><?php _e('Draft Posts Only', 'ai-auto-content-generator'); ?></option>
                    <option value="last_7_days"><?php _e('Last 7 Days', 'ai-auto-content-generator'); ?></option>
                    <option value="last_30_days"><?php _e('Last 30 Days', 'ai-auto-content-generator'); ?></option>
                </select>
            </label>
        </div>

        <div style="margin-bottom: 15px;">
            <label>
                <strong><?php _e('New Status:', 'ai-auto-content-generator'); ?></strong><br>
                <select id="aiacg-batch-new-status" class="regular-text">
                    <option value="publish"><?php _e('Publish', 'ai-auto-content-generator'); ?></option>
                    <option value="draft"><?php _e('Draft', 'ai-auto-content-generator'); ?></option>
                    <option value="private"><?php _e('Private', 'ai-auto-content-generator'); ?></option>
                    <option value="pending"><?php _e('Pending Review', 'ai-auto-content-generator'); ?></option>
                </select>
            </label>
        </div>

        <button type="button" class="button button-primary aiacg-batch-update-status">
            <span class="dashicons dashicons-edit" style="margin-top: 3px;"></span>
            <?php _e('Update Post Status', 'ai-auto-content-generator'); ?>
        </button>

        <div id="aiacg-batch-status-progress" style="margin-top: 15px; display: none;">
            <div class="progress-bar" style="width: 100%; height: 24px; background: #e0e0e0; border-radius: 12px; overflow: hidden;">
                <div class="progress-fill" style="height: 100%; width: 0%; background: linear-gradient(90deg, #46b450 0%, #0073aa 100%); transition: width 0.3s ease;"></div>
            </div>
            <p id="aiacg-batch-status-status" style="margin-top: 10px; font-weight: 600;"></p>
        </div>
    </div>

    <div class="aiacg-tool-box" style="margin-top: 20px;">
        <h3><?php _e('Batch Delete Posts', 'ai-auto-content-generator'); ?></h3>
        <p class="description" style="color: #d63638;">
            <?php _e('WARNING: This will permanently delete the selected posts and their history records. This action cannot be undone!', 'ai-auto-content-generator'); ?>
        </p>

        <div style="margin-bottom: 15px;">
            <label>
                <strong><?php _e('Delete Posts:', 'ai-auto-content-generator'); ?></strong><br>
                <select id="aiacg-batch-delete-filter" class="regular-text">
                    <option value=""><?php _e('— Select —', 'ai-auto-content-generator'); ?></option>
                    <option value="draft_only"><?php _e('Draft Posts Only', 'ai-auto-content-generator'); ?></option>
                    <option value="low_quality"><?php _e('Low Quality Posts (Score < 60)', 'ai-auto-content-generator'); ?></option>
                    <option value="failed"><?php _e('Failed Generation Records', 'ai-auto-content-generator'); ?></option>
                    <option value="older_than_90"><?php _e('Older than 90 Days', 'ai-auto-content-generator'); ?></option>
                    <option value="older_than_180"><?php _e('Older than 180 Days', 'ai-auto-content-generator'); ?></option>
                </select>
            </label>
        </div>

        <label style="display: block; margin-bottom: 15px;">
            <input type="checkbox" id="aiacg-batch-delete-confirm" value="1">
            <?php _e('I understand this action cannot be undone', 'ai-auto-content-generator'); ?>
        </label>

        <button type="button" class="button aiacg-batch-delete-posts" style="color: #d63638; border-color: #d63638;" disabled>
            <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
            <?php _e('Delete Posts', 'ai-auto-content-generator'); ?>
        </button>

        <div id="aiacg-batch-delete-progress" style="margin-top: 15px; display: none;">
            <div class="progress-bar" style="width: 100%; height: 24px; background: #e0e0e0; border-radius: 12px; overflow: hidden;">
                <div class="progress-fill" style="height: 100%; width: 0%; background: linear-gradient(90deg, #dc3232 0%, #ffb900 100%); transition: width 0.3s ease;"></div>
            </div>
            <p id="aiacg-batch-delete-status" style="margin-top: 10px; font-weight: 600;"></p>
        </div>
    </div>

    <hr style="margin: 30px 0;">

    <h2><?php _e('System Tools', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-tool-box">
        <h3><?php _e('Test WordPress Cron', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Manually trigger the daily generation task to test if WordPress Cron is working correctly.', 'ai-auto-content-generator'); ?></p>

        <button type="button" class="button aiacg-test-cron">
            <span class="dashicons dashicons-clock" style="margin-top: 3px;"></span>
            <?php _e('Test Cron Task', 'ai-auto-content-generator'); ?>
        </button>

        <div id="aiacg-cron-test-result" style="margin-top: 10px;"></div>
    </div>

    <div class="aiacg-tool-box" style="margin-top: 20px;">
        <h3><?php _e('System Information', 'ai-auto-content-generator'); ?></h3>
        <p><?php _e('Copy this information when reporting issues or seeking support.', 'ai-auto-content-generator'); ?></p>

        <textarea id="aiacg-system-info" readonly style="width: 100%; height: 200px; font-family: monospace; font-size: 12px;">
Plugin Version: <?php echo esc_html(AIACG_VERSION); ?>

WordPress Version: <?php echo esc_html(get_bloginfo('version')); ?>

PHP Version: <?php echo esc_html(PHP_VERSION); ?>

MySQL Version: <?php global $wpdb; echo esc_html($wpdb->db_version()); ?>

Server: <?php echo esc_html($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'); ?>

WP Cron: <?php echo (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) ? 'Disabled' : 'Enabled'; ?>

Memory Limit: <?php echo esc_html(ini_get('memory_limit')); ?>

Max Execution Time: <?php echo esc_html(ini_get('max_execution_time')); ?>s

Active Plugins: <?php echo count(get_option('active_plugins', array())); ?>

Active Theme: <?php $theme = wp_get_theme(); echo esc_html($theme->get('Name') . ' ' . $theme->get('Version')); ?>

<?php
$stats = AIACG_Database::get_statistics();
?>
Total Generated Posts: <?php echo esc_html($stats['total_generated']); ?>

This Month: <?php echo esc_html($stats['this_month']); ?>

Success Rate: <?php echo esc_html(number_format($stats['success_rate'], 1)); ?>%

Total Cost: $<?php echo esc_html(number_format($stats['total_cost'], 4)); ?>

</textarea>

        <button type="button" class="button aiacg-copy-system-info" style="margin-top: 10px;">
            <span class="dashicons dashicons-clipboard" style="margin-top: 3px;"></span>
            <?php _e('Copy to Clipboard', 'ai-auto-content-generator'); ?>
        </button>
    </div>

    <hr style="margin: 30px 0;">

    <h2><?php _e('Danger Zone', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-tool-box" style="border: 2px solid #d63638; background: #fff3f3;">
        <h3 style="color: #d63638;"><?php _e('Reset Plugin to Defaults', 'ai-auto-content-generator'); ?></h3>
        <p class="description">
            <?php _e('This will delete all plugin settings, history records, and logs. Generated posts will NOT be deleted. This action cannot be undone.', 'ai-auto-content-generator'); ?>
        </p>

        <label>
            <input type="checkbox" id="aiacg-confirm-reset">
            <?php _e('I understand this will delete all plugin data', 'ai-auto-content-generator'); ?>
        </label>
        <br><br>

        <button type="button" class="button aiacg-reset-plugin" disabled style="background: #d63638; border-color: #d63638; color: #fff;">
            <span class="dashicons dashicons-warning" style="margin-top: 3px;"></span>
            <?php _e('Reset Plugin', 'ai-auto-content-generator'); ?>
        </button>
    </div>
</div>

<style>
.aiacg-tool-box {
    background: #fff;
    border: 1px solid #dcdcde;
    padding: 20px;
    border-radius: 4px;
}

.aiacg-tool-box h3 {
    margin-top: 0;
}

.aiacg-tool-box .button .dashicons {
    display: inline-block;
    vertical-align: middle;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Enable/disable reset button
    $('#aiacg-confirm-reset').on('change', function() {
        $('.aiacg-reset-plugin').prop('disabled', !this.checked);
    });

    // Re-evaluate quality scores
    $('.aiacg-reevaluate-quality').on('click', function() {
        if (!confirm('<?php _e('This will re-evaluate the quality scores for all posts without scores. Continue?', 'ai-auto-content-generator'); ?>')) {
            return;
        }

        var $button = $(this);
        var $progress = $('#aiacg-reevaluate-progress');
        var $progressFill = $progress.find('.progress-fill');
        var $status = $('#aiacg-reevaluate-status');
        var totalProcessed = 0;
        var initialRemaining = parseInt($button.prev('.description').find('strong').text()) || 0;

        $button.prop('disabled', true);
        $progress.show();

        function processNextBatch() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aiacg_reevaluate_quality',
                    nonce: aiacgAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        totalProcessed += response.data.processed;
                        var remaining = response.data.remaining;

                        // Update progress bar
                        var percentage = initialRemaining > 0
                            ? Math.round((totalProcessed / initialRemaining) * 100)
                            : 100;
                        $progressFill.css('width', percentage + '%');

                        // Update status message
                        $status.html(
                            '<?php _e('Processed:', 'ai-auto-content-generator'); ?> <strong>' + totalProcessed + '</strong> | ' +
                            '<?php _e('Remaining:', 'ai-auto-content-generator'); ?> <strong>' + remaining + '</strong>'
                        );

                        // If there are more records, process next batch
                        if (remaining > 0) {
                            setTimeout(processNextBatch, 500); // Small delay to prevent server overload
                        } else {
                            // All done
                            $status.html('<span style="color: #46b450;">✓ ' +
                                '<?php _e('All quality scores have been re-evaluated!', 'ai-auto-content-generator'); ?>' +
                                '</span>');

                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        alert('Error: ' + (response.data.message || 'Unknown error'));
                        $button.prop('disabled', false);
                        $progress.hide();
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX error: ' + error);
                    $button.prop('disabled', false);
                    $progress.hide();
                }
            });
        }

        processNextBatch();
    });

    // Export settings
    $('.aiacg-export-settings').on('click', function() {
        window.location.href = ajaxurl + '?action=aiacg_export_settings&nonce=' + aiacgAdmin.nonce;
    });

    // Clean old records
    $('.aiacg-cleanup-old-records').on('click', function() {
        var days = $('#aiacg-cleanup-days').val();

        if (!confirm('Are you sure you want to delete records older than ' + days + ' days?')) {
            return;
        }

        var $button = $(this);
        $button.prop('disabled', true).text('Cleaning...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aiacg_cleanup_records',
                days: days,
                nonce: aiacgAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                } else {
                    alert('Error: ' + (response.data.message || 'Unknown error'));
                }
            },
            complete: function() {
                $button.prop('disabled', false).html('<span class="dashicons dashicons-trash" style="margin-top: 3px;"></span> Clean Old Records');
            }
        });
    });

    // Copy system info
    $('.aiacg-copy-system-info').on('click', function() {
        var $textarea = $('#aiacg-system-info');
        $textarea.select();
        document.execCommand('copy');

        var $button = $(this);
        var originalText = $button.html();
        $button.html('<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> Copied!');

        setTimeout(function() {
            $button.html(originalText);
        }, 2000);
    });

    // Reset plugin
    $('.aiacg-reset-plugin').on('click', function() {
        if (!confirm('Are you ABSOLUTELY SURE? This will delete all plugin settings, history, and logs. This cannot be undone!')) {
            return;
        }

        if (!confirm('Last chance! Click OK to proceed with the reset.')) {
            return;
        }

        var $button = $(this);
        $button.prop('disabled', true).text('Resetting...');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aiacg_reset_plugin',
                nonce: aiacgAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Plugin has been reset. The page will reload.');
                    location.reload();
                } else {
                    alert('Error: ' + (response.data.message || 'Unknown error'));
                }
            }
        });
    });

    // ========================================================================
    // Batch Operations
    // ========================================================================

    // Enable/disable delete button based on confirmation
    $('#aiacg-batch-delete-confirm').on('change', function() {
        $('.aiacg-batch-delete-posts').prop('disabled', !this.checked);
    });

    // Batch update taxonomy
    $('.aiacg-batch-update-taxonomy').on('click', function() {
        var $button = $(this);
        var $progress = $('#aiacg-batch-taxonomy-progress');
        var $progressFill = $progress.find('.progress-fill');
        var $status = $('#aiacg-batch-taxonomy-status');

        var dateFrom = $('#aiacg-batch-date-from').val();
        var dateTo = $('#aiacg-batch-date-to').val();
        var categoryId = $('#aiacg-batch-category').val();
        var tags = $('#aiacg-batch-tags').val();

        if (!categoryId && !tags) {
            alert('<?php _e('Please select at least one option (category or tags)', 'ai-auto-content-generator'); ?>');
            return;
        }

        if (!confirm('<?php _e('This will update categories/tags for all matching generated posts. Continue?', 'ai-auto-content-generator'); ?>')) {
            return;
        }

        $button.prop('disabled', true);
        $progress.show();
        $progressFill.css('width', '50%');
        $status.html('<?php _e('Processing...', 'ai-auto-content-generator'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aiacg_batch_update_taxonomy',
                nonce: aiacgAdmin.nonce,
                date_from: dateFrom,
                date_to: dateTo,
                category_id: categoryId,
                tags: tags
            },
            success: function(response) {
                $progressFill.css('width', '100%');
                if (response.success) {
                    $status.html('<span style="color: #46b450;">✓ ' + response.data.message + '</span>');
                    setTimeout(function() {
                        $progress.hide();
                        $button.prop('disabled', false);
                    }, 2000);
                } else {
                    $status.html('<span style="color: #dc3232;">✗ ' + (response.data.message || 'Error') + '</span>');
                    $button.prop('disabled', false);
                }
            },
            error: function() {
                $status.html('<span style="color: #dc3232;">✗ AJAX error</span>');
                $button.prop('disabled', false);
            }
        });
    });

    // Batch update status
    $('.aiacg-batch-update-status').on('click', function() {
        var $button = $(this);
        var $progress = $('#aiacg-batch-status-progress');
        var $progressFill = $progress.find('.progress-fill');
        var $status = $('#aiacg-batch-status-status');

        var filter = $('#aiacg-batch-status-filter').val();
        var newStatus = $('#aiacg-batch-new-status').val();

        if (!confirm('<?php _e('This will change the post status for all matching posts. Continue?', 'ai-auto-content-generator'); ?>')) {
            return;
        }

        $button.prop('disabled', true);
        $progress.show();
        $progressFill.css('width', '50%');
        $status.html('<?php _e('Processing...', 'ai-auto-content-generator'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aiacg_batch_update_status',
                nonce: aiacgAdmin.nonce,
                filter: filter,
                new_status: newStatus
            },
            success: function(response) {
                $progressFill.css('width', '100%');
                if (response.success) {
                    $status.html('<span style="color: #46b450;">✓ ' + response.data.message + '</span>');
                    setTimeout(function() {
                        $progress.hide();
                        $button.prop('disabled', false);
                    }, 2000);
                } else {
                    $status.html('<span style="color: #dc3232;">✗ ' + (response.data.message || 'Error') + '</span>');
                    $button.prop('disabled', false);
                }
            },
            error: function() {
                $status.html('<span style="color: #dc3232;">✗ AJAX error</span>');
                $button.prop('disabled', false);
            }
        });
    });

    // Batch delete posts
    $('.aiacg-batch-delete-posts').on('click', function() {
        var $button = $(this);
        var $progress = $('#aiacg-batch-delete-progress');
        var $progressFill = $progress.find('.progress-fill');
        var $status = $('#aiacg-batch-delete-status');

        var filter = $('#aiacg-batch-delete-filter').val();

        if (!filter) {
            alert('<?php _e('Please select what to delete', 'ai-auto-content-generator'); ?>');
            return;
        }

        if (!confirm('<?php _e('WARNING: This will PERMANENTLY delete posts and records. This action CANNOT be undone! Are you absolutely sure?', 'ai-auto-content-generator'); ?>')) {
            return;
        }

        if (!confirm('<?php _e('Last chance! Click OK to proceed with deletion.', 'ai-auto-content-generator'); ?>')) {
            return;
        }

        $button.prop('disabled', true);
        $progress.show();
        $progressFill.css('width', '50%');
        $status.html('<?php _e('Deleting...', 'ai-auto-content-generator'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aiacg_batch_delete_posts',
                nonce: aiacgAdmin.nonce,
                filter: filter
            },
            success: function(response) {
                $progressFill.css('width', '100%');
                if (response.success) {
                    $status.html('<span style="color: #46b450;">✓ ' + response.data.message + '</span>');
                    $('#aiacg-batch-delete-confirm').prop('checked', false);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $status.html('<span style="color: #dc3232;">✗ ' + (response.data.message || 'Error') + '</span>');
                    $button.prop('disabled', false);
                }
            },
            error: function() {
                $status.html('<span style="color: #dc3232;">✗ AJAX error</span>');
                $button.prop('disabled', false);
            }
        });
    });
});
</script>
