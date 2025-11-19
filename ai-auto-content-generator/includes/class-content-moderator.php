<?php
/**
 * Content Moderator
 *
 * Manages content moderation workflow before publishing
 *
 * @package AI_Auto_Content_Generator
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class AIACG_Content_Moderator {

    /**
     * Moderation statuses
     */
    const STATUS_PENDING = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_NEEDS_REVISION = 'needs_revision';

    /**
     * Initialize moderation hooks
     */
    public static function init() {
        // Add moderation column to posts list
        add_filter('manage_posts_columns', array(__CLASS__, 'add_moderation_column'));
        add_action('manage_posts_custom_column', array(__CLASS__, 'display_moderation_column'), 10, 2);

        // Add moderation meta box
        add_action('add_meta_boxes', array(__CLASS__, 'add_moderation_meta_box'));
        add_action('save_post', array(__CLASS__, 'save_moderation_data'));
    }

    /**
     * Set moderation status for a post
     *
     * @param int $post_id Post ID
     * @param string $status Moderation status
     * @param string $notes Moderation notes
     * @param int $moderator_id User ID of moderator
     * @return bool Success
     */
    public static function set_moderation_status($post_id, $status, $notes = '', $moderator_id = 0) {
        // Validate status
        if (!in_array($status, array(self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_NEEDS_REVISION))) {
            return false;
        }

        // Validate post exists
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }

        if ($moderator_id === 0) {
            $moderator_id = get_current_user_id();
        }

        $moderation_data = array(
            'status' => $status,
            'notes' => sanitize_textarea_field($notes),
            'moderator_id' => intval($moderator_id),
            'moderated_at' => current_time('mysql'),
        );

        update_post_meta($post_id, '_aiacg_moderation_status', $status);
        update_post_meta($post_id, '_aiacg_moderation_data', $moderation_data);

        // Add to moderation history
        $history = get_post_meta($post_id, '_aiacg_moderation_history', true);
        if (!is_array($history)) {
            $history = array();
        }
        $history[] = $moderation_data;
        update_post_meta($post_id, '_aiacg_moderation_history', $history);

        // Auto-publish if approved and enabled
        if ($status === self::STATUS_APPROVED) {
            $auto_publish = get_option('aiacg_moderation_auto_publish', false);
            if ($auto_publish && $post->post_status !== 'publish') {
                $result = wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'publish',
                ), true);

                // Log error if update failed
                if (is_wp_error($result)) {
                    error_log('AIACG: Failed to auto-publish post ' . $post_id . ': ' . $result->get_error_message());
                }
            }
        }

        // Send notification if enabled
        self::send_moderation_notification($post_id, $status, $notes);

        return true;
    }

    /**
     * Get moderation status for a post
     *
     * @param int $post_id Post ID
     * @return array|false Moderation data or false
     */
    public static function get_moderation_status($post_id) {
        $status = get_post_meta($post_id, '_aiacg_moderation_status', true);
        $data = get_post_meta($post_id, '_aiacg_moderation_data', true);

        if (empty($status)) {
            return false;
        }

        return $data;
    }

    /**
     * Get posts pending moderation
     *
     * @param array $args Query arguments
     * @return array Array of post IDs
     */
    public static function get_pending_posts($args = array()) {
        $defaults = array(
            'post_type' => 'post',
            'post_status' => 'draft',
            'meta_query' => array(
                array(
                    'key' => '_aiacg_moderation_status',
                    'value' => self::STATUS_PENDING,
                ),
                array(
                    'key' => '_aiacg_generated_post',
                    'value' => '1',
                ),
            ),
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        $args = wp_parse_args($args, $defaults);
        $query = new WP_Query($args);

        return $query->posts;
    }

    /**
     * Get moderation statistics
     *
     * @return array Statistics
     */
    public static function get_moderation_stats() {
        global $wpdb;

        $stats = array(
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'needs_revision' => 0,
            'total_moderated' => 0,
            'avg_moderation_time' => 0,
        );

        // Count by status
        $counts = $wpdb->get_results("
            SELECT meta_value as status, COUNT(*) as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = '_aiacg_moderation_status'
            GROUP BY meta_value
        ");

        foreach ($counts as $row) {
            $status_key = str_replace('_review', '', str_replace('pending_', 'pending', $row->status));
            if (isset($stats[$status_key])) {
                $stats[$status_key] = intval($row->count);
            }
            $stats['total_moderated'] += intval($row->count);
        }

        return $stats;
    }

    /**
     * Mark post as requiring moderation
     *
     * @param int $post_id Post ID
     * @return bool Success
     */
    public static function mark_for_moderation($post_id) {
        update_post_meta($post_id, '_aiacg_generated_post', '1');
        return self::set_moderation_status($post_id, self::STATUS_PENDING, __('Auto-generated content awaiting review', 'ai-auto-content-generator'));
    }

    /**
     * Send moderation notification
     *
     * @param int $post_id Post ID
     * @param string $status New status
     * @param string $notes Moderation notes
     */
    private static function send_moderation_notification($post_id, $status, $notes) {
        $notify_enabled = get_option('aiacg_moderation_notifications', false);
        if (!$notify_enabled) {
            return;
        }

        $post = get_post($post_id);
        if (!$post) {
            return;
        }

        $admin_email = get_option('admin_email');
        $notify_email = get_option('aiacg_moderation_notify_email', $admin_email);

        $subject = sprintf(
            __('[%s] Content Moderation: %s', 'ai-auto-content-generator'),
            get_bloginfo('name'),
            $post->post_title
        );

        $status_labels = array(
            self::STATUS_PENDING => __('Pending Review', 'ai-auto-content-generator'),
            self::STATUS_APPROVED => __('Approved', 'ai-auto-content-generator'),
            self::STATUS_REJECTED => __('Rejected', 'ai-auto-content-generator'),
            self::STATUS_NEEDS_REVISION => __('Needs Revision', 'ai-auto-content-generator'),
        );

        $message = sprintf(
            __("Post: %s\nStatus: %s\nModerator Notes: %s\n\nView Post: %s\nEdit Post: %s", 'ai-auto-content-generator'),
            $post->post_title,
            $status_labels[$status] ?? $status,
            $notes,
            get_permalink($post_id),
            admin_url('post.php?post=' . $post_id . '&action=edit')
        );

        wp_mail($notify_email, $subject, $message);
    }

    /**
     * Add moderation column to posts list
     *
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public static function add_moderation_column($columns) {
        $moderation_enabled = get_option('aiacg_enable_moderation', false);
        if (!$moderation_enabled) {
            return $columns;
        }

        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['aiacg_moderation'] = __('Moderation', 'ai-auto-content-generator');
            }
        }

        return $new_columns;
    }

    /**
     * Display moderation column content
     *
     * @param string $column Column name
     * @param int $post_id Post ID
     */
    public static function display_moderation_column($column, $post_id) {
        if ($column !== 'aiacg_moderation') {
            return;
        }

        $is_generated = get_post_meta($post_id, '_aiacg_generated_post', true);
        if (!$is_generated) {
            echo '—';
            return;
        }

        $moderation_data = self::get_moderation_status($post_id);
        if (!$moderation_data) {
            echo '<span class="aiacg-mod-badge mod-none">' . esc_html__('No Status', 'ai-auto-content-generator') . '</span>';
            return;
        }

        $status_labels = array(
            self::STATUS_PENDING => __('Pending', 'ai-auto-content-generator'),
            self::STATUS_APPROVED => __('Approved', 'ai-auto-content-generator'),
            self::STATUS_REJECTED => __('Rejected', 'ai-auto-content-generator'),
            self::STATUS_NEEDS_REVISION => __('Needs Revision', 'ai-auto-content-generator'),
        );

        $status_classes = array(
            self::STATUS_PENDING => 'mod-pending',
            self::STATUS_APPROVED => 'mod-approved',
            self::STATUS_REJECTED => 'mod-rejected',
            self::STATUS_NEEDS_REVISION => 'mod-revision',
        );

        $status = $moderation_data['status'];
        $label = $status_labels[$status] ?? $status;
        $class = $status_classes[$status] ?? 'mod-none';

        echo '<span class="aiacg-mod-badge ' . esc_attr($class) . '">' . esc_html($label) . '</span>';
    }

    /**
     * Add moderation meta box to post editor
     */
    public static function add_moderation_meta_box() {
        $moderation_enabled = get_option('aiacg_enable_moderation', false);
        if (!$moderation_enabled) {
            return;
        }

        add_meta_box(
            'aiacg_moderation_meta_box',
            __('AI Content Moderation', 'ai-auto-content-generator'),
            array(__CLASS__, 'render_moderation_meta_box'),
            'post',
            'side',
            'high'
        );
    }

    /**
     * Render moderation meta box
     *
     * @param WP_Post $post Post object
     */
    public static function render_moderation_meta_box($post) {
        $is_generated = get_post_meta($post->ID, '_aiacg_generated_post', true);
        if (!$is_generated) {
            echo '<p>' . esc_html__('This post was not auto-generated.', 'ai-auto-content-generator') . '</p>';
            return;
        }

        $moderation_data = self::get_moderation_status($post->ID);
        $current_status = $moderation_data ? $moderation_data['status'] : self::STATUS_PENDING;

        wp_nonce_field('aiacg_moderation_nonce', 'aiacg_moderation_nonce');

        echo '<div class="aiacg-moderation-box">';
        echo '<p><strong>' . esc_html__('Moderation Status:', 'ai-auto-content-generator') . '</strong></p>';
        echo '<select name="aiacg_moderation_status" id="aiacg_moderation_status" class="widefat">';

        $statuses = array(
            self::STATUS_PENDING => __('Pending Review', 'ai-auto-content-generator'),
            self::STATUS_APPROVED => __('Approved', 'ai-auto-content-generator'),
            self::STATUS_NEEDS_REVISION => __('Needs Revision', 'ai-auto-content-generator'),
            self::STATUS_REJECTED => __('Rejected', 'ai-auto-content-generator'),
        );

        foreach ($statuses as $value => $label) {
            echo '<option value="' . esc_attr($value) . '"' . selected($current_status, $value, false) . '>' . esc_html($label) . '</option>';
        }

        echo '</select>';

        echo '<p><strong>' . esc_html__('Moderator Notes:', 'ai-auto-content-generator') . '</strong></p>';
        echo '<textarea name="aiacg_moderation_notes" class="widefat" rows="4" placeholder="' . esc_attr__('Add notes about this review...', 'ai-auto-content-generator') . '">' . esc_textarea($moderation_data['notes'] ?? '') . '</textarea>';

        // Show moderation history
        $history = get_post_meta($post->ID, '_aiacg_moderation_history', true);
        if (!empty($history) && is_array($history)) {
            echo '<p><strong>' . esc_html__('Moderation History:', 'ai-auto-content-generator') . '</strong></p>';
            echo '<div class="aiacg-mod-history">';
            foreach (array_reverse($history) as $entry) {
                $moderator = get_userdata($entry['moderator_id']);
                $moderator_name = $moderator ? $moderator->display_name : __('Unknown', 'ai-auto-content-generator');

                echo '<div class="mod-history-entry">';
                echo '<small>' . esc_html($entry['moderated_at']) . '</small><br>';
                echo '<strong>' . esc_html($statuses[$entry['status']] ?? $entry['status']) . '</strong> ';
                echo esc_html__('by', 'ai-auto-content-generator') . ' ' . esc_html($moderator_name);
                if (!empty($entry['notes'])) {
                    echo '<br><em>' . esc_html($entry['notes']) . '</em>';
                }
                echo '</div>';
            }
            echo '</div>';
        }

        echo '</div>';
    }

    /**
     * Save moderation data from meta box
     *
     * @param int $post_id Post ID
     */
    public static function save_moderation_data($post_id) {
        // Check nonce
        if (!isset($_POST['aiacg_moderation_nonce']) || !wp_verify_nonce($_POST['aiacg_moderation_nonce'], 'aiacg_moderation_nonce')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save moderation status
        if (isset($_POST['aiacg_moderation_status'])) {
            $status = sanitize_text_field($_POST['aiacg_moderation_status']);
            $notes = isset($_POST['aiacg_moderation_notes']) ? sanitize_textarea_field($_POST['aiacg_moderation_notes']) : '';

            self::set_moderation_status($post_id, $status, $notes);
        }
    }
}

// Initialize moderation hooks
AIACG_Content_Moderator::init();
