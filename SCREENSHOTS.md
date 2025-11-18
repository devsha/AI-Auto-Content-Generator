# Screenshot Guide for WordPress.org

This document describes the screenshots to be taken for the WordPress.org plugin repository.

## Screenshot Specifications

**WordPress.org Requirements:**
- Format: PNG or JPEG
- Recommended size: 1280×960 pixels
- Maximum file size: 1MB per screenshot
- Naming convention: `screenshot-1.png`, `screenshot-2.png`, etc.
- Location: `/assets/` directory in SVN repository

## Required Screenshots

### Screenshot 1: Basic Settings Tab
**File:** `screenshot-1.png`

**What to show:**
- Main settings page with "Basic Settings" tab active
- Visible fields:
  - Main Topic
  - Topic Description
  - Sub Topics (list)
  - Writing Style dropdown
  - Target Audience
  - Daily Post Count slider
  - Word Count slider
  - Generation Time picker
  - Publish Mode radio buttons
  - Default Category dropdown
  - Auto Generate Tags checkbox
  - SEO Optimization checkbox
- Sidebar with Quick Actions and Generation Schedule widgets
- "Save Changes" button visible

**Caption:** "Basic Settings - Configure your topic, style, and posting schedule"

**How to capture:**
1. Navigate to WordPress Admin → AI Content
2. Ensure "Basic Settings" tab is active
3. Fill in sample data (e.g., Topic: "Technology News")
4. Capture full page with browser at 1280px width

---

### Screenshot 2: API Configuration Tab
**File:** `screenshot-2.png`

**What to show:**
- "API Configuration" tab active
- All three API sections visible:
  - Google Gemini API
  - DeepSeek API
  - OpenAI API
- For at least one API (Gemini), show:
  - API Key field (with dummy key or masked)
  - Model dropdown
  - Temperature slider
  - Max Tokens input
  - "Test Connection" button
  - Success indicator (green checkmark) after testing
- Usage statistics box showing:
  - Total Requests
  - Total Tokens
  - Avg Latency
  - Estimated Cost

**Caption:** "API Configuration - Set up multiple AI providers with testing tools"

**How to capture:**
1. Navigate to "API Configuration" tab
2. Enter a test API key (can be dummy for screenshot)
3. Show successful connection test result
4. Ensure usage stats are visible
5. Capture full page

---

### Screenshot 3: Content Templates Tab
**File:** `screenshot-3.png`

**What to show:**
- "Content Templates" tab active
- Three main sections:
  1. **System Prompt** - Large textarea with sample professional prompt visible
  2. **User Prompt Template** - Textarea showing template with variables ({topic}, {style}, etc.)
  3. **Writing Angles** - List showing different angles:
     - News reporting
     - How-to guide
     - In-depth analysis
     - Product review
     - etc.
- Available variables helper text visible
- "Save Changes" button

**Caption:** "Content Templates - Customize prompts and writing angles"

**How to capture:**
1. Navigate to "Content Templates" tab
2. Ensure system prompt has content visible
3. Show user prompt template with variables
4. Display all writing angles
5. Capture full page

---

### Screenshot 4: History & Stats Tab
**File:** `screenshot-4.png`

**What to show:**
- "History & Stats" tab active
- Statistics Dashboard at top with 4 metrics:
  - Total Posts Generated: 127
  - Success Rate: 98.4%
  - Total Cost: $0.45
  - Avg Generation Time: 12.3s
- Generation History table with multiple rows showing:
  - ID
  - Title (sample article titles)
  - Topic
  - API Used (Gemini, DeepSeek, OpenAI)
  - Tokens
  - Cost
  - Time
  - Status (Success/Failed indicators)
  - Actions (View, Delete buttons)
- Pagination visible at bottom
- Batch selection checkboxes visible

**Caption:** "History & Stats - Track generation history and costs"

**How to capture:**
1. Navigate to "History & Stats" tab
2. Ensure database has sample history records (generate a few posts)
3. Show variety of API usage
4. Display both successful and one failed status
5. Capture full page with table visible

---

### Screenshot 5: Logs & Monitoring Tab
**File:** `screenshot-5.png`

**What to show:**
- "Logs & Monitoring" tab active
- Log filter dropdown (All Levels, Info, Warning, Error)
- "Clear Logs" button
- System Logs table with various log entries:
  - Timestamps
  - Level badges (Info - blue, Warning - orange, Error - red)
  - Messages showing typical operations:
    - "Starting content generation..."
    - "API connection successful"
    - "Post published successfully"
    - Maybe one warning
- System Information box showing:
  - Plugin Version: 1.0.1
  - WordPress Version
  - PHP Version
  - Server Software
  - WP Cron Status: Enabled (green)
- Scheduled Tasks Status showing next run time

**Caption:** "Logs & Monitoring - View system logs and status information"

**How to capture:**
1. Navigate to "Logs & Monitoring" tab
2. Ensure logs have various entries (run some operations first)
3. Show mix of log levels
4. System info should show actual values
5. Capture full page

---

### Screenshot 6: Tools Tab (NEW in v1.0.1)
**File:** `screenshot-6.png`

**What to show:**
- "Tools" tab active
- All tool sections visible:
  1. **Settings Management**
     - Export Settings button
     - Import Settings file input and button
  2. **Database Management**
     - Clean Old Records with days input
     - Reset Statistics button
  3. **System Tools**
     - Test WordPress Cron button
     - Copy System Info button
  4. **Danger Zone** (red border)
     - Reset Plugin button with warning text
- Each section with descriptive text
- Clean, organized layout

**Caption:** "Tools - Import/export settings, database cleanup, and system diagnostics"

**How to capture:**
1. Navigate to "Tools" tab
2. Ensure all sections are visible
3. Show the danger zone with red styling
4. Capture full page

---

## Additional Optional Screenshots

### Screenshot 7: Mobile Responsive View
**File:** `screenshot-7.png`

**Specs:** 768×1024 pixels (tablet view)

**What to show:**
- How the interface adapts to smaller screens
- Tab navigation responsive behavior
- Settings forms stacking properly

---

### Screenshot 8: Quick Actions Success
**File:** `screenshot-8.png`

**What to show:**
- Success notification after clicking "Generate 1 Post Now"
- Toast message or admin notice
- Shows the user experience of manual generation

---

## Screenshot Preparation Checklist

Before taking screenshots:

- [ ] Install WordPress with a clean, default admin theme
- [ ] Install and activate the plugin
- [ ] Configure at least one API (Gemini recommended)
- [ ] Generate 5-10 sample posts to populate history
- [ ] Fill in all settings with realistic sample data
- [ ] Ensure no sensitive data is visible (real API keys, personal info)
- [ ] Test API connections to show success indicators
- [ ] Generate logs by running various operations
- [ ] Browser zoom at 100%
- [ ] Clear browser console errors
- [ ] Hide WordPress admin notices/warnings that might distract

## Image Editing

After capturing:

1. **Crop & Resize:**
   - Crop to 1280×960 pixels
   - Remove browser chrome if needed
   - Keep only WordPress admin area

2. **Annotate (Optional):**
   - Add arrows pointing to key features
   - Add callout boxes for important elements
   - Use plugin branding colors

3. **Optimize:**
   - Compress PNG files (TinyPNG, etc.)
   - Ensure under 1MB each
   - Maintain quality

4. **File Naming:**
   - `screenshot-1.png`
   - `screenshot-2.png`
   - etc.

## Uploading to WordPress.org

Screenshots go in the SVN repository:

```
https://plugins.svn.wordpress.org/ai-auto-content-generator/
└── assets/
    ├── screenshot-1.png
    ├── screenshot-2.png
    ├── screenshot-3.png
    ├── screenshot-4.png
    ├── screenshot-5.png
    └── screenshot-6.png
```

**Commands:**
```bash
svn checkout https://plugins.svn.wordpress.org/ai-auto-content-generator/
cd ai-auto-content-generator
mkdir assets
# Add your screenshots to assets/ folder
svn add assets/screenshot-*.png
svn commit -m "Add plugin screenshots"
```

## Screenshot Description Format

In `readme.txt`, screenshots are described as:

```
== Screenshots ==

1. Basic Settings - Configure your topic, style, and posting schedule
2. API Configuration - Set up multiple AI providers with testing tools
3. Content Templates - Customize prompts and writing angles
4. History & Stats - Track generation history and costs
5. Logs & Monitoring - View system logs and status
6. Tools - Import/export settings, database cleanup, system info
```

## Quality Standards

✅ **Good Screenshots:**
- Clear, readable text
- Consistent styling
- Real, meaningful data (not just "Test 1", "Test 2")
- Show plugin value at a glance
- Professional appearance

❌ **Avoid:**
- Blurry images
- Lorem ipsum everywhere
- Real API keys or sensitive data
- Browser errors/warnings visible
- Inconsistent data between screenshots
- Empty states unless intentional

---

**Ready to Submit?**

Once screenshots are ready:
1. Upload to WordPress.org SVN
2. Verify they display correctly on plugin page preview
3. Update if needed based on reviewer feedback

**Questions?** See [WordPress.org Plugin Assets Guidelines](https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/)
