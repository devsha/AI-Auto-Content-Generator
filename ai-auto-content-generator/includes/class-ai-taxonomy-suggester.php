<?php
/**
 * AI Taxonomy Suggester
 *
 * Uses AI to suggest categories and tags for content
 *
 * @package AI_Auto_Content_Generator
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_AI_Taxonomy_Suggester {

    /**
     * Get taxonomy suggestions for content
     *
     * @param string $title Post title
     * @param string $content Post content
     * @param array $options Suggestion options
     * @return array|WP_Error Suggestions or error
     */
    public static function get_suggestions($title, $content, $options = array()) {
        $defaults = array(
            'max_categories' => 2,
            'max_tags' => 5,
            'use_existing_only' => false,
            'api' => 'auto', // auto, gemini, deepseek, openai
        );

        $options = wp_parse_args($options, $defaults);

        // Get existing categories and tags for context
        $existing_categories = get_categories(array('hide_empty' => false));
        $existing_tags = get_tags(array('hide_empty' => false));

        // Build AI prompt
        $prompt = self::build_suggestion_prompt($title, $content, $existing_categories, $existing_tags, $options);

        // Call AI API
        $api_result = self::call_ai_api($prompt, $options['api']);

        if (is_wp_error($api_result)) {
            return $api_result;
        }

        // Parse AI response
        $suggestions = self::parse_ai_response($api_result, $existing_categories, $existing_tags, $options);

        return $suggestions;
    }

    /**
     * Build AI prompt for taxonomy suggestions
     *
     * @param string $title Post title
     * @param string $content Post content
     * @param array $existing_categories Existing categories
     * @param array $existing_tags Existing tags
     * @param array $options Options
     * @return string Prompt
     */
    private static function build_suggestion_prompt($title, $content, $existing_categories, $existing_tags, $options) {
        $prompt = "Analyze the following blog post and suggest appropriate categories and tags.\n\n";
        $prompt .= "Title: " . $title . "\n\n";
        $prompt .= "Content: " . wp_strip_all_tags(substr($content, 0, 2000)) . "\n\n";

        if ($options['use_existing_only'] && !empty($existing_categories)) {
            $category_names = wp_list_pluck($existing_categories, 'name');
            $prompt .= "Choose ONLY from these existing categories:\n" . implode(', ', $category_names) . "\n\n";
        } else {
            if (!empty($existing_categories)) {
                $category_names = wp_list_pluck($existing_categories, 'name');
                $prompt .= "Existing categories (prefer these if relevant): " . implode(', ', $category_names) . "\n";
            }
            $prompt .= "You may suggest new categories if needed.\n\n";
        }

        if ($options['use_existing_only'] && !empty($existing_tags)) {
            $tag_names = wp_list_pluck($existing_tags, 'name');
            $prompt .= "Choose ONLY from these existing tags:\n" . implode(', ', $tag_names) . "\n\n";
        } else {
            if (!empty($existing_tags)) {
                $tag_names = wp_list_pluck($existing_tags, 'name');
                $prompt .= "Existing tags (prefer these if relevant): " . implode(', ', $tag_names) . "\n";
            }
            $prompt .= "You may suggest new tags if needed.\n\n";
        }

        $prompt .= sprintf("Suggest up to %d categories and up to %d tags.\n\n", $options['max_categories'], $options['max_tags']);
        $prompt .= "Respond in JSON format:\n";
        $prompt .= "{\n";
        $prompt .= '  "categories": ["category1", "category2"],'."\n";
        $prompt .= '  "tags": ["tag1", "tag2", "tag3"],'."\n";
        $prompt .= '  "reasoning": "Brief explanation of why these were chosen"'."\n";
        $prompt .= "}";

        return $prompt;
    }

    /**
     * Call AI API for suggestions
     *
     * @param string $prompt AI prompt
     * @param string $api_preference API preference
     * @return string|WP_Error AI response or error
     */
    private static function call_ai_api($prompt, $api_preference) {
        // Determine which API to use
        $api_to_use = $api_preference;

        if ($api_preference === 'auto') {
            // Auto-select based on availability
            if (get_option('aiacg_gemini_api_key')) {
                $api_to_use = 'gemini';
            } elseif (get_option('aiacg_deepseek_api_key')) {
                $api_to_use = 'deepseek';
            } elseif (get_option('aiacg_openai_api_key')) {
                $api_to_use = 'openai';
            } else {
                return new WP_Error('no_api_configured', __('No AI API configured', 'ai-auto-content-generator'));
            }
        }

        // Call the appropriate API
        try {
            switch ($api_to_use) {
                case 'gemini':
                    return self::call_gemini_api($prompt);
                case 'deepseek':
                    return self::call_deepseek_api($prompt);
                case 'openai':
                    return self::call_openai_api($prompt);
                default:
                    return new WP_Error('invalid_api', __('Invalid API specified', 'ai-auto-content-generator'));
            }
        } catch (Exception $e) {
            return new WP_Error('api_error', $e->getMessage());
        }
    }

    /**
     * Call Google Gemini API
     *
     * @param string $prompt Prompt
     * @return string|WP_Error Response
     */
    private static function call_gemini_api($prompt) {
        $api_key = get_option('aiacg_gemini_api_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('Gemini API key not configured', 'ai-auto-content-generator'));
        }

        $model = get_option('aiacg_gemini_model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$api_key}";

        $response = wp_remote_post($url, array(
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode(array(
                'contents' => array(
                    array('parts' => array(array('text' => $prompt))),
                ),
            )),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['candidates'][0]['content']['parts'][0]['text'])) {
            return $body['candidates'][0]['content']['parts'][0]['text'];
        }

        return new WP_Error('api_error', __('Unexpected API response format', 'ai-auto-content-generator'));
    }

    /**
     * Call DeepSeek API
     *
     * @param string $prompt Prompt
     * @return string|WP_Error Response
     */
    private static function call_deepseek_api($prompt) {
        $api_key = get_option('aiacg_deepseek_api_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('DeepSeek API key not configured', 'ai-auto-content-generator'));
        }

        $model = get_option('aiacg_deepseek_model', 'deepseek-chat');
        $url = 'https://api.deepseek.com/v1/chat/completions';

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body' => json_encode(array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'user', 'content' => $prompt),
                ),
            )),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['choices'][0]['message']['content'])) {
            return $body['choices'][0]['message']['content'];
        }

        return new WP_Error('api_error', __('Unexpected API response format', 'ai-auto-content-generator'));
    }

    /**
     * Call OpenAI API
     *
     * @param string $prompt Prompt
     * @return string|WP_Error Response
     */
    private static function call_openai_api($prompt) {
        $api_key = get_option('aiacg_openai_api_key');
        if (empty($api_key)) {
            return new WP_Error('no_api_key', __('OpenAI API key not configured', 'ai-auto-content-generator'));
        }

        $model = get_option('aiacg_openai_model', 'gpt-3.5-turbo');
        $url = 'https://api.openai.com/v1/chat/completions';

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ),
            'body' => json_encode(array(
                'model' => $model,
                'messages' => array(
                    array('role' => 'user', 'content' => $prompt),
                ),
            )),
            'timeout' => 30,
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['choices'][0]['message']['content'])) {
            return $body['choices'][0]['message']['content'];
        }

        return new WP_Error('api_error', __('Unexpected API response format', 'ai-auto-content-generator'));
    }

    /**
     * Parse AI response into structured suggestions
     *
     * @param string $ai_response AI response
     * @param array $existing_categories Existing categories
     * @param array $existing_tags Existing tags
     * @param array $options Options
     * @return array Parsed suggestions
     */
    private static function parse_ai_response($ai_response, $existing_categories, $existing_tags, $options) {
        $suggestions = array(
            'categories' => array(),
            'tags' => array(),
            'reasoning' => '',
            'new_categories' => array(),
            'new_tags' => array(),
        );

        // Try to extract JSON from response
        preg_match('/\{[\s\S]*\}/', $ai_response, $matches);
        if (empty($matches)) {
            return new WP_Error('parse_error', __('Could not parse AI response', 'ai-auto-content-generator'));
        }

        $json_data = json_decode($matches[0], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('Invalid JSON in AI response', 'ai-auto-content-generator'));
        }

        // Extract categories
        if (isset($json_data['categories']) && is_array($json_data['categories'])) {
            $existing_cat_names = wp_list_pluck($existing_categories, 'name');
            foreach (array_slice($json_data['categories'], 0, $options['max_categories']) as $cat_name) {
                $cat_name = trim($cat_name);
                if (in_array($cat_name, $existing_cat_names)) {
                    $suggestions['categories'][] = $cat_name;
                } elseif (!$options['use_existing_only']) {
                    $suggestions['new_categories'][] = $cat_name;
                }
            }
        }

        // Extract tags
        if (isset($json_data['tags']) && is_array($json_data['tags'])) {
            $existing_tag_names = wp_list_pluck($existing_tags, 'name');
            foreach (array_slice($json_data['tags'], 0, $options['max_tags']) as $tag_name) {
                $tag_name = trim($tag_name);
                if (in_array($tag_name, $existing_tag_names)) {
                    $suggestions['tags'][] = $tag_name;
                } elseif (!$options['use_existing_only']) {
                    $suggestions['new_tags'][] = $tag_name;
                }
            }
        }

        // Extract reasoning
        if (isset($json_data['reasoning'])) {
            $suggestions['reasoning'] = sanitize_text_field($json_data['reasoning']);
        }

        return $suggestions;
    }

    /**
     * Apply suggestions to a post
     *
     * @param int $post_id Post ID
     * @param array $suggestions Suggestions array
     * @param bool $create_new Whether to create new categories/tags
     * @return bool Success
     */
    public static function apply_suggestions($post_id, $suggestions, $create_new = true) {
        // Validate post exists
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }

        // Validate suggestions structure
        if (!is_array($suggestions)) {
            return false;
        }

        $category_ids = array();
        $tag_names = array();

        // Process categories
        if (!empty($suggestions['categories']) && is_array($suggestions['categories'])) {
            foreach ($suggestions['categories'] as $cat_name) {
                $cat = get_category_by_slug(sanitize_title($cat_name));
                if (!$cat) {
                    $cat = get_term_by('name', $cat_name, 'category');
                }
                if ($cat) {
                    $category_ids[] = $cat->term_id;
                }
            }
        }

        // Create new categories if allowed
        if ($create_new && !empty($suggestions['new_categories']) && is_array($suggestions['new_categories'])) {
            foreach ($suggestions['new_categories'] as $cat_name) {
                $result = wp_insert_term($cat_name, 'category');
                if (!is_wp_error($result)) {
                    $category_ids[] = $result['term_id'];
                }
            }
        }

        // Process tags
        $existing_tags = !empty($suggestions['tags']) && is_array($suggestions['tags']) ? $suggestions['tags'] : array();
        $new_tags = ($create_new && !empty($suggestions['new_tags']) && is_array($suggestions['new_tags'])) ? $suggestions['new_tags'] : array();
        $tag_names = array_merge($existing_tags, $new_tags);

        // Apply to post
        if (!empty($category_ids)) {
            wp_set_post_categories($post_id, $category_ids);
        }

        if (!empty($tag_names)) {
            wp_set_post_tags($post_id, $tag_names);
        }

        // Store suggestion metadata
        update_post_meta($post_id, '_aiacg_taxonomy_suggestions', $suggestions);
        update_post_meta($post_id, '_aiacg_taxonomy_suggested_at', current_time('mysql'));

        return true;
    }

    /**
     * Get suggestion statistics
     *
     * @return array Statistics
     */
    public static function get_suggestion_stats() {
        global $wpdb;

        $stats = array(
            'total_suggestions' => 0,
            'categories_suggested' => 0,
            'tags_suggested' => 0,
            'new_categories_created' => 0,
            'new_tags_created' => 0,
        );

        $count = $wpdb->get_var("
            SELECT COUNT(*)
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_aiacg_taxonomy_suggestions'
        ");

        $stats['total_suggestions'] = intval($count);

        return $stats;
    }
}
