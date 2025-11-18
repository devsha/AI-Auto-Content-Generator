<?php
/**
 * Google Gemini API实现
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Gemini_API implements AIACG_AI_API_Interface {

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
    private $api_endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/';

    /**
     * 使用统计
     * @var array
     */
    private $usage_stats = array();

    /**
     * 构造函数
     */
    public function __construct() {
        $this->api_key = get_option('aiacg_gemini_api_key', '');
        $this->model = get_option('aiacg_gemini_model', 'gemini-1.5-pro');
        $this->load_usage_stats();
    }

    /**
     * 加载使用统计
     */
    private function load_usage_stats() {
        $this->usage_stats = get_option('aiacg_gemini_usage_stats', array(
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
        update_option('aiacg_gemini_usage_stats', $this->usage_stats);
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
                'error' => __('Gemini API configuration is invalid', 'ai-auto-content-generator'),
                'cost' => 0,
            );
        }

        $defaults = array(
            'temperature' => floatval(get_option('aiacg_gemini_temperature', 0.7)),
            'max_tokens' => intval(get_option('aiacg_gemini_max_tokens', 2048)),
            'top_p' => floatval(get_option('aiacg_gemini_top_p', 0.95)),
            'top_k' => intval(get_option('aiacg_gemini_top_k', 40)),
        );

        $params = wp_parse_args($params, $defaults);

        $start_time = microtime(true);

        $url = $this->api_endpoint . $this->model . ':generateContent?key=' . $this->api_key;

        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => $params['temperature'],
                'maxOutputTokens' => $params['max_tokens'],
                'topP' => $params['top_p'],
                'topK' => $params['top_k'],
            ),
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($body),
            'timeout' => 60,
        ));

        $latency = (microtime(true) - $start_time) * 1000; // 转换为毫秒

        if (is_wp_error($response)) {
            AIACG_Database::log('Gemini API error: ' . $response->get_error_message(), 'error');
            return array(
                'success' => false,
                'content' => '',
                'tokens' => 0,
                'error' => $response->get_error_message(),
                'cost' => 0,
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown error';
            AIACG_Database::log('Gemini API error: ' . $error_message, 'error', $data);
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

        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $content = $data['candidates'][0]['content']['parts'][0]['text'];
        }

        // Gemini API响应中包含token计数
        if (isset($data['usageMetadata'])) {
            $tokens = $data['usageMetadata']['totalTokenCount'] ?? 0;
        } else {
            // 如果没有返回token信息，使用估算
            $tokens = $this->estimate_tokens($prompt . $content);
        }

        $cost = $this->calculate_cost($tokens, $this->model);

        // 更新统计
        $this->usage_stats['total_calls']++;
        $this->usage_stats['total_tokens'] += $tokens;
        $this->usage_stats['total_cost'] += $cost;
        $this->usage_stats['latencies'][] = $latency;

        // 只保留最近100次延迟记录
        if (count($this->usage_stats['latencies']) > 100) {
            array_shift($this->usage_stats['latencies']);
        }

        $this->save_usage_stats();

        AIACG_Database::log(
            'Gemini content generated successfully',
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
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash',
            'gemini-pro' => 'Gemini Pro',
            'gemini-1.0-pro' => 'Gemini 1.0 Pro',
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

        // Gemini定价（根据官方定价，2024年）
        // 注意：这些是示例价格，请根据实际情况调整
        $pricing = array(
            'gemini-1.5-pro' => array(
                'input' => 0.00025 / 1000,  // $0.25 per 1M tokens
                'output' => 0.0005 / 1000,  // $0.50 per 1M tokens
            ),
            'gemini-1.5-flash' => array(
                'input' => 0.00005 / 1000,  // $0.05 per 1M tokens
                'output' => 0.0001 / 1000,  // $0.10 per 1M tokens
            ),
            'gemini-pro' => array(
                'input' => 0.0005 / 1000,
                'output' => 0.0015 / 1000,
            ),
            'gemini-1.0-pro' => array(
                'input' => 0.0005 / 1000,
                'output' => 0.0015 / 1000,
            ),
        );

        $model_pricing = isset($pricing[$model]) ? $pricing[$model] : $pricing['gemini-1.5-pro'];

        // 简化计算，假设输入输出各占50%
        $cost = $tokens * ($model_pricing['input'] + $model_pricing['output']) / 2;

        return round($cost, 6);
    }

    /**
     * 估算token数量
     */
    private function estimate_tokens($text) {
        // 简单估算：英文约4字符=1token，中文约2字符=1token
        $length = mb_strlen($text, 'UTF-8');
        // 假设混合文本，使用平均值
        return intval($length / 3);
    }

    /**
     * 获取API名称
     */
    public function get_name() {
        return 'Gemini';
    }

    /**
     * 验证API配置
     */
    public function validate_config() {
        return !empty($this->api_key) && !empty($this->model);
    }
}
