<?php
/**
 * 定时任务管理类
 *
 * @package AI_Auto_Content_Generator
 * @since 1.1.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Cron_Manager {

    /**
     * 获取定时任务状态
     */
    public static function get_cron_status() {
        $next_run = wp_next_scheduled('aiacg_daily_generation');
        $is_enabled = (bool) $next_run;

        $generation_time = get_option('aiacg_generation_time', '08:00');
        list($hour, $minute) = explode(':', $generation_time);

        return array(
            'enabled' => $is_enabled,
            'next_run' => $next_run,
            'next_run_formatted' => $next_run ? date('Y-m-d H:i:s', $next_run) : '',
            'next_run_relative' => $next_run ? human_time_diff($next_run, current_time('timestamp')) : '',
            'scheduled_time' => $generation_time,
            'is_past_due' => $next_run && $next_run < current_time('timestamp'),
            'last_execution' => get_option('aiacg_last_generation_time', ''),
        );
    }

    /**
     * 启用定时任务
     */
    public static function enable_cron() {
        $generation_time = get_option('aiacg_generation_time', '08:00');
        list($hour, $minute) = explode(':', $generation_time);

        // 清除现有的定时任务
        wp_clear_scheduled_hook('aiacg_daily_generation');

        // 计算下次运行时间
        $current_time = current_time('timestamp');
        $scheduled_time = strtotime('today ' . $generation_time, $current_time);

        // 如果今天的时间已过，则安排到明天
        if ($scheduled_time <= $current_time) {
            $scheduled_time = strtotime('tomorrow ' . $generation_time, $current_time);
        }

        // 安排定时任务
        $scheduled = wp_schedule_event($scheduled_time, 'daily', 'aiacg_daily_generation');

        if ($scheduled !== false) {
            AIACG_Database::log('Cron job enabled. Next run: ' . date('Y-m-d H:i:s', $scheduled_time), 'info');
            return array(
                'success' => true,
                'message' => __('Automatic generation enabled', 'ai-auto-content-generator'),
                'next_run' => $scheduled_time,
            );
        } else {
            AIACG_Database::log('Failed to enable cron job', 'error');
            return array(
                'success' => false,
                'message' => __('Failed to enable automatic generation', 'ai-auto-content-generator'),
            );
        }
    }

    /**
     * 禁用定时任务
     */
    public static function disable_cron() {
        $cleared = wp_clear_scheduled_hook('aiacg_daily_generation');

        if ($cleared !== false) {
            AIACG_Database::log('Cron job disabled', 'info');
            return array(
                'success' => true,
                'message' => __('Automatic generation disabled', 'ai-auto-content-generator'),
            );
        } else {
            return array(
                'success' => false,
                'message' => __('Failed to disable automatic generation', 'ai-auto-content-generator'),
            );
        }
    }

    /**
     * 重新安排定时任务（时间更改后）
     */
    public static function reschedule_cron() {
        // 先禁用
        self::disable_cron();

        // 再启用（会使用新时间）
        return self::enable_cron();
    }

    /**
     * 检查定时任务健康状态
     */
    public static function health_check() {
        $status = self::get_cron_status();
        $issues = array();
        $warnings = array();

        // 检查是否启用
        if (!$status['enabled']) {
            $warnings[] = __('Automatic generation is disabled', 'ai-auto-content-generator');
        }

        // 检查是否过期
        if ($status['is_past_due']) {
            $issues[] = __('Scheduled task is past due. WordPress cron may not be running.', 'ai-auto-content-generator');
        }

        // 检查WordPress cron是否被禁用
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            $issues[] = __('WordPress cron is disabled (DISABLE_WP_CRON = true). You need to set up system cron.', 'ai-auto-content-generator');
        }

        // 检查最后执行时间
        if ($status['last_execution']) {
            $last_exec_time = strtotime($status['last_execution']);
            $hours_since = (current_time('timestamp') - $last_exec_time) / 3600;

            if ($hours_since > 48) {
                $warnings[] = sprintf(
                    __('Last execution was %s hours ago. Consider checking cron status.', 'ai-auto-content-generator'),
                    round($hours_since)
                );
            }
        }

        $health_status = 'good';
        if (!empty($issues)) {
            $health_status = 'critical';
        } elseif (!empty($warnings)) {
            $health_status = 'warning';
        }

        return array(
            'status' => $health_status,
            'issues' => $issues,
            'warnings' => $warnings,
            'cron_status' => $status,
        );
    }

    /**
     * 获取所有WordPress定时任务（用于调试）
     */
    public static function get_all_cron_jobs() {
        $crons = _get_cron_array();
        $events = array();

        if (empty($crons)) {
            return $events;
        }

        foreach ($crons as $timestamp => $cron) {
            foreach ($cron as $hook => $dings) {
                foreach ($dings as $sig => $data) {
                    $events[] = array(
                        'hook' => $hook,
                        'timestamp' => $timestamp,
                        'formatted_time' => date('Y-m-d H:i:s', $timestamp),
                        'schedule' => isset($data['schedule']) ? $data['schedule'] : 'single',
                        'args' => isset($data['args']) ? $data['args'] : array(),
                    );
                }
            }
        }

        return $events;
    }

    /**
     * 手动触发定时任务
     */
    public static function manual_trigger() {
        AIACG_Database::log('Manual cron trigger initiated', 'info');

        // 执行定时任务
        do_action('aiacg_daily_generation');

        return array(
            'success' => true,
            'message' => __('Generation task triggered manually', 'ai-auto-content-generator'),
        );
    }
}
