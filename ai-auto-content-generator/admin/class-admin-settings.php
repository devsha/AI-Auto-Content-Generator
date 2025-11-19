<?php
/**
 * 管理后台设置类
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Admin_Settings {

    /**
     * 构造函数
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_settings_import'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_aiacg_test_api', array($this, 'ajax_test_api'));
        add_action('wp_ajax_aiacg_generate_now', array($this, 'ajax_generate_now'));
        add_action('wp_ajax_aiacg_clear_logs', array($this, 'ajax_clear_logs'));
        add_action('wp_ajax_aiacg_export_settings', array($this, 'ajax_export_settings'));
        add_action('wp_ajax_aiacg_delete_history', array($this, 'ajax_delete_history'));
        add_action('wp_ajax_aiacg_cleanup_records', array($this, 'ajax_cleanup_records'));
        add_action('wp_ajax_aiacg_reset_stats', array($this, 'ajax_reset_stats'));
        add_action('wp_ajax_aiacg_reset_plugin', array($this, 'ajax_reset_plugin'));
        add_action('wp_ajax_aiacg_export_history_csv', array($this, 'ajax_export_history_csv'));
        add_action('wp_ajax_aiacg_reevaluate_quality', array($this, 'ajax_reevaluate_quality'));
        add_filter('plugin_action_links_' . AIACG_PLUGIN_BASENAME, array($this, 'add_plugin_action_links'));
    }

    /**
     * 添加管理菜单
     */
    public function add_admin_menu() {
        add_menu_page(
            __('AI Content Generator', 'ai-auto-content-generator'),
            __('AI Content', 'ai-auto-content-generator'),
            'manage_options',
            'aiacg-settings',
            array($this, 'render_settings_page'),
            'dashicons-edit-large',
            30
        );

        add_submenu_page(
            'aiacg-settings',
            __('Settings', 'ai-auto-content-generator'),
            __('Settings', 'ai-auto-content-generator'),
            'manage_options',
            'aiacg-settings'
        );
    }

    /**
     * 注册设置
     */
    public function register_settings() {
        // 基本设置
        register_setting('aiacg_basic_settings', 'aiacg_main_topic');
        register_setting('aiacg_basic_settings', 'aiacg_topic_description');
        register_setting('aiacg_basic_settings', 'aiacg_sub_topics');
        register_setting('aiacg_basic_settings', 'aiacg_writing_style');
        register_setting('aiacg_basic_settings', 'aiacg_target_audience');
        register_setting('aiacg_basic_settings', 'aiacg_daily_post_count');
        register_setting('aiacg_basic_settings', 'aiacg_word_count');
        register_setting('aiacg_basic_settings', 'aiacg_generation_time');
        register_setting('aiacg_basic_settings', 'aiacg_publish_mode');
        register_setting('aiacg_basic_settings', 'aiacg_publish_interval');
        register_setting('aiacg_basic_settings', 'aiacg_default_category');
        register_setting('aiacg_basic_settings', 'aiacg_auto_tags');
        register_setting('aiacg_basic_settings', 'aiacg_generate_featured_image');
        register_setting('aiacg_basic_settings', 'aiacg_seo_optimization');

        // API设置
        register_setting('aiacg_api_settings', 'aiacg_active_api');
        register_setting('aiacg_api_settings', 'aiacg_enable_api_rotation');
        register_setting('aiacg_api_settings', 'aiacg_api_priority');
        register_setting('aiacg_api_settings', 'aiacg_auto_switch_on_failure');

        // Gemini API
        register_setting('aiacg_api_settings', 'aiacg_gemini_api_key');
        register_setting('aiacg_api_settings', 'aiacg_gemini_model');
        register_setting('aiacg_api_settings', 'aiacg_gemini_temperature');
        register_setting('aiacg_api_settings', 'aiacg_gemini_max_tokens');

        // DeepSeek API
        register_setting('aiacg_api_settings', 'aiacg_deepseek_api_key');
        register_setting('aiacg_api_settings', 'aiacg_deepseek_model');
        register_setting('aiacg_api_settings', 'aiacg_deepseek_temperature');
        register_setting('aiacg_api_settings', 'aiacg_deepseek_max_tokens');

        // OpenAI API
        register_setting('aiacg_api_settings', 'aiacg_openai_api_key');
        register_setting('aiacg_api_settings', 'aiacg_openai_model');
        register_setting('aiacg_api_settings', 'aiacg_openai_temperature');
        register_setting('aiacg_api_settings', 'aiacg_openai_max_tokens');

        // 模板设置
        register_setting('aiacg_template_settings', 'aiacg_system_prompt');
        register_setting('aiacg_template_settings', 'aiacg_user_prompt_template');
        register_setting('aiacg_template_settings', 'aiacg_writing_angles');
        register_setting('aiacg_template_settings', 'aiacg_title_min_length');
        register_setting('aiacg_template_settings', 'aiacg_title_max_length');

        // 其他设置
        register_setting('aiacg_other_settings', 'aiacg_enable_logging');
        register_setting('aiacg_other_settings', 'aiacg_email_on_error');
        register_setting('aiacg_other_settings', 'aiacg_history_retention_days');

        // 预算控制设置
        register_setting('aiacg_budget_settings', 'aiacg_daily_budget_limit');
        register_setting('aiacg_budget_settings', 'aiacg_monthly_budget_limit');
        register_setting('aiacg_budget_settings', 'aiacg_email_on_budget_warning');
        register_setting('aiacg_budget_settings', 'aiacg_budget_warning_threshold');
    }

    /**
     * 加载管理资源
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'aiacg-settings') === false) {
            return;
        }

        wp_enqueue_style(
            'aiacg-admin-css',
            AIACG_PLUGIN_URL . 'admin/assets/css/admin-style.css',
            array(),
            AIACG_VERSION
        );

        wp_enqueue_script(
            'aiacg-admin-js',
            AIACG_PLUGIN_URL . 'admin/assets/js/admin-script.js',
            array('jquery'),
            AIACG_VERSION,
            true
        );

        wp_localize_script('aiacg-admin-js', 'aiacgAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aiacg_admin_nonce'),
            'strings' => array(
                'testing' => __('Testing...', 'ai-auto-content-generator'),
                'generating' => __('Generating...', 'ai-auto-content-generator'),
                'success' => __('Success!', 'ai-auto-content-generator'),
                'error' => __('Error!', 'ai-auto-content-generator'),
                'confirmClear' => __('Are you sure you want to clear all logs?', 'ai-auto-content-generator'),
            ),
        ));
    }

    /**
     * 渲染设置页面
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'ai-auto-content-generator'));
        }

        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';

        include AIACG_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    /**
     * AJAX: 测试API连接
     */
    public function ajax_test_api() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        $api_name = isset($_POST['api']) ? sanitize_text_field($_POST['api']) : '';

        if (empty($api_name)) {
            wp_send_json_error(array('message' => __('API name is required', 'ai-auto-content-generator')));
        }

        $manager = new AIACG_AI_Manager();
        $api = $manager->get_api($api_name);

        if (!$api) {
            wp_send_json_error(array('message' => __('Invalid API', 'ai-auto-content-generator')));
        }

        $result = $api->test_connection();

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * AJAX: 立即生成文章
     */
    public function ajax_generate_now() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        $count = isset($_POST['count']) ? intval($_POST['count']) : 1;
        $count = max(1, min(20, $count)); // 限制1-20篇

        $scheduler = new AIACG_Scheduler();
        $result = $scheduler->manual_generation($count);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * AJAX: 清除日志
     */
    public function ajax_clear_logs() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        AIACG_Database::clear_logs();
        wp_send_json_success(array('message' => __('Logs cleared successfully', 'ai-auto-content-generator')));
    }

    /**
     * 添加插件操作链接
     */
    public function add_plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=aiacg-settings') . '">' . __('Settings', 'ai-auto-content-generator') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * 渲染基本设置Tab
     */
    public static function render_basic_settings_tab() {
        ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('aiacg_basic_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th colspan="2"><h2><?php _e('Topic Settings', 'ai-auto-content-generator'); ?></h2></th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_main_topic"><?php _e('Main Topic', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="aiacg_main_topic" name="aiacg_main_topic"
                               value="<?php echo esc_attr(get_option('aiacg_main_topic', '')); ?>"
                               class="regular-text" required>
                        <p class="description"><?php _e('e.g., Technology News, Health & Wellness, Finance', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_topic_description"><?php _e('Topic Description', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <textarea id="aiacg_topic_description" name="aiacg_topic_description"
                                  rows="3" class="large-text"><?php echo esc_textarea(get_option('aiacg_topic_description', '')); ?></textarea>
                        <p class="description"><?php _e('Describe your topic and content focus', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_sub_topics"><?php _e('Sub Topics / Keywords', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="aiacg_sub_topics" name="aiacg_sub_topics"
                               value="<?php echo esc_attr(implode(', ', get_option('aiacg_sub_topics', array()))); ?>"
                               class="large-text">
                        <p class="description"><?php _e('Comma-separated list of sub-topics or keywords', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_writing_style"><?php _e('Writing Style', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <select id="aiacg_writing_style" name="aiacg_writing_style">
                            <?php
                            $styles = array(
                                'professional' => __('Professional', 'ai-auto-content-generator'),
                                'casual' => __('Casual', 'ai-auto-content-generator'),
                                'formal' => __('Formal', 'ai-auto-content-generator'),
                                'conversational' => __('Conversational', 'ai-auto-content-generator'),
                            );
                            $current_style = get_option('aiacg_writing_style', 'professional');
                            foreach ($styles as $value => $label) {
                                echo '<option value="' . esc_attr($value) . '"' . selected($current_style, $value, false) . '>' . esc_html($label) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_target_audience"><?php _e('Target Audience', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="aiacg_target_audience" name="aiacg_target_audience"
                               value="<?php echo esc_attr(get_option('aiacg_target_audience', '')); ?>"
                               class="regular-text">
                        <p class="description"><?php _e('e.g., Tech professionals, General readers, Students', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th colspan="2"><h2><?php _e('Generation Settings', 'ai-auto-content-generator'); ?></h2></th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_daily_post_count"><?php _e('Daily Post Count', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="aiacg_daily_post_count" name="aiacg_daily_post_count"
                               value="<?php echo esc_attr(get_option('aiacg_daily_post_count', 3)); ?>"
                               min="0" max="20" step="1">
                        <p class="description"><?php _e('Number of posts to generate daily (1-20, 0 to disable)', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_word_count"><?php _e('Word Count', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="range" id="aiacg_word_count" name="aiacg_word_count"
                               value="<?php echo esc_attr(get_option('aiacg_word_count', 1000)); ?>"
                               min="500" max="3000" step="100"
                               oninput="this.nextElementSibling.value = this.value">
                        <output><?php echo esc_html(get_option('aiacg_word_count', 1000)); ?></output> words
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_generation_time"><?php _e('Generation Time', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="time" id="aiacg_generation_time" name="aiacg_generation_time"
                               value="<?php echo esc_attr(get_option('aiacg_generation_time', '02:00')); ?>">
                        <p class="description"><?php _e('Time of day to run daily generation', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_publish_mode"><?php _e('Publish Mode', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <select id="aiacg_publish_mode" name="aiacg_publish_mode">
                            <?php
                            $modes = array(
                                'publish' => __('Publish Immediately', 'ai-auto-content-generator'),
                                'draft' => __('Save as Draft', 'ai-auto-content-generator'),
                            );
                            $current_mode = get_option('aiacg_publish_mode', 'publish');
                            foreach ($modes as $value => $label) {
                                echo '<option value="' . esc_attr($value) . '"' . selected($current_mode, $value, false) . '>' . esc_html($label) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_publish_interval"><?php _e('Publish Interval (minutes)', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="aiacg_publish_interval" name="aiacg_publish_interval"
                               value="<?php echo esc_attr(get_option('aiacg_publish_interval', 30)); ?>"
                               min="0" max="1440" step="5">
                        <p class="description"><?php _e('Time interval between publishing multiple posts (0 for no delay)', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th colspan="2"><h2><?php _e('Content Settings', 'ai-auto-content-generator'); ?></h2></th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_default_category"><?php _e('Default Category', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <?php
                        wp_dropdown_categories(array(
                            'name' => 'aiacg_default_category',
                            'id' => 'aiacg_default_category',
                            'selected' => get_option('aiacg_default_category', 1),
                            'show_option_none' => __('Select Category', 'ai-auto-content-generator'),
                            'hide_empty' => false,
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_auto_tags"><?php _e('Auto Generate Tags', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="aiacg_auto_tags" name="aiacg_auto_tags" value="1"
                                   <?php checked(get_option('aiacg_auto_tags', true)); ?>>
                            <?php _e('Automatically generate and assign tags', 'ai-auto-content-generator'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_seo_optimization"><?php _e('SEO Optimization', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="aiacg_seo_optimization" name="aiacg_seo_optimization" value="1"
                                   <?php checked(get_option('aiacg_seo_optimization', true)); ?>>
                            <?php _e('Enable SEO meta data generation', 'ai-auto-content-generator'); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th colspan="2"><h2><?php _e('Budget Control (v1.1.0)', 'ai-auto-content-generator'); ?></h2></th>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_daily_budget_limit"><?php _e('Daily Budget Limit ($)', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="aiacg_daily_budget_limit" name="aiacg_daily_budget_limit"
                               value="<?php echo esc_attr(get_option('aiacg_daily_budget_limit', 0)); ?>"
                               min="0" max="1000" step="0.01" style="width: 150px;">
                        <p class="description">
                            <?php _e('Maximum daily spending on API calls (0 = no limit). Generation will stop when limit reached.', 'ai-auto-content-generator'); ?>
                        </p>
                        <?php
                        $daily_status = AIACG_Budget_Manager::get_period_cost('today');
                        $daily_limit = get_option('aiacg_daily_budget_limit', 0);
                        if ($daily_limit > 0) {
                            $daily_percentage = ($daily_status / $daily_limit) * 100;
                            $status_class = $daily_percentage >= 80 ? 'error' : ($daily_percentage >= 60 ? 'warning' : 'success');
                            echo '<p class="description"><strong>' . __('Today:', 'ai-auto-content-generator') . '</strong> $' . number_format($daily_status, 2) . ' / $' . number_format($daily_limit, 2) . ' (' . number_format($daily_percentage, 1) . '%) <span class="aiacg-status-' . $status_class . '">●</span></p>';
                        } else {
                            echo '<p class="description"><strong>' . __('Today:', 'ai-auto-content-generator') . '</strong> $' . number_format($daily_status, 2) . ' <span class="aiacg-status-success">●</span></p>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_monthly_budget_limit"><?php _e('Monthly Budget Limit ($)', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="aiacg_monthly_budget_limit" name="aiacg_monthly_budget_limit"
                               value="<?php echo esc_attr(get_option('aiacg_monthly_budget_limit', 0)); ?>"
                               min="0" max="10000" step="0.01" style="width: 150px;">
                        <p class="description">
                            <?php _e('Maximum monthly spending on API calls (0 = no limit). Generation will stop when limit reached.', 'ai-auto-content-generator'); ?>
                        </p>
                        <?php
                        $monthly_status = AIACG_Budget_Manager::get_period_cost('month');
                        $monthly_limit = get_option('aiacg_monthly_budget_limit', 0);
                        if ($monthly_limit > 0) {
                            $monthly_percentage = ($monthly_status / $monthly_limit) * 100;
                            $status_class = $monthly_percentage >= 80 ? 'error' : ($monthly_percentage >= 60 ? 'warning' : 'success');
                            echo '<p class="description"><strong>' . __('This Month:', 'ai-auto-content-generator') . '</strong> $' . number_format($monthly_status, 2) . ' / $' . number_format($monthly_limit, 2) . ' (' . number_format($monthly_percentage, 1) . '%) <span class="aiacg-status-' . $status_class . '">●</span></p>';
                        } else {
                            echo '<p class="description"><strong>' . __('This Month:', 'ai-auto-content-generator') . '</strong> $' . number_format($monthly_status, 2) . ' <span class="aiacg-status-success">●</span></p>';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_email_on_budget_warning"><?php _e('Budget Warning Email', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="aiacg_email_on_budget_warning" name="aiacg_email_on_budget_warning" value="1"
                                   <?php checked(get_option('aiacg_email_on_budget_warning', false)); ?>>
                            <?php _e('Send email alert when budget threshold is reached', 'ai-auto-content-generator'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="aiacg_budget_warning_threshold"><?php _e('Warning Threshold (%)', 'ai-auto-content-generator'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="aiacg_budget_warning_threshold" name="aiacg_budget_warning_threshold"
                               value="<?php echo esc_attr(get_option('aiacg_budget_warning_threshold', 80)); ?>"
                               min="50" max="95" step="5" style="width: 100px;">
                        <p class="description"><?php _e('Send warning when budget reaches this percentage (default: 80%)', 'ai-auto-content-generator'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * AJAX: 导出设置
     */
    public function ajax_export_settings() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        $settings = $this->get_all_settings();

        // 创建JSON文件
        $filename = 'aiacg-settings-' . date('Y-m-d-His') . '.json';
        $json = wp_json_encode($settings, JSON_PRETTY_PRINT);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($json));

        echo $json;
        exit;
    }

    /**
     * 处理设置导入
     */
    public function handle_settings_import() {
        if (!isset($_POST['aiacg_import_settings'])) {
            return;
        }

        check_admin_referer('aiacg_import_settings');

        if (!current_user_can('manage_options')) {
            wp_die(__('Permission denied', 'ai-auto-content-generator'));
        }

        if (empty($_FILES['settings_file']['tmp_name'])) {
            add_settings_error(
                'aiacg_settings',
                'import_error',
                __('Please select a file to import', 'ai-auto-content-generator'),
                'error'
            );
            return;
        }

        // 验证文件类型
        $file_type = $_FILES['settings_file']['type'];
        $file_name = $_FILES['settings_file']['name'];
        $file_size = $_FILES['settings_file']['size'];

        // 只允许.json文件
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($file_ext !== 'json') {
            add_settings_error(
                'aiacg_settings',
                'import_error',
                __('Only JSON files are allowed', 'ai-auto-content-generator'),
                'error'
            );
            return;
        }

        // 限制文件大小为1MB
        if ($file_size > 1048576) {
            add_settings_error(
                'aiacg_settings',
                'import_error',
                __('File size must be less than 1MB', 'ai-auto-content-generator'),
                'error'
            );
            return;
        }

        $file = $_FILES['settings_file']['tmp_name'];
        $json = file_get_contents($file);

        // 验证JSON内容大小
        if (strlen($json) > 1048576) {
            add_settings_error(
                'aiacg_settings',
                'import_error',
                __('File content is too large', 'ai-auto-content-generator'),
                'error'
            );
            return;
        }

        $settings = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            add_settings_error(
                'aiacg_settings',
                'import_error',
                __('Invalid JSON file', 'ai-auto-content-generator'),
                'error'
            );
            return;
        }

        // 验证是否是数组
        if (!is_array($settings)) {
            add_settings_error(
                'aiacg_settings',
                'import_error',
                __('Invalid settings format', 'ai-auto-content-generator'),
                'error'
            );
            return;
        }

        // 只允许导入以aiacg_开头的设置，防止覆盖其他WordPress选项
        $allowed_prefix = 'aiacg_';
        $imported_count = 0;

        foreach ($settings as $key => $value) {
            // 验证键名
            if (strpos($key, $allowed_prefix) !== 0) {
                continue; // 跳过非插件设置
            }

            // 消毒value（如果是字符串）
            if (is_string($value)) {
                $value = sanitize_text_field($value);
            }

            update_option($key, $value);
            $imported_count++;
        }

        if ($imported_count === 0) {
            add_settings_error(
                'aiacg_settings',
                'import_error',
                __('No valid settings found in file', 'ai-auto-content-generator'),
                'error'
            );
            return;
        }

        add_settings_error(
            'aiacg_settings',
            'import_success',
            __('Settings imported successfully!', 'ai-auto-content-generator'),
            'updated'
        );

        // 重新安排定时任务
        $scheduler = new AIACG_Scheduler();
        $scheduler->reschedule_daily_generation(get_option('aiacg_generation_time', '02:00'));
    }

    /**
     * 获取所有设置
     */
    private function get_all_settings() {
        $settings = array();

        $option_keys = array(
            'aiacg_main_topic',
            'aiacg_topic_description',
            'aiacg_sub_topics',
            'aiacg_writing_style',
            'aiacg_target_audience',
            'aiacg_daily_post_count',
            'aiacg_word_count',
            'aiacg_generation_time',
            'aiacg_publish_mode',
            'aiacg_publish_interval',
            'aiacg_default_category',
            'aiacg_auto_tags',
            'aiacg_generate_featured_image',
            'aiacg_seo_optimization',
            'aiacg_active_api',
            'aiacg_enable_api_rotation',
            'aiacg_api_priority',
            'aiacg_auto_switch_on_failure',
            'aiacg_gemini_model',
            'aiacg_gemini_temperature',
            'aiacg_gemini_max_tokens',
            'aiacg_deepseek_model',
            'aiacg_deepseek_temperature',
            'aiacg_deepseek_max_tokens',
            'aiacg_openai_model',
            'aiacg_openai_temperature',
            'aiacg_openai_max_tokens',
            'aiacg_system_prompt',
            'aiacg_user_prompt_template',
            'aiacg_writing_angles',
            'aiacg_title_min_length',
            'aiacg_title_max_length',
        );

        foreach ($option_keys as $key) {
            $value = get_option($key);
            if ($value !== false) {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }

    /**
     * AJAX: 删除历史记录
     */
    public function ajax_delete_history() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        $history_ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : array();

        if (empty($history_ids)) {
            wp_send_json_error(array('message' => __('No records selected', 'ai-auto-content-generator')));
        }

        $deleted = 0;
        foreach ($history_ids as $id) {
            if (AIACG_Database::delete_history($id)) {
                $deleted++;
            }
        }

        wp_send_json_success(array(
            'message' => sprintf(__('Deleted %d record(s)', 'ai-auto-content-generator'), $deleted),
            'deleted' => $deleted
        ));
    }

    /**
     * AJAX: 清理旧记录
     */
    public function ajax_cleanup_records() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        $days = isset($_POST['days']) ? intval($_POST['days']) : 90;
        $days = max(7, min(365, $days)); // 限制在7-365天之间

        $deleted = AIACG_Database::cleanup_old_records($days);

        wp_send_json_success(array(
            'message' => sprintf(__('Deleted %d old record(s)', 'ai-auto-content-generator'), $deleted),
            'deleted' => $deleted
        ));
    }

    /**
     * AJAX: 重置统计数据
     */
    public function ajax_reset_stats() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        // 重置所有API的使用统计
        delete_option('aiacg_gemini_usage_stats');
        delete_option('aiacg_deepseek_usage_stats');
        delete_option('aiacg_openai_usage_stats');

        wp_send_json_success(array(
            'message' => __('All statistics have been reset', 'ai-auto-content-generator')
        ));
    }

    /**
     * AJAX: 重置插件
     */
    public function ajax_reset_plugin() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        // 删除所有选项
        $options = array(
            'aiacg_main_topic', 'aiacg_topic_description', 'aiacg_sub_topics',
            'aiacg_writing_style', 'aiacg_target_audience', 'aiacg_daily_post_count',
            'aiacg_word_count', 'aiacg_generation_time', 'aiacg_publish_mode',
            'aiacg_publish_interval', 'aiacg_default_category', 'aiacg_auto_tags',
            'aiacg_generate_featured_image', 'aiacg_seo_optimization', 'aiacg_active_api',
            'aiacg_gemini_api_key', 'aiacg_gemini_model', 'aiacg_gemini_temperature',
            'aiacg_gemini_max_tokens', 'aiacg_deepseek_api_key', 'aiacg_deepseek_model',
            'aiacg_deepseek_temperature', 'aiacg_openai_api_key', 'aiacg_openai_model',
            'aiacg_openai_temperature', 'aiacg_enable_api_rotation', 'aiacg_api_priority',
            'aiacg_auto_switch_on_failure', 'aiacg_system_prompt', 'aiacg_user_prompt_template',
            'aiacg_writing_angles', 'aiacg_title_min_length', 'aiacg_title_max_length',
            'aiacg_gemini_usage_stats', 'aiacg_deepseek_usage_stats', 'aiacg_openai_usage_stats',
            'aiacg_logs', 'aiacg_last_generation_time', 'aiacg_last_generation_result'
        );

        foreach ($options as $option) {
            delete_option($option);
        }

        // 清空数据库表
        global $wpdb;
        $table_name = $wpdb->prefix . 'aiacg_content_history';
        $wpdb->query("TRUNCATE TABLE {$table_name}");

        // 清除定时任务
        wp_clear_scheduled_hook('aiacg_daily_generation');

        wp_send_json_success(array(
            'message' => __('Plugin has been reset to defaults', 'ai-auto-content-generator')
        ));
    }

    /**
     * AJAX: 导出历史记录为CSV
     */
    public function ajax_export_history_csv() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die(__('Permission denied', 'ai-auto-content-generator'));
        }

        // 获取所有历史记录
        $history = AIACG_Database::get_history(array(
            'limit' => 10000, // 获取大量记录
            'offset' => 0,
        ));

        // 设置CSV header
        $filename = 'aiacg-history-' . date('Y-m-d-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // 创建输出流
        $output = fopen('php://output', 'w');

        // 添加BOM以支持Excel正确显示UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // 写入CSV表头
        fputcsv($output, array(
            __('ID', 'ai-auto-content-generator'),
            __('Date', 'ai-auto-content-generator'),
            __('Title', 'ai-auto-content-generator'),
            __('Post ID', 'ai-auto-content-generator'),
            __('Topic', 'ai-auto-content-generator'),
            __('Status', 'ai-auto-content-generator'),
            __('Quality Score', 'ai-auto-content-generator'),
            __('Quality Grade', 'ai-auto-content-generator'),
            __('API Used', 'ai-auto-content-generator'),
            __('Word Count', 'ai-auto-content-generator'),
            __('Tokens', 'ai-auto-content-generator'),
            __('Cost', 'ai-auto-content-generator'),
            __('Writing Angle', 'ai-auto-content-generator'),
            __('Similarity Score', 'ai-auto-content-generator'),
            __('Error Message', 'ai-auto-content-generator'),
        ));

        // 写入数据行
        foreach ($history as $record) {
            $quality_grade = '';
            if (isset($record->quality_score) && $record->quality_score > 0) {
                $quality_grade = AIACG_Content_Quality::get_quality_grade($record->quality_score);
            }

            fputcsv($output, array(
                $record->id,
                $record->generation_time,
                $record->generated_title,
                $record->post_id ? $record->post_id : '',
                $record->topic,
                $record->status,
                isset($record->quality_score) ? $record->quality_score : '',
                $quality_grade,
                ucfirst($record->api_used),
                $record->word_count,
                $record->tokens_used,
                $record->cost_estimate,
                $record->writing_angle,
                $record->similarity_score,
                $record->error_message,
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * AJAX: 批量重新评估质量分数
     */
    public function ajax_reevaluate_quality() {
        check_ajax_referer('aiacg_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'ai-auto-content-generator')));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . AIACG_Database::TABLE_NAME;

        // 获取所有需要重新评估的记录（quality_score = 0 或 NULL，且状态为 completed）
        $records = $wpdb->get_results(
            "SELECT id, post_id, generated_title FROM {$table_name}
             WHERE (quality_score = 0 OR quality_score IS NULL)
             AND status = 'completed'
             AND post_id > 0
             LIMIT 100"
        );

        if (empty($records)) {
            wp_send_json_success(array(
                'message' => __('No posts need quality re-evaluation', 'ai-auto-content-generator'),
                'processed' => 0,
                'remaining' => 0,
            ));
        }

        $processed = 0;
        $errors = array();

        foreach ($records as $record) {
            $post = get_post($record->post_id);

            if (!$post) {
                $errors[] = sprintf(__('Post #%d not found', 'ai-auto-content-generator'), $record->post_id);
                continue;
            }

            // 评估质量
            $quality_evaluation = AIACG_Content_Quality::evaluate($post->post_title, $post->post_content);

            // 更新数据库
            $updated = $wpdb->update(
                $table_name,
                array('quality_score' => $quality_evaluation['overall_score']),
                array('id' => $record->id),
                array('%d'),
                array('%d')
            );

            if ($updated !== false) {
                $processed++;
            } else {
                $errors[] = sprintf(__('Failed to update record #%d', 'ai-auto-content-generator'), $record->id);
            }
        }

        // 检查是否还有剩余记录
        $remaining = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$table_name}
             WHERE (quality_score = 0 OR quality_score IS NULL)
             AND status = 'completed'
             AND post_id > 0"
        );

        $message = sprintf(
            __('Processed %d posts. %d remaining.', 'ai-auto-content-generator'),
            $processed,
            $remaining
        );

        if (!empty($errors)) {
            $message .= ' ' . __('Some errors occurred:', 'ai-auto-content-generator') . ' ' . implode('; ', $errors);
        }

        wp_send_json_success(array(
            'message' => $message,
            'processed' => $processed,
            'remaining' => intval($remaining),
            'errors' => $errors,
        ));
    }
}
