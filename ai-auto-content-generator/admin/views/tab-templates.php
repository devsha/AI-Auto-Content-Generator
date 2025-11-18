<?php
/**
 * 内容模板Tab
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$generator = new AIACG_Content_Generator();
?>

<form method="post" action="options.php">
    <?php settings_fields('aiacg_template_settings'); ?>

    <table class="form-table">
        <tr>
            <th colspan="2"><h2><?php _e('Prompt Templates', 'ai-auto-content-generator'); ?></h2></th>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_system_prompt"><?php _e('System Prompt', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <textarea id="aiacg_system_prompt" name="aiacg_system_prompt"
                          rows="5" class="large-text code"><?php echo esc_textarea(get_option('aiacg_system_prompt', 'You are a professional content writer. Create high-quality, engaging, and SEO-optimized articles.')); ?></textarea>
                <p class="description"><?php _e('This sets the AI\'s behavior and writing style', 'ai-auto-content-generator'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_user_prompt_template"><?php _e('User Prompt Template', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <textarea id="aiacg_user_prompt_template" name="aiacg_user_prompt_template"
                          rows="10" class="large-text code"><?php
$default_prompt = "Write a comprehensive article with the following specifications:\n\n" .
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
echo esc_textarea(get_option('aiacg_user_prompt_template', $default_prompt));
?></textarea>
                <p class="description">
                    <?php _e('Available variables:', 'ai-auto-content-generator'); ?>
                    <code>{title}</code>, <code>{topic}</code>, <code>{word_count}</code>,
                    <code>{style}</code>, <code>{angle}</code>, <code>{date}</code>, <code>{audience}</code>
                </p>
            </td>
        </tr>

        <tr>
            <th colspan="2"><h2><?php _e('Writing Angles', 'ai-auto-content-generator'); ?></h2></th>
        </tr>
        <tr>
            <td colspan="2">
                <p class="description"><?php _e('Define different writing angles to ensure content diversity. Each angle will be randomly selected for new articles.', 'ai-auto-content-generator'); ?></p>

                <?php
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

                $saved_angles = get_option('aiacg_writing_angles', $default_angles);
                ?>

                <table class="widefat" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th><?php _e('Angle Key', 'ai-auto-content-generator'); ?></th>
                            <th><?php _e('Description', 'ai-auto-content-generator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saved_angles as $key => $description): ?>
                        <tr>
                            <td><strong><?php echo esc_html($key); ?></strong></td>
                            <td><?php echo esc_html($description); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p style="margin-top: 10px;">
                    <em><?php _e('Note: To customize writing angles, you can modify them via the database or filters.', 'ai-auto-content-generator'); ?></em>
                </p>
            </td>
        </tr>

        <tr>
            <th colspan="2"><h2><?php _e('Title Generation Rules', 'ai-auto-content-generator'); ?></h2></th>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_title_min_length"><?php _e('Minimum Title Length', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="number" id="aiacg_title_min_length" name="aiacg_title_min_length"
                       value="<?php echo esc_attr(get_option('aiacg_title_min_length', 40)); ?>"
                       min="10" max="100" step="5">
                <?php _e('characters', 'ai-auto-content-generator'); ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_title_max_length"><?php _e('Maximum Title Length', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="number" id="aiacg_title_max_length" name="aiacg_title_max_length"
                       value="<?php echo esc_attr(get_option('aiacg_title_max_length', 70)); ?>"
                       min="20" max="150" step="5">
                <?php _e('characters', 'ai-auto-content-generator'); ?>
            </td>
        </tr>
    </table>

    <?php submit_button(); ?>
</form>
