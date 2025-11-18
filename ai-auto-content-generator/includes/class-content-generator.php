<?php
/**
 * 内容生成器类
 *
 * 负责生成文章内容，包括标题、正文、摘要等，并实现防重复机制
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Content_Generator {

    /**
     * AI管理器实例
     * @var AIACG_AI_Manager
     */
    private $ai_manager;

    /**
     * 写作角度列表
     * @var array
     */
    private $writing_angles = array();

    /**
     * 构造函数
     */
    public function __construct() {
        $this->ai_manager = new AIACG_AI_Manager();
        $this->load_writing_angles();
    }

    /**
     * 加载写作角度
     */
    private function load_writing_angles() {
        $default_angles = array(
            'news' => __('News Report - Latest developments and breaking news', 'ai-auto-content-generator'),
            'analysis' => __('In-depth Analysis - Detailed examination and insights', 'ai-auto-content-generator'),
            'guide' => __('How-to Guide - Step-by-step instructions', 'ai-auto-content-generator'),
            'case_study' => __('Case Study - Real-world examples and lessons', 'ai-auto-content-generator'),
            'trend' => __('Trend Analysis - Future predictions and patterns', 'ai-auto-content-generator'),
            'comparison' => __('Comparison - Pros, cons, and alternatives', 'ai-auto-content-generator'),
            'opinion' => __('Opinion Piece - Expert perspectives', 'ai-auto-content-generator'),
            'tutorial' => __('Tutorial - Practical learning content', 'ai-auto-content-generator'),
        );

        $saved_angles = get_option('aiacg_writing_angles', array());
        $this->writing_angles = !empty($saved_angles) ? $saved_angles : $default_angles;
    }

    /**
     * 生成单篇文章
     *
     * @param array $args 生成参数
     * @return array {
     *     @type bool $success 是否成功
     *     @type int $post_id 文章ID
     *     @type string $error 错误信息
     *     @type array $details 详细信息
     * }
     */
    public function generate_post($args = array()) {
        $defaults = array(
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

        $args = wp_parse_args($args, $defaults);

        // 验证必要参数
        if (empty($args['topic'])) {
            return array(
                'success' => false,
                'post_id' => 0,
                'error' => __('Main topic is not set', 'ai-auto-content-generator'),
                'details' => array(),
            );
        }

        // 创建历史记录
        $history_id = AIACG_Database::insert_history(array(
            'topic' => $args['topic'],
            'status' => 'generating',
        ));

        AIACG_Database::log('Starting content generation', 'info', array('history_id' => $history_id));

        // 1. 生成唯一标题
        $title_result = $this->generate_unique_title($args);
        if (!$title_result['success']) {
            AIACG_Database::update_history($history_id, array(
                'status' => 'failed',
                'error_message' => $title_result['error'],
            ));
            return $title_result;
        }

        $title = $title_result['title'];
        $angle = $title_result['angle'];

        // 2. 生成文章内容
        $content_result = $this->generate_article_content($title, $args, $angle);
        if (!$content_result['success']) {
            AIACG_Database::update_history($history_id, array(
                'status' => 'failed',
                'error_message' => $content_result['error'],
                'generated_title' => $title,
                'writing_angle' => $angle,
            ));
            return $content_result;
        }

        // 3. 生成摘要
        $excerpt = $this->generate_excerpt($content_result['content'], $args);

        // 4. 生成标签
        $tags = array();
        if ($args['auto_tags']) {
            $tags = $this->generate_tags($title, $content_result['content'], $args);
        }

        // 5. 创建WordPress文章
        $post_data = array(
            'post_title' => $title,
            'post_content' => $content_result['content'],
            'post_excerpt' => $excerpt,
            'post_status' => $args['publish_mode'] === 'draft' ? 'draft' : 'publish',
            'post_category' => array($args['category']),
            'post_author' => get_current_user_id() > 0 ? get_current_user_id() : 1,
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            AIACG_Database::update_history($history_id, array(
                'status' => 'failed',
                'error_message' => $post_id->get_error_message(),
                'generated_title' => $title,
                'writing_angle' => $angle,
            ));

            return array(
                'success' => false,
                'post_id' => 0,
                'error' => $post_id->get_error_message(),
                'details' => array(),
            );
        }

        // 添加标签
        if (!empty($tags)) {
            wp_set_post_tags($post_id, $tags, false);
        }

        // 添加SEO meta（如果启用）
        if ($args['seo_optimization']) {
            $this->add_seo_meta($post_id, $title, $excerpt, $tags);
        }

        // 更新历史记录
        AIACG_Database::update_history($history_id, array(
            'post_id' => $post_id,
            'generated_title' => $title,
            'status' => 'success',
            'api_used' => $content_result['api_used'],
            'tokens_used' => $content_result['tokens'],
            'word_count' => str_word_count(strip_tags($content_result['content'])),
            'writing_angle' => $angle,
            'cost_estimate' => $content_result['cost'],
            'prompt_used' => $content_result['prompt_used'],
        ));

        AIACG_Database::log('Content generated successfully', 'info', array(
            'post_id' => $post_id,
            'title' => $title,
        ));

        return array(
            'success' => true,
            'post_id' => $post_id,
            'error' => '',
            'details' => array(
                'title' => $title,
                'word_count' => str_word_count(strip_tags($content_result['content'])),
                'tags' => $tags,
                'angle' => $angle,
                'api_used' => $content_result['api_used'],
                'tokens' => $content_result['tokens'],
                'cost' => $content_result['cost'],
            ),
        );
    }

    /**
     * 生成唯一标题（带防重复机制）
     *
     * @param array $args 参数
     * @return array
     */
    private function generate_unique_title($args) {
        $max_attempts = 5;
        $existing_titles = AIACG_Database::get_all_titles();

        for ($attempt = 0; $attempt < $max_attempts; $attempt++) {
            // 随机选择一个写作角度
            $angle_keys = array_keys($this->writing_angles);
            $angle_key = $angle_keys[array_rand($angle_keys)];
            $angle_description = $this->writing_angles[$angle_key];

            // 构建标题生成提示词
            $prompt = $this->build_title_prompt($args, $angle_description, $attempt);

            $result = $this->ai_manager->generate_content($prompt, array(
                'max_tokens' => 100,
                'temperature' => 0.8 + ($attempt * 0.05), // 每次尝试增加温度以获得更多变化
            ));

            if (!$result['success']) {
                continue;
            }

            // 清理和验证标题
            $title = $this->clean_title($result['content']);

            // 检查唯一性
            if ($this->is_title_unique($title, $existing_titles)) {
                return array(
                    'success' => true,
                    'title' => $title,
                    'angle' => $angle_key,
                    'error' => '',
                );
            }

            $attempt_num = $attempt + 1;
            AIACG_Database::log(
                "Generated title is too similar to existing titles, retrying (attempt {$attempt_num})",
                'warning',
                array('title' => $title)
            );
        }

        return array(
            'success' => false,
            'title' => '',
            'angle' => '',
            'error' => __('Failed to generate unique title after multiple attempts', 'ai-auto-content-generator'),
        );
    }

    /**
     * 构建标题生成提示词
     *
     * @param array $args 参数
     * @param string $angle_description 写作角度描述
     * @param int $attempt 尝试次数
     * @return string
     */
    private function build_title_prompt($args, $angle_description, $attempt) {
        $current_date = current_time('F j, Y');
        $sub_topics_str = !empty($args['sub_topics']) ? implode(', ', $args['sub_topics']) : '';

        $prompt = "Generate a unique, SEO-friendly article title based on these requirements:\n\n";
        $prompt .= "Main Topic: {$args['topic']}\n";

        if (!empty($sub_topics_str)) {
            $prompt .= "Sub-topics/Keywords: {$sub_topics_str}\n";
        }

        $prompt .= "Writing Angle: {$angle_description}\n";
        $prompt .= "Date Context: {$current_date}\n";
        $prompt .= "Target Audience: {$args['target_audience']}\n\n";

        $prompt .= "Requirements:\n";
        $prompt .= "- Create a compelling, click-worthy title\n";
        $prompt .= "- Length: 40-70 characters\n";
        $prompt .= "- Include relevant keywords naturally\n";
        $prompt .= "- Make it unique and specific\n";
        $prompt .= "- Use active voice\n";

        if ($attempt > 0) {
            $attempt_num = $attempt + 1;
            $prompt .= "- This is attempt #{$attempt_num}, make it significantly different from previous attempts\n";
        }

        $prompt .= "\nReturn ONLY the title, nothing else.";

        return $prompt;
    }

    /**
     * 生成文章内容
     *
     * @param string $title 标题
     * @param array $args 参数
     * @param string $angle 写作角度
     * @return array
     */
    private function generate_article_content($title, $args, $angle) {
        $system_prompt = get_option('aiacg_system_prompt', $this->get_default_system_prompt());
        $user_prompt_template = get_option('aiacg_user_prompt_template', $this->get_default_user_prompt());

        // 替换变量
        $replacements = array(
            '{title}' => $title,
            '{topic}' => $args['topic'],
            '{word_count}' => $args['word_count'],
            '{style}' => $args['writing_style'],
            '{angle}' => $this->writing_angles[$angle],
            '{date}' => current_time('F j, Y'),
            '{audience}' => $args['target_audience'],
        );

        $user_prompt = str_replace(array_keys($replacements), array_values($replacements), $user_prompt_template);

        $result = $this->ai_manager->generate_content($user_prompt, array(
            'max_tokens' => intval($args['word_count'] * 2), // 粗略估算
            'temperature' => 0.7,
            'system_prompt' => $system_prompt,
        ));

        if ($result['success']) {
            $result['prompt_used'] = $user_prompt;
        }

        return $result;
    }

    /**
     * 生成摘要
     *
     * @param string $content 内容
     * @param array $args 参数
     * @return string
     */
    private function generate_excerpt($content, $args) {
        // 移除HTML标签
        $plain_content = wp_strip_all_tags($content);

        // 如果内容较短，直接截取
        if (mb_strlen($plain_content) < 300) {
            return $plain_content;
        }

        // 使用AI生成摘要
        $prompt = "Summarize the following article in 2-3 sentences (maximum 150 words):\n\n{$plain_content}";

        $result = $this->ai_manager->generate_content($prompt, array(
            'max_tokens' => 200,
            'temperature' => 0.5,
        ));

        if ($result['success']) {
            return wp_strip_all_tags($result['content']);
        }

        // 如果AI生成失败，使用简单截取
        return mb_substr($plain_content, 0, 150) . '...';
    }

    /**
     * 生成标签
     *
     * @param string $title 标题
     * @param string $content 内容
     * @param array $args 参数
     * @return array
     */
    private function generate_tags($title, $content, $args) {
        $prompt = "Extract 5-8 relevant tags/keywords from the following article:\n\n";
        $prompt .= "Title: {$title}\n\n";
        $prompt .= "Content: " . wp_strip_all_tags(mb_substr($content, 0, 500)) . "...\n\n";
        $prompt .= "Return tags as a comma-separated list.";

        $result = $this->ai_manager->generate_content($prompt, array(
            'max_tokens' => 100,
            'temperature' => 0.5,
        ));

        if ($result['success']) {
            $tags_str = $result['content'];
            $tags = array_map('trim', explode(',', $tags_str));
            // 清理和验证标签
            $tags = array_filter($tags, function($tag) {
                return !empty($tag) && mb_strlen($tag) > 2 && mb_strlen($tag) < 50;
            });
            return array_slice($tags, 0, 8);
        }

        return array();
    }

    /**
     * 清理标题
     *
     * @param string $title 原始标题
     * @return string
     */
    private function clean_title($title) {
        // 移除多余的引号、换行符等
        $title = trim($title);
        $title = preg_replace('/^["\']+|["\']+$/', '', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $title = wp_strip_all_tags($title);

        return $title;
    }

    /**
     * 检查标题唯一性
     *
     * @param string $title 标题
     * @param array $existing_titles 现有标题列表
     * @return bool
     */
    private function is_title_unique($title, $existing_titles) {
        $similarity_threshold = 85; // 相似度阈值（百分比）

        foreach ($existing_titles as $existing_title) {
            $similarity = $this->calculate_similarity($title, $existing_title);

            if ($similarity >= $similarity_threshold) {
                return false;
            }
        }

        // 也检查WordPress中的现有文章
        $existing_post = get_page_by_title($title, OBJECT, 'post');
        if ($existing_post) {
            return false;
        }

        return true;
    }

    /**
     * 计算两个字符串的相似度
     *
     * @param string $str1 字符串1
     * @param string $str2 字符串2
     * @return float 相似度百分比
     */
    private function calculate_similarity($str1, $str2) {
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        // Levenshtein函数限制字符串长度不超过255字符
        // 如果超过，截取前255字符进行比较
        if (strlen($str1) > 255) {
            $str1 = substr($str1, 0, 255);
        }
        if (strlen($str2) > 255) {
            $str2 = substr($str2, 0, 255);
        }

        // 使用Levenshtein距离算法
        $lev = levenshtein($str1, $str2);
        $max_len = max(strlen($str1), strlen($str2));

        if ($max_len == 0) {
            return 100;
        }

        $similarity = (1 - ($lev / $max_len)) * 100;

        return $similarity;
    }

    /**
     * 添加SEO元数据
     *
     * @param int $post_id 文章ID
     * @param string $title 标题
     * @param string $excerpt 摘要
     * @param array $tags 标签
     */
    private function add_seo_meta($post_id, $title, $excerpt, $tags) {
        // 添加meta描述
        update_post_meta($post_id, '_aiacg_meta_description', $excerpt);

        // 添加关键词
        if (!empty($tags)) {
            update_post_meta($post_id, '_aiacg_meta_keywords', implode(', ', $tags));
        }

        // 如果安装了Yoast SEO或Rank Math，添加兼容性
        if (defined('WPSEO_VERSION')) {
            update_post_meta($post_id, '_yoast_wpseo_metadesc', $excerpt);
        }

        if (defined('RANK_MATH_VERSION')) {
            update_post_meta($post_id, 'rank_math_description', $excerpt);
        }
    }

    /**
     * 获取默认系统提示词
     *
     * @return string
     */
    private function get_default_system_prompt() {
        return "You are a professional content writer. Create high-quality, engaging, and SEO-optimized articles. " .
               "Write in a clear, informative style with proper structure including introduction, body paragraphs, and conclusion. " .
               "Use headings (H2, H3) to organize content. Include relevant examples and actionable insights.";
    }

    /**
     * 获取默认用户提示词模板
     *
     * @return string
     */
    private function get_default_user_prompt() {
        return "Write a comprehensive article with the following specifications:\n\n" .
               "Title: {title}\n" .
               "Topic: {topic}\n" .
               "Target Word Count: {word_count} words\n" .
               "Writing Style: {style}\n" .
               "Angle: {angle}\n" .
               "Date Context: {date}\n" .
               "Target Audience: {audience}\n\n" .
               "Requirements:\n" .
               "- Use proper HTML formatting (p, h2, h3, ul, ol tags)\n" .
               "- Include 3-5 subheadings\n" .
               "- Write engaging introduction and strong conclusion\n" .
               "- Include practical examples where appropriate\n" .
               "- Ensure content is original and valuable\n" .
               "- Optimize for SEO without keyword stuffing\n\n" .
               "Return ONLY the article content in HTML format, without the title.";
    }

    /**
     * 批量生成文章
     *
     * @param int $count 生成数量
     * @param array $args 参数
     * @return array
     */
    public function generate_multiple_posts($count, $args = array()) {
        $results = array(
            'success' => array(),
            'failed' => array(),
            'total' => $count,
        );

        $publish_interval = get_option('aiacg_publish_interval', 30); // 分钟

        for ($i = 0; $i < $count; $i++) {
            $result = $this->generate_post($args);

            if ($result['success']) {
                $results['success'][] = $result;

                // 如果不是草稿模式且设置了发布间隔，等待
                if ($args['publish_mode'] !== 'draft' && $publish_interval > 0 && $i < $count - 1) {
                    // 设置文章发布时间为当前时间加上间隔
                    $publish_time = current_time('mysql');
                    $timestamp = strtotime($publish_time) + ($i * $publish_interval * 60);
                    wp_update_post(array(
                        'ID' => $result['post_id'],
                        'post_date' => date('Y-m-d H:i:s', $timestamp),
                        'post_date_gmt' => gmdate('Y-m-d H:i:s', $timestamp),
                    ));
                }
            } else {
                $results['failed'][] = $result;
            }

            // 防止过载，每篇文章之间稍作延迟
            if ($i < $count - 1) {
                sleep(2);
            }
        }

        return $results;
    }
}
