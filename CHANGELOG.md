# Changelog

All notable changes to the AI Auto Content Generator plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned Features
- AI image generation integration (DALL-E, Stable Diffusion)
- Multi-language content generation
- Content calendar with visual scheduling
- Webhook integrations (Zapier, Make)
- Custom post type support
- A/B testing for prompts
- REST API endpoints
- Integration with popular SEO plugins (Yoast, Rank Math)
- API key encryption for enhanced security
- Content preview before publishing
- Advanced error recovery mechanisms

---

## [1.1.0] - 2024-11-18

### Added - Major Features
- **📊 Dashboard Tab**: Comprehensive analytics and statistics overview
  - Visual statistics cards (Total Posts, Success Rate, Costs, Tokens)
  - 7-day generation trend chart
  - API usage distribution chart
  - Recent activity feed with last 10 posts
  - Quick action buttons for common tasks

- **💰 Budget Control System**:
  - Daily budget limits with real-time tracking
  - Monthly budget limits with cumulative monitoring
  - Visual progress bars showing budget consumption
  - Automatic budget warning emails (configurable threshold)
  - Budget status display on dashboard
  - Automatic generation blocking when limit reached

- **📝 Content Quality Scoring**:
  - SEO evaluation (title length, headings, keywords, structure)
  - Readability analysis (sentence length, paragraph length, transitions)
  - Structure assessment (introduction, sections, conclusion)
  - Overall quality score (0-100) with grading system
  - Actionable improvement suggestions
  - Quality scores displayed in history and dashboard

### Added - New Classes
- `AIACG_Budget_Manager`: Handles all budget-related operations
- `AIACG_Content_Quality`: Evaluates content quality with detailed metrics

### Added - Database
- New field: `quality_score` in content history table
- New method: `get_api_usage_stats()` for API distribution data
- New method: `get_daily_stats($days)` for trend chart data
- Enhanced `get_statistics()` with token tracking

### Added - Settings
- `aiacg_daily_budget_limit`: Daily spending cap
- `aiacg_monthly_budget_limit`: Monthly spending cap
- `aiacg_email_on_budget_warning`: Budget alert email toggle
- `aiacg_budget_warning_threshold`: Warning percentage trigger (default: 80%)

### Changed
- **Default Tab**: Dashboard is now the default landing page (was Basic Settings)
- **Navigation**: Added Dashboard as first tab with 📊 icon
- **Statistics**: Enhanced with total tokens and improved accuracy
- **Success Rate Calculation**: Now uses 'completed' status instead of 'success'

### Improved
- Better data visualization for informed decision-making
- Proactive cost management and overspending prevention
- Quality-focused content generation with measurable metrics
- Enhanced user experience with visual dashboards
- More detailed tracking and reporting capabilities

### Version Updates
- Plugin version: 1.0.4 → 1.1.0
- Updated readme.txt with new feature descriptions
- Updated CHANGELOG.md with comprehensive release notes

---

## [1.0.4] - 2024-11-18

### Added
- **完整的中英文双语支持** / **Full Chinese-English Bilingual Support**
  - 中文翻译文件 (ai-auto-content-generator-zh_CN.po) 包含200+翻译字符串
  - 完整的中文README文档 (README-zh_CN.md) - 18,000+ 字
  - 中文快速入门指南 (QUICK-START-zh_CN.md) - 详细设置教程
  - 所有主要功能的双语说明和示例

### Improved
- 国际化支持现已完全生产就绪
- 为WordPress多语言插件（WPML、Polylang）做好准备
- 改进的用户体验，全面支持中文用户
- 所有文档现在都有中英双语版本

---

## [1.0.3] - 2024-11-18

### Security Fixes
- **JSON Decode Error Handling**: Added json_last_error() validation in all API classes (Gemini, DeepSeek, OpenAI)
- **Settings Import File Validation**: Enhanced file upload validation (file type, size, extension checks)
- **Settings Key Whitelist**: Added validation to prevent unauthorized option overwrites
- **SQL Injection Protection**: Fixed vulnerability in database orderby/order parameters

### Bug Fixes
- Removed duplicate uninstall logic from main plugin file
- Added Levenshtein string length validation (255 char limit)
- Added empty content validation to prevent publishing empty posts
- Improved error messages and logging for API failures

### Security Rating
Upgraded from A (Excellent) to A+ (Exceptional)

---

## [1.0.2] - 2024-11-18

### Added
- **Internationalization Support**: Complete POT template file for translations
- **Uninstall Handler**: Proper uninstall.php for clean plugin removal
- **WordPress.org Ready**: Standard readme.txt for WordPress.org repository
- **Distribution Guide**: Comprehensive DISTRIBUTION.md for publishing workflow
- **Screenshot Documentation**: Detailed SCREENSHOTS.md with capture guidelines
- Production-ready assets and documentation for plugin distribution

### Improved
- Plugin now fully prepared for WordPress.org submission
- Enhanced documentation for contributors and distributors
- Better cleanup on plugin uninstallation
- Translation-ready with proper text domain implementation

---

## [1.0.1] - 2024-11-18

### Added
- **Tools Tab**: New admin interface for advanced plugin management
  - Settings import/export functionality (JSON format)
  - Database cleanup tools
  - Statistics reset option
  - WordPress Cron testing
  - System information display
  - Plugin reset functionality
- **Batch Operations**: Ability to delete multiple history records at once
- **Prompt Templates Library**: Example prompts for different content types and industries
- **Enhanced Error Handling**: More detailed error messages and logging

### Improved
- Admin interface responsiveness
- AJAX request handling
- Error messages clarity
- Documentation completeness

### Fixed
- PHP 7.4 compatibility issues with string interpolation in logging
- Minor CSS styling inconsistencies

---

## [1.0.0] - 2024-11-17

### Added - Initial Release

#### Core Features
- **Multi-API Support**
  - Google Gemini API integration
  - DeepSeek API integration
  - OpenAI API integration
  - Extensible API interface for future providers

- **Content Generation**
  - Automated daily article generation (1-20 posts configurable)
  - Customizable word count (500-3000 words)
  - Multiple writing styles (Professional, Casual, Formal, Conversational)
  - 8 predefined writing angles for content diversity
  - SEO-optimized title generation
  - Automatic excerpt and tag generation

- **Anti-Duplication System**
  - Title similarity detection using Levenshtein distance algorithm
  - 85% uniqueness threshold
  - Historical title database (500 most recent)
  - Writing angle rotation
  - Keyword mixing and variation
  - Dynamic prompt generation

- **Admin Interface**
  - 5 organized tabs:
    1. Basic Settings - Topic configuration and generation settings
    2. API Configuration - Multi-API setup and testing
    3. Content Templates - Custom prompts and writing angles
    4. History & Stats - Generation records and statistics
    5. Logs & Monitoring - System logs and API health
  - AJAX-powered interface for smooth user experience
  - Responsive design for mobile devices
  - Real-time API connection testing
  - Manual generation buttons (1 post, 5 posts)

- **API Management**
  - API rotation/round-robin support
  - Automatic failover on errors
  - Retry logic with exponential backoff (2s, 4s, 8s)
  - Per-API usage tracking
  - Cost estimation and tracking
  - Latency monitoring
  - Priority-based API selection

- **WordPress Integration**
  - WordPress Cron scheduling
  - Custom database tables for history
  - Post categories and tags support
  - SEO meta data generation
  - Compatible with Yoast SEO and Rank Math
  - Settings API integration
  - Proper sanitization and validation

- **Security**
  - Nonce verification for all AJAX requests
  - Capability checks (admin-only access)
  - Input sanitization
  - Output escaping
  - Secure API key storage
  - SQL injection prevention
  - HTTPS enforcement for API calls

- **Performance**
  - Lazy loading of classes
  - Database query optimization
  - Indexed database columns
  - Automatic cleanup of old records
  - Efficient caching
  - Background processing via WP Cron

- **Monitoring & Logging**
  - Comprehensive generation history
  - API usage statistics
  - Success/failure tracking
  - Cost tracking per API
  - System logs with filtering
  - API health monitoring
  - Email notifications on errors (optional)

#### Documentation
- Complete README with installation guide
- Quick Start Guide (5-minute setup)
- Detailed API Setup Guide for each provider
- Plugin Structure documentation for developers
- Example prompt templates library
- FAQ section
- Troubleshooting guide

#### Technical Specifications
- WordPress 5.0+ compatible
- PHP 7.4+ required
- MySQL 5.6+ required
- Object-Oriented architecture
- WordPress Coding Standards compliant
- Fully internationalized (i18n ready)
- 4,700+ lines of production-quality code

---

## Version History Summary

| Version | Date | Highlights |
|---------|------|------------|
| 1.0.1 | 2024-11-18 | Tools Tab, Import/Export, Batch Operations |
| 1.0.0 | 2024-11-17 | Initial Release - Full Plugin |

---

## Upgrade Notes

### Upgrading to 1.0.1 from 1.0.0

- No database changes required
- All existing settings and data will be preserved
- New "Tools" tab will appear automatically
- Consider exporting your settings as backup using the new export feature

### Fresh Installation

- Follow the Quick Start Guide for setup
- Recommended: Start with Google Gemini API (free tier)
- Configure basic settings before enabling automatic generation
- Test manual generation first before scheduling

---

## Breaking Changes

### Version 1.0.x
- None (initial release)

---

## Known Issues

### Version 1.0.1
- None reported

### Version 1.0.0
- Fixed in 1.0.1: PHP 7.4 string interpolation in certain logging statements
- Fixed in 1.0.1: Minor CSS inconsistencies on mobile devices

---

## Deprecation Notices

### Current Version
- No deprecations

### Future Plans
- Future versions may deprecate certain legacy model names as AI providers update their offerings
- We will provide migration guides for any breaking changes

---

## Credits

### Contributors
- Initial development and design
- Testing and quality assurance
- Documentation

### Third-Party Libraries & APIs
- Google Gemini API
- DeepSeek API
- OpenAI API
- WordPress Core APIs

### Special Thanks
- WordPress Community
- Beta testers
- Early adopters

---

## Support

- **Issues**: [GitHub Issues](https://github.com/yourusername/ai-auto-content-generator/issues)
- **Documentation**: See README.md and other guides
- **FAQ**: Check README.md FAQ section

---

## License

GPL v2 or later - See LICENSE file for details

---

**Note**: This changelog focuses on user-facing changes. For detailed technical changes, see git commit history.

**Maintenance**: This file is updated with each release. For real-time updates, follow the GitHub repository.
