<?php
/**
 * 数据库管理类
 *
 * 负责创建和管理插件所需的数据库表
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Database {

    /**
     * 数据库表名
     */
    const TABLE_NAME = 'aiacg_content_history';

    /**
     * 创建数据库表
     */
    public static function create_tables() {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED DEFAULT NULL,
            topic VARCHAR(255) NOT NULL,
            generated_title VARCHAR(500) NOT NULL,
            prompt_used TEXT,
            api_used VARCHAR(50) NOT NULL,
            tokens_used INT DEFAULT 0,
            generation_time DATETIME NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            error_message TEXT,
            word_count INT DEFAULT 0,
            writing_angle VARCHAR(100),
            similarity_score FLOAT DEFAULT 0,
            cost_estimate DECIMAL(10,6) DEFAULT 0,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY status (status),
            KEY generation_time (generation_time),
            KEY api_used (api_used)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * 插入生成历史记录
     *
     * @param array $data 记录数据
     * @return int|false 插入的记录ID或false
     */
    public static function insert_history($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $defaults = array(
            'post_id' => null,
            'topic' => '',
            'generated_title' => '',
            'prompt_used' => '',
            'api_used' => '',
            'tokens_used' => 0,
            'generation_time' => current_time('mysql'),
            'status' => 'pending',
            'error_message' => '',
            'word_count' => 0,
            'writing_angle' => '',
            'similarity_score' => 0,
            'cost_estimate' => 0,
        );

        $data = wp_parse_args($data, $defaults);

        $result = $wpdb->insert(
            $table_name,
            $data,
            array(
                '%d', // post_id
                '%s', // topic
                '%s', // generated_title
                '%s', // prompt_used
                '%s', // api_used
                '%d', // tokens_used
                '%s', // generation_time
                '%s', // status
                '%s', // error_message
                '%d', // word_count
                '%s', // writing_angle
                '%f', // similarity_score
                '%f', // cost_estimate
            )
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * 更新历史记录
     *
     * @param int $id 记录ID
     * @param array $data 更新数据
     * @return bool
     */
    public static function update_history($id, $data) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        return $wpdb->update(
            $table_name,
            $data,
            array('id' => $id),
            null,
            array('%d')
        );
    }

    /**
     * 获取历史记录
     *
     * @param array $args 查询参数
     * @return array
     */
    public static function get_history($args = array()) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $defaults = array(
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'generation_time',
            'order' => 'DESC',
            'status' => '',
            'api_used' => '',
            'date_from' => '',
            'date_to' => '',
        );

        $args = wp_parse_args($args, $defaults);

        $where = array('1=1');
        $where_values = array();

        if (!empty($args['status'])) {
            $where[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        if (!empty($args['api_used'])) {
            $where[] = 'api_used = %s';
            $where_values[] = $args['api_used'];
        }

        if (!empty($args['date_from'])) {
            $where[] = 'generation_time >= %s';
            $where_values[] = $args['date_from'];
        }

        if (!empty($args['date_to'])) {
            $where[] = 'generation_time <= %s';
            $where_values[] = $args['date_to'];
        }

        $where_clause = implode(' AND ', $where);

        $sql = "SELECT * FROM {$table_name} WHERE {$where_clause}";

        if (!empty($where_values)) {
            $sql = $wpdb->prepare($sql, $where_values);
        }

        $sql .= $wpdb->prepare(
            " ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d",
            $args['limit'],
            $args['offset']
        );

        return $wpdb->get_results($sql);
    }

    /**
     * 获取统计信息
     *
     * @return array
     */
    public static function get_statistics() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $stats = array(
            'total_generated' => 0,
            'this_month' => 0,
            'total_api_calls' => 0,
            'total_cost' => 0,
            'success_rate' => 0,
            'by_api' => array(),
            'by_status' => array(),
        );

        // 总生成数
        $stats['total_generated'] = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");

        // 本月生成数
        $stats['this_month'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE MONTH(generation_time) = %d AND YEAR(generation_time) = %d",
            date('n'),
            date('Y')
        ));

        // API调用统计
        $api_stats = $wpdb->get_results(
            "SELECT api_used, COUNT(*) as count, SUM(cost_estimate) as cost FROM {$table_name} GROUP BY api_used"
        );

        foreach ($api_stats as $stat) {
            $stats['by_api'][$stat->api_used] = array(
                'count' => $stat->count,
                'cost' => $stat->cost,
            );
            $stats['total_api_calls'] += $stat->count;
            $stats['total_cost'] += $stat->cost;
        }

        // 状态统计
        $status_stats = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM {$table_name} GROUP BY status"
        );

        foreach ($status_stats as $stat) {
            $stats['by_status'][$stat->status] = $stat->count;
        }

        // 成功率
        $success_count = isset($stats['by_status']['success']) ? $stats['by_status']['success'] : 0;
        if ($stats['total_generated'] > 0) {
            $stats['success_rate'] = ($success_count / $stats['total_generated']) * 100;
        }

        return $stats;
    }

    /**
     * 获取所有历史标题（用于防重复检查）
     *
     * @param int $limit 限制数量（默认获取最近500条）
     * @return array
     */
    public static function get_all_titles($limit = 500) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT generated_title FROM {$table_name} ORDER BY generation_time DESC LIMIT %d",
            $limit
        ));

        $titles = array();
        foreach ($results as $result) {
            $titles[] = $result->generated_title;
        }

        return $titles;
    }

    /**
     * 删除历史记录
     *
     * @param int $id 记录ID
     * @return bool
     */
    public static function delete_history($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * 清理旧记录
     *
     * @param int $days 保留最近多少天的记录
     * @return int 删除的记录数
     */
    public static function cleanup_old_records($days = 90) {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table_name} WHERE generation_time < %s",
            $date
        ));
    }

    /**
     * 记录日志
     *
     * @param string $message 日志消息
     * @param string $level 日志级别 (info, warning, error)
     * @param array $context 上下文信息
     */
    public static function log($message, $level = 'info', $context = array()) {
        if (!get_option('aiacg_enable_logging', true)) {
            return;
        }

        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        );

        $logs = get_option('aiacg_logs', array());

        // 只保留最近100条日志
        if (count($logs) >= 100) {
            array_shift($logs);
        }

        $logs[] = $log_entry;
        update_option('aiacg_logs', $logs);

        // 如果是错误级别，可以发送邮件通知
        if ($level === 'error' && get_option('aiacg_email_on_error', false)) {
            $admin_email = get_option('admin_email');
            $subject = '[AI Content Generator] Error: ' . $message;
            $body = "Error occurred at: " . current_time('mysql') . "\n\n";
            $body .= "Message: " . $message . "\n\n";
            $body .= "Context: " . print_r($context, true);

            wp_mail($admin_email, $subject, $body);
        }
    }

    /**
     * 获取日志
     *
     * @param string $level 过滤级别
     * @param int $limit 限制数量
     * @return array
     */
    public static function get_logs($level = '', $limit = 100) {
        $logs = get_option('aiacg_logs', array());

        if (!empty($level)) {
            $logs = array_filter($logs, function($log) use ($level) {
                return $log['level'] === $level;
            });
        }

        // 返回最新的日志
        $logs = array_reverse($logs);

        if ($limit > 0) {
            $logs = array_slice($logs, 0, $limit);
        }

        return $logs;
    }

    /**
     * 清除日志
     */
    public static function clear_logs() {
        delete_option('aiacg_logs');
    }
}
