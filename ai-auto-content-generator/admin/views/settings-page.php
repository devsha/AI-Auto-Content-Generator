<?php
/**
 * 管理设置页面视图
 *
 * @package AI_Auto_Content_Generator
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// 处理表单提交
if (isset($_POST['aiacg_sub_topics']) && is_string($_POST['aiacg_sub_topics'])) {
    $_POST['aiacg_sub_topics'] = array_map('trim', explode(',', $_POST['aiacg_sub_topics']));
}

$scheduler = new AIACG_Scheduler();
$schedule_status = $scheduler->get_schedule_status();
$statistics = AIACG_Database::get_statistics();
?>

<div class="wrap aiacg-settings-wrap">
    <h1><?php _e('AI Auto Content Generator Settings', 'ai-auto-content-generator'); ?></h1>

    <?php settings_errors(); ?>

    <div class="aiacg-header-actions">
        <button type="button" class="button button-primary aiacg-generate-now" data-count="1">
            <?php _e('Generate 1 Post Now', 'ai-auto-content-generator'); ?>
        </button>
        <button type="button" class="button aiacg-generate-now" data-count="5">
            <?php _e('Generate 5 Posts', 'ai-auto-content-generator'); ?>
        </button>
    </div>

    <h2 class="nav-tab-wrapper">
        <a href="?page=aiacg-settings&tab=basic" class="nav-tab <?php echo $active_tab === 'basic' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Basic Settings', 'ai-auto-content-generator'); ?>
        </a>
        <a href="?page=aiacg-settings&tab=api" class="nav-tab <?php echo $active_tab === 'api' ? 'nav-tab-active' : ''; ?>">
            <?php _e('API Configuration', 'ai-auto-content-generator'); ?>
        </a>
        <a href="?page=aiacg-settings&tab=templates" class="nav-tab <?php echo $active_tab === 'templates' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Content Templates', 'ai-auto-content-generator'); ?>
        </a>
        <a href="?page=aiacg-settings&tab=history" class="nav-tab <?php echo $active_tab === 'history' ? 'nav-tab-active' : ''; ?>">
            <?php _e('History & Stats', 'ai-auto-content-generator'); ?>
        </a>
        <a href="?page=aiacg-settings&tab=logs" class="nav-tab <?php echo $active_tab === 'logs' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Logs & Monitoring', 'ai-auto-content-generator'); ?>
        </a>
        <a href="?page=aiacg-settings&tab=tools" class="nav-tab <?php echo $active_tab === 'tools' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Tools', 'ai-auto-content-generator'); ?>
        </a>
    </h2>

    <div class="aiacg-tab-content">
        <?php
        switch ($active_tab) {
            case 'api':
                include __DIR__ . '/tab-api-config.php';
                break;
            case 'templates':
                include __DIR__ . '/tab-templates.php';
                break;
            case 'history':
                include __DIR__ . '/tab-history.php';
                break;
            case 'logs':
                include __DIR__ . '/tab-logs.php';
                break;
            case 'tools':
                include __DIR__ . '/tab-tools.php';
                break;
            case 'basic':
            default:
                AIACG_Admin_Settings::render_basic_settings_tab();
                break;
        }
        ?>
    </div>

    <div class="aiacg-sidebar">
        <div class="aiacg-info-box">
            <h3><?php _e('Schedule Status', 'ai-auto-content-generator'); ?></h3>
            <p>
                <strong><?php _e('Status:', 'ai-auto-content-generator'); ?></strong>
                <?php echo $schedule_status['is_scheduled'] ? '<span style="color: green;">✓ ' . __('Active', 'ai-auto-content-generator') . '</span>' : '<span style="color: red;">✗ ' . __('Inactive', 'ai-auto-content-generator') . '</span>'; ?>
            </p>
            <?php if ($schedule_status['is_scheduled']): ?>
                <p>
                    <strong><?php _e('Next Run:', 'ai-auto-content-generator'); ?></strong><br>
                    <?php echo esc_html($schedule_status['next_run']); ?><br>
                    <small>(<?php echo esc_html($schedule_status['next_run_relative']); ?> from now)</small>
                </p>
            <?php endif; ?>
            <?php if (!empty($schedule_status['last_run'])): ?>
                <p>
                    <strong><?php _e('Last Run:', 'ai-auto-content-generator'); ?></strong><br>
                    <?php echo esc_html($schedule_status['last_run']); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="aiacg-info-box">
            <h3><?php _e('Statistics', 'ai-auto-content-generator'); ?></h3>
            <p>
                <strong><?php _e('Total Generated:', 'ai-auto-content-generator'); ?></strong>
                <?php echo esc_html($statistics['total_generated']); ?>
            </p>
            <p>
                <strong><?php _e('This Month:', 'ai-auto-content-generator'); ?></strong>
                <?php echo esc_html($statistics['this_month']); ?>
            </p>
            <p>
                <strong><?php _e('Success Rate:', 'ai-auto-content-generator'); ?></strong>
                <?php echo esc_html(number_format($statistics['success_rate'], 1)); ?>%
            </p>
            <p>
                <strong><?php _e('Total Cost:', 'ai-auto-content-generator'); ?></strong>
                $<?php echo esc_html(number_format($statistics['total_cost'], 4)); ?>
            </p>
        </div>

        <div class="aiacg-info-box">
            <h3><?php _e('Quick Links', 'ai-auto-content-generator'); ?></h3>
            <ul>
                <li><a href="<?php echo admin_url('edit.php'); ?>"><?php _e('View All Posts', 'ai-auto-content-generator'); ?></a></li>
                <li><a href="<?php echo admin_url('edit.php?post_status=draft&post_type=post'); ?>"><?php _e('View Drafts', 'ai-auto-content-generator'); ?></a></li>
                <li><a href="https://ai.google.dev/gemini-api/docs/api-key" target="_blank"><?php _e('Get Gemini API Key', 'ai-auto-content-generator'); ?></a></li>
                <li><a href="https://platform.deepseek.com/api_keys" target="_blank"><?php _e('Get DeepSeek API Key', 'ai-auto-content-generator'); ?></a></li>
                <li><a href="https://platform.openai.com/api-keys" target="_blank"><?php _e('Get OpenAI API Key', 'ai-auto-content-generator'); ?></a></li>
            </ul>
        </div>
    </div>
</div>
