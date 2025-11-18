<?php
/**
 * 预算管理类
 *
 * 管理API使用成本和预算限制
 *
 * @package AI_Auto_Content_Generator
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Budget_Manager {

    /**
     * 检查是否可以生成（是否在预算内）
     *
     * @return array {
     *     @type bool $allowed 是否允许
     *     @type string $reason 不允许的原因
     * }
     */
    public static function can_generate() {
        $daily_limit = get_option('aiacg_daily_budget_limit', 0);
        $monthly_limit = get_option('aiacg_monthly_budget_limit', 0);

        // 如果未设置预算限制，总是允许
        if ($daily_limit <= 0 && $monthly_limit <= 0) {
            return array(
                'allowed' => true,
                'reason' => '',
            );
        }

        $status = self::get_budget_status();

        // 检查每日预算
        if ($daily_limit > 0 && $status['daily_used'] >= $daily_limit) {
            return array(
                'allowed' => false,
                'reason' => sprintf(
                    __('Daily budget limit reached ($%s / $%s)', 'ai-auto-content-generator'),
                    number_format($status['daily_used'], 2),
                    number_format($daily_limit, 2)
                ),
            );
        }

        // 检查每月预算
        if ($monthly_limit > 0 && $status['monthly_used'] >= $monthly_limit) {
            return array(
                'allowed' => false,
                'reason' => sprintf(
                    __('Monthly budget limit reached ($%s / $%s)', 'ai-auto-content-generator'),
                    number_format($status['monthly_used'], 2),
                    number_format($monthly_limit, 2)
                ),
            );
        }

        return array(
            'allowed' => true,
            'reason' => '',
        );
    }

    /**
     * 获取预算状态
     *
     * @return array
     */
    public static function get_budget_status() {
        $daily_limit = get_option('aiacg_daily_budget_limit', 0);
        $monthly_limit = get_option('aiacg_monthly_budget_limit', 0);

        $daily_used = self::get_period_cost('today');
        $monthly_used = self::get_period_cost('month');

        $daily_percentage = $daily_limit > 0 ? min(($daily_used / $daily_limit) * 100, 100) : 0;
        $monthly_percentage = $monthly_limit > 0 ? min(($monthly_used / $monthly_limit) * 100, 100) : 0;

        return array(
            'daily_limit' => $daily_limit,
            'daily_used' => $daily_used,
            'daily_remaining' => max($daily_limit - $daily_used, 0),
            'daily_percentage' => $daily_percentage,
            'monthly_limit' => $monthly_limit,
            'monthly_used' => $monthly_used,
            'monthly_remaining' => max($monthly_limit - $monthly_used, 0),
            'monthly_percentage' => $monthly_percentage,
        );
    }

    /**
     * 获取指定时间段的成本
     *
     * @param string $period 时间段 ('today', 'month', 'week')
     * @return float
     */
    public static function get_period_cost($period = 'today') {
        global $wpdb;
        $table_name = $wpdb->prefix . AIACG_Database::TABLE_NAME;

        switch ($period) {
            case 'today':
                $date_from = date('Y-m-d 00:00:00');
                $date_to = date('Y-m-d 23:59:59');
                break;

            case 'month':
                $date_from = date('Y-m-01 00:00:00');
                $date_to = date('Y-m-t 23:59:59');
                break;

            case 'week':
                $date_from = date('Y-m-d 00:00:00', strtotime('monday this week'));
                $date_to = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                break;

            default:
                return 0;
        }

        $cost = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(cost_estimate) FROM {$table_name}
            WHERE generation_time >= %s AND generation_time <= %s AND status = 'completed'",
            $date_from,
            $date_to
        ));

        return floatval($cost);
    }

    /**
     * 发送预算警告邮件
     *
     * @param string $type 预算类型 ('daily' 或 'monthly')
     * @param array $status 预算状态
     */
    public static function send_budget_warning($type, $status) {
        if (!get_option('aiacg_email_on_budget_warning', false)) {
            return;
        }

        $threshold = get_option('aiacg_budget_warning_threshold', 80);
        $percentage = $type === 'daily' ? $status['daily_percentage'] : $status['monthly_percentage'];

        if ($percentage < $threshold) {
            return;
        }

        // 检查是否已发送过警告（避免重复发送）
        $last_warning = get_option('aiacg_last_budget_warning_' . $type, '');
        $today = date('Y-m-d');

        if ($last_warning === $today) {
            return;
        }

        $admin_email = get_option('admin_email');
        $site_name = get_option('blogname');

        $period_name = $type === 'daily' ? __('Daily', 'ai-auto-content-generator') : __('Monthly', 'ai-auto-content-generator');
        $subject = "[{$site_name}] " . sprintf(__('%s Budget Warning', 'ai-auto-content-generator'), $period_name);

        $used = $type === 'daily' ? $status['daily_used'] : $status['monthly_used'];
        $limit = $type === 'daily' ? $status['daily_limit'] : $status['monthly_limit'];

        $body = sprintf(__('%s budget warning:', 'ai-auto-content-generator'), $period_name) . "\n\n";
        $body .= sprintf(__('Used: $%s', 'ai-auto-content-generator'), number_format($used, 2)) . "\n";
        $body .= sprintf(__('Limit: $%s', 'ai-auto-content-generator'), number_format($limit, 2)) . "\n";
        $body .= sprintf(__('Percentage: %s%%', 'ai-auto-content-generator'), number_format($percentage, 1)) . "\n\n";
        $body .= __('Content generation may stop when the budget limit is reached.', 'ai-auto-content-generator') . "\n\n";
        $body .= __('Dashboard:', 'ai-auto-content-generator') . ' ' . admin_url('admin.php?page=aiacg-settings');

        wp_mail($admin_email, $subject, $body);

        update_option('aiacg_last_budget_warning_' . $type, $today);
    }

    /**
     * 检查预算并发送警告
     */
    public static function check_and_warn() {
        $status = self::get_budget_status();

        self::send_budget_warning('daily', $status);
        self::send_budget_warning('monthly', $status);
    }
}
