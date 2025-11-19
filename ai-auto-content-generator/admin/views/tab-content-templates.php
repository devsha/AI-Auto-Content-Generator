<?php
/**
 * Content Templates Tab View
 *
 * @package AI_Auto_Content_Generator
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$templates = AIACG_Content_Template_Manager::get_templates();
$writing_styles = AIACG_Content_Template_Manager::get_writing_styles();
$tones = AIACG_Content_Template_Manager::get_tones();
$categories = get_categories(array('hide_empty' => false));
?>

<div class="aiacg-content-templates-page">
    <h2><?php _e('Content Templates', 'ai-auto-content-generator'); ?></h2>
    <p class="description">
        <?php _e('Create reusable templates for consistent content generation with custom prompts, styles, and default settings.', 'ai-auto-content-generator'); ?>
    </p>

    <div class="templates-grid" id="aiacg-templates-grid">
        <?php foreach ($templates as $template_id => $template): ?>
            <div class="template-card" data-template-id="<?php echo esc_attr($template_id); ?>">
                <h3><?php echo esc_html($template['name']); ?></h3>
                <?php if (!empty($template['description'])): ?>
                    <p class="template-description"><?php echo esc_html($template['description']); ?></p>
                <?php endif; ?>
                <div class="template-meta">
                    <span><?php _e('Style:', 'ai-auto-content-generator'); ?> <?php echo esc_html($writing_styles[$template['writing_style']] ?? $template['writing_style']); ?></span> |
                    <span><?php _e('Tone:', 'ai-auto-content-generator'); ?> <?php echo esc_html($tones[$template['tone']] ?? $template['tone']); ?></span> |
                    <span><?php echo esc_html($template['word_count_min']); ?>-<?php echo esc_html($template['word_count_max']); ?> <?php _e('words', 'ai-auto-content-generator'); ?></span>
                </div>
                <div class="template-actions">
                    <?php if ($template_id !== 'default'): ?>
                        <button type="button" class="button aiacg-delete-template" data-id="<?php echo esc_attr($template_id); ?>"><?php _e('Delete', 'ai-auto-content-generator'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p><?php _e('Template management interface will be available in the next update.', 'ai-auto-content-generator'); ?></p>
</div>
