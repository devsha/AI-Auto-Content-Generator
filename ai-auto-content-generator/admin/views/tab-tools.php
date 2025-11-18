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
});
</script>
