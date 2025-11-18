<?php
/**
 * Plugin Uninstall Handler
 *
 * Fired when the plugin is uninstalled.
 * This file handles complete cleanup of all plugin data.
 *
 * @package    AI_Auto_Content_Generator
 * @since      1.0.1
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Complete Plugin Cleanup
 *
 * This removes all traces of the plugin from WordPress:
 * - Database tables
 * - Options
 * - Scheduled cron tasks
 * - Transients
 *
 * NOTE: Generated WordPress posts are NOT deleted as they are considered
 * user content that should persist even after plugin removal.
 */
function aiacg_uninstall_cleanup() {
    global $wpdb;

    // 1. Drop custom database tables
    $table_name = $wpdb->prefix . 'aiacg_content_history';
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

    // 2. Delete all plugin options
    $options_to_delete = array(
        // Basic Settings
        'aiacg_main_topic',
        'aiacg_topic_description',
        'aiacg_sub_topics',
        'aiacg_writing_style',
        'aiacg_target_audience',
        'aiacg_daily_post_count',
        'aiacg_word_count',
        'aiacg_generation_time',
        'aiacg_publish_mode',
        'aiacg_publish_interval',
        'aiacg_default_category',
        'aiacg_auto_tags',
        'aiacg_seo_optimization',

        // API Settings
        'aiacg_active_api',
        'aiacg_enable_api_rotation',
        'aiacg_api_priority',
        'aiacg_auto_switch_on_failure',

        // Gemini Settings
        'aiacg_gemini_api_key',
        'aiacg_gemini_model',
        'aiacg_gemini_temperature',
        'aiacg_gemini_max_tokens',
        'aiacg_gemini_usage_stats',

        // DeepSeek Settings
        'aiacg_deepseek_api_key',
        'aiacg_deepseek_model',
        'aiacg_deepseek_temperature',
        'aiacg_deepseek_max_tokens',
        'aiacg_deepseek_usage_stats',

        // OpenAI Settings
        'aiacg_openai_api_key',
        'aiacg_openai_model',
        'aiacg_openai_temperature',
        'aiacg_openai_max_tokens',
        'aiacg_openai_usage_stats',

        // Template Settings
        'aiacg_system_prompt',
        'aiacg_user_prompt_template',
        'aiacg_writing_angles',
        'aiacg_title_min_length',
        'aiacg_title_max_length',

        // Other Settings
        'aiacg_enable_logging',
        'aiacg_log_retention_days',
        'aiacg_history_retention_days',
        'aiacg_logs',

        // System Data
        'aiacg_version',
        'aiacg_last_generation_time',
        'aiacg_last_generation_result',
        'aiacg_db_version',
    );

    foreach ($options_to_delete as $option) {
        delete_option($option);
    }

    // 3. Clear all scheduled cron tasks
    $cron_hooks = array(
        'aiacg_daily_generation',
        'aiacg_cleanup',
    );

    foreach ($cron_hooks as $hook) {
        $timestamp = wp_next_scheduled($hook);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook);
        }
        // Clear all instances of the hook
        wp_clear_scheduled_hook($hook);
    }

    // 4. Delete any transients
    delete_transient('aiacg_api_test_result');
    delete_transient('aiacg_generation_in_progress');

    // 5. Delete any user meta (if plugin stored any)
    // Currently none, but keeping for future extensibility

    // 6. Clean up post meta (SEO data added by plugin)
    // Note: We keep the posts themselves, but optionally clean up plugin-specific meta
    // Uncomment the following if you want to remove plugin meta from posts:
    /*
    $wpdb->query(
        "DELETE FROM {$wpdb->postmeta}
         WHERE meta_key LIKE 'aiacg_%'"
    );
    */
}

// Run the cleanup
aiacg_uninstall_cleanup();

// Log uninstallation (if error_log is enabled)
if (defined('WP_DEBUG') && WP_DEBUG === true) {
    error_log('AI Auto Content Generator: Plugin uninstalled and all data cleaned up');
}
