<?php
/**
 * Settings Import/Export
 *
 * Handles exporting and importing plugin settings
 *
 * @package AI_Auto_Content_Generator
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Settings_Import_Export {

    /**
     * Export all plugin settings
     *
     * @param bool $include_api_keys Whether to include API keys (security risk!)
     * @return array Settings data
     */
    public static function export_settings($include_api_keys = false) {
        $settings = array(
            'version' => AIACG_VERSION,
            'export_date' => current_time('mysql'),
            'site_url' => get_site_url(),
            'settings' => array(),
            'templates' => array(),
        );

        // Get all plugin options
        $option_keys = self::get_all_option_keys();

        foreach ($option_keys as $key) {
            $value = get_option($key);

            // Skip API keys unless explicitly included
            if (!$include_api_keys && strpos($key, '_api_key') !== false) {
                continue;
            }

            if ($value !== false) {
                $settings['settings'][$key] = $value;
            }
        }

        // Export content templates
        $templates = get_option('aiacg_content_templates', array());
        $settings['templates'] = $templates;

        return $settings;
    }

    /**
     * Import settings from data array
     *
     * @param array $data Settings data
     * @param array $options Import options
     * @return array|WP_Error Result or error
     */
    public static function import_settings($data, $options = array()) {
        $defaults = array(
            'overwrite_existing' => false,
            'import_templates' => true,
            'import_api_keys' => false,
            'skip_validation' => false,
        );

        $options = wp_parse_args($options, $defaults);

        // Validate data structure
        if (!$options['skip_validation']) {
            $validation = self::validate_import_data($data);
            if (is_wp_error($validation)) {
                return $validation;
            }
        }

        $imported = array(
            'settings_imported' => 0,
            'templates_imported' => 0,
            'skipped' => 0,
            'errors' => array(),
        );

        // Import settings
        if (isset($data['settings']) && is_array($data['settings'])) {
            foreach ($data['settings'] as $key => $value) {
                // Skip API keys unless explicitly allowed
                if (!$options['import_api_keys'] && strpos($key, '_api_key') !== false) {
                    $imported['skipped']++;
                    continue;
                }

                // Check if should overwrite
                if (!$options['overwrite_existing'] && get_option($key) !== false) {
                    $imported['skipped']++;
                    continue;
                }

                // Import the setting
                $result = update_option($key, $value);
                if ($result) {
                    $imported['settings_imported']++;
                } else {
                    $imported['errors'][] = sprintf(__('Failed to import setting: %s', 'ai-auto-content-generator'), $key);
                }
            }
        }

        // Import templates
        if ($options['import_templates'] && isset($data['templates']) && is_array($data['templates'])) {
            $existing_templates = get_option('aiacg_content_templates', array());

            foreach ($data['templates'] as $template_id => $template) {
                // Skip if exists and not overwriting
                if (!$options['overwrite_existing'] && isset($existing_templates[$template_id])) {
                    $imported['skipped']++;
                    continue;
                }

                $existing_templates[$template_id] = $template;
                $imported['templates_imported']++;
            }

            update_option('aiacg_content_templates', $existing_templates);
        }

        return $imported;
    }

    /**
     * Validate import data
     *
     * @param array $data Data to validate
     * @return bool|WP_Error True if valid, WP_Error otherwise
     */
    private static function validate_import_data($data) {
        if (!is_array($data)) {
            return new WP_Error('invalid_data', __('Import data must be an array', 'ai-auto-content-generator'));
        }

        if (!isset($data['version'])) {
            return new WP_Error('missing_version', __('Import data missing version information', 'ai-auto-content-generator'));
        }

        // Check version compatibility
        if (version_compare($data['version'], '1.0.0', '<')) {
            return new WP_Error('incompatible_version', __('Import data is from an incompatible plugin version', 'ai-auto-content-generator'));
        }

        return true;
    }

    /**
     * Export settings to JSON file
     *
     * @param bool $include_api_keys Whether to include API keys
     * @param string $filename Optional filename
     * @return string File path
     */
    public static function export_to_file($include_api_keys = false, $filename = '') {
        $settings = self::export_settings($include_api_keys);

        if (empty($filename)) {
            $filename = 'aiacg-settings-' . date('Y-m-d-His') . '.json';
        }

        // Sanitize filename
        $filename = sanitize_file_name($filename);
        if (!preg_match('/\.json$/i', $filename)) {
            $filename .= '.json';
        }

        $upload_dir = wp_upload_dir();

        // Check for upload directory errors
        if ($upload_dir['error']) {
            throw new Exception($upload_dir['error']);
        }

        $export_dir = $upload_dir['basedir'] . '/aiacg-exports';

        // Create directory if it doesn't exist
        if (!file_exists($export_dir)) {
            if (!wp_mkdir_p($export_dir)) {
                throw new Exception(__('Failed to create export directory', 'ai-auto-content-generator'));
            }

            // Add .htaccess to protect exports
            $htaccess = $export_dir . '/.htaccess';
            $htaccess_content = 'deny from all';
            if (file_put_contents($htaccess, $htaccess_content) === false) {
                error_log('AIACG: Failed to create .htaccess for export directory');
            }
        }

        $file_path = $export_dir . '/' . $filename;
        $json_data = wp_json_encode($settings, JSON_PRETTY_PRINT);

        if ($json_data === false) {
            throw new Exception(__('Failed to encode settings as JSON', 'ai-auto-content-generator'));
        }

        if (file_put_contents($file_path, $json_data) === false) {
            throw new Exception(__('Failed to write export file', 'ai-auto-content-generator'));
        }

        return $file_path;
    }

    /**
     * Import settings from JSON file
     *
     * @param string $file_path File path
     * @param array $options Import options
     * @return array|WP_Error Result or error
     */
    public static function import_from_file($file_path, $options = array()) {
        if (!file_exists($file_path)) {
            return new WP_Error('file_not_found', __('Import file not found', 'ai-auto-content-generator'));
        }

        $json_data = file_get_contents($file_path);
        $data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_json', __('Invalid JSON in import file', 'ai-auto-content-generator'));
        }

        return self::import_settings($data, $options);
    }

    /**
     * Get all plugin option keys
     *
     * @return array Option keys
     */
    private static function get_all_option_keys() {
        return array(
            // API Settings
            'aiacg_gemini_api_key',
            'aiacg_gemini_model',
            'aiacg_deepseek_api_key',
            'aiacg_deepseek_model',
            'aiacg_openai_api_key',
            'aiacg_openai_model',
            'aiacg_api_priority',

            // Basic Settings
            'aiacg_default_language',
            'aiacg_default_word_count',
            'aiacg_custom_prompt',
            'aiacg_default_category',
            'aiacg_default_post_status',

            // Scheduler Settings
            'aiacg_scheduler_enabled',
            'aiacg_scheduler_time',
            'aiacg_scheduler_posts_per_day',

            // Budget Settings
            'aiacg_enable_budget_control',
            'aiacg_daily_budget_limit',
            'aiacg_monthly_budget_limit',
            'aiacg_budget_warning_threshold',
            'aiacg_budget_notification_email',

            // Deduplication Settings
            'aiacg_enable_title_check',
            'aiacg_title_similarity_threshold',
            'aiacg_enable_content_check',
            'aiacg_content_similarity_threshold',
            'aiacg_check_timeframe',

            // Notification Settings
            'aiacg_enable_notifications',
            'aiacg_notification_email',
            'aiacg_notify_on_success',
            'aiacg_notify_on_failure',
            'aiacg_notify_on_budget_warning',

            // Moderation Settings (v1.2.0)
            'aiacg_enable_moderation',
            'aiacg_moderation_auto_publish',
            'aiacg_moderation_notifications',
            'aiacg_moderation_notify_email',

            // Taxonomy Suggester Settings (v1.2.0)
            'aiacg_enable_taxonomy_suggestions',
            'aiacg_auto_apply_suggestions',
            'aiacg_taxonomy_api_preference',
            'aiacg_max_suggested_categories',
            'aiacg_max_suggested_tags',

            // Content Templates
            'aiacg_content_templates',
            'aiacg_default_template',
        );
    }

    /**
     * Download export file
     *
     * @param string $file_path File path
     */
    public static function download_file($file_path) {
        if (!file_exists($file_path)) {
            wp_die(__('Export file not found', 'ai-auto-content-generator'));
        }

        $filename = basename($file_path);

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($file_path);
        exit;
    }

    /**
     * Clean up old export files
     *
     * @param int $days_old Delete files older than this many days
     * @return int Number of files deleted
     */
    public static function cleanup_old_exports($days_old = 7) {
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/aiacg-exports';

        if (!file_exists($export_dir)) {
            return 0;
        }

        $deleted = 0;
        $cutoff_time = time() - ($days_old * DAY_IN_SECONDS);

        $files = glob($export_dir . '/aiacg-settings-*.json');
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff_time) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    /**
     * Get list of available export files
     *
     * @return array Export files with metadata
     */
    public static function get_export_files() {
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/aiacg-exports';

        if (!file_exists($export_dir)) {
            return array();
        }

        $files = glob($export_dir . '/aiacg-settings-*.json');
        $export_files = array();

        foreach ($files as $file) {
            $export_files[] = array(
                'filename' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'created' => filemtime($file),
            );
        }

        // Sort by creation time, newest first
        usort($export_files, function($a, $b) {
            return $b['created'] - $a['created'];
        });

        return $export_files;
    }
}
