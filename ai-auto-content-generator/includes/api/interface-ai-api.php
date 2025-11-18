<?php
/**
 * AI API接口
 *
 * 定义所有AI API实现必须遵循的接口
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

interface AIACG_AI_API_Interface {

    /**
     * 生成内容
     *
     * @param string $prompt 提示词
     * @param array $params 参数配置
     * @return array {
     *     @type bool $success 是否成功
     *     @type string $content 生成的内容
     *     @type int $tokens 使用的token数
     *     @type string $error 错误信息
     *     @type float $cost 预估成本
     * }
     */
    public function generate_content($prompt, $params = array());

    /**
     * 测试API连接
     *
     * @return array {
     *     @type bool $success 是否成功
     *     @type string $message 测试消息
     *     @type float $latency 响应延迟（毫秒）
     * }
     */
    public function test_connection();

    /**
     * 获取可用模型列表
     *
     * @return array
     */
    public function get_models();

    /**
     * 获取使用统计
     *
     * @return array {
     *     @type int $total_calls 总调用次数
     *     @type int $total_tokens 总token使用量
     *     @type float $total_cost 总成本
     *     @type float $avg_latency 平均延迟
     * }
     */
    public function get_usage_stats();

    /**
     * 计算预估成本
     *
     * @param int $tokens Token数量
     * @param string $model 模型名称
     * @return float
     */
    public function calculate_cost($tokens, $model = '');

    /**
     * 获取API名称
     *
     * @return string
     */
    public function get_name();

    /**
     * 验证API配置
     *
     * @return bool
     */
    public function validate_config();
}
