<?php
/**
 * API健康监控类
 *
 * @package AI_Auto_Content_Generator
 * @since 1.1.1
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_API_Health_Monitor {

    /**
     * 检查所有配置的API的健康状态
     */
    public static function check_all_apis() {
        $results = array();

        // Check Gemini
        if (get_option('aiacg_gemini_api_key')) {
            $results['gemini'] = self::check_gemini();
        }

        // Check DeepSeek
        if (get_option('aiacg_deepseek_api_key')) {
            $results['deepseek'] = self::check_deepseek();
        }

        // Check OpenAI
        if (get_option('aiacg_openai_api_key')) {
            $results['openai'] = self::check_openai();
        }

        return $results;
    }

    /**
     * 检查Gemini API健康状态
     */
    private static function check_gemini() {
        $start_time = microtime(true);

        try {
            $api_key = get_option('aiacg_gemini_api_key');
            $model = get_option('aiacg_gemini_model', 'gemini-pro');

            if (empty($api_key)) {
                return array(
                    'status' => 'not_configured',
                    'message' => __('API key not configured', 'ai-auto-content-generator'),
                    'response_time' => 0,
                );
            }

            // Simple test request
            $url = 'https://generativelanguage.googleapis.com/v1/models/' . $model . ':generateContent?key=' . $api_key;

            $response = wp_remote_post($url, array(
                'timeout' => 10,
                'headers' => array('Content-Type' => 'application/json'),
                'body' => json_encode(array(
                    'contents' => array(
                        array(
                            'parts' => array(
                                array('text' => 'Test')
                            )
                        )
                    )
                )),
            ));

            $response_time = round((microtime(true) - $start_time) * 1000); // ms

            if (is_wp_error($response)) {
                return array(
                    'status' => 'error',
                    'message' => $response->get_error_message(),
                    'response_time' => $response_time,
                );
            }

            $status_code = wp_remote_retrieve_response_code($response);

            if ($status_code === 200) {
                return array(
                    'status' => 'healthy',
                    'message' => __('API is responding normally', 'ai-auto-content-generator'),
                    'response_time' => $response_time,
                    'last_checked' => current_time('mysql'),
                );
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown error';

                return array(
                    'status' => 'error',
                    'message' => sprintf(__('HTTP %d: %s', 'ai-auto-content-generator'), $status_code, $error_message),
                    'response_time' => $response_time,
                );
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'response_time' => round((microtime(true) - $start_time) * 1000),
            );
        }
    }

    /**
     * 检查DeepSeek API健康状态
     */
    private static function check_deepseek() {
        $start_time = microtime(true);

        try {
            $api_key = get_option('aiacg_deepseek_api_key');
            $model = get_option('aiacg_deepseek_model', 'deepseek-chat');

            if (empty($api_key)) {
                return array(
                    'status' => 'not_configured',
                    'message' => __('API key not configured', 'ai-auto-content-generator'),
                    'response_time' => 0,
                );
            }

            $url = 'https://api.deepseek.com/v1/chat/completions';

            $response = wp_remote_post($url, array(
                'timeout' => 10,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ),
                'body' => json_encode(array(
                    'model' => $model,
                    'messages' => array(
                        array('role' => 'user', 'content' => 'Test')
                    ),
                    'max_tokens' => 5,
                )),
            ));

            $response_time = round((microtime(true) - $start_time) * 1000);

            if (is_wp_error($response)) {
                return array(
                    'status' => 'error',
                    'message' => $response->get_error_message(),
                    'response_time' => $response_time,
                );
            }

            $status_code = wp_remote_retrieve_response_code($response);

            if ($status_code === 200) {
                return array(
                    'status' => 'healthy',
                    'message' => __('API is responding normally', 'ai-auto-content-generator'),
                    'response_time' => $response_time,
                    'last_checked' => current_time('mysql'),
                );
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown error';

                return array(
                    'status' => 'error',
                    'message' => sprintf(__('HTTP %d: %s', 'ai-auto-content-generator'), $status_code, $error_message),
                    'response_time' => $response_time,
                );
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'response_time' => round((microtime(true) - $start_time) * 1000),
            );
        }
    }

    /**
     * 检查OpenAI API健康状态
     */
    private static function check_openai() {
        $start_time = microtime(true);

        try {
            $api_key = get_option('aiacg_openai_api_key');
            $model = get_option('aiacg_openai_model', 'gpt-3.5-turbo');

            if (empty($api_key)) {
                return array(
                    'status' => 'not_configured',
                    'message' => __('API key not configured', 'ai-auto-content-generator'),
                    'response_time' => 0,
                );
            }

            $url = 'https://api.openai.com/v1/chat/completions';

            $response = wp_remote_post($url, array(
                'timeout' => 10,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $api_key,
                ),
                'body' => json_encode(array(
                    'model' => $model,
                    'messages' => array(
                        array('role' => 'user', 'content' => 'Test')
                    ),
                    'max_tokens' => 5,
                )),
            ));

            $response_time = round((microtime(true) - $start_time) * 1000);

            if (is_wp_error($response)) {
                return array(
                    'status' => 'error',
                    'message' => $response->get_error_message(),
                    'response_time' => $response_time,
                );
            }

            $status_code = wp_remote_retrieve_response_code($response);

            if ($status_code === 200) {
                return array(
                    'status' => 'healthy',
                    'message' => __('API is responding normally', 'ai-auto-content-generator'),
                    'response_time' => $response_time,
                    'last_checked' => current_time('mysql'),
                );
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                $error_message = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown error';

                return array(
                    'status' => 'error',
                    'message' => sprintf(__('HTTP %d: %s', 'ai-auto-content-generator'), $status_code, $error_message),
                    'response_time' => $response_time,
                );
            }
        } catch (Exception $e) {
            return array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'response_time' => round((microtime(true) - $start_time) * 1000),
            );
        }
    }

    /**
     * 获取API健康状态摘要
     */
    public static function get_health_summary() {
        $all_results = self::check_all_apis();

        if (empty($all_results)) {
            return array(
                'overall_status' => 'not_configured',
                'message' => __('No APIs configured', 'ai-auto-content-generator'),
                'healthy_count' => 0,
                'total_count' => 0,
            );
        }

        $healthy_count = 0;
        $total_count = count($all_results);
        $has_error = false;
        $has_warning = false;

        foreach ($all_results as $api => $result) {
            if ($result['status'] === 'healthy') {
                $healthy_count++;
            } elseif ($result['status'] === 'error') {
                $has_error = true;
            } elseif ($result['status'] === 'not_configured') {
                $has_warning = true;
            }
        }

        $overall_status = 'healthy';
        if ($has_error && $healthy_count === 0) {
            $overall_status = 'critical';
        } elseif ($has_error) {
            $overall_status = 'degraded';
        } elseif ($has_warning) {
            $overall_status = 'warning';
        }

        return array(
            'overall_status' => $overall_status,
            'healthy_count' => $healthy_count,
            'total_count' => $total_count,
            'api_results' => $all_results,
        );
    }

    /**
     * 获取平均响应时间
     */
    public static function get_average_response_time() {
        $all_results = self::check_all_apis();

        if (empty($all_results)) {
            return 0;
        }

        $total_time = 0;
        $count = 0;

        foreach ($all_results as $result) {
            if (isset($result['response_time']) && $result['response_time'] > 0) {
                $total_time += $result['response_time'];
                $count++;
            }
        }

        return $count > 0 ? round($total_time / $count) : 0;
    }
}
