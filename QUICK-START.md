# Quick Start Guide

Get your AI Auto Content Generator up and running in 5 minutes!

## Prerequisites

- WordPress 5.0+ installed
- Administrator access to WordPress
- At least one AI API key (see recommendations below)

## Recommended: Start with Gemini (Free)

We recommend starting with **Google Gemini** because:
- ✅ Free tier available (no credit card needed)
- ✅ 60 requests per minute
- ✅ Excellent quality
- ✅ Easy setup

## Installation Steps

### 1. Install the Plugin

**Option A: Via WordPress Admin**
```
1. Download the plugin ZIP
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Choose the ZIP file → Install Now
4. Click "Activate"
```

**Option B: Manual Upload**
```bash
# Upload to your WordPress plugins directory
cp -r ai-auto-content-generator /path/to/wordpress/wp-content/plugins/
# Then activate via WordPress Admin → Plugins
```

### 2. Get Your API Key (Gemini - 2 minutes)

1. Visit: https://ai.google.dev/
2. Click "Get API key"
3. Sign in with Google
4. Click "Create API key in new project"
5. **Copy the key** (you'll need it next!)

### 3. Basic Configuration (3 minutes)

#### A. Enter API Key
1. Go to **WordPress Admin → AI Content**
2. Click **"API Configuration"** tab
3. Paste your Gemini API key
4. Click **"Test Connection"** (should see green checkmark)
5. Click **"Save Changes"**

#### B. Set Your Topic
1. Click **"Basic Settings"** tab
2. Fill in:
   - **Main Topic**: e.g., "Technology News"
   - **Sub Topics**: e.g., "AI, Cloud Computing, Cybersecurity"
   - **Writing Style**: Professional
   - **Daily Post Count**: 3
   - **Word Count**: 1000 (use slider)
3. Click **"Save Changes"**

### 4. Generate Your First Post!

1. At the top of the page, click **"Generate 1 Post Now"**
2. Wait 10-30 seconds
3. You'll be redirected to your Posts page
4. **Edit and review** the generated post
5. Publish when ready!

## What Happens Next?

### Automatic Daily Generation

The plugin will now automatically:
- Generate 3 posts every day at 2:00 AM (default time)
- Save them as published posts (or drafts, based on your settings)
- Track all generations in the History tab

### Check the Results

Go to **AI Content → History & Stats** to see:
- All generated posts
- Success/failure status
- Cost estimates
- API usage stats

## Customization Tips

### Want Different Content Each Day?

The plugin automatically ensures variety by:
- ✅ Checking for duplicate titles
- ✅ Rotating writing angles (news, guides, analysis, etc.)
- ✅ Mixing keywords from your sub-topics
- ✅ Adding date context to prompts

### Adjust Quality vs. Speed

In **API Configuration** tab, adjust Temperature:
- **0.3-0.5**: More focused, factual content
- **0.7** (default): Balanced creativity
- **0.9-1.0**: More creative, varied content

### Change Posting Schedule

In **Basic Settings**:
- **Generation Time**: Change from 2:00 AM to any time
- **Publish Interval**: Space out multiple posts (e.g., 30 min apart)
- **Publish Mode**: Switch to "Draft" for manual review

## Troubleshooting

### No Posts Generated?

1. Check **Logs & Monitoring** tab for errors
2. Verify WP Cron is enabled (shown in Logs tab)
3. Try manual generation first: "Generate 1 Post Now"

### API Connection Failed?

1. Double-check you copied the entire API key
2. No extra spaces before/after the key
3. For Gemini: Make sure you're signed in to the correct Google account

### Content Quality Issues?

1. Go to **Content Templates** tab
2. Customize the prompts to be more specific
3. Add more detailed instructions
4. Increase word count for more comprehensive articles

## Next Steps

### 1. Review Generated Content
- Always edit generated content before publishing
- Add your own insights and examples
- Check for factual accuracy

### 2. Optimize Prompts
- Go to **Content Templates** tab
- Customize the system and user prompts
- Add specific instructions for your niche

### 3. Monitor Costs
- Check **History & Stats** → Total Cost
- Gemini free tier: 60 requests/min is generous
- Upgrade to paid when needed

### 4. Set Up Multiple APIs (Optional)
- Add DeepSeek or OpenAI as backups
- Enable **"Auto Switch on Failure"**
- Provides redundancy if one API is down

## Cost Examples

**With Gemini 1.5 Flash (cheapest option):**

| Posts/Day | Monthly Cost |
|-----------|--------------|
| 3 posts   | ~$0.03       |
| 10 posts  | ~$0.11       |
| 30 posts  | ~$0.33       |

*(Based on 1000 words/post)*

**Free tier covers most small to medium sites!**

## Common Use Cases

### Personal Blog
```
Daily Posts: 1-2
Word Count: 800
Topic: Your niche (travel, recipes, tech reviews)
Cost: Likely free with Gemini
```

### Niche Authority Site
```
Daily Posts: 5-10
Word Count: 1200
Topic: Specific industry or topic
Cost: $0.10-0.30/month with Gemini Flash
```

### Content Marketing Agency
```
Daily Posts: 20-50
Word Count: 1500
Use: DeepSeek or multiple APIs
Cost: $0.50-2.00/month
```

## Best Practices

1. ✅ **Always review** generated content before publishing
2. ✅ **Add human touch**: personal stories, recent news, specific examples
3. ✅ **Check facts**: AI can make mistakes
4. ✅ **SEO optimization**: Review titles and meta descriptions
5. ✅ **Monitor quality**: Adjust prompts based on results
6. ✅ **Start small**: Test with 1-3 posts/day initially
7. ✅ **Track performance**: Use Google Analytics to see what works

## Support & Resources

- 📖 **Full Documentation**: See [README.md](README.md)
- 🔑 **API Setup Guide**: See [API-SETUP-GUIDE.md](API-SETUP-GUIDE.md)
- 💬 **Issues**: Open a GitHub issue
- 📧 **Email**: support@example.com

## Upgrade Path

As you grow:

1. **Week 1-2**: Use Gemini free tier, 1-3 posts/day
2. **Month 1**: Increase to 5-10 posts/day if quality is good
3. **Month 2+**: Consider DeepSeek for high-volume (very affordable)
4. **Premium**: Use GPT-4 for high-value client content

---

## You're Ready! 🚀

You've successfully set up AI Auto Content Generator. Here's what to do now:

1. ✅ Generate your first test post
2. ✅ Review and edit it
3. ✅ Adjust settings based on results
4. ✅ Let it run automatically
5. ✅ Monitor quality and costs

**Happy content generating!**

---

**Questions?** Check the [FAQ section in README.md](README.md#faq) or open an issue.
