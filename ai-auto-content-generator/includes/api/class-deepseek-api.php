<?php
/**
 * DeepSeek API实现
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_DeepSeek_API implements AIACG_AI_API_Interface {

    /**
     * API Key
     * @var string
     */
    private $api_key;

    /**
     * 模型
     * @var string
     */
    private $model;

    /**
     * API端点
     * @var string
     */
    private $api_endpoint = 'https://api.deepseek.com/v1/chat/completions';

    /**
     * 使用统计
     * @var array
     */
    private $usage_stats = array();

    /**
     * 构造函数
     */
    public function __construct() {
        $this->api_key = get_option('aiacg_deepseek_api_key', '');
        $this->model = get_option('aiacg_deepseek_model', 'deepseek-chat');
        $this->load_usage_stats();
    }

    /**
     * 加载使用统计
     */
    private function load_usage_stats() {
        $this->usage_stats = get_option('aiacg_deepseek_usage_stats', array(
            'total_calls' => 0,
            'total_tokens' => 0,
            'total_cost' => 0,
            'latencies' => array(),
        ));
    }

    /**
     * 保存使用统计
     */
    private function save_usage_stats() {
        update_option('aiacg_deepseek_usage_stats', $this->usage_stats);
    }

    /**
     * 生成内容
     */
    public function generate_content($prompt, $params = array()) {
        if (!$this->validate_config()) {
            return array(
                'success' => false,
                'content' => '',
                'tokens' => 0,
                'error' => __('DeepSeek API configuration is invalid', 'ai-auto-content-generator'),
                'cost' => 0,
            );
        }

        $defaults = array(
            'temperature' => floatval(get_option('aiacg_deepseek_temperature', 0.7)),
            'max_tokens' => intval(get_option('aiacg_deepseek_max_tokens', 2048)),
            'top_p' => floatval(get_option('aiacg_deepseek_top_p', 0.95)),
            'system_prompt' => '',
        );

        $params = wp_parse_args($params, $defaults);

        $start_time = microtime(true);

        $messages = array();

        if (!empty($params['system_prompt'])) {
            $messages[] = array(
                'role' => 'system',
                'content' => $params['system_prompt'],
            );
        }

        $messages[] = array(
            'role' => 'user',
            'content' => $prompt,
        );

        $body = array(
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $params['temperature'],
            'max_tokens' => $params['max_tokens'],
            'top_p' => $params['top_p'],
            'stream' => false,
        );

        $response = wp_remote_post($this->api_endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key,
            ),
            'body' => wp_json_encode($body),
            'timeout' => 60,
        ));

        $latency = (microtime(true) - $start_time) * 1000;

        if (is_wp_error($response)) {
            AIACG_Database::log('DeepSeek API error: ' . $response->get_error_message(), 'error');
            return array(
                'success' => false,
                'content' => '',
                'tokens' => 0,
                'error' => $response->get_error_message(),
                'cost' => 0,
            );
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($response_code !== 200 || isset($data['error'])) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown error';
            AIACG_Database::log('DeepSeek API error: ' . $error_message, 'error', $data);
            return array(
                'success' => false,
                'content' => '',
                'tokens' => 0,
                'error' => $error_message,
                'cost' => 0,
            );
        }

        // 解析响应
        $content = '';
        $tokens = 0;

        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];
        }

        if (isset($data['usage']['total_tokens'])) {
            $tokens = $data['usage']['total_tokens'];
        }

        $cost = $this->calculate_cost($tokens, $this->model);

        // 更新统计
        $this->usage_stats['total_calls']++;
        $this->usage_stats['total_tokens'] += $tokens;
        $this->usage_stats['total_cost'] += $cost;
        $this->usage_stats['latencies'][] = $latency;

        if (count($this->usage_stats['latencies']) > 100) {
            array_shift($this->usage_stats['latencies']);
        }

        $this->save_usage_stats();

        AIACG_Database::log(
            'DeepSeek content generated successfully',
            'info',
            array('tokens' => $tokens, 'cost' => $cost, 'latency' => $latency)
        );

        return array(
            'success' => true,
            'content' => $content,
            'tokens' => $tokens,
            'error' => '',
            'cost' => $cost,
            'latency' => $latency,
        );
    }

    /**
     * 测试API连接
     */
    public function test_connection() {
        if (empty($this->api_key)) {
            return array(
                'success' => false,
                'message' => __('API Key is not configured', 'ai-auto-content-generator'),
                'latency' => 0,
            );
        }

        $start_time = microtime(true);

        $result = $this->generate_content('Say "Hello, I am working!" in one sentence.', array(
            'max_tokens' => 50,
        ));

        $latency = (microtime(true) - $start_time) * 1000;

        if ($result['success']) {
            return array(
                'success' => true,
                'message' => __('Connection successful!', 'ai-auto-content-generator') . ' ' . __('Response:', 'ai-auto-content-generator') . ' ' . substr($result['content'], 0, 100),
                'latency' => $latency,
            );
        } else {
            return array(
                'success' => false,
                'message' => __('Connection failed:', 'ai-auto-content-generator') . ' ' . $result['error'],
                'latency' => $latency,
            );
        }
    }

    /**
     * 获取可用模型列表
     */
    public function get_models() {
        return array(
            'deepseek-chat' => 'DeepSeek Chat',
            'deepseek-coder' => 'DeepSeek Coder',
        );
    }

    /**
     * 获取使用统计
     */
    public function get_usage_stats() {
        $avg_latency = 0;
        if (!empty($this->usage_stats['latencies'])) {
            $avg_latency = array_sum($this->usage_stats['latencies']) / count($this->usage_stats['latencies']);
        }

        return array(
            'total_calls' => $this->usage_stats['total_calls'],
            'total_tokens' => $this->usage_stats['total_tokens'],
            'total_cost' => $this->usage_stats['total_cost'],
            'avg_latency' => round($avg_latency, 2),
        );
    }

    /**
     * 计算预估成本
     */
    public function calculate_cost($tokens, $model = '') {
        if (empty($model)) {
            $model = $this->model;
        }

        // DeepSeek定价（根据官方定价）
        $pricing = array(
            'deepseek-chat' => array(
                'input' => 0.00014 / 1000,   // $0.14 per 1M tokens
                'output' => 0.00028 / 1000,  // $0.28 per 1M tokens
            ),
            'deepseek-coder' => array(
                'input' => 0.00014 / 1000,
                'output' => 0.00028 / 1000,
            ),
        );

        $model_pricing = isset($pricing[$model]) ? $pricing[$model] : $pricing['deepseek-chat'];

        // 简化计算，假设输入输出各占50%
        $cost = $tokens * ($model_pricing['input'] + $model_pricing['output']) / 2;

        return round($cost, 6);
    }

    /**
     * 获取API名称
     */
    public function get_name() {
        return 'DeepSeek';
    }

    /**
     * 验证API配置
     */
    public function validate_config() {
        return !empty($this->api_key) && !empty($this->model);
    }
}
