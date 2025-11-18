# Distribution Guide

Complete guide for distributing the AI Auto Content Generator plugin via WordPress.org and other channels.

## Table of Contents

1. [Pre-Distribution Checklist](#pre-distribution-checklist)
2. [WordPress.org Submission](#wordpressorg-submission)
3. [GitHub Releases](#github-releases)
4. [Manual Distribution](#manual-distribution)
5. [Update Management](#update-management)

---

## Pre-Distribution Checklist

Before submitting to WordPress.org or distributing publicly, ensure:

### Code Quality
- [x] All PHP files follow WordPress Coding Standards
- [x] All functions are properly documented
- [x] No PHP errors or warnings
- [x] Tested on PHP 7.4, 8.0, 8.1, 8.2
- [x] Tested on WordPress 5.0, 5.5, 6.0, 6.4

### Security
- [x] All user inputs sanitized
- [x] All outputs escaped
- [x] Nonce verification on all AJAX requests
- [x] Capability checks (manage_options)
- [x] SQL queries use $wpdb->prepare()
- [x] No eval() or similar dangerous functions
- [x] API keys stored securely

### Functionality
- [x] Plugin activates without errors
- [x] Plugin deactivates cleanly
- [x] Uninstall removes all data properly
- [x] Settings save and load correctly
- [x] All AJAX functions work
- [x] Cron jobs schedule properly
- [x] Database tables create correctly
- [x] No JavaScript errors in console

### Internationalization
- [x] All strings wrapped in translation functions
- [x] Text domain: 'ai-auto-content-generator'
- [x] POT file generated and included
- [x] load_plugin_textdomain() called properly

### Documentation
- [x] readme.txt in WordPress.org format
- [x] README.md with complete documentation
- [x] CHANGELOG.md with version history
- [x] Inline code comments
- [x] API setup guides
- [x] Quick start guide

### Assets
- [ ] Banner image (1544×500 and 772×250 px)
- [ ] Icon image (256×256 and 128×128 px)
- [ ] 6 screenshots (1280×960 px)
- [ ] All images optimized

### Legal
- [x] GPL v2 or later license
- [x] LICENSE file included
- [x] Copyright headers in all files
- [x] No proprietary dependencies

---

## WordPress.org Submission

### Step 1: Prepare Plugin Package

```bash
# Create a clean distribution directory
cd /path/to/plugin
mkdir -p dist
cp -r ai-auto-content-generator dist/

# Remove development files
cd dist/ai-auto-content-generator
rm -rf .git .gitignore node_modules
rm -rf tests phpcs.xml .phpcs.xml.dist

# Create ZIP file
cd ..
zip -r ai-auto-content-generator-1.0.1.zip ai-auto-content-generator/
```

### Step 2: Create WordPress.org Account

1. Go to https://wordpress.org/support/register.php
2. Create an account
3. Verify your email address

### Step 3: Submit Plugin

1. Visit https://wordpress.org/plugins/developers/add/
2. Upload your plugin ZIP file
3. Fill in the submission form:
   - Plugin Name: AI Auto Content Generator
   - Plugin Description: Brief description
   - Check "I have read and agree to the guidelines"
4. Click "Upload"

### Step 4: Wait for Review

- Review typically takes 1-15 days
- Check email for responses from WordPress.org Plugin Review Team
- Be prepared to make changes based on feedback

### Step 5: Create SVN Repository

Once approved, you'll receive SVN repository access:

```bash
# Checkout repository
svn co https://plugins.svn.wordpress.org/ai-auto-content-generator
cd ai-auto-content-generator

# Directory structure:
# /trunk       - Development version
# /tags        - Release versions
# /assets      - Screenshots, banners, icons
# /branches    - Optional branches

# Add plugin files to trunk
cp -r /path/to/plugin/ai-auto-content-generator/* trunk/

# Add files to SVN
cd trunk
svn add --force * --auto-props --parents --depth infinity -q
svn commit -m "Initial commit of version 1.0.1"
```

### Step 6: Add Assets

```bash
# Create assets directory
cd ..
mkdir assets

# Add your images:
# - banner-1544x500.png (high-res banner)
# - banner-772x250.png (low-res banner)
# - icon-256x256.png (high-res icon)
# - icon-128x128.png (low-res icon)
# - screenshot-1.png through screenshot-6.png

# Copy assets
cp /path/to/assets/* assets/

# Commit assets
svn add assets/*
svn commit -m "Add plugin assets"
```

### Step 7: Create First Release Tag

```bash
# Copy trunk to tags/1.0.1
svn cp trunk tags/1.0.1
svn commit -m "Tagging version 1.0.1"
```

Your plugin is now live on WordPress.org! 🎉

### Step 8: Monitor Plugin Page

- Check: https://wordpress.org/plugins/ai-auto-content-generator/
- Ensure all screenshots display correctly
- Verify readme.txt formatting
- Test installation from WordPress.org

---

## Asset Creation Guidelines

### Banner Images

**High Resolution (1544×500 px):**
- Use for Retina displays
- Should include:
  - Plugin name
  - Brief tagline
  - Visual representing AI/content
  - Branding colors

**Standard Resolution (772×250 px):**
- Scaled-down version of high-res banner
- Used on standard displays

**Design Tips:**
- Use high-contrast text
- Avoid small text (must be readable when scaled)
- Include plugin logo if available
- Match WordPress.org aesthetic

### Icon Images

**High Resolution (256×256 px):**
- Clear, simple icon
- Recognizable at small sizes
- Represents plugin function (AI, content, automation)
- Transparent background (PNG)

**Standard Resolution (128×128 px):**
- Scaled version of 256×256

**Icon Ideas:**
- Stylized "AI" letters
- Document with sparkles/stars
- Robot/brain hybrid
- Pen + circuit board

### Screenshot Guidelines

See [SCREENSHOTS.md](SCREENSHOTS.md) for detailed instructions.

---

## GitHub Releases

### Step 1: Tag Release

```bash
git tag -a v1.0.1 -m "Version 1.0.1 - Tools tab and enhanced features"
git push origin v1.0.1
```

### Step 2: Create Release on GitHub

1. Go to your GitHub repository
2. Click "Releases" → "Draft a new release"
3. Select tag: v1.0.1
4. Release title: "v1.0.1 - Enhanced Tools & Documentation"
5. Description: Copy from CHANGELOG.md
6. Attach ZIP file (without dev dependencies)
7. Click "Publish release"

### Step 3: Update Release Notes

Include:
- What's new
- Bug fixes
- Upgrade instructions
- Breaking changes (if any)
- Known issues

---

## Manual Distribution

### Creating Distribution ZIP

```bash
#!/bin/bash
# build-release.sh

VERSION="1.0.1"
PLUGIN_SLUG="ai-auto-content-generator"
BUILD_DIR="build"
DIST_DIR="dist"

# Clean previous builds
rm -rf $BUILD_DIR $DIST_DIR
mkdir -p $BUILD_DIR $DIST_DIR

# Copy plugin files
cp -r $PLUGIN_SLUG $BUILD_DIR/

# Remove development files
cd $BUILD_DIR/$PLUGIN_SLUG
rm -rf .git .gitignore node_modules tests
rm -rf .github .vscode .idea
rm build-release.sh

# Create ZIP
cd ..
zip -r "../$DIST_DIR/$PLUGIN_SLUG-$VERSION.zip" $PLUGIN_SLUG/

# Cleanup
cd ..
rm -rf $BUILD_DIR

echo "✅ Release package created: $DIST_DIR/$PLUGIN_SLUG-$VERSION.zip"
```

### Distribution Channels

1. **Your Website:**
   - Host ZIP file
   - Provide download link
   - Include installation instructions

2. **Third-Party Marketplaces:**
   - CodeCanyon (Envato Market)
   - Creative Market
   - Your own store

3. **Direct Sales:**
   - Gumroad
   - Easy Digital Downloads
   - WooCommerce

---

## Update Management

### Semantic Versioning

Follow [SemVer](https://semver.org/):

- **MAJOR** (1.0.0 → 2.0.0): Breaking changes
- **MINOR** (1.0.0 → 1.1.0): New features, backwards compatible
- **PATCH** (1.0.0 → 1.0.1): Bug fixes, backwards compatible

### Releasing Updates

#### On WordPress.org:

```bash
# 1. Update version in main plugin file
# ai-auto-content-generator.php
# Version: 1.0.2

# 2. Update readme.txt
# Stable tag: 1.0.2
# Add changelog entry

# 3. Commit to trunk
svn up
# Update files
svn commit -m "Update to version 1.0.2"

# 4. Create new tag
svn cp trunk tags/1.0.2
svn commit -m "Tagging version 1.0.2"

# WordPress.org will automatically detect and push update
```

#### On GitHub:

```bash
# 1. Update version numbers
# 2. Commit changes
git commit -am "Bump version to 1.0.2"

# 3. Tag release
git tag -a v1.0.2 -m "Version 1.0.2"
git push origin v1.0.2

# 4. Create GitHub release
```

### Update Notifications

WordPress.org automatically handles update notifications:

1. User sees update available in Plugins page
2. User clicks "Update"
3. WordPress downloads from your SVN tag
4. Plugin is updated automatically

### Update Server (Self-Hosted)

If distributing outside WordPress.org, implement update server:

```php
// In main plugin file
add_filter('pre_set_site_transient_update_plugins', 'aiacg_check_for_updates');

function aiacg_check_for_updates($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $remote = wp_remote_get(
        'https://yourdomain.com/updates/ai-auto-content-generator.json',
        array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json'
            )
        )
    );

    if (is_wp_error($remote) || 200 !== wp_remote_retrieve_response_code($remote) || empty(wp_remote_retrieve_body($remote))) {
        return $transient;
    }

    $remote = json_decode(wp_remote_retrieve_body($remote));

    if ($remote && version_compare(AIACG_VERSION, $remote->version, '<')) {
        $transient->response[AIACG_PLUGIN_BASENAME] = (object) array(
            'slug' => 'ai-auto-content-generator',
            'new_version' => $remote->version,
            'package' => $remote->download_url,
            'url' => $remote->homepage
        );
    }

    return $transient;
}
```

**JSON Response (yourdomain.com/updates/ai-auto-content-generator.json):**
```json
{
    "version": "1.0.2",
    "download_url": "https://yourdomain.com/downloads/ai-auto-content-generator-1.0.2.zip",
    "homepage": "https://yourdomain.com/plugins/ai-auto-content-generator",
    "requires": "5.0",
    "tested": "6.4",
    "requires_php": "7.4",
    "sections": {
        "description": "Plugin description",
        "changelog": "What's new in 1.0.2"
    }
}
```

---

## Beta Testing Program

### Setting Up Beta Channel

1. Create separate SVN branch or GitHub branch
2. Distribute beta ZIP to testers
3. Collect feedback
4. Fix issues before public release

### Beta Plugin Header

```php
/*
Plugin Name: AI Auto Content Generator (Beta)
Version: 1.1.0-beta.1
*/
```

---

## Marketing & Promotion

### Launch Checklist

- [ ] Create product page on your website
- [ ] Write launch blog post
- [ ] Share on social media (Twitter, LinkedIn, Facebook)
- [ ] Submit to plugin directories:
  - [ ] WordPress.org
  - [ ] WP Plugin Directory aggregators
- [ ] Reach out to WordPress bloggers/reviewers
- [ ] Create demo video (YouTube)
- [ ] Post in WordPress communities:
  - [ ] Reddit (r/WordPress, r/Blogging)
  - [ ] WordPress Facebook groups
  - [ ] WordPress forums
- [ ] Consider WordPress-focused newsletters
- [ ] Reach out to AI/automation blogs

### Ongoing Marketing

- Regular blog posts about use cases
- Tutorial videos
- User testimonials
- Case studies
- Guest posts on WordPress sites
- SEO optimization for plugin keywords

---

## Support Strategy

### Support Channels

1. **WordPress.org Support Forum**
   - Monitor daily
   - Respond within 24-48 hours
   - Mark resolved topics

2. **GitHub Issues**
   - Bug reports
   - Feature requests
   - Categorize with labels

3. **Email Support** (optional)
   - For premium support

### Documentation Maintenance

- Keep README.md updated
- Add FAQ entries based on common questions
- Create video tutorials
- Update screenshots when UI changes

---

## Analytics & Monitoring

### Track These Metrics

1. **Downloads:** WordPress.org stats
2. **Active Installations:** WordPress.org
3. **Ratings & Reviews:** Monitor and respond
4. **Support Questions:** Volume and topics
5. **Version Adoption:** Are users updating?

### Tools

- WordPress.org plugin stats dashboard
- Google Analytics (on plugin website)
- GitHub Insights
- User surveys

---

## Monetization Options (Optional)

If you want to monetize:

1. **Freemium Model:**
   - Free version on WordPress.org
   - Premium version with extra features

2. **Addon Plugins:**
   - Core plugin free
   - Paid addons for advanced features

3. **Support Plans:**
   - Free plugin
   - Paid priority support

4. **White Label:**
   - License to agencies for rebranding

---

## Legal Considerations

### Terms of Use

Create terms covering:
- API usage responsibility
- Content accuracy disclaimer
- No warranty clause
- Limitation of liability

### Privacy Policy

If collecting any data:
- Disclose what's collected
- How it's used
- Where it's stored
- User rights (GDPR, CCPA)

### Trademark

Consider trademarking:
- Plugin name
- Logo
- Tagline

---

## Version 1.0.1 Distribution Checklist

Ready to distribute v1.0.1:

- [x] Code complete and tested
- [x] readme.txt created
- [x] POT file generated
- [x] uninstall.php added
- [x] All documentation updated
- [x] CHANGELOG.md updated
- [x] GPL license included
- [ ] Screenshots created (see SCREENSHOTS.md)
- [ ] Banner images created
- [ ] Icon images created
- [ ] WordPress.org account created
- [ ] SVN repository initialized
- [ ] Plugin submitted for review

**Next Steps:**
1. Create assets (banner, icon, screenshots)
2. Submit to WordPress.org
3. Create GitHub release
4. Announce launch

---

**Questions?** See:
- [WordPress.org Plugin Developer Handbook](https://developer.wordpress.org/plugins/)
- [Plugin Review Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [SVN Usage Guide](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)

---

**Version:** 1.0.1
**Last Updated:** 2024-11-18
**Maintainer:** AI Auto Content Generator Team
