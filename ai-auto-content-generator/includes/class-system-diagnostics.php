<?php
/**
 * 系统诊断类
 *
 * @package AI_Auto_Content_Generator
 * @since 1.1.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_System_Diagnostics {

    /**
     * 运行完整的系统诊断
     */
    public static function run_full_diagnostic() {
        return array(
            'wordpress' => self::check_wordpress_environment(),
            'php' => self::check_php_environment(),
            'database' => self::check_database(),
            'plugin' => self::check_plugin_configuration(),
            'cron' => self::check_cron_system(),
            'api' => self::check_api_configuration(),
            'permissions' => self::check_file_permissions(),
            'performance' => self::check_performance(),
        );
    }

    /**
     * 检查WordPress环境
     */
    private static function check_wordpress_environment() {
        global $wp_version;

        $issues = array();
        $warnings = array();

        // WordPress版本检查
        if (version_compare($wp_version, '5.0', '<')) {
            $issues[] = sprintf(__('WordPress version %s is too old. Minimum required: 5.0', 'ai-auto-content-generator'), $wp_version);
        }

        // 调试模式检查
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $warnings[] = __('WP_DEBUG is enabled. Consider disabling in production.', 'ai-auto-content-generator');
        }

        // HTTPS检查
        if (!is_ssl()) {
            $warnings[] = __('Site is not using HTTPS. Recommended for API security.', 'ai-auto-content-generator');
        }

        // 内存限制检查
        $memory_limit = wp_convert_hr_to_bytes(WP_MEMORY_LIMIT);
        if ($memory_limit < 128 * 1024 * 1024) { // Less than 128MB
            $warnings[] = sprintf(__('WordPress memory limit is low: %s. Recommended: 256M or higher.', 'ai-auto-content-generator'), WP_MEMORY_LIMIT);
        }

        return array(
            'status' => empty($issues) ? (empty($warnings) ? 'good' : 'warning') : 'critical',
            'version' => $wp_version,
            'memory_limit' => WP_MEMORY_LIMIT,
            'is_ssl' => is_ssl(),
            'debug_mode' => defined('WP_DEBUG') && WP_DEBUG,
            'issues' => $issues,
            'warnings' => $warnings,
        );
    }

    /**
     * 检查PHP环境
     */
    private static function check_php_environment() {
        $issues = array();
        $warnings = array();

        // PHP版本检查
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $issues[] = sprintf(__('PHP version %s is too old. Minimum required: 7.4', 'ai-auto-content-generator'), PHP_VERSION);
        } elseif (version_compare(PHP_VERSION, '8.0', '<')) {
            $warnings[] = sprintf(__('PHP version %s. Consider upgrading to PHP 8.0+', 'ai-auto-content-generator'), PHP_VERSION);
        }

        // 必需扩展检查
        $required_extensions = array('curl', 'json', 'mbstring');
        foreach ($required_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $issues[] = sprintf(__('Required PHP extension missing: %s', 'ai-auto-content-generator'), $ext);
            }
        }

        // 推荐扩展检查
        $recommended_extensions = array('gd', 'imagick');
        foreach ($recommended_extensions as $ext) {
            if (!extension_loaded($ext)) {
                $warnings[] = sprintf(__('Recommended PHP extension missing: %s (for featured images)', 'ai-auto-content-generator'), $ext);
            }
        }

        // 内存限制检查
        $memory_limit = ini_get('memory_limit');
        $memory_bytes = wp_convert_hr_to_bytes($memory_limit);
        if ($memory_bytes < 256 * 1024 * 1024) {
            $warnings[] = sprintf(__('PHP memory limit is low: %s. Recommended: 256M or higher.', 'ai-auto-content-generator'), $memory_limit);
        }

        // 最大执行时间检查
        $max_execution_time = ini_get('max_execution_time');
        if ($max_execution_time > 0 && $max_execution_time < 60) {
            $warnings[] = sprintf(__('max_execution_time is low: %s seconds. May cause timeouts.', 'ai-auto-content-generator'), $max_execution_time);
        }

        return array(
            'status' => empty($issues) ? (empty($warnings) ? 'good' : 'warning') : 'critical',
            'version' => PHP_VERSION,
            'memory_limit' => $memory_limit,
            'max_execution_time' => $max_execution_time,
            'extensions' => get_loaded_extensions(),
            'issues' => $issues,
            'warnings' => $warnings,
        );
    }

    /**
     * 检查数据库
     */
    private static function check_database() {
        global $wpdb;

        $issues = array();
        $warnings = array();

        // 检查表是否存在
        $table_name = $wpdb->prefix . AIACG_Database::TABLE_NAME;
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

        if (!$table_exists) {
            $issues[] = __('Plugin database table is missing. Please deactivate and reactivate the plugin.', 'ai-auto-content-generator');
        } else {
            // 检查表大小
            $table_size = $wpdb->get_var("SELECT
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = '{$wpdb->dbname}'
                AND TABLE_NAME = '{$table_name}'");

            if ($table_size > 100) {
                $warnings[] = sprintf(__('Database table is large (%.2f MB). Consider cleanup.', 'ai-auto-content-generator'), $table_size);
            }

            // 检查记录数
            $record_count = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
            if ($record_count > 10000) {
                $warnings[] = sprintf(__('Large number of history records (%d). Consider cleanup.', 'ai-auto-content-generator'), $record_count);
            }
        }

        // 数据库版本检查
        $db_version = $wpdb->db_version();
        if (version_compare($db_version, '5.6', '<')) {
            $warnings[] = sprintf(__('MySQL version %s. Recommend 5.7 or higher.', 'ai-auto-content-generator'), $db_version);
        }

        return array(
            'status' => empty($issues) ? (empty($warnings) ? 'good' : 'warning') : 'critical',
            'table_exists' => $table_exists,
            'db_version' => $db_version,
            'table_size_mb' => $table_size ?? 0,
            'record_count' => $record_count ?? 0,
            'issues' => $issues,
            'warnings' => $warnings,
        );
    }

    /**
     * 检查插件配置
     */
    private static function check_plugin_configuration() {
        $issues = array();
        $warnings = array();

        // 检查是否配置了主题
        if (!get_option('aiacg_main_topic')) {
            $issues[] = __('Main topic not configured', 'ai-auto-content-generator');
        }

        // 检查是否配置了至少一个API
        $has_api = false;
        if (get_option('aiacg_gemini_api_key')) $has_api = true;
        if (get_option('aiacg_deepseek_api_key')) $has_api = true;
        if (get_option('aiacg_openai_api_key')) $has_api = true;

        if (!$has_api) {
            $issues[] = __('No API keys configured. At least one API is required.', 'ai-auto-content-generator');
        }

        // 检查日常发布数量
        $daily_count = get_option('aiacg_daily_post_count', 1);
        if ($daily_count > 20) {
            $warnings[] = sprintf(__('Daily post count is very high (%d). May cause performance issues.', 'ai-auto-content-generator'), $daily_count);
        }

        // 检查字数设置
        $word_count = get_option('aiacg_word_count', 800);
        if ($word_count > 3000) {
            $warnings[] = sprintf(__('Word count is very high (%d). May increase API costs.', 'ai-auto-content-generator'), $word_count);
        }

        // 检查预算设置
        $has_budget = get_option('aiacg_daily_budget_limit', 0) > 0 || get_option('aiacg_monthly_budget_limit', 0) > 0;
        if (!$has_budget) {
            $warnings[] = __('No budget limits set. Recommend setting limits to control costs.', 'ai-auto-content-generator');
        }

        return array(
            'status' => empty($issues) ? (empty($warnings) ? 'good' : 'warning') : 'critical',
            'has_topic' => (bool) get_option('aiacg_main_topic'),
            'has_api' => $has_api,
            'has_budget' => $has_budget,
            'daily_count' => $daily_count,
            'word_count' => $word_count,
            'issues' => $issues,
            'warnings' => $warnings,
        );
    }

    /**
     * 检查定时任务系统
     */
    private static function check_cron_system() {
        $cron_status = AIACG_Cron_Manager::get_cron_status();
        $cron_health = AIACG_Cron_Manager::health_check();

        return array(
            'status' => $cron_health['status'],
            'enabled' => $cron_status['enabled'],
            'next_run' => $cron_status['next_run_formatted'],
            'issues' => $cron_health['issues'],
            'warnings' => $cron_health['warnings'],
        );
    }

    /**
     * 检查API配置
     */
    private static function check_api_configuration() {
        $api_health = AIACG_API_Health_Monitor::get_health_summary();

        return array(
            'status' => $api_health['overall_status'],
            'healthy_count' => $api_health['healthy_count'],
            'total_count' => $api_health['total_count'],
            'api_results' => $api_health['api_results'],
            'issues' => array(),
            'warnings' => array(),
        );
    }

    /**
     * 检查文件权限
     */
    private static function check_file_permissions() {
        $issues = array();
        $warnings = array();

        // 检查上传目录
        $upload_dir = wp_upload_dir();
        if (!wp_is_writable($upload_dir['basedir'])) {
            $issues[] = sprintf(__('Upload directory not writable: %s', 'ai-auto-content-generator'), $upload_dir['basedir']);
        }

        // 检查插件目录（用于日志等）
        if (!is_writable(AIACG_PLUGIN_DIR)) {
            $warnings[] = __('Plugin directory not writable. Logging may not work.', 'ai-auto-content-generator');
        }

        return array(
            'status' => empty($issues) ? (empty($warnings) ? 'good' : 'warning') : 'critical',
            'upload_dir_writable' => wp_is_writable($upload_dir['basedir']),
            'plugin_dir_writable' => is_writable(AIACG_PLUGIN_DIR),
            'issues' => $issues,
            'warnings' => $warnings,
        );
    }

    /**
     * 检查性能指标
     */
    private static function check_performance() {
        global $wpdb;

        $warnings = array();

        // 获取平均生成时间
        $table_name = $wpdb->prefix . AIACG_Database::TABLE_NAME;
        $avg_time = $wpdb->get_var("SELECT AVG(TIMESTAMPDIFF(SECOND, generation_time, generation_time)) FROM {$table_name} WHERE status = 'completed' LIMIT 100");

        // 获取成功率
        $statistics = AIACG_Database::get_statistics();
        if ($statistics['success_rate'] < 80) {
            $warnings[] = sprintf(__('Success rate is low: %.1f%%. Check API configuration and logs.', 'ai-auto-content-generator'), $statistics['success_rate']);
        }

        // 获取平均质量分数
        $avg_quality = $wpdb->get_var("SELECT AVG(quality_score) FROM {$table_name} WHERE quality_score > 0");
        if ($avg_quality && $avg_quality < 60) {
            $warnings[] = sprintf(__('Average quality score is low: %.1f. Review content templates.', 'ai-auto-content-generator'), $avg_quality);
        }

        return array(
            'status' => empty($warnings) ? 'good' : 'warning',
            'avg_generation_time' => $avg_time,
            'success_rate' => $statistics['success_rate'],
            'avg_quality_score' => $avg_quality,
            'total_generated' => $statistics['total_generated'],
            'issues' => array(),
            'warnings' => $warnings,
        );
    }

    /**
     * 获取诊断摘要
     */
    public static function get_diagnostic_summary() {
        $full_diagnostic = self::run_full_diagnostic();

        $total_issues = 0;
        $total_warnings = 0;
        $critical_sections = array();

        foreach ($full_diagnostic as $section => $data) {
            $total_issues += count($data['issues']);
            $total_warnings += count($data['warnings']);

            if ($data['status'] === 'critical') {
                $critical_sections[] = $section;
            }
        }

        $overall_status = 'good';
        if ($total_issues > 0) {
            $overall_status = 'critical';
        } elseif ($total_warnings > 0) {
            $overall_status = 'warning';
        }

        return array(
            'overall_status' => $overall_status,
            'total_issues' => $total_issues,
            'total_warnings' => $total_warnings,
            'critical_sections' => $critical_sections,
            'timestamp' => current_time('mysql'),
        );
    }

    /**
     * 生成诊断报告（HTML格式）
     */
    public static function generate_html_report() {
        $diagnostic = self::run_full_diagnostic();
        $summary = self::get_diagnostic_summary();

        ob_start();
        ?>
        <div class="aiacg-diagnostic-report">
            <h2><?php _e('System Diagnostic Report', 'ai-auto-content-generator'); ?></h2>
            <p class="report-timestamp"><?php printf(__('Generated: %s', 'ai-auto-content-generator'), $summary['timestamp']); ?></p>

            <div class="diagnostic-summary status-<?php echo esc_attr($summary['overall_status']); ?>">
                <h3><?php _e('Overall Status:', 'ai-auto-content-generator'); ?>
                    <?php
                    if ($summary['overall_status'] === 'good') {
                        echo '✓ ' . __('Healthy', 'ai-auto-content-generator');
                    } elseif ($summary['overall_status'] === 'warning') {
                        echo '⚠ ' . __('Warnings Detected', 'ai-auto-content-generator');
                    } else {
                        echo '✗ ' . __('Issues Detected', 'ai-auto-content-generator');
                    }
                    ?>
                </h3>
                <p>
                    <?php printf(__('Issues: %d | Warnings: %d', 'ai-auto-content-generator'), $summary['total_issues'], $summary['total_warnings']); ?>
                </p>
            </div>

            <?php foreach ($diagnostic as $section => $data): ?>
                <div class="diagnostic-section status-<?php echo esc_attr($data['status']); ?>">
                    <h3><?php echo esc_html(ucfirst($section)); ?>
                        <span class="status-badge"><?php echo esc_html($data['status']); ?></span>
                    </h3>

                    <?php if (!empty($data['issues'])): ?>
                        <div class="issues">
                            <h4><?php _e('Issues:', 'ai-auto-content-generator'); ?></h4>
                            <ul>
                                <?php foreach ($data['issues'] as $issue): ?>
                                    <li class="issue"><?php echo esc_html($issue); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['warnings'])): ?>
                        <div class="warnings">
                            <h4><?php _e('Warnings:', 'ai-auto-content-generator'); ?></h4>
                            <ul>
                                <?php foreach ($data['warnings'] as $warning): ?>
                                    <li class="warning"><?php echo esc_html($warning); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
