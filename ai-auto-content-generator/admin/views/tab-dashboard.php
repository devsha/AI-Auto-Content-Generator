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
