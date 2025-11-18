<?php
/**
 * AI管理器类
 *
 * 负责管理多个AI API，包括轮询、切换和故障转移
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_AI_Manager {

    /**
     * 可用的AI API实例
     * @var array
     */
    private $apis = array();

    /**
     * 当前活动的API
     * @var string
     */
    private $active_api;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->active_api = get_option('aiacg_active_api', 'gemini');
        $this->init_apis();
    }

    /**
     * 初始化所有API实例
     */
    private function init_apis() {
        $this->apis = array(
            'gemini' => new AIACG_Gemini_API(),
            'deepseek' => new AIACG_DeepSeek_API(),
            'openai' => new AIACG_OpenAI_API(),
        );
    }

    /**
     * 获取当前活动的API实例
     *
     * @return AIACG_AI_API_Interface|null
     */
    public function get_active_api() {
        if (isset($this->apis[$this->active_api])) {
            return $this->apis[$this->active_api];
        }
        return null;
    }

    /**
     * 切换活动API
     *
     * @param string $api_name API名称
     * @return bool
     */
    public function switch_api($api_name) {
        if (isset($this->apis[$api_name])) {
            $this->active_api = $api_name;
            update_option('aiacg_active_api', $api_name);
            AIACG_Database::log("Switched to {$api_name} API", 'info');
            return true;
        }
        return false;
    }

    /**
     * 生成内容（支持自动故障转移）
     *
     * @param string $prompt 提示词
     * @param array $params 参数
     * @param int $max_retries 最大重试次数
     * @return array
     */
    public function generate_content($prompt, $params = array(), $max_retries = 3) {
        $enable_rotation = get_option('aiacg_enable_api_rotation', false);
        $auto_switch = get_option('aiacg_auto_switch_on_failure', true);
        $api_priority = get_option('aiacg_api_priority', array('gemini', 'deepseek', 'openai'));

        $tried_apis = array();
        $last_error = '';

        // 如果启用了轮询，使用优先级列表
        $apis_to_try = $enable_rotation ? $api_priority : array($this->active_api);

        foreach ($apis_to_try as $api_name) {
            if (!isset($this->apis[$api_name]) || in_array($api_name, $tried_apis)) {
                continue;
            }

            $api = $this->apis[$api_name];
            $tried_apis[] = $api_name;

            // 验证API配置
            if (!$api->validate_config()) {
                AIACG_Database::log(
                    "API {$api_name} is not properly configured, skipping",
                    'warning'
                );
                continue;
            }

            // 尝试生成内容，带重试机制
            for ($i = 0; $i < $max_retries; $i++) {
                $result = $api->generate_content($prompt, $params);

                if ($result['success']) {
                    // 成功生成，更新当前活动API
                    if ($api_name !== $this->active_api) {
                        $this->switch_api($api_name);
                    }

                    $result['api_used'] = $api_name;
                    return $result;
                }

                $last_error = $result['error'];

                // 如果不是临时错误，不要重试
                if (!$this->is_retryable_error($result['error'])) {
                    break;
                }

                // 指数退避
                if ($i < $max_retries - 1) {
                    $wait_time = pow(2, $i);
                    $retry_num = $i + 1;
                    AIACG_Database::log(
                        "Retry {$retry_num} for {$api_name} API after {$wait_time}s",
                        'info'
                    );
                    sleep($wait_time);
                }
            }

            AIACG_Database::log(
                "API {$api_name} failed after {$max_retries} retries: {$last_error}",
                'error'
            );

            // 如果禁用了自动切换，不要尝试其他API
            if (!$auto_switch) {
                break;
            }
        }

        // 所有API都失败了
        return array(
            'success' => false,
            'content' => '',
            'tokens' => 0,
            'error' => __('All API attempts failed. Last error:', 'ai-auto-content-generator') . ' ' . $last_error,
            'cost' => 0,
            'api_used' => implode(', ', $tried_apis),
        );
    }

    /**
     * 判断错误是否可重试
     *
     * @param string $error 错误信息
     * @return bool
     */
    private function is_retryable_error($error) {
        $retryable_patterns = array(
            'timeout',
            'network',
            'connection',
            'rate limit',
            '429',
            '500',
            '502',
            '503',
            '504',
        );

        $error_lower = strtolower($error);

        foreach ($retryable_patterns as $pattern) {
            if (strpos($error_lower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 测试所有API连接
     *
     * @return array
     */
    public function test_all_connections() {
        $results = array();

        foreach ($this->apis as $name => $api) {
            if (!$api->validate_config()) {
                $results[$name] = array(
                    'success' => false,
                    'message' => __('Not configured', 'ai-auto-content-generator'),
                    'latency' => 0,
                );
                continue;
            }

            $results[$name] = $api->test_connection();
        }

        return $results;
    }

    /**
     * 获取所有API的使用统计
     *
     * @return array
     */
    public function get_all_usage_stats() {
        $stats = array();

        foreach ($this->apis as $name => $api) {
            $stats[$name] = $api->get_usage_stats();
        }

        return $stats;
    }

    /**
     * 获取所有可用的API列表
     *
     * @return array
     */
    public function get_available_apis() {
        $available = array();

        foreach ($this->apis as $name => $api) {
            $available[$name] = array(
                'name' => $api->get_name(),
                'configured' => $api->validate_config(),
                'models' => $api->get_models(),
            );
        }

        return $available;
    }

    /**
     * 获取API实例
     *
     * @param string $api_name API名称
     * @return AIACG_AI_API_Interface|null
     */
    public function get_api($api_name) {
        return isset($this->apis[$api_name]) ? $this->apis[$api_name] : null;
    }
}
