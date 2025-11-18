<?php
/**
 * API配置Tab
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$ai_manager = new AIACG_AI_Manager();
$available_apis = $ai_manager->get_available_apis();
$usage_stats = $ai_manager->get_all_usage_stats();
?>

<form method="post" action="options.php">
    <?php settings_fields('aiacg_api_settings'); ?>

    <table class="form-table">
        <tr>
            <th colspan="2"><h2><?php _e('API Strategy', 'ai-auto-content-generator'); ?></h2></th>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_active_api"><?php _e('Active API', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <select id="aiacg_active_api" name="aiacg_active_api">
                    <?php
                    $active_api = get_option('aiacg_active_api', 'gemini');
                    foreach ($available_apis as $key => $api) {
                        $label = $api['name'];
                        if (!$api['configured']) {
                            $label .= ' (' . __('Not Configured', 'ai-auto-content-generator') . ')';
                        }
                        echo '<option value="' . esc_attr($key) . '"' . selected($active_api, $key, false) . '>' . esc_html($label) . '</option>';
                    }
                    ?>
                </select>
                <p class="description"><?php _e('Select which AI API to use by default', 'ai-auto-content-generator'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_enable_api_rotation"><?php _e('Enable API Rotation', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <label>
                    <input type="checkbox" id="aiacg_enable_api_rotation" name="aiacg_enable_api_rotation" value="1"
                           <?php checked(get_option('aiacg_enable_api_rotation', false)); ?>>
                    <?php _e('Use multiple APIs in rotation based on priority', 'ai-auto-content-generator'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_auto_switch_on_failure"><?php _e('Auto Switch on Failure', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <label>
                    <input type="checkbox" id="aiacg_auto_switch_on_failure" name="aiacg_auto_switch_on_failure" value="1"
                           <?php checked(get_option('aiacg_auto_switch_on_failure', true)); ?>>
                    <?php _e('Automatically switch to backup API if primary fails', 'ai-auto-content-generator'); ?>
                </label>
            </td>
        </tr>

        <!-- Gemini API -->
        <tr>
            <th colspan="2"><h2><?php _e('Google Gemini API', 'ai-auto-content-generator'); ?></h2></th>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_gemini_api_key"><?php _e('API Key', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="password" id="aiacg_gemini_api_key" name="aiacg_gemini_api_key"
                       value="<?php echo esc_attr(get_option('aiacg_gemini_api_key', '')); ?>"
                       class="regular-text">
                <button type="button" class="button aiacg-test-api" data-api="gemini">
                    <?php _e('Test Connection', 'ai-auto-content-generator'); ?>
                </button>
                <p class="description">
                    <?php _e('Get your API key from', 'ai-auto-content-generator'); ?>
                    <a href="https://ai.google.dev/gemini-api/docs/api-key" target="_blank">Google AI Studio</a>
                </p>
                <div class="aiacg-test-result" data-api="gemini"></div>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_gemini_model"><?php _e('Model', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <select id="aiacg_gemini_model" name="aiacg_gemini_model">
                    <?php
                    $gemini_models = $available_apis['gemini']['models'];
                    $current_model = get_option('aiacg_gemini_model', 'gemini-1.5-pro');
                    foreach ($gemini_models as $value => $label) {
                        echo '<option value="' . esc_attr($value) . '"' . selected($current_model, $value, false) . '>' . esc_html($label) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_gemini_temperature"><?php _e('Temperature', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="range" id="aiacg_gemini_temperature" name="aiacg_gemini_temperature"
                       value="<?php echo esc_attr(get_option('aiacg_gemini_temperature', 0.7)); ?>"
                       min="0" max="1" step="0.1"
                       oninput="this.nextElementSibling.value = this.value">
                <output><?php echo esc_html(get_option('aiacg_gemini_temperature', 0.7)); ?></output>
                <p class="description"><?php _e('Controls randomness (0 = focused, 1 = creative)', 'ai-auto-content-generator'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_gemini_max_tokens"><?php _e('Max Tokens', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="number" id="aiacg_gemini_max_tokens" name="aiacg_gemini_max_tokens"
                       value="<?php echo esc_attr(get_option('aiacg_gemini_max_tokens', 2048)); ?>"
                       min="100" max="8000" step="100">
            </td>
        </tr>
        <?php if (isset($usage_stats['gemini'])): ?>
        <tr>
            <th scope="row"><?php _e('Usage Statistics', 'ai-auto-content-generator'); ?></th>
            <td>
                <p><strong><?php _e('Total Calls:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html($usage_stats['gemini']['total_calls']); ?></p>
                <p><strong><?php _e('Total Tokens:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html(number_format($usage_stats['gemini']['total_tokens'])); ?></p>
                <p><strong><?php _e('Total Cost:', 'ai-auto-content-generator'); ?></strong> $<?php echo esc_html(number_format($usage_stats['gemini']['total_cost'], 4)); ?></p>
                <p><strong><?php _e('Avg Latency:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html($usage_stats['gemini']['avg_latency']); ?>ms</p>
            </td>
        </tr>
        <?php endif; ?>

        <!-- DeepSeek API -->
        <tr>
            <th colspan="2"><h2><?php _e('DeepSeek API', 'ai-auto-content-generator'); ?></h2></th>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_deepseek_api_key"><?php _e('API Key', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="password" id="aiacg_deepseek_api_key" name="aiacg_deepseek_api_key"
                       value="<?php echo esc_attr(get_option('aiacg_deepseek_api_key', '')); ?>"
                       class="regular-text">
                <button type="button" class="button aiacg-test-api" data-api="deepseek">
                    <?php _e('Test Connection', 'ai-auto-content-generator'); ?>
                </button>
                <p class="description">
                    <?php _e('Get your API key from', 'ai-auto-content-generator'); ?>
                    <a href="https://platform.deepseek.com/api_keys" target="_blank">DeepSeek Platform</a>
                </p>
                <div class="aiacg-test-result" data-api="deepseek"></div>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_deepseek_model"><?php _e('Model', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <select id="aiacg_deepseek_model" name="aiacg_deepseek_model">
                    <?php
                    $deepseek_models = $available_apis['deepseek']['models'];
                    $current_model = get_option('aiacg_deepseek_model', 'deepseek-chat');
                    foreach ($deepseek_models as $value => $label) {
                        echo '<option value="' . esc_attr($value) . '"' . selected($current_model, $value, false) . '>' . esc_html($label) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_deepseek_temperature"><?php _e('Temperature', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="range" id="aiacg_deepseek_temperature" name="aiacg_deepseek_temperature"
                       value="<?php echo esc_attr(get_option('aiacg_deepseek_temperature', 0.7)); ?>"
                       min="0" max="1" step="0.1"
                       oninput="this.nextElementSibling.value = this.value">
                <output><?php echo esc_html(get_option('aiacg_deepseek_temperature', 0.7)); ?></output>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_deepseek_max_tokens"><?php _e('Max Tokens', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="number" id="aiacg_deepseek_max_tokens" name="aiacg_deepseek_max_tokens"
                       value="<?php echo esc_attr(get_option('aiacg_deepseek_max_tokens', 2048)); ?>"
                       min="100" max="8000" step="100">
            </td>
        </tr>
        <?php if (isset($usage_stats['deepseek'])): ?>
        <tr>
            <th scope="row"><?php _e('Usage Statistics', 'ai-auto-content-generator'); ?></th>
            <td>
                <p><strong><?php _e('Total Calls:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html($usage_stats['deepseek']['total_calls']); ?></p>
                <p><strong><?php _e('Total Tokens:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html(number_format($usage_stats['deepseek']['total_tokens'])); ?></p>
                <p><strong><?php _e('Total Cost:', 'ai-auto-content-generator'); ?></strong> $<?php echo esc_html(number_format($usage_stats['deepseek']['total_cost'], 4)); ?></p>
                <p><strong><?php _e('Avg Latency:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html($usage_stats['deepseek']['avg_latency']); ?>ms</p>
            </td>
        </tr>
        <?php endif; ?>

        <!-- OpenAI API -->
        <tr>
            <th colspan="2"><h2><?php _e('OpenAI API', 'ai-auto-content-generator'); ?></h2></th>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_openai_api_key"><?php _e('API Key', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="password" id="aiacg_openai_api_key" name="aiacg_openai_api_key"
                       value="<?php echo esc_attr(get_option('aiacg_openai_api_key', '')); ?>"
                       class="regular-text">
                <button type="button" class="button aiacg-test-api" data-api="openai">
                    <?php _e('Test Connection', 'ai-auto-content-generator'); ?>
                </button>
                <p class="description">
                    <?php _e('Get your API key from', 'ai-auto-content-generator'); ?>
                    <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a>
                </p>
                <div class="aiacg-test-result" data-api="openai"></div>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_openai_model"><?php _e('Model', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <select id="aiacg_openai_model" name="aiacg_openai_model">
                    <?php
                    $openai_models = $available_apis['openai']['models'];
                    $current_model = get_option('aiacg_openai_model', 'gpt-3.5-turbo');
                    foreach ($openai_models as $value => $label) {
                        echo '<option value="' . esc_attr($value) . '"' . selected($current_model, $value, false) . '>' . esc_html($label) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_openai_temperature"><?php _e('Temperature', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="range" id="aiacg_openai_temperature" name="aiacg_openai_temperature"
                       value="<?php echo esc_attr(get_option('aiacg_openai_temperature', 0.7)); ?>"
                       min="0" max="1" step="0.1"
                       oninput="this.nextElementSibling.value = this.value">
                <output><?php echo esc_html(get_option('aiacg_openai_temperature', 0.7)); ?></output>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="aiacg_openai_max_tokens"><?php _e('Max Tokens', 'ai-auto-content-generator'); ?></label>
            </th>
            <td>
                <input type="number" id="aiacg_openai_max_tokens" name="aiacg_openai_max_tokens"
                       value="<?php echo esc_attr(get_option('aiacg_openai_max_tokens', 2048)); ?>"
                       min="100" max="8000" step="100">
            </td>
        </tr>
        <?php if (isset($usage_stats['openai'])): ?>
        <tr>
            <th scope="row"><?php _e('Usage Statistics', 'ai-auto-content-generator'); ?></th>
            <td>
                <p><strong><?php _e('Total Calls:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html($usage_stats['openai']['total_calls']); ?></p>
                <p><strong><?php _e('Total Tokens:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html(number_format($usage_stats['openai']['total_tokens'])); ?></p>
                <p><strong><?php _e('Total Cost:', 'ai-auto-content-generator'); ?></strong> $<?php echo esc_html(number_format($usage_stats['openai']['total_cost'], 4)); ?></p>
                <p><strong><?php _e('Avg Latency:', 'ai-auto-content-generator'); ?></strong> <?php echo esc_html($usage_stats['openai']['avg_latency']); ?>ms</p>
            </td>
        </tr>
        <?php endif; ?>
    </table>

    <?php submit_button(); ?>
</form>
