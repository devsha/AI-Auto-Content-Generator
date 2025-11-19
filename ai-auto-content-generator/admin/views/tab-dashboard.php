<?php
/**
 * Dashboard Tab View
 *
 * @package AI_Auto_Content_Generator
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$statistics = AIACG_Database::get_statistics();
$scheduler = new AIACG_Scheduler();
$schedule_status = $scheduler->get_schedule_status();

// Get extended statistics
$recent_posts = AIACG_Database::get_history(array('limit' => 10));
$api_usage = AIACG_Database::get_api_usage_stats();
$daily_stats = AIACG_Database::get_daily_stats(7); // Last 7 days
$budget_status = AIACG_Budget_Manager::get_budget_status();

// Get cron status
$cron_status = AIACG_Cron_Manager::get_cron_status();
$cron_health = AIACG_Cron_Manager::health_check();

// Get API health
$api_health_summary = AIACG_API_Health_Monitor::get_health_summary();
?>

<div class="aiacg-dashboard">
    <!-- Statistics Cards -->
    <div class="aiacg-stats-grid">
        <div class="aiacg-stat-card aiacg-stat-primary">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <h3><?php echo esc_html($statistics['total_generated']); ?></h3>
                <p><?php _e('Total Posts Generated', 'ai-auto-content-generator'); ?></p>
            </div>
        </div>

        <div class="aiacg-stat-card aiacg-stat-success">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <h3><?php echo esc_html(number_format($statistics['success_rate'], 1)); ?>%</h3>
                <p><?php _e('Success Rate', 'ai-auto-content-generator'); ?></p>
            </div>
        </div>

        <div class="aiacg-stat-card aiacg-stat-cost">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <h3>$<?php echo esc_html(number_format($statistics['total_cost'], 2)); ?></h3>
                <p><?php _e('Total Cost', 'ai-auto-content-generator'); ?></p>
            </div>
        </div>

        <div class="aiacg-stat-card aiacg-stat-tokens">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
                <h3><?php echo esc_html(number_format($statistics['total_tokens'])); ?></h3>
                <p><?php _e('Total Tokens Used', 'ai-auto-content-generator'); ?></p>
            </div>
        </div>
    </div>

    <!-- Budget Status -->
    <div class="aiacg-budget-section">
        <h2><?php _e('Budget Status', 'ai-auto-content-generator'); ?></h2>
        <div class="budget-cards">
            <div class="budget-card">
                <h4><?php _e('Daily Budget', 'ai-auto-content-generator'); ?></h4>
                <div class="budget-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo esc_attr($budget_status['daily_percentage']); ?>%;"></div>
                    </div>
                    <p>
                        $<?php echo esc_html(number_format($budget_status['daily_used'], 2)); ?> /
                        $<?php echo esc_html(number_format($budget_status['daily_limit'], 2)); ?>
                        (<?php echo esc_html(number_format($budget_status['daily_percentage'], 1)); ?>%)
                    </p>
                </div>
            </div>

            <div class="budget-card">
                <h4><?php _e('Monthly Budget', 'ai-auto-content-generator'); ?></h4>
                <div class="budget-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo esc_attr($budget_status['monthly_percentage']); ?>%;"></div>
                    </div>
                    <p>
                        $<?php echo esc_html(number_format($budget_status['monthly_used'], 2)); ?> /
                        $<?php echo esc_html(number_format($budget_status['monthly_limit'], 2)); ?>
                        (<?php echo esc_html(number_format($budget_status['monthly_percentage'], 1)); ?>%)
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cron Job Status -->
    <div class="aiacg-cron-section">
        <h2><?php _e('Automatic Generation Status', 'ai-auto-content-generator'); ?></h2>
        <div class="cron-status-card" id="aiacg-cron-status-card">
            <?php
            $status_class = $cron_status['enabled'] ? 'status-enabled' : 'status-disabled';
            $health_class = 'health-' . $cron_health['status']; // good, warning, critical
            ?>
            <div class="cron-header">
                <div class="cron-status-indicator <?php echo esc_attr($status_class); ?>">
                    <span class="status-dot"></span>
                    <?php if ($cron_status['enabled']): ?>
                        <strong><?php _e('Enabled', 'ai-auto-content-generator'); ?></strong>
                    <?php else: ?>
                        <strong><?php _e('Disabled', 'ai-auto-content-generator'); ?></strong>
                    <?php endif; ?>
                </div>
                <div class="cron-health <?php echo esc_attr($health_class); ?>">
                    <?php
                    if ($cron_health['status'] === 'good') {
                        echo '✓ ' . __('Healthy', 'ai-auto-content-generator');
                    } elseif ($cron_health['status'] === 'warning') {
                        echo '⚠ ' . __('Warning', 'ai-auto-content-generator');
                    } else {
                        echo '✗ ' . __('Issues Detected', 'ai-auto-content-generator');
                    }
                    ?>
                </div>
            </div>

            <div class="cron-details">
                <?php if ($cron_status['enabled']): ?>
                    <div class="cron-detail-item">
                        <span class="detail-label"><?php _e('Next Run:', 'ai-auto-content-generator'); ?></span>
                        <span class="detail-value">
                            <?php echo esc_html($cron_status['next_run_formatted']); ?>
                            <small>(<?php
                            if ($cron_status['is_past_due']) {
                                echo __('Past due!', 'ai-auto-content-generator');
                            } else {
                                printf(__('in %s', 'ai-auto-content-generator'), $cron_status['next_run_relative']);
                            }
                            ?>)</small>
                        </span>
                    </div>
                <?php endif; ?>

                <div class="cron-detail-item">
                    <span class="detail-label"><?php _e('Scheduled Time:', 'ai-auto-content-generator'); ?></span>
                    <span class="detail-value"><?php echo esc_html($cron_status['scheduled_time']); ?></span>
                </div>

                <?php if ($cron_status['last_execution']): ?>
                    <div class="cron-detail-item">
                        <span class="detail-label"><?php _e('Last Execution:', 'ai-auto-content-generator'); ?></span>
                        <span class="detail-value"><?php echo esc_html($cron_status['last_execution']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($cron_health['issues']) || !empty($cron_health['warnings'])): ?>
                <div class="cron-alerts">
                    <?php foreach ($cron_health['issues'] as $issue): ?>
                        <div class="alert alert-error">
                            <span class="dashicons dashicons-warning"></span>
                            <?php echo esc_html($issue); ?>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach ($cron_health['warnings'] as $warning): ?>
                        <div class="alert alert-warning">
                            <span class="dashicons dashicons-info"></span>
                            <?php echo esc_html($warning); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="cron-actions">
                <?php if ($cron_status['enabled']): ?>
                    <button type="button" class="button aiacg-toggle-cron" data-action="disable">
                        <span class="dashicons dashicons-no"></span>
                        <?php _e('Disable Auto-Generation', 'ai-auto-content-generator'); ?>
                    </button>
                    <button type="button" class="button button-secondary aiacg-trigger-cron">
                        <span class="dashicons dashicons-controls-play"></span>
                        <?php _e('Run Now', 'ai-auto-content-generator'); ?>
                    </button>
                <?php else: ?>
                    <button type="button" class="button button-primary aiacg-toggle-cron" data-action="enable">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Enable Auto-Generation', 'ai-auto-content-generator'); ?>
                    </button>
                <?php endif; ?>
                <button type="button" class="button button-secondary aiacg-refresh-cron-status">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Refresh Status', 'ai-auto-content-generator'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- API Health Status -->
    <div class="aiacg-api-health-section">
        <h2><?php _e('API Health Status', 'ai-auto-content-generator'); ?></h2>
        <div class="api-health-card" id="aiacg-api-health-card">
            <?php
            $overall_status_class = 'status-' . $api_health_summary['overall_status'];
            ?>
            <div class="api-health-header">
                <div class="api-health-overall <?php echo esc_attr($overall_status_class); ?>">
                    <?php
                    $status_icons = array(
                        'healthy' => '✓',
                        'degraded' => '⚠',
                        'critical' => '✗',
                        'not_configured' => '○',
                    );
                    $status_icon = isset($status_icons[$api_health_summary['overall_status']])
                        ? $status_icons[$api_health_summary['overall_status']]
                        : '?';

                    echo '<span class="status-icon">' . $status_icon . '</span>';

                    if ($api_health_summary['overall_status'] === 'healthy') {
                        _e('All Systems Operational', 'ai-auto-content-generator');
                    } elseif ($api_health_summary['overall_status'] === 'degraded') {
                        _e('Partial Service Disruption', 'ai-auto-content-generator');
                    } elseif ($api_health_summary['overall_status'] === 'critical') {
                        _e('Service Disruption', 'ai-auto-content-generator');
                    } else {
                        _e('No APIs Configured', 'ai-auto-content-generator');
                    }
                    ?>
                </div>
                <div class="api-health-summary">
                    <?php
                    printf(
                        __('%d of %d APIs Healthy', 'ai-auto-content-generator'),
                        $api_health_summary['healthy_count'],
                        $api_health_summary['total_count']
                    );
                    ?>
                </div>
            </div>

            <?php if (!empty($api_health_summary['api_results'])): ?>
                <div class="api-health-details">
                    <?php foreach ($api_health_summary['api_results'] as $api_name => $api_result): ?>
                        <div class="api-health-item status-<?php echo esc_attr($api_result['status']); ?>">
                            <div class="api-name">
                                <?php
                                $api_labels = array(
                                    'gemini' => 'Google Gemini',
                                    'deepseek' => 'DeepSeek',
                                    'openai' => 'OpenAI',
                                );
                                echo esc_html($api_labels[$api_name] ?? ucfirst($api_name));
                                ?>
                            </div>
                            <div class="api-status">
                                <span class="status-badge">
                                    <?php
                                    if ($api_result['status'] === 'healthy') {
                                        echo '✓ ' . __('Healthy', 'ai-auto-content-generator');
                                    } elseif ($api_result['status'] === 'error') {
                                        echo '✗ ' . __('Error', 'ai-auto-content-generator');
                                    } else {
                                        echo '○ ' . __('Not Configured', 'ai-auto-content-generator');
                                    }
                                    ?>
                                </status-badge>
                                <?php if (isset($api_result['response_time']) && $api_result['response_time'] > 0): ?>
                                    <span class="response-time"><?php echo esc_html($api_result['response_time']); ?>ms</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($api_result['message']) && $api_result['status'] !== 'healthy'): ?>
                                <div class="api-message"><?php echo esc_html($api_result['message']); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="api-health-actions">
                <button type="button" class="button button-secondary aiacg-refresh-api-health">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Refresh API Health', 'ai-auto-content-generator'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="aiacg-charts-section">
        <div class="chart-container">
            <h3><?php _e('7-Day Generation Trend', 'ai-auto-content-generator'); ?></h3>
            <canvas id="aiacg-trend-chart" data-stats='<?php echo json_encode($daily_stats); ?>'></canvas>
        </div>

        <div class="chart-container">
            <h3><?php _e('API Usage Distribution', 'ai-auto-content-generator'); ?></h3>
            <canvas id="aiacg-api-chart" data-stats='<?php echo json_encode($api_usage); ?>'></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="aiacg-recent-activity">
        <h2><?php _e('Recent Posts Generated', 'ai-auto-content-generator'); ?></h2>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php _e('Title', 'ai-auto-content-generator'); ?></th>
                    <th><?php _e('API', 'ai-auto-content-generator'); ?></th>
                    <th><?php _e('Words', 'ai-auto-content-generator'); ?></th>
                    <th><?php _e('Cost', 'ai-auto-content-generator'); ?></th>
                    <th><?php _e('Quality Score', 'ai-auto-content-generator'); ?></th>
                    <th><?php _e('Time', 'ai-auto-content-generator'); ?></th>
                    <th><?php _e('Status', 'ai-auto-content-generator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent_posts)): ?>
                    <?php foreach ($recent_posts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post->post_id): ?>
                                    <a href="<?php echo get_edit_post_link($post->post_id); ?>" target="_blank">
                                        <?php echo esc_html($post->generated_title); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo esc_html($post->generated_title); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html(strtoupper($post->api_used)); ?></td>
                            <td><?php echo esc_html(number_format($post->word_count)); ?></td>
                            <td>$<?php echo esc_html(number_format($post->cost_estimate, 4)); ?></td>
                            <td>
                                <?php
                                $quality_score = isset($post->quality_score) ? $post->quality_score : 0;
                                $score_class = $quality_score >= 80 ? 'score-high' : ($quality_score >= 60 ? 'score-medium' : 'score-low');
                                ?>
                                <span class="quality-badge <?php echo $score_class; ?>">
                                    <?php echo esc_html(number_format($quality_score)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(human_time_diff(strtotime($post->generation_time), current_time('timestamp'))); ?> <?php _e('ago', 'ai-auto-content-generator'); ?></td>
                            <td>
                                <?php if ($post->status === 'completed'): ?>
                                    <span class="status-success">✓ <?php _e('Success', 'ai-auto-content-generator'); ?></span>
                                <?php else: ?>
                                    <span class="status-failed">✗ <?php _e('Failed', 'ai-auto-content-generator'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            <?php _e('No posts generated yet. Click "Generate 1 Post Now" to get started!', 'ai-auto-content-generator'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Quick Actions -->
    <div class="aiacg-quick-actions">
        <h2><?php _e('Quick Actions', 'ai-auto-content-generator'); ?></h2>
        <div class="action-buttons">
            <button type="button" class="button button-primary button-large aiacg-generate-now" data-count="1">
                📝 <?php _e('Generate 1 Post', 'ai-auto-content-generator'); ?>
            </button>
            <button type="button" class="button button-secondary button-large" onclick="location.href='?page=aiacg-settings&tab=api'">
                ⚙️ <?php _e('Configure APIs', 'ai-auto-content-generator'); ?>
            </button>
            <button type="button" class="button button-secondary button-large" onclick="location.href='?page=aiacg-settings&tab=history'">
                📊 <?php _e('View Full History', 'ai-auto-content-generator'); ?>
            </button>
            <button type="button" class="button button-secondary button-large" onclick="location.href='<?php echo admin_url('edit.php?post_status=draft&post_type=post'); ?>'">
                📄 <?php _e('View Drafts', 'ai-auto-content-generator'); ?>
            </button>
        </div>
    </div>
</div>
