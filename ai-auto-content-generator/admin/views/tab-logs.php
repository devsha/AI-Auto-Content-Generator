<?php
/**
 * 日志和监控Tab
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$filter = isset($_GET['log_level']) ? sanitize_text_field($_GET['log_level']) : '';
$logs = AIACG_Database::get_logs($filter, 100);

$ai_manager = new AIACG_AI_Manager();
$api_status = $ai_manager->test_all_connections();
$usage_stats = $ai_manager->get_all_usage_stats();
?>

<div class="aiacg-logs-section">
    <h2><?php _e('System Logs', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-logs-controls">
        <select id="aiacg-log-filter" onchange="window.location.href='?page=aiacg-settings&tab=logs&log_level=' + this.value">
            <option value="" <?php selected($filter, ''); ?>><?php _e('All Levels', 'ai-auto-content-generator'); ?></option>
            <option value="info" <?php selected($filter, 'info'); ?>><?php _e('Info', 'ai-auto-content-generator'); ?></option>
            <option value="warning" <?php selected($filter, 'warning'); ?>><?php _e('Warning', 'ai-auto-content-generator'); ?></option>
            <option value="error" <?php selected($filter, 'error'); ?>><?php _e('Error', 'ai-auto-content-generator'); ?></option>
        </select>

        <button type="button" class="button aiacg-clear-logs" style="margin-left: 10px;">
            <?php _e('Clear All Logs', 'ai-auto-content-generator'); ?>
        </button>
    </div>

    <?php if (!empty($logs)): ?>
    <div class="aiacg-logs-container">
        <table class="widefat striped">
            <thead>
                <tr>
                    <th style="width: 150px;"><?php _e('Timestamp', 'ai-auto-content-generator'); ?></th>
                    <th style="width: 80px;"><?php _e('Level', 'ai-auto-content-generator'); ?></th>
                    <th><?php _e('Message', 'ai-auto-content-generator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr class="aiacg-log-<?php echo esc_attr($log['level']); ?>">
                    <td><?php echo esc_html($log['timestamp']); ?></td>
                    <td>
                        <span class="aiacg-log-level aiacg-log-level-<?php echo esc_attr($log['level']); ?>">
                            <?php echo esc_html(strtoupper($log['level'])); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo esc_html($log['message']); ?>
                        <?php if (!empty($log['context'])): ?>
                        <details style="margin-top: 5px;">
                            <summary style="cursor: pointer; color: #666;"><?php _e('View details', 'ai-auto-content-generator'); ?></summary>
                            <pre style="background: #f5f5f5; padding: 10px; margin-top: 5px; overflow-x: auto;"><?php echo esc_html(print_r($log['context'], true)); ?></pre>
                        </details>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p><?php _e('No logs found.', 'ai-auto-content-generator'); ?></p>
    <?php endif; ?>

    <h2 style="margin-top: 30px;"><?php _e('API Monitoring', 'ai-auto-content-generator'); ?></h2>

    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('API', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Status', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Message', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Latency', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Total Calls', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Avg Latency', 'ai-auto-content-generator'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($api_status as $api_name => $status): ?>
            <tr>
                <td><strong><?php echo esc_html(ucfirst($api_name)); ?></strong></td>
                <td>
                    <?php if ($status['success']): ?>
                        <span class="aiacg-status-success">✓ <?php _e('Online', 'ai-auto-content-generator'); ?></span>
                    <?php else: ?>
                        <span class="aiacg-status-error">✗ <?php _e('Offline', 'ai-auto-content-generator'); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo esc_html($status['message']); ?></td>
                <td><?php echo esc_html(round($status['latency'], 2)); ?>ms</td>
                <td>
                    <?php
                    echo isset($usage_stats[$api_name]) ? esc_html($usage_stats[$api_name]['total_calls']) : '0';
                    ?>
                </td>
                <td>
                    <?php
                    echo isset($usage_stats[$api_name]) ? esc_html($usage_stats[$api_name]['avg_latency']) . 'ms' : '-';
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 style="margin-top: 30px;"><?php _e('System Information', 'ai-auto-content-generator'); ?></h2>

    <table class="widefat">
        <tbody>
            <tr>
                <th style="width: 200px;"><?php _e('Plugin Version', 'ai-auto-content-generator'); ?></th>
                <td><?php echo esc_html(AIACG_VERSION); ?></td>
            </tr>
            <tr>
                <th><?php _e('WordPress Version', 'ai-auto-content-generator'); ?></th>
                <td><?php echo esc_html(get_bloginfo('version')); ?></td>
            </tr>
            <tr>
                <th><?php _e('PHP Version', 'ai-auto-content-generator'); ?></th>
                <td><?php echo esc_html(PHP_VERSION); ?></td>
            </tr>
            <tr>
                <th><?php _e('WP Cron Status', 'ai-auto-content-generator'); ?></th>
                <td>
                    <?php
                    if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
                        echo '<span class="aiacg-status-error">✗ ' . __('Disabled', 'ai-auto-content-generator') . '</span>';
                        echo '<p class="description">' . __('WP Cron is disabled. Make sure you have set up a system cron job.', 'ai-auto-content-generator') . '</p>';
                    } else {
                        echo '<span class="aiacg-status-success">✓ ' . __('Enabled', 'ai-auto-content-generator') . '</span>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php _e('Memory Limit', 'ai-auto-content-generator'); ?></th>
                <td><?php echo esc_html(ini_get('memory_limit')); ?></td>
            </tr>
            <tr>
                <th><?php _e('Max Execution Time', 'ai-auto-content-generator'); ?></th>
                <td><?php echo esc_html(ini_get('max_execution_time')); ?>s</td>
            </tr>
        </tbody>
    </table>

    <h3 style="margin-top: 20px;"><?php _e('Scheduled Tasks', 'ai-auto-content-generator'); ?></h3>

    <?php
    $scheduler = new AIACG_Scheduler();
    $schedule_status = $scheduler->get_schedule_status();
    ?>

    <table class="widefat">
        <tbody>
            <tr>
                <th style="width: 200px;"><?php _e('Daily Generation', 'ai-auto-content-generator'); ?></th>
                <td>
                    <?php if ($schedule_status['is_scheduled']): ?>
                        <span class="aiacg-status-success">✓ <?php _e('Scheduled', 'ai-auto-content-generator'); ?></span>
                        <br>
                        <strong><?php _e('Next run:', 'ai-auto-content-generator'); ?></strong>
                        <?php echo esc_html($schedule_status['next_run']); ?>
                        (<?php echo esc_html($schedule_status['next_run_relative']); ?> from now)
                    <?php else: ?>
                        <span class="aiacg-status-error">✗ <?php _e('Not scheduled', 'ai-auto-content-generator'); ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if (!empty($schedule_status['last_run'])): ?>
            <tr>
                <th><?php _e('Last Execution', 'ai-auto-content-generator'); ?></th>
                <td>
                    <?php echo esc_html($schedule_status['last_run']); ?>
                    <?php if (!empty($schedule_status['last_result'])): ?>
                        <br>
                        <strong><?php _e('Result:', 'ai-auto-content-generator'); ?></strong>
                        <?php
                        $result = $schedule_status['last_result'];
                        $success_count = isset($result['success']) ? count($result['success']) : 0;
                        $failed_count = isset($result['failed']) ? count($result['failed']) : 0;
                        echo sprintf(__('%d succeeded, %d failed', 'ai-auto-content-generator'), $success_count, $failed_count);
                        ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
