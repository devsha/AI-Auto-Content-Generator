<?php
/**
 * Content Template Manager
 *
 * Manages content generation templates for reusability and consistency
 *
 * @package AI_Auto_Content_Generator
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Content_Template_Manager {

    /**
     * Get all templates
     *
     * @return array Array of template objects
     */
    public static function get_templates() {
        $templates = get_option('aiacg_content_templates', array());

        // Ensure we always have a default template
        if (empty($templates)) {
            $templates = array(
                'default' => self::get_default_template(),
            );
            update_option('aiacg_content_templates', $templates);
        }

        return $templates;
    }

    /**
     * Get a specific template by ID
     *
     * @param string $template_id Template ID
     * @return array|false Template data or false if not found
     */
    public static function get_template($template_id) {
        $templates = self::get_templates();
        return isset($templates[$template_id]) ? $templates[$template_id] : false;
    }

    /**
     * Save a template
     *
     * @param string $template_id Template ID (empty for new template)
     * @param array $template_data Template data
     * @return string|WP_Error Template ID on success, WP_Error on failure
     */
    public static function save_template($template_id, $template_data) {
        $templates = self::get_templates();

        // Validate required fields
        if (empty($template_data['name'])) {
            return new WP_Error('missing_name', __('Template name is required', 'ai-auto-content-generator'));
        }

        // Generate ID for new templates
        if (empty($template_id)) {
            $template_id = sanitize_title($template_data['name']) . '_' . time();
        }

        // Sanitize and prepare template data
        $template = array(
            'name' => sanitize_text_field($template_data['name']),
            'description' => sanitize_textarea_field($template_data['description'] ?? ''),
            'prompt_template' => sanitize_textarea_field($template_data['prompt_template'] ?? ''),
            'writing_style' => sanitize_text_field($template_data['writing_style'] ?? 'professional'),
            'tone' => sanitize_text_field($template_data['tone'] ?? 'neutral'),
            'word_count_min' => intval($template_data['word_count_min'] ?? 500),
            'word_count_max' => intval($template_data['word_count_max'] ?? 1500),
            'include_sections' => isset($template_data['include_sections']) ? (bool) $template_data['include_sections'] : true,
            'include_conclusion' => isset($template_data['include_conclusion']) ? (bool) $template_data['include_conclusion'] : true,
            'include_faq' => isset($template_data['include_faq']) ? (bool) $template_data['include_faq'] : false,
            'seo_focus' => isset($template_data['seo_focus']) ? (bool) $template_data['seo_focus'] : true,
            'default_category' => intval($template_data['default_category'] ?? 0),
            'default_tags' => sanitize_text_field($template_data['default_tags'] ?? ''),
            'post_status' => sanitize_text_field($template_data['post_status'] ?? 'draft'),
            'created_at' => $templates[$template_id]['created_at'] ?? current_time('mysql'),
            'updated_at' => current_time('mysql'),
        );

        $templates[$template_id] = $template;
        update_option('aiacg_content_templates', $templates);

        return $template_id;
    }

    /**
     * Delete a template
     *
     * @param string $template_id Template ID
     * @return bool Success
     */
    public static function delete_template($template_id) {
        // Prevent deletion of default template
        if ($template_id === 'default') {
            return false;
        }

        $templates = self::get_templates();

        if (isset($templates[$template_id])) {
            unset($templates[$template_id]);
            update_option('aiacg_content_templates', $templates);
            return true;
        }

        return false;
    }

    /**
     * Duplicate a template
     *
     * @param string $template_id Template ID to duplicate
     * @return string|WP_Error New template ID on success, WP_Error on failure
     */
    public static function duplicate_template($template_id) {
        $template = self::get_template($template_id);

        if (!$template) {
            return new WP_Error('template_not_found', __('Template not found', 'ai-auto-content-generator'));
        }

        // Modify template for duplication
        $template['name'] = $template['name'] . ' (Copy)';

        return self::save_template('', $template);
    }

    /**
     * Apply template to generation settings
     *
     * @param string $template_id Template ID
     * @param array $base_settings Base settings to merge with
     * @return array Combined settings
     */
    public static function apply_template($template_id, $base_settings = array()) {
        $template = self::get_template($template_id);

        if (!$template) {
            return $base_settings;
        }

        // Build enhanced prompt
        $prompt_parts = array();

        if (!empty($template['prompt_template'])) {
            $prompt_parts[] = $template['prompt_template'];
        }

        // Add style and tone instructions
        $style_instruction = sprintf(
            __('Writing Style: %s. Tone: %s.', 'ai-auto-content-generator'),
            $template['writing_style'],
            $template['tone']
        );
        $prompt_parts[] = $style_instruction;

        // Add word count requirement
        $word_count_instruction = sprintf(
            __('Target word count: %d-%d words.', 'ai-auto-content-generator'),
            $template['word_count_min'],
            $template['word_count_max']
        );
        $prompt_parts[] = $word_count_instruction;

        // Add section requirements
        $section_requirements = array();
        if ($template['include_sections']) {
            $section_requirements[] = __('multiple sections with headings', 'ai-auto-content-generator');
        }
        if ($template['include_conclusion']) {
            $section_requirements[] = __('a conclusion section', 'ai-auto-content-generator');
        }
        if ($template['include_faq']) {
            $section_requirements[] = __('an FAQ section', 'ai-auto-content-generator');
        }

        if (!empty($section_requirements)) {
            $prompt_parts[] = __('Include: ', 'ai-auto-content-generator') . implode(', ', $section_requirements) . '.';
        }

        // Add SEO focus
        if ($template['seo_focus']) {
            $prompt_parts[] = __('Optimize for SEO with proper keyword usage and structure.', 'ai-auto-content-generator');
        }

        // Merge with base settings
        $settings = array_merge($base_settings, array(
            'custom_prompt' => implode("\n\n", $prompt_parts),
            'word_count' => $template['word_count_max'],
            'post_status' => $template['post_status'],
        ));

        // Add category and tags if set
        if ($template['default_category'] > 0) {
            $settings['category_id'] = $template['default_category'];
        }
        if (!empty($template['default_tags'])) {
            $settings['tags'] = $template['default_tags'];
        }

        return $settings;
    }

    /**
     * Get default template structure
     *
     * @return array Default template
     */
    private static function get_default_template() {
        return array(
            'name' => __('Default Template', 'ai-auto-content-generator'),
            'description' => __('Standard blog post template', 'ai-auto-content-generator'),
            'prompt_template' => __('Write a comprehensive blog post about the given topic.', 'ai-auto-content-generator'),
            'writing_style' => 'professional',
            'tone' => 'neutral',
            'word_count_min' => 500,
            'word_count_max' => 1500,
            'include_sections' => true,
            'include_conclusion' => true,
            'include_faq' => false,
            'seo_focus' => true,
            'default_category' => 0,
            'default_tags' => '',
            'post_status' => 'draft',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        );
    }

    /**
     * Get available writing styles
     *
     * @return array Writing styles
     */
    public static function get_writing_styles() {
        return array(
            'professional' => __('Professional', 'ai-auto-content-generator'),
            'casual' => __('Casual', 'ai-auto-content-generator'),
            'academic' => __('Academic', 'ai-auto-content-generator'),
            'creative' => __('Creative', 'ai-auto-content-generator'),
            'technical' => __('Technical', 'ai-auto-content-generator'),
            'conversational' => __('Conversational', 'ai-auto-content-generator'),
        );
    }

    /**
     * Get available tones
     *
     * @return array Tones
     */
    public static function get_tones() {
        return array(
            'neutral' => __('Neutral', 'ai-auto-content-generator'),
            'formal' => __('Formal', 'ai-auto-content-generator'),
            'friendly' => __('Friendly', 'ai-auto-content-generator'),
            'authoritative' => __('Authoritative', 'ai-auto-content-generator'),
            'enthusiastic' => __('Enthusiastic', 'ai-auto-content-generator'),
            'informative' => __('Informative', 'ai-auto-content-generator'),
            'persuasive' => __('Persuasive', 'ai-auto-content-generator'),
        );
    }

    /**
     * Get template usage statistics
     *
     * @return array Statistics for each template
     */
    public static function get_template_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . AIACG_Database::TABLE_NAME;

        $templates = self::get_templates();
        $stats = array();

        foreach ($templates as $template_id => $template) {
            // Get usage count from history (stored in custom meta if implemented)
            // For now, return placeholder
            $stats[$template_id] = array(
                'usage_count' => 0,
                'last_used' => null,
                'avg_quality' => 0,
            );
        }

        return $stats;
    }
}
