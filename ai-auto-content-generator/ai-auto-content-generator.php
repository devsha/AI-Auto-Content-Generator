<?php
/**
 * Plugin Name: AI Auto Content Generator
 * Plugin URI: https://github.com/yourusername/ai-auto-content-generator
 * Description: 基于AI API自动生成和发布文章的WordPress插件，支持Gemini、DeepSeek、OpenAI等多种AI服务
 * Version: 1.0.2
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-auto-content-generator
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// 如果直接访问此文件，则退出
if (!defined('ABSPATH')) {
    exit;
}

// 定义插件常量
define('AIACG_VERSION', '1.0.2');
define('AIACG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AIACG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AIACG_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * 主插件类
 */
class AI_Auto_Content_Generator {

    /**
     * 单例实例
     * @var AI_Auto_Content_Generator
     */
    private static $instance = null;

    /**
     * 获取单例实例
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 构造函数
     */
    private function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * 加载依赖文件
     */
    private function load_dependencies() {
        // 核心类
        require_once AIACG_PLUGIN_DIR . 'includes/class-database.php';
        require_once AIACG_PLUGIN_DIR . 'includes/class-ai-manager.php';
        require_once AIACG_PLUGIN_DIR . 'includes/class-content-generator.php';
        require_once AIACG_PLUGIN_DIR . 'includes/class-scheduler.php';

        // API接口和实现
        require_once AIACG_PLUGIN_DIR . 'includes/api/interface-ai-api.php';
        require_once AIACG_PLUGIN_DIR . 'includes/api/class-gemini-api.php';
        require_once AIACG_PLUGIN_DIR . 'includes/api/class-deepseek-api.php';
        require_once AIACG_PLUGIN_DIR . 'includes/api/class-openai-api.php';

        // 管理后台
        if (is_admin()) {
            require_once AIACG_PLUGIN_DIR . 'admin/class-admin-settings.php';
        }
    }

    /**
     * 设置本地化
     */
    private function set_locale() {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    /**
     * 加载插件文本域
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'ai-auto-content-generator',
            false,
            dirname(AIACG_PLUGIN_BASENAME) . '/languages/'
        );
    }

    /**
     * 定义管理后台钩子
     */
    private function define_admin_hooks() {
        if (is_admin()) {
            $admin = new AIACG_Admin_Settings();
        }
    }

    /**
     * 定义公共钩子
     */
    private function define_public_hooks() {
        // 初始化调度器
        $scheduler = new AIACG_Scheduler();
    }
}

/**
 * 插件激活时的操作
 */
function aiacg_activate() {
    // 创建数据库表
    AIACG_Database::create_tables();

    // 设置默认选项
    $default_options = array(
        'main_topic' => '',
        'topic_description' => '',
        'sub_topics' => array(),
        'writing_style' => 'professional',
        'target_audience' => '',
        'daily_post_count' => 3,
        'word_count' => 1000,
        'generation_time' => '02:00',
        'publish_mode' => 'publish',
        'publish_interval' => 30,
        'default_category' => 1,
        'auto_tags' => true,
        'generate_featured_image' => false,
        'seo_optimization' => true,
        'active_api' => 'gemini',
        'enable_api_rotation' => false,
        'api_priority' => array('gemini', 'deepseek', 'openai'),
        'auto_switch_on_failure' => true,
    );

    foreach ($default_options as $key => $value) {
        if (get_option('aiacg_' . $key) === false) {
            add_option('aiacg_' . $key, $value);
        }
    }

    // 设置定时任务
    if (!wp_next_scheduled('aiacg_daily_generation')) {
        $generation_time = get_option('aiacg_generation_time', '02:00');
        $time_parts = explode(':', $generation_time);
        $timestamp = strtotime("today {$time_parts[0]}:{$time_parts[1]}:00");

        if ($timestamp < time()) {
            $timestamp = strtotime("tomorrow {$time_parts[0]}:{$time_parts[1]}:00");
        }

        wp_schedule_event($timestamp, 'daily', 'aiacg_daily_generation');
    }

    // 设置版本号
    update_option('aiacg_version', AIACG_VERSION);

    // 刷新重写规则
    flush_rewrite_rules();
}

/**
 * 插件停用时的操作
 */
function aiacg_deactivate() {
    // 清除定时任务
    $timestamp = wp_next_scheduled('aiacg_daily_generation');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'aiacg_daily_generation');
    }

    // 清除所有相关的定时任务
    wp_clear_scheduled_hook('aiacg_daily_generation');

    // 刷新重写规则
    flush_rewrite_rules();
}

// 注册激活和停用钩子
// 注意：卸载处理在单独的 uninstall.php 文件中
register_activation_hook(__FILE__, 'aiacg_activate');
register_deactivation_hook(__FILE__, 'aiacg_deactivate');

/**
 * 开始运行插件
 */
function aiacg_run() {
    return AI_Auto_Content_Generator::get_instance();
}

// 启动插件
aiacg_run();
