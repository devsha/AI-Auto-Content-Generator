# AI Auto Content Generator

A powerful WordPress plugin that automatically generates and publishes high-quality articles using AI APIs (Gemini, DeepSeek, OpenAI, and more).

## 🌟 Features

- **Multiple AI API Support**: Seamlessly integrate with Gemini, DeepSeek, OpenAI, and other compatible APIs
- **Automatic Content Generation**: Schedule daily content generation with customizable post counts
- **Anti-Duplication System**: Advanced title similarity detection ensures unique content
- **Multi-Angle Writing**: Rotate between different writing styles (news, analysis, guides, etc.)
- **SEO Optimization**: Auto-generate meta descriptions, tags, and SEO-friendly titles
- **Smart Scheduling**: WordPress Cron integration for automated publishing
- **API Rotation & Failover**: Automatic switching between APIs if one fails
- **Comprehensive Logging**: Track generation history, API usage, and costs
- **User-Friendly Interface**: Beautiful admin dashboard with 5 organized tabs

## 📋 Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **API Key**: At least one AI API key (Gemini, DeepSeek, or OpenAI)

## 🚀 Installation

### Method 1: Upload via WordPress Admin

1. Download the plugin ZIP file
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Choose the ZIP file and click "Install Now"
4. Activate the plugin

### Method 2: Manual Installation

1. Download and extract the plugin files
2. Upload the `ai-auto-content-generator` folder to `/wp-content/plugins/`
3. Go to WordPress Admin → Plugins
4. Activate "AI Auto Content Generator"

### Method 3: Upload to WordPress Plugin Directory

```bash
# Copy the entire plugin folder to your WordPress plugins directory
cp -r ai-auto-content-generator /path/to/wordpress/wp-content/plugins/
```

## ⚙️ Configuration

### Step 1: Get API Keys

You'll need at least one API key from the following providers:

#### Google Gemini (Recommended)
1. Visit [Google AI Studio](https://ai.google.dev/gemini-api/docs/api-key)
2. Sign in with your Google account
3. Click "Get API Key"
4. Copy the generated API key
5. **Free Tier**: 60 requests per minute

#### DeepSeek
1. Visit [DeepSeek Platform](https://platform.deepseek.com/api_keys)
2. Sign up or log in
3. Navigate to API Keys section
4. Create a new API key
5. Copy the key
6. **Cost**: Very affordable (~$0.14 per 1M tokens)

#### OpenAI
1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Sign up or log in
3. Navigate to API Keys
4. Create a new secret key
5. Copy the key (you won't be able to see it again)
6. **Note**: Requires payment method on file

### Step 2: Configure Basic Settings

1. Go to **WordPress Admin → AI Content**
2. Navigate to the **Basic Settings** tab
3. Fill in the following:

   - **Main Topic**: Your content niche (e.g., "Technology News", "Health & Wellness")
   - **Topic Description**: Brief description of your content focus
   - **Sub Topics/Keywords**: Comma-separated keywords for diversity
   - **Writing Style**: Choose from Professional, Casual, Formal, or Conversational
   - **Target Audience**: Define who you're writing for
   - **Daily Post Count**: How many posts to generate daily (1-20)
   - **Word Count**: Target length for articles (500-3000 words)
   - **Generation Time**: When to run the daily task (default: 2:00 AM)
   - **Publish Mode**: Publish immediately or save as draft
   - **Default Category**: WordPress category for generated posts

4. Click **Save Changes**

### Step 3: Configure API Settings

1. Navigate to the **API Configuration** tab
2. Enter your API key(s) in the respective fields
3. Click **Test Connection** to verify each API
4. Configure API strategy:
   - **Active API**: Select your primary API
   - **Enable API Rotation**: Use multiple APIs in rotation
   - **Auto Switch on Failure**: Automatically use backup APIs

5. Adjust model parameters:
   - **Temperature**: Control creativity (0 = focused, 1 = creative)
   - **Max Tokens**: Limit response length

6. Click **Save Changes**

### Step 4: Customize Content Templates (Optional)

1. Navigate to the **Content Templates** tab
2. Customize the **System Prompt** to define AI behavior
3. Edit the **User Prompt Template** to control article structure
4. Available variables:
   - `{title}` - Article title
   - `{topic}` - Main topic
   - `{word_count}` - Target word count
   - `{style}` - Writing style
   - `{angle}` - Writing angle
   - `{date}` - Current date
   - `{audience}` - Target audience

5. Click **Save Changes**

## 🎯 Usage

### Manual Generation

Generate posts on-demand from the plugin dashboard:

1. Go to **WordPress Admin → AI Content**
2. Click **Generate 1 Post Now** or **Generate 5 Posts**
3. Wait for the process to complete
4. View your new posts in **Posts → All Posts**

### Automatic Scheduled Generation

The plugin automatically generates posts based on your settings:

1. Ensure **Daily Post Count** is set (greater than 0)
2. Set your preferred **Generation Time**
3. The WordPress Cron will run daily at the specified time
4. Check the **History & Stats** tab to view results

### View Generation History

1. Navigate to the **History & Stats** tab
2. View detailed statistics:
   - Total posts generated
   - Monthly count
   - API usage
   - Cost estimates
   - Success rate

3. Browse the generation history table
4. Click on post titles to edit
5. View error messages for failed generations

### Monitor System Logs

1. Navigate to the **Logs & Monitoring** tab
2. View system logs filtered by level (Info, Warning, Error)
3. Check API status and health
4. Monitor scheduled task status
5. Clear logs as needed

## 🔧 Advanced Configuration

### Custom Writing Angles

The plugin rotates between different writing angles to ensure content diversity:

- **News Report**: Latest developments and breaking news
- **In-depth Analysis**: Detailed examination and insights
- **How-to Guide**: Step-by-step instructions
- **Case Study**: Real-world examples and lessons
- **Trend Analysis**: Future predictions and patterns
- **Comparison**: Pros, cons, and alternatives
- **Opinion Piece**: Expert perspectives
- **Tutorial**: Practical learning content

### Anti-Duplication System

The plugin uses multiple mechanisms to prevent duplicate content:

1. **Title Similarity Check**: Levenshtein distance algorithm (85% threshold)
2. **Writing Angle Rotation**: Varies perspective for each post
3. **Keyword Mixing**: Random combinations from your keyword pool
4. **Prompt Variation**: Dynamic prompts with changing parameters
5. **Historical Title Database**: Stores last 500 generated titles

### API Rotation Strategy

Configure multiple APIs for better reliability:

1. **Priority Order**: Drag to set API priority
2. **Automatic Failover**: Switch to backup if primary fails
3. **Retry Logic**: Exponential backoff (2s, 4s, 8s)
4. **Cost Optimization**: Use cheaper APIs first

### WordPress Cron vs System Cron

**Default (WordPress Cron)**:
- Runs when someone visits your site
- Easy to set up, no server access needed
- May be unreliable on low-traffic sites

**System Cron (Recommended for production)**:
1. Add to wp-config.php: `define('DISABLE_WP_CRON', true);`
2. Set up system cron job:
```bash
# Run every hour
0 * * * * wget -q -O - https://yoursite.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1
```

## 📊 Cost Estimates

### Google Gemini
- **Gemini 1.5 Pro**: $0.25 per 1M input tokens, $0.50 per 1M output tokens
- **Gemini 1.5 Flash**: $0.05 per 1M input tokens, $0.10 per 1M output tokens
- **Free Tier**: 60 requests per minute

### DeepSeek
- **DeepSeek Chat**: $0.14 per 1M input tokens, $0.28 per 1M output tokens
- **Very affordable** for high-volume usage

### OpenAI
- **GPT-4**: $30 per 1M input tokens, $60 per 1M output tokens
- **GPT-3.5 Turbo**: $0.50 per 1M input tokens, $1.50 per 1M output tokens

**Example**: Generating one 1000-word article (~2500 tokens total):
- Gemini 1.5 Flash: ~$0.0004 (recommended for beginners)
- DeepSeek Chat: ~$0.0005
- GPT-3.5 Turbo: ~$0.0025

## 🐛 Troubleshooting

### Posts Not Generating Automatically

1. **Check WP Cron Status**:
   - Go to Logs & Monitoring tab
   - Verify "WP Cron Status" shows "Enabled"
   - If disabled, set up system cron (see above)

2. **Verify Schedule**:
   - Check "Schedule Status" in sidebar
   - Ensure "Next Run" shows a future time

3. **Test Manual Generation**:
   - Click "Generate 1 Post Now"
   - Check error messages in logs

### API Connection Failures

1. **Verify API Key**:
   - Go to API Configuration tab
   - Click "Test Connection" for each API
   - Check error messages

2. **Common Errors**:
   - **401 Unauthorized**: Invalid API key
   - **429 Rate Limit**: Too many requests, wait and retry
   - **500 Server Error**: API service issue, try later

3. **Enable Failover**:
   - Configure multiple APIs
   - Enable "Auto Switch on Failure"

### Duplicate Content Issues

1. **Check Title History**:
   - Increase uniqueness threshold in code
   - Clear old history: `AIACG_Database::cleanup_old_records(30)`

2. **Add More Keywords**:
   - Expand your sub-topics list
   - Use more diverse keywords

3. **Adjust Temperature**:
   - Increase temperature (0.8-1.0) for more creativity

### Performance Issues

1. **Increase PHP Limits**:
   ```php
   // In wp-config.php or .htaccess
   max_execution_time = 300
   memory_limit = 256M
   ```

2. **Reduce Concurrent Generation**:
   - Lower daily post count
   - Increase publish interval

3. **Optimize Database**:
   - Regularly clean old records
   - Reduce history retention days

## 🔐 Security

### API Key Storage

- API keys are stored in WordPress options table
- Transmitted only over HTTPS
- Never exposed in frontend/JavaScript
- Consider using environment variables for production

### Permissions

- Only administrators can access settings
- Uses WordPress nonces for AJAX requests
- All inputs are sanitized and validated

### Best Practices

1. **Use HTTPS** for your WordPress site
2. **Limit API Keys** to specific domains/IPs when possible
3. **Monitor Usage** to detect unauthorized access
4. **Rotate Keys** periodically
5. **Set Budget Limits** on API provider dashboards

## 🤝 Contributing

We welcome contributions! Here's how you can help:

1. **Report Bugs**: Open an issue with detailed information
2. **Suggest Features**: Describe your use case and proposal
3. **Submit Pull Requests**: Follow WordPress coding standards
4. **Improve Documentation**: Fix typos, add examples

## 📝 FAQ

**Q: Can I use this for commercial websites?**
A: Yes, the plugin is GPL licensed. Just ensure you comply with AI provider terms.

**Q: Will generated content pass AI detection?**
A: AI-generated content may be detected. We recommend human review and editing.

**Q: Can I edit generated posts?**
A: Absolutely! All posts can be edited like any WordPress post.

**Q: Does this work with Gutenberg/Classic Editor?**
A: Yes, content is generated as HTML and works with both editors.

**Q: How do I stop automatic generation?**
A: Set "Daily Post Count" to 0 in Basic Settings.

**Q: Can I generate in languages other than English?**
A: Yes, specify the target language in your prompts or topic description.

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2024 AI Auto Content Generator

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

## 🙏 Credits

- **Google Gemini API** - https://ai.google.dev/
- **DeepSeek API** - https://www.deepseek.com/
- **OpenAI API** - https://openai.com/
- WordPress Community

## 📞 Support

- **Issues**: Open a GitHub issue
- **Documentation**: See `/docs` folder
- **Email**: support@example.com (replace with your email)

## 🗺️ Roadmap

- [ ] Image generation integration (DALL-E, Stable Diffusion)
- [ ] Multi-language content generation
- [ ] Advanced SEO features (schema markup, internal linking)
- [ ] Content scheduling with calendar view
- [ ] Webhook integrations (Zapier, Make)
- [ ] Custom post type support
- [ ] Bulk editing and regeneration
- [ ] A/B testing for prompts
- [ ] Integration with popular SEO plugins
- [ ] REST API endpoints

---

Made with ❤️ for the WordPress community
