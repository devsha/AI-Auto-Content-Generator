# Plugin Structure Documentation

Complete overview of the AI Auto Content Generator plugin architecture and file organization.

## Directory Structure

```
ai-auto-content-generator/
├── ai-auto-content-generator.php    # Main plugin file (entry point)
├── README.md                         # Main documentation
├── API-SETUP-GUIDE.md               # API configuration guide
├── QUICK-START.md                   # Quick start guide
│
├── includes/                         # Core plugin classes
│   ├── class-database.php           # Database operations
│   ├── class-ai-manager.php         # AI API manager
│   ├── class-content-generator.php  # Content generation logic
│   ├── class-scheduler.php          # Cron task scheduler
│   │
│   └── api/                         # AI API implementations
│       ├── interface-ai-api.php     # API interface
│       ├── class-gemini-api.php     # Google Gemini API
│       ├── class-deepseek-api.php   # DeepSeek API
│       └── class-openai-api.php     # OpenAI API
│
├── admin/                           # Admin interface
│   ├── class-admin-settings.php    # Admin settings controller
│   │
│   ├── views/                       # Admin page templates
│   │   ├── settings-page.php       # Main settings page
│   │   ├── tab-api-config.php      # API configuration tab
│   │   ├── tab-history.php         # History & stats tab
│   │   ├── tab-logs.php            # Logs & monitoring tab
│   │   └── tab-templates.php       # Content templates tab
│   │
│   └── assets/                      # Frontend resources
│       ├── css/
│       │   └── admin-style.css     # Admin styles
│       └── js/
│           └── admin-script.js     # Admin JavaScript
│
└── languages/                       # Internationalization files
    └── (translation files .po/.mo)
```

## Core Files

### Main Plugin File

**`ai-auto-content-generator.php`**
- Plugin header and metadata
- Main plugin class (`AI_Auto_Content_Generator`)
- Activation, deactivation, and uninstall hooks
- Dependency loader
- Plugin initialization

**Key Functions:**
- `aiacg_activate()` - Sets up database, default options, schedules cron
- `aiacg_deactivate()` - Clears cron jobs
- `aiacg_uninstall()` - Removes database tables and options

---

## Includes Directory

### Database Management

**`includes/class-database.php`**

**Class:** `AIACG_Database`

**Purpose:** Handle all database operations for the plugin

**Key Methods:**
- `create_tables()` - Create plugin database tables
- `insert_history($data)` - Insert generation record
- `update_history($id, $data)` - Update existing record
- `get_history($args)` - Retrieve generation history
- `get_statistics()` - Get usage statistics
- `get_all_titles($limit)` - Get historical titles (for anti-duplication)
- `log($message, $level, $context)` - Write log entry
- `get_logs($level, $limit)` - Retrieve logs

**Database Table:**
```sql
wp_aiacg_content_history
- id (primary key)
- post_id (WordPress post ID)
- topic
- generated_title
- prompt_used
- api_used
- tokens_used
- generation_time
- status
- error_message
- word_count
- writing_angle
- similarity_score
- cost_estimate
```

---

### AI Manager

**`includes/class-ai-manager.php`**

**Class:** `AIACG_AI_Manager`

**Purpose:** Manage multiple AI APIs with rotation and failover

**Key Methods:**
- `get_active_api()` - Get current active API instance
- `switch_api($api_name)` - Switch to different API
- `generate_content($prompt, $params, $max_retries)` - Generate with auto-failover
- `test_all_connections()` - Test all API connections
- `get_all_usage_stats()` - Get statistics for all APIs
- `is_retryable_error($error)` - Determine if error is retryable

**Features:**
- API priority management
- Automatic failover on errors
- Exponential backoff retry logic
- Usage tracking across APIs

---

### Content Generator

**`includes/class-content-generator.php`**

**Class:** `AIACG_Content_Generator`

**Purpose:** Core content generation with anti-duplication

**Key Methods:**
- `generate_post($args)` - Generate single post
- `generate_multiple_posts($count, $args)` - Batch generation
- `generate_unique_title($args)` - Generate unique title
- `generate_article_content($title, $args, $angle)` - Generate content
- `generate_excerpt($content, $args)` - Generate excerpt
- `generate_tags($title, $content, $args)` - Generate tags
- `is_title_unique($title, $existing_titles)` - Check uniqueness
- `calculate_similarity($str1, $str2)` - Calculate Levenshtein distance
- `add_seo_meta($post_id, $title, $excerpt, $tags)` - Add SEO data

**Anti-Duplication Mechanisms:**
1. Title similarity checking (85% threshold)
2. Writing angle rotation
3. Keyword mixing
4. Dynamic prompt generation
5. Historical title database

---

### Scheduler

**`includes/class-scheduler.php`**

**Class:** `AIACG_Scheduler`

**Purpose:** Manage WordPress Cron tasks

**Key Methods:**
- `add_cron_intervals($schedules)` - Add custom cron intervals
- `run_daily_generation()` - Execute scheduled generation
- `run_cleanup()` - Clean old records
- `reschedule_daily_generation($time)` - Update schedule time
- `manual_generation($count)` - Trigger manual generation
- `get_schedule_status()` - Get cron status info

**Hooks:**
- `aiacg_daily_generation` - Main generation task
- `aiacg_cleanup` - Weekly cleanup task

---

## API Directory

### API Interface

**`includes/api/interface-ai-api.php`**

**Interface:** `AIACG_AI_API_Interface`

**Required Methods:**
- `generate_content($prompt, $params)` - Generate content
- `test_connection()` - Test API connectivity
- `get_models()` - Return available models
- `get_usage_stats()` - Return usage statistics
- `calculate_cost($tokens, $model)` - Calculate cost estimate
- `get_name()` - Return API name
- `validate_config()` - Validate configuration

---

### Gemini API

**`includes/api/class-gemini-api.php`**

**Class:** `AIACG_Gemini_API implements AIACG_AI_API_Interface`

**Purpose:** Google Gemini API integration

**Endpoints:**
- `https://generativelanguage.googleapis.com/v1beta/models/`

**Models Supported:**
- gemini-1.5-pro
- gemini-1.5-flash
- gemini-pro
- gemini-1.0-pro

**Features:**
- Free tier support (60 RPM)
- Token usage tracking
- Cost estimation
- Latency monitoring

---

### DeepSeek API

**`includes/api/class-deepseek-api.php`**

**Class:** `AIACG_DeepSeek_API implements AIACG_AI_API_Interface`

**Purpose:** DeepSeek API integration

**Endpoints:**
- `https://api.deepseek.com/v1/chat/completions`

**Models Supported:**
- deepseek-chat
- deepseek-coder

**Features:**
- OpenAI-compatible API
- Very low cost
- System prompt support
- Streaming disabled (for simplicity)

---

### OpenAI API

**`includes/api/class-openai-api.php`**

**Class:** `AIACG_OpenAI_API implements AIACG_AI_API_Interface`

**Purpose:** OpenAI API integration

**Endpoints:**
- `https://api.openai.com/v1/chat/completions`

**Models Supported:**
- gpt-4
- gpt-4-turbo-preview
- gpt-3.5-turbo
- gpt-3.5-turbo-16k

**Features:**
- Industry-standard API
- Advanced parameters (frequency_penalty, presence_penalty)
- Comprehensive error handling

---

## Admin Directory

### Admin Settings Controller

**`admin/class-admin-settings.php`**

**Class:** `AIACG_Admin_Settings`

**Purpose:** Manage admin interface and settings

**Key Methods:**
- `add_admin_menu()` - Register admin menu
- `register_settings()` - Register WordPress settings
- `enqueue_admin_assets($hook)` - Load CSS/JS
- `render_settings_page()` - Render main page
- `ajax_test_api()` - AJAX: Test API connection
- `ajax_generate_now()` - AJAX: Manual generation
- `ajax_clear_logs()` - AJAX: Clear logs

**Settings Groups:**
- `aiacg_basic_settings` - Topic, generation, content settings
- `aiacg_api_settings` - API keys and configuration
- `aiacg_template_settings` - Prompts and templates
- `aiacg_other_settings` - Logging, retention

---

### Admin Views

**`admin/views/settings-page.php`**
- Main settings page layout
- Tab navigation
- Sidebar widgets (stats, schedule status)
- Quick action buttons

**`admin/views/tab-api-config.php`**
- API key inputs
- Model selection
- Temperature/token settings
- Connection testing
- Usage statistics display

**`admin/views/tab-history.php`**
- Statistics dashboard
- Generation history table
- API usage breakdown
- Pagination

**`admin/views/tab-logs.php`**
- System logs table
- Log filtering (info/warning/error)
- API monitoring status
- System information
- Scheduled task status

**`admin/views/tab-templates.php`**
- System prompt editor
- User prompt template editor
- Writing angles list
- Title generation rules

---

### Admin Assets

**`admin/assets/css/admin-style.css`**

**Purpose:** Admin interface styling

**Features:**
- Responsive grid layout
- Status indicators (success/error/warning)
- Statistics boxes
- Log level styling
- Loading animations
- Modern UI components

**`admin/assets/js/admin-script.js`**

**Purpose:** Admin interactivity

**Features:**
- AJAX API testing
- Manual post generation
- Log clearing
- Form state management
- Range input syncing
- Password field toggle
- Tab state persistence
- Clipboard functionality

---

## Data Flow

### Content Generation Flow

```
1. Trigger (Manual or Cron)
   ↓
2. AIACG_Scheduler::run_daily_generation()
   ↓
3. AIACG_Content_Generator::generate_post()
   ↓
4. Generate unique title
   - Check against database
   - Calculate similarity
   - Retry if duplicate (max 5 times)
   ↓
5. Generate content
   - Build prompt with variables
   - AIACG_AI_Manager::generate_content()
   - Try APIs in priority order
   - Retry on retryable errors
   ↓
6. Generate excerpt and tags
   ↓
7. Create WordPress post
   - wp_insert_post()
   - Add tags
   - Add SEO meta
   ↓
8. Record in database
   - AIACG_Database::insert_history()
   - Update statistics
   ↓
9. Log result
   - AIACG_Database::log()
```

### API Call Flow

```
1. AIACG_AI_Manager::generate_content()
   ↓
2. Get active API or use rotation
   ↓
3. Loop through APIs based on strategy
   ↓
4. For each API:
   - Validate configuration
   - Call API::generate_content()
   - Handle response
   - If error: check if retryable
   - If retryable: exponential backoff
   - If failed: try next API
   ↓
5. Return result or error
```

---

## WordPress Options

All plugin settings are stored as WordPress options:

**Basic Settings:**
- `aiacg_main_topic`
- `aiacg_topic_description`
- `aiacg_sub_topics` (array)
- `aiacg_writing_style`
- `aiacg_target_audience`
- `aiacg_daily_post_count`
- `aiacg_word_count`
- `aiacg_generation_time`
- `aiacg_publish_mode`
- `aiacg_publish_interval`
- `aiacg_default_category`
- `aiacg_auto_tags`
- `aiacg_seo_optimization`

**API Settings:**
- `aiacg_active_api`
- `aiacg_enable_api_rotation`
- `aiacg_api_priority` (array)
- `aiacg_auto_switch_on_failure`
- `aiacg_gemini_api_key`
- `aiacg_gemini_model`
- `aiacg_gemini_temperature`
- `aiacg_gemini_max_tokens`
- (Similar for DeepSeek and OpenAI)

**Template Settings:**
- `aiacg_system_prompt`
- `aiacg_user_prompt_template`
- `aiacg_writing_angles` (array)
- `aiacg_title_min_length`
- `aiacg_title_max_length`

**Usage Statistics:**
- `aiacg_gemini_usage_stats`
- `aiacg_deepseek_usage_stats`
- `aiacg_openai_usage_stats`

**System:**
- `aiacg_logs` (array, max 100 entries)
- `aiacg_version`
- `aiacg_last_generation_time`
- `aiacg_last_generation_result`

---

## Hooks and Filters

### Actions

**Plugin Lifecycle:**
- `register_activation_hook` - On plugin activation
- `register_deactivation_hook` - On plugin deactivation
- `register_uninstall_hook` - On plugin deletion

**WordPress Hooks:**
- `plugins_loaded` - Load text domain
- `admin_menu` - Add admin menu
- `admin_init` - Register settings
- `admin_enqueue_scripts` - Load admin assets

**AJAX Actions:**
- `wp_ajax_aiacg_test_api` - Test API connection
- `wp_ajax_aiacg_generate_now` - Manual generation
- `wp_ajax_aiacg_clear_logs` - Clear logs

**Cron Hooks:**
- `aiacg_daily_generation` - Daily generation task
- `aiacg_cleanup` - Weekly cleanup task

### Filters

**Cron:**
- `cron_schedules` - Add custom intervals

**Plugin Links:**
- `plugin_action_links_{basename}` - Add settings link

---

## Security Measures

1. **Nonce Verification:**
   - All AJAX requests verified with `wp_create_nonce()`
   - Form submissions checked with `wp_verify_nonce()`

2. **Capability Checks:**
   - All admin pages check `current_user_can('manage_options')`
   - AJAX handlers verify user permissions

3. **Input Sanitization:**
   - `sanitize_text_field()` for text inputs
   - `sanitize_textarea_field()` for textarea
   - `intval()` for integers
   - `floatval()` for floats
   - `esc_url()` for URLs

4. **Output Escaping:**
   - `esc_html()` for HTML output
   - `esc_attr()` for attribute output
   - `esc_js()` for JavaScript strings
   - `wp_kses_post()` for post content

5. **Database:**
   - `$wpdb->prepare()` for all queries
   - Parameterized queries prevent SQL injection

6. **API Keys:**
   - Stored in WordPress options (not in code)
   - Never exposed to frontend
   - Transmitted only via HTTPS

---

## Performance Considerations

1. **Lazy Loading:**
   - API classes instantiated only when needed
   - Admin assets loaded only on plugin pages

2. **Database Optimization:**
   - Indexed columns (post_id, status, generation_time)
   - Automatic cleanup of old records
   - Limited log storage (100 entries)

3. **Caching:**
   - Usage statistics cached in options table
   - Historical titles limited to 500 most recent

4. **Async Operations:**
   - Content generation via WordPress Cron
   - Doesn't block user interactions

5. **Error Handling:**
   - Timeout limits on API calls (60s)
   - Exponential backoff prevents hammering APIs
   - Graceful degradation on failures

---

## Extensibility

### Adding a New AI API

1. Create new file: `includes/api/class-{api-name}-api.php`
2. Implement `AIACG_AI_API_Interface`
3. Add to `includes/class-ai-manager.php`:
   ```php
   $this->apis['{api-name}'] = new AIACG_{API_Name}_API();
   ```
4. Add settings in `admin/class-admin-settings.php`
5. Add UI in `admin/views/tab-api-config.php`

### Customizing Prompts

Users can customize via admin interface, or developers can use filters:

```php
add_filter('aiacg_system_prompt', function($prompt) {
    return 'Your custom system prompt';
});

add_filter('aiacg_user_prompt', function($prompt, $vars) {
    // Modify prompt with access to variables
    return $prompt;
}, 10, 2);
```

### Adding Writing Angles

```php
add_filter('aiacg_writing_angles', function($angles) {
    $angles['interview'] = 'Interview format with Q&A';
    return $angles;
});
```

---

## Testing Checklist

- [ ] Plugin activation/deactivation
- [ ] Database table creation
- [ ] API connection testing (all 3 APIs)
- [ ] Manual post generation
- [ ] Title uniqueness checking
- [ ] Scheduled generation (WP Cron)
- [ ] API failover
- [ ] Settings save/load
- [ ] AJAX functionality
- [ ] Log viewing/clearing
- [ ] Statistics display
- [ ] SEO meta generation
- [ ] Tag generation
- [ ] Excerpt generation
- [ ] Cost calculation
- [ ] Error handling
- [ ] Security (nonce, capabilities)

---

**Version:** 1.0.0
**Last Updated:** 2024
**Author:** Your Name

For usage instructions, see [README.md](README.md)
