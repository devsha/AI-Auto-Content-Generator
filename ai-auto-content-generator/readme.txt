=== AI Auto Content Generator ===
Contributors: yourusername
Tags: ai, content, automation, gemini, openai, deepseek, auto-post, blog, writing
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically generate high-quality blog posts using AI (Google Gemini, DeepSeek, OpenAI). Schedule daily content with anti-duplication.

== Description ==

**AI Auto Content Generator** is a powerful WordPress plugin that uses artificial intelligence to automatically generate and publish high-quality blog posts on your site. Save time while maintaining consistent content output!

= Key Features =

* **Multiple AI Providers**: Support for Google Gemini, DeepSeek, and OpenAI APIs
* **Automatic Scheduling**: Generate 1-20 posts daily at your preferred time
* **Anti-Duplication System**: Advanced algorithms ensure unique content every time
* **API Rotation & Failover**: Automatic switching if one API fails
* **Customizable Templates**: Full control over prompts and writing styles
* **Cost Tracking**: Monitor API usage and costs in real-time
* **SEO Optimization**: Automatic meta descriptions and tags
* **Comprehensive History**: Track all generations with detailed statistics
* **Batch Operations**: Delete multiple history records at once
* **Settings Import/Export**: Easy migration between sites
* **Database Cleanup Tools**: Maintain optimal performance

= Supported AI APIs =

1. **Google Gemini** (Recommended for beginners)
   - Generous free tier (60 requests/minute)
   - Excellent quality
   - Very affordable: $0.05-0.50 per 1M tokens

2. **DeepSeek** (Best for high-volume)
   - Extremely affordable: $0.14-0.28 per 1M tokens
   - Great quality
   - Multilingual support

3. **OpenAI** (Premium option)
   - Industry-leading models (GPT-4, GPT-3.5)
   - Best for high-value content
   - $0.50-60 per 1M tokens

= Why Choose This Plugin? =

* **Save Time**: Automate your content creation workflow
* **Maintain Consistency**: Never miss a posting schedule
* **Affordable**: Start with free API tiers
* **No Coding Required**: User-friendly interface
* **Production Ready**: Professional-grade code and security
* **Extensible**: Open-source and developer-friendly

= Anti-Duplication Features =

* Title similarity checking (Levenshtein algorithm)
* Historical title database (500 most recent)
* 8 rotating writing angles
* Dynamic keyword mixing
* Automatic retry with variations

= Perfect For =

* Personal bloggers who want consistent content
* Niche authority sites needing high-volume publishing
* Content marketers managing multiple sites
* Affiliate marketers requiring regular updates
* News and magazine sites

= Cost Examples =

**With Gemini 1.5 Flash:**
* 3 posts/day: ~$0.03/month
* 10 posts/day: ~$0.11/month
* 30 posts/day: ~$0.33/month

*(Based on 1000 words per post. Free tier covers most small to medium sites!)*

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Go to Plugins → Add New
3. Search for "AI Auto Content Generator"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Go to Plugins → Add New → Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Click "Activate Plugin"

= Quick Setup (5 minutes) =

1. Get a free API key from [Google AI Studio](https://ai.google.dev/)
2. Go to WordPress Admin → AI Content
3. Click "API Configuration" tab
4. Paste your Gemini API key
5. Click "Test Connection" (should see green checkmark)
6. Click "Basic Settings" tab
7. Set your topic and preferences
8. Click "Generate 1 Post Now" to test
9. Done! The plugin will now generate posts automatically

For detailed setup instructions, see [Quick Start Guide](https://github.com/yourusername/ai-auto-content-generator/blob/main/QUICK-START.md)

== Frequently Asked Questions ==

= Do I need all three API keys? =

No! You only need one API key to get started. We recommend starting with Google Gemini because it has a generous free tier and is easy to set up.

= How much does it cost? =

The plugin itself is free. You only pay for the AI API usage. With Google Gemini's free tier, you can generate dozens of posts per day at no cost. DeepSeek is extremely affordable at ~$0.16/month for 300 posts.

= Will the content be unique? =

Yes! The plugin includes advanced anti-duplication mechanisms:
- Title similarity checking
- Historical title database
- Rotating writing angles
- Dynamic keyword mixing

However, we always recommend reviewing and editing AI-generated content before publishing.

= Can I customize the writing style? =

Absolutely! You have full control over:
- System prompts (AI personality)
- User prompt templates
- Writing angles
- Word count
- Target audience
- Topic focus

= What if I run out of API quota? =

The plugin includes automatic API failover. If one API fails or hits rate limits, it automatically switches to your backup API. You can also enable API rotation to distribute load.

= Will this work with my theme? =

Yes! The plugin creates standard WordPress posts that work with any theme. It supports:
- Categories
- Tags
- Featured images (if set)
- Custom post statuses (Draft/Published)

= Is it safe to use? =

Yes! The plugin follows WordPress coding standards and includes:
- Nonce verification
- Capability checks
- Input sanitization
- Output escaping
- Secure API key storage

= Can I schedule posts for different times? =

Yes! You can:
- Set daily generation time (e.g., 2:00 AM)
- Configure publish intervals between posts
- Set publish mode (immediate or draft for review)

= How do I get support? =

- Check the [Documentation](https://github.com/yourusername/ai-auto-content-generator)
- Open an [Issue on GitHub](https://github.com/yourusername/ai-auto-content-generator/issues)
- Review the [FAQ in README](https://github.com/yourusername/ai-auto-content-generator#faq)

== Screenshots ==

1. Basic Settings - Configure your topic, style, and posting schedule
2. API Configuration - Set up multiple AI providers with testing tools
3. Content Templates - Customize prompts and writing angles
4. History & Stats - Track generation history and costs
5. Logs & Monitoring - View system logs and status
6. Tools - Import/export settings, database cleanup, system info

== Changelog ==

= 1.0.2 - 2024-11-18 =

**Added:**
* Internationalization support with complete POT template file
* Proper uninstall.php for clean plugin removal
* WordPress.org standard readme.txt
* Comprehensive distribution guide (DISTRIBUTION.md)
* Screenshot documentation guide (SCREENSHOTS.md)
* Production-ready documentation for plugin distribution

**Improved:**
* Plugin now fully prepared for WordPress.org submission
* Enhanced cleanup on uninstallation
* Translation-ready implementation
* Better documentation for distributors

= 1.0.1 - 2024-11-18 =

**Added:**
* New "Tools" tab with advanced management features
* Settings import/export functionality (JSON format)
* Database cleanup tools (delete old records)
* Statistics reset function
* System information display with copy function
* Plugin reset functionality
* Batch operations for history records (select multiple, delete all)
* PROMPT-TEMPLATES.md with 15+ industry-specific templates
* CHANGELOG.md with semantic versioning
* CONTRIBUTING.md with contribution guidelines
* UPDATE-NOTES-v1.0.1.md with comprehensive release notes
* Internationalization POT template file
* Proper uninstall.php for clean plugin removal
* WordPress.org compatible readme.txt

**Improved:**
* Enhanced error handling and user feedback
* Better AJAX response formatting
* More detailed system diagnostics
* Improved CSS styling consistency

**Fixed:**
* PHP 7.4 compatibility issues with string interpolation
* Various minor bugs in admin interface

= 1.0.0 - 2024-11-15 =

**Initial Release:**
* Multi-API support (Gemini, DeepSeek, OpenAI)
* Automatic daily content generation
* Anti-duplication system with Levenshtein algorithm
* 5-tab admin interface
* WordPress Cron integration
* API rotation and failover
* Usage statistics and cost tracking
* SEO optimization features
* Comprehensive documentation

== Upgrade Notice ==

= 1.0.2 =
Adds complete internationalization support and WordPress.org distribution readiness. Includes proper uninstall handler and comprehensive distribution documentation.

= 1.0.1 =
Adds powerful new Tools tab with settings import/export, database cleanup, and system diagnostics. Includes 1200+ lines of new prompt templates. Highly recommended upgrade.

= 1.0.0 =
Initial release of AI Auto Content Generator.

== Support ==

For support, please:

1. Check the [Documentation](https://github.com/yourusername/ai-auto-content-generator)
2. Review [Quick Start Guide](https://github.com/yourusername/ai-auto-content-generator/blob/main/QUICK-START.md)
3. Check [API Setup Guide](https://github.com/yourusername/ai-auto-content-generator/blob/main/API-SETUP-GUIDE.md)
4. Open an [Issue on GitHub](https://github.com/yourusername/ai-auto-content-generator/issues)

== Privacy & Data ==

This plugin:
* Stores API keys locally in your WordPress database (never transmitted to our servers)
* Sends your configured prompts and topics to the AI providers you choose
* Does not collect any analytics or usage data
* Does not phone home or connect to external services except the AI APIs you configure

You are responsible for:
* Complying with the AI provider's terms of service
* Reviewing AI-generated content before publishing
* Ensuring content accuracy and appropriateness

== Contributing ==

We welcome contributions! Please see our [Contributing Guide](https://github.com/yourusername/ai-auto-content-generator/blob/main/CONTRIBUTING.md) for details on:
* Bug reports
* Feature requests
* Code contributions
* Documentation improvements

== Credits ==

* Developed with ❤️ for the WordPress community
* Uses Google Gemini, DeepSeek, and OpenAI APIs
* Built following WordPress coding standards

== License ==

This plugin is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify
> it under the terms of the GNU General Public License as published by
> the Free Software Foundation; either version 2 of the License, or
> (at your option) any later version.
>
> This program is distributed in the hope that it will be useful,
> but WITHOUT ANY WARRANTY; without even the implied warranty of
> MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
> GNU General Public License for more details.
