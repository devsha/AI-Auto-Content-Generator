<?php
/**
 * 调度器类
 *
 * 负责管理WordPress Cron定时任务
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Scheduler {

    /**
     * 构造函数
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * 初始化钩子
     */
    private function init_hooks() {
        // 添加自定义Cron间隔
        add_filter('cron_schedules', array($this, 'add_cron_intervals'));

        // 注册定时任务执行函数
        add_action('aiacg_daily_generation', array($this, 'run_daily_generation'));

        // 注册清理任务
        add_action('aiacg_cleanup', array($this, 'run_cleanup'));

        // 如果清理任务未设置，设置一个
        if (!wp_next_scheduled('aiacg_cleanup')) {
            wp_schedule_event(time(), 'weekly', 'aiacg_cleanup');
        }
    }

    /**
     * 添加自定义Cron间隔
     *
     * @param array $schedules 现有间隔
     * @return array
     */
    public function add_cron_intervals($schedules) {
        // 每小时
        $schedules['hourly'] = array(
            'interval' => 3600,
            'display' => __('Once Hourly', 'ai-auto-content-generator'),
        );

        // 每12小时
        $schedules['twicedaily'] = array(
            'interval' => 43200,
            'display' => __('Twice Daily', 'ai-auto-content-generator'),
        );

        // 每周
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __('Once Weekly', 'ai-auto-content-generator'),
        );

        return $schedules;
    }

    /**
     * 执行每日生成任务
     */
    public function run_daily_generation() {
        AIACG_Database::log('Daily generation task started', 'info');

        $daily_count = get_option('aiacg_daily_post_count', 3);

        if ($daily_count <= 0) {
            AIACG_Database::log('Daily generation is disabled (count = 0)', 'info');
            return;
        }

        $generator = new AIACG_Content_Generator();

        $args = array(
            'topic' => get_option('aiacg_main_topic', ''),
            'sub_topics' => get_option('aiacg_sub_topics', array()),
            'word_count' => get_option('aiacg_word_count', 1000),
            'writing_style' => get_option('aiacg_writing_style', 'professional'),
            'target_audience' => get_option('aiacg_target_audience', ''),
            'publish_mode' => get_option('aiacg_publish_mode', 'publish'),
            'category' => get_option('aiacg_default_category', 1),
            'auto_tags' => get_option('aiacg_auto_tags', true),
            'seo_optimization' => get_option('aiacg_seo_optimization', true),
        );

        // 验证主题是否已设置
        if (empty($args['topic'])) {
            AIACG_Database::log('Daily generation failed: Main topic not set', 'error');
            $this->send_error_notification('Daily generation failed: Main topic is not configured');
            return;
        }

        // 批量生成文章
        $results = $generator->generate_multiple_posts($daily_count, $args);

        $success_count = count($results['success']);
        $failed_count = count($results['failed']);

        AIACG_Database::log(
            "Daily generation completed: {$success_count} success, {$failed_count} failed",
            $failed_count > 0 ? 'warning' : 'info',
            $results
        );

        // 如果有失败，发送通知
        if ($failed_count > 0 && get_option('aiacg_email_on_error', false)) {
            $this->send_error_notification(
                "Daily generation completed with errors: {$success_count} succeeded, {$failed_count} failed"
            );
        }

        // 更新最后运行时间
        update_option('aiacg_last_generation_time', current_time('mysql'));
        update_option('aiacg_last_generation_result', $results);
    }

    /**
     * 执行清理任务
     */
    public function run_cleanup() {
        AIACG_Database::log('Running cleanup task', 'info');

        // 清理90天前的历史记录
        $retention_days = get_option('aiacg_history_retention_days', 90);
        $deleted = AIACG_Database::cleanup_old_records($retention_days);

        AIACG_Database::log("Cleanup completed: {$deleted} records deleted", 'info');

        // 清理旧日志（只保留最近100条）
        $logs = get_option('aiacg_logs', array());
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
            update_option('aiacg_logs', $logs);
        }
    }

    /**
     * 重新安排定时任务
     *
     * @param string $time 时间（HH:MM格式）
     */
    public function reschedule_daily_generation($time = '02:00') {
        // 清除现有任务
        $timestamp = wp_next_scheduled('aiacg_daily_generation');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'aiacg_daily_generation');
        }

        // 计算下次运行时间
        $time_parts = explode(':', $time);
        $hour = intval($time_parts[0]);
        $minute = isset($time_parts[1]) ? intval($time_parts[1]) : 0;

        $timestamp = strtotime("today {$hour}:{$minute}:00");

        // 如果时间已过，设置为明天
        if ($timestamp < time()) {
            $timestamp = strtotime("tomorrow {$hour}:{$minute}:00");
        }

        // 安排新任务
        wp_schedule_event($timestamp, 'daily', 'aiacg_daily_generation');

        AIACG_Database::log(
            "Daily generation rescheduled to {$time}",
            'info',
            array('next_run' => date('Y-m-d H:i:s', $timestamp))
        );

        return $timestamp;
    }

    /**
     * 手动触发生成任务
     *
     * @param int $count 生成数量
     * @return array
     */
    public function manual_generation($count = 1) {
        AIACG_Database::log("Manual generation triggered for {$count} post(s)", 'info');

        $generator = new AIACG_Content_Generator();

        $args = array(
            'topic' => get_option('aiacg_main_topic', ''),
            'sub_topics' => get_option('aiacg_sub_topics', array()),
            'word_count' => get_option('aiacg_word_count', 1000),
            'writing_style' => get_option('aiacg_writing_style', 'professional'),
            'target_audience' => get_option('aiacg_target_audience', ''),
            'publish_mode' => get_option('aiacg_publish_mode', 'publish'),
            'category' => get_option('aiacg_default_category', 1),
            'auto_tags' => get_option('aiacg_auto_tags', true),
            'seo_optimization' => get_option('aiacg_seo_optimization', true),
        );

        if (empty($args['topic'])) {
            return array(
                'success' => false,
                'message' => __('Main topic is not configured', 'ai-auto-content-generator'),
                'results' => array(),
            );
        }

        if ($count == 1) {
            $result = $generator->generate_post($args);
            return array(
                'success' => $result['success'],
                'message' => $result['success']
                    ? __('Post generated successfully!', 'ai-auto-content-generator')
                    : $result['error'],
                'results' => array($result),
            );
        } else {
            $results = $generator->generate_multiple_posts($count, $args);
            $success_count = count($results['success']);
            $failed_count = count($results['failed']);

            return array(
                'success' => $success_count > 0,
                'message' => sprintf(
                    __('%d posts generated successfully, %d failed', 'ai-auto-content-generator'),
                    $success_count,
                    $failed_count
                ),
                'results' => $results,
            );
        }
    }

    /**
     * 获取下次运行时间
     *
     * @return int|false
     */
    public function get_next_scheduled_time() {
        return wp_next_scheduled('aiacg_daily_generation');
    }

    /**
     * 获取调度状态
     *
     * @return array
     */
    public function get_schedule_status() {
        $next_run = $this->get_next_scheduled_time();
        $last_run = get_option('aiacg_last_generation_time', '');
        $last_result = get_option('aiacg_last_generation_result', array());

        return array(
            'is_scheduled' => $next_run !== false,
            'next_run' => $next_run ? date('Y-m-d H:i:s', $next_run) : '',
            'next_run_relative' => $next_run ? human_time_diff($next_run, time()) : '',
            'last_run' => $last_run,
            'last_result' => $last_result,
        );
    }

    /**
     * 发送错误通知
     *
     * @param string $message 错误消息
     */
    private function send_error_notification($message) {
        $admin_email = get_option('admin_email');
        $site_name = get_option('blogname');

        $subject = "[{$site_name}] AI Content Generator - Error Notification";

        $body = "An error occurred in the AI Auto Content Generator plugin:\n\n";
        $body .= "Time: " . current_time('Y-m-d H:i:s') . "\n";
        $body .= "Message: {$message}\n\n";
        $body .= "Please check the plugin settings and logs for more details.\n";
        $body .= "Dashboard: " . admin_url('admin.php?page=aiacg-settings');

        wp_mail($admin_email, $subject, $body);
    }

    /**
     * 测试Cron功能
     *
     * @return array
     */
    public function test_cron() {
        $result = array(
            'wp_cron_enabled' => !(defined('DISABLE_WP_CRON') && DISABLE_WP_CRON),
            'scheduled' => $this->get_next_scheduled_time() !== false,
            'schedules' => wp_get_schedules(),
            'all_events' => _get_cron_array(),
        );

        return $result;
    }
}
