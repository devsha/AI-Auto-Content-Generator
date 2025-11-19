<?php
/**
 * 历史记录Tab
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$history = AIACG_Database::get_history(array(
    'limit' => $per_page,
    'offset' => $offset,
));

$statistics = AIACG_Database::get_statistics();
?>

<div class="aiacg-history-section">
    <h2><?php _e('Generation History', 'ai-auto-content-generator'); ?></h2>

    <div class="aiacg-statistics-grid">
        <div class="aiacg-stat-box">
            <div class="aiacg-stat-value"><?php echo esc_html($statistics['total_generated']); ?></div>
            <div class="aiacg-stat-label"><?php _e('Total Generated', 'ai-auto-content-generator'); ?></div>
        </div>
        <div class="aiacg-stat-box">
            <div class="aiacg-stat-value"><?php echo esc_html($statistics['this_month']); ?></div>
            <div class="aiacg-stat-label"><?php _e('This Month', 'ai-auto-content-generator'); ?></div>
        </div>
        <div class="aiacg-stat-box">
            <div class="aiacg-stat-value"><?php echo esc_html($statistics['total_api_calls']); ?></div>
            <div class="aiacg-stat-label"><?php _e('API Calls', 'ai-auto-content-generator'); ?></div>
        </div>
        <div class="aiacg-stat-box">
            <div class="aiacg-stat-value">$<?php echo esc_html(number_format($statistics['total_cost'], 4)); ?></div>
            <div class="aiacg-stat-label"><?php _e('Total Cost', 'ai-auto-content-generator'); ?></div>
        </div>
        <div class="aiacg-stat-box">
            <div class="aiacg-stat-value"><?php echo esc_html(number_format($statistics['success_rate'], 1)); ?>%</div>
            <div class="aiacg-stat-label"><?php _e('Success Rate', 'ai-auto-content-generator'); ?></div>
        </div>
        <div class="aiacg-stat-box">
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . AIACG_Database::TABLE_NAME;
            $avg_quality = $wpdb->get_var("SELECT AVG(quality_score) FROM {$table_name} WHERE quality_score > 0");
            $quality_score = $avg_quality ? round($avg_quality) : 0;
            $quality_class = $quality_score >= 80 ? 'aiacg-status-success' : ($quality_score >= 60 ? 'aiacg-status-pending' : 'aiacg-status-error');
            ?>
            <div class="aiacg-stat-value <?php echo $quality_class; ?>"><?php echo esc_html($quality_score); ?></div>
            <div class="aiacg-stat-label"><?php _e('Avg Quality Score', 'ai-auto-content-generator'); ?></div>
        </div>
    </div>

    <?php if (!empty($statistics['by_api'])): ?>
    <h3><?php _e('Usage by API', 'ai-auto-content-generator'); ?></h3>
    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e('API', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Calls', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Cost', 'ai-auto-content-generator'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statistics['by_api'] as $api => $stats): ?>
            <tr>
                <td><strong><?php echo esc_html(ucfirst($api)); ?></strong></td>
                <td><?php echo esc_html($stats['count']); ?></td>
                <td>$<?php echo esc_html(number_format($stats['cost'], 4)); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <h3><?php _e('Recent Generations', 'ai-auto-content-generator'); ?></h3>

    <?php if (!empty($history)): ?>
    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php _e('Date', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Title', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Status', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Quality', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('API', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Words', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Tokens', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Cost', 'ai-auto-content-generator'); ?></th>
                <th><?php _e('Actions', 'ai-auto-content-generator'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($history as $record): ?>
            <tr>
                <td><?php echo esc_html(date('Y-m-d H:i', strtotime($record->generation_time))); ?></td>
                <td>
                    <?php
                    if ($record->post_id && get_post($record->post_id)) {
                        echo '<a href="' . get_edit_post_link($record->post_id) . '">' . esc_html($record->generated_title) . '</a>';
                    } else {
                        echo esc_html($record->generated_title);
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $status_class = '';
                    $status_label = '';
                    switch ($record->status) {
                        case 'completed':
                        case 'success':
                            $status_class = 'aiacg-status-success';
                            $status_label = __('Success', 'ai-auto-content-generator');
                            break;
                        case 'failed':
                            $status_class = 'aiacg-status-error';
                            $status_label = __('Failed', 'ai-auto-content-generator');
                            break;
                        default:
                            $status_class = 'aiacg-status-pending';
                            $status_label = ucfirst($record->status);
                    }
                    echo '<span class="' . esc_attr($status_class) . '">' . esc_html($status_label) . '</span>';
                    ?>
                </td>
                <td>
                    <?php
                    if (isset($record->quality_score) && $record->quality_score > 0) {
                        $quality_score = intval($record->quality_score);
                        $score_class = $quality_score >= 80 ? 'score-high' : ($quality_score >= 60 ? 'score-medium' : 'score-low');
                        $grade = AIACG_Content_Quality::get_quality_grade($quality_score);
                        echo '<span class="quality-badge ' . esc_attr($score_class) . '" title="' . esc_attr($grade) . '">';
                        echo esc_html($quality_score);
                        echo '</span>';
                    } else {
                        echo '<span style="color: #999;">-</span>';
                    }
                    ?>
                </td>
                <td><?php echo esc_html(ucfirst($record->api_used)); ?></td>
                <td><?php echo esc_html($record->word_count); ?></td>
                <td><?php echo esc_html($record->tokens_used); ?></td>
                <td>$<?php echo esc_html(number_format($record->cost_estimate, 6)); ?></td>
                <td>
                    <?php if ($record->post_id && get_post($record->post_id)): ?>
                        <a href="<?php echo get_permalink($record->post_id); ?>" target="_blank"><?php _e('View', 'ai-auto-content-generator'); ?></a> |
                        <a href="<?php echo get_edit_post_link($record->post_id); ?>"><?php _e('Edit', 'ai-auto-content-generator'); ?></a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($record->status === 'failed' && !empty($record->error_message)): ?>
            <tr class="aiacg-error-row">
                <td colspan="9">
                    <strong><?php _e('Error:', 'ai-auto-content-generator'); ?></strong>
                    <?php echo esc_html($record->error_message); ?>
                </td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    // 简单的分页（实际应用中可以使用WordPress的分页函数）
    if ($statistics['total_generated'] > $per_page) {
        $total_pages = ceil($statistics['total_generated'] / $per_page);
        echo '<div class="tablenav"><div class="tablenav-pages">';
        echo paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $total_pages,
            'current' => $page,
        ));
        echo '</div></div>';
    }
    ?>

    <?php else: ?>
    <p><?php _e('No generation history yet. Start generating content to see records here.', 'ai-auto-content-generator'); ?></p>
    <?php endif; ?>
</div>
