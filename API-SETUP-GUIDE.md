# API Setup Guide

Complete guide to obtaining and configuring API keys for the AI Auto Content Generator plugin.

## Table of Contents

1. [Google Gemini API](#google-gemini-api)
2. [DeepSeek API](#deepseek-api)
3. [OpenAI API](#openai-api)
4. [API Comparison](#api-comparison)
5. [Cost Management](#cost-management)
6. [Troubleshooting](#troubleshooting)

---

## Google Gemini API

### Why Choose Gemini?
- ✅ Generous free tier (60 requests/minute)
- ✅ High-quality content generation
- ✅ Fast response times
- ✅ Affordable pricing
- ✅ Great for beginners

### Step-by-Step Setup

#### 1. Create a Google Account
If you don't have one, sign up at [accounts.google.com](https://accounts.google.com)

#### 2. Access Google AI Studio
1. Visit [https://ai.google.dev/](https://ai.google.dev/)
2. Click "Get started" or "Get API key"
3. Sign in with your Google account

#### 3. Create API Key
1. In Google AI Studio, click **"Get API key"**
2. Select **"Create API key in new project"** or choose an existing project
3. Copy the generated API key
4. **Important**: Save this key securely - you'll need it for the plugin

#### 4. Configure in Plugin
1. Go to WordPress Admin → AI Content → API Configuration
2. Paste your key in the **"Gemini API Key"** field
3. Select your preferred model:
   - **gemini-1.5-pro**: Best quality (recommended)
   - **gemini-1.5-flash**: Fastest, most economical
   - **gemini-pro**: Legacy model
4. Click **"Test Connection"** to verify
5. Click **"Save Changes"**

### Pricing (as of 2024)

| Model | Input Tokens | Output Tokens | Free Tier |
|-------|--------------|---------------|-----------|
| Gemini 1.5 Pro | $0.25 / 1M | $0.50 / 1M | 60 req/min |
| Gemini 1.5 Flash | $0.05 / 1M | $0.10 / 1M | 60 req/min |

### Rate Limits
- **Free Tier**: 60 requests per minute
- **Paid Tier**: 360 requests per minute (with billing enabled)

### Best Practices
- Start with the free tier to test
- Use **Gemini 1.5 Flash** for high-volume generation
- Enable billing only when you exceed free limits
- Set up budget alerts in Google Cloud Console

---

## DeepSeek API

### Why Choose DeepSeek?
- ✅ Very affordable pricing
- ✅ High-quality Chinese and English content
- ✅ Simple API structure (OpenAI-compatible)
- ✅ Good for budget-conscious users
- ✅ Fast response times

### Step-by-Step Setup

#### 1. Create DeepSeek Account
1. Visit [https://platform.deepseek.com/](https://platform.deepseek.com/)
2. Click **"Sign Up"**
3. Register with email or phone number
4. Verify your account

#### 2. Add Credits
1. Navigate to **"Billing"** or **"Balance"**
2. Click **"Recharge"** or **"Add Credits"**
3. Minimum: Usually $10-20 USD
4. Choose payment method (Credit Card, Alipay, etc.)
5. Complete payment

#### 3. Generate API Key
1. Go to **"API Keys"** section
2. Click **"Create New Key"**
3. Give it a name (e.g., "WordPress Plugin")
4. Copy the generated key
5. **Important**: You can only see the key once - save it!

#### 4. Configure in Plugin
1. Go to WordPress Admin → AI Content → API Configuration
2. Paste your key in the **"DeepSeek API Key"** field
3. Select model:
   - **deepseek-chat**: General content (recommended)
   - **deepseek-coder**: Technical content
4. Click **"Test Connection"**
5. Click **"Save Changes"**

### Pricing

| Model | Input Tokens | Output Tokens |
|-------|--------------|---------------|
| DeepSeek Chat | $0.14 / 1M | $0.28 / 1M |
| DeepSeek Coder | $0.14 / 1M | $0.28 / 1M |

**Example Cost**: Generating 100 posts (1000 words each):
- Total tokens: ~250,000
- Cost: **~$0.05** (incredibly affordable!)

### Rate Limits
- Depends on your account tier
- Usually 60-120 requests per minute
- Check your dashboard for specific limits

### Best Practices
- Start with a small credit top-up ($10)
- Monitor usage in DeepSeek dashboard
- DeepSeek excels at multilingual content
- Great for high-volume publishing

---

## OpenAI API

### Why Choose OpenAI?
- ✅ Industry-leading models (GPT-4, GPT-3.5)
- ✅ Excellent content quality
- ✅ Strong reasoning capabilities
- ✅ Best for premium content
- ❌ More expensive than alternatives

### Step-by-Step Setup

#### 1. Create OpenAI Account
1. Visit [https://platform.openai.com/](https://platform.openai.com/)
2. Click **"Sign Up"**
3. Register with email or Google/Microsoft account
4. Verify your email

#### 2. Add Payment Method
1. Go to **Settings → Billing**
2. Click **"Add payment method"**
3. Enter credit card information
4. **Note**: OpenAI requires a payment method even for trial credits

#### 3. Set Usage Limits (Important!)
1. Still in Billing section, click **"Usage limits"**
2. Set **Monthly budget cap** (e.g., $10, $50)
3. Set **Email notification threshold** (e.g., 80%)
4. This prevents unexpected charges

#### 4. Generate API Key
1. Navigate to **API keys** section
2. Click **"Create new secret key"**
3. Name it (e.g., "WordPress AI Generator")
4. Copy the key **immediately** - you won't see it again
5. Store it securely (use a password manager)

#### 5. Configure in Plugin
1. Go to WordPress Admin → AI Content → API Configuration
2. Paste your key in the **"OpenAI API Key"** field
3. Select model:
   - **GPT-4**: Highest quality (expensive)
   - **GPT-3.5 Turbo**: Great balance (recommended)
   - **GPT-4 Turbo**: Latest, faster, cheaper than GPT-4
4. Click **"Test Connection"**
5. Click **"Save Changes"**

### Pricing

| Model | Input Tokens | Output Tokens |
|-------|--------------|---------------|
| GPT-4 | $30 / 1M | $60 / 1M |
| GPT-4 Turbo | $10 / 1M | $30 / 1M |
| GPT-3.5 Turbo | $0.50 / 1M | $1.50 / 1M |

**Example Cost**: Generating 100 posts (1000 words each) with GPT-3.5:
- Total tokens: ~250,000
- Cost: **~$0.25-0.50**

### Rate Limits
- **Free Trial**: Limited (if available)
- **Tier 1** (after $5 spent): 500 RPM, 60,000 TPM
- **Tier 2** (after $50 spent): 5,000 RPM, 450,000 TPM
- See [OpenAI Rate Limits](https://platform.openai.com/docs/guides/rate-limits)

### Best Practices
- **Start with GPT-3.5 Turbo** - excellent quality, affordable
- Set strict usage limits to control costs
- Use for high-value content only
- Monitor usage daily in OpenAI dashboard
- Consider GPT-4 only for premium/client work

---

## API Comparison

### Quick Comparison Table

| Feature | Gemini | DeepSeek | OpenAI |
|---------|--------|----------|--------|
| **Free Tier** | ✅ Yes (generous) | ❌ No | Limited trial |
| **Cost (per 1M tokens)** | $0.25-0.50 | $0.14-0.28 | $0.50-60 |
| **Best For** | Beginners, testing | High volume | Premium content |
| **Quality** | Excellent | Very good | Excellent |
| **Speed** | Fast | Fast | Fast |
| **Multilingual** | Good | Excellent | Excellent |
| **Setup Difficulty** | Easy | Medium | Medium |
| **Payment Required** | No (for free tier) | Yes | Yes |

### Recommendation by Use Case

**For Beginners / Testing**:
- Start with **Gemini** (free tier)
- No payment required
- Learn the plugin features

**For Budget-Conscious Users**:
- Use **DeepSeek** for production
- Extremely affordable
- Great quality for the price

**For High-Volume Publishing**:
- **Gemini 1.5 Flash** or **DeepSeek**
- Best cost per article
- Reliable uptime

**For Premium/Client Content**:
- **GPT-4 Turbo** or **Gemini 1.5 Pro**
- Highest quality output
- Worth the extra cost

**For Multilingual Sites**:
- **DeepSeek** (especially for Chinese)
- **Gemini** (good all-rounder)
- **GPT-4** (excellent but expensive)

---

## Cost Management

### Monitoring Usage

#### In the Plugin
1. Go to **History & Stats** tab
2. Check **Total Cost** metric
3. View cost breakdown by API
4. Export history for accounting

#### In API Dashboards
- **Gemini**: [Google Cloud Console](https://console.cloud.google.com/)
- **DeepSeek**: Platform dashboard → Billing
- **OpenAI**: Platform → Usage

### Setting Budgets

#### Google Cloud (Gemini)
1. Go to Google Cloud Console
2. Navigate to **Billing → Budgets & alerts**
3. Create budget with email notifications

#### DeepSeek
1. Platform dashboard → Balance
2. Recharge only what you need
3. Monitor balance regularly

#### OpenAI
1. Platform → Settings → Billing → Usage limits
2. Set monthly budget cap
3. Enable email notifications

### Optimization Tips

1. **Start Small**: Test with 1-5 posts before scaling
2. **Use Cheaper Models**: Flash/Turbo variants often sufficient
3. **Optimize Prompts**: Shorter prompts = lower costs
4. **Reduce Word Count**: 800 words vs 2000 words = 60% savings
5. **Enable API Rotation**: Use cheaper APIs first
6. **Schedule Wisely**: Spread generation throughout the day
7. **Monitor Daily**: Check costs before they accumulate

### Cost Calculation Example

**Scenario**: Generate 10 posts/day, 30 days/month, 1000 words/post

**Gemini 1.5 Flash**:
- Tokens per post: ~2,500
- Total monthly tokens: 750,000
- **Cost**: ~$0.11/month

**DeepSeek Chat**:
- Tokens per post: ~2,500
- Total monthly tokens: 750,000
- **Cost**: ~$0.16/month

**GPT-3.5 Turbo**:
- Tokens per post: ~2,500
- Total monthly tokens: 750,000
- **Cost**: ~$0.75/month

**GPT-4**:
- Tokens per post: ~2,500
- Total monthly tokens: 750,000
- **Cost**: ~$33/month

---

## Troubleshooting

### Common Errors

#### "Invalid API Key" (401 Error)
**Cause**: Wrong key or key not activated
**Solution**:
1. Double-check you copied the entire key
2. Verify key is active in provider dashboard
3. For Gemini: Ensure project is enabled
4. For OpenAI: Ensure billing is set up

#### "Rate Limit Exceeded" (429 Error)
**Cause**: Too many requests too quickly
**Solution**:
1. Enable "Auto Switch on Failure" to use backup API
2. Reduce daily post count
3. Increase publish interval
4. Upgrade your API tier

#### "Insufficient Credits" (DeepSeek)
**Cause**: Account balance too low
**Solution**:
1. Add more credits in DeepSeek platform
2. Check current balance
3. Set up auto-recharge if available

#### "Model Not Found"
**Cause**: Wrong model name or deprecated model
**Solution**:
1. Check model name in plugin settings
2. Verify model availability in API docs
3. Try a different model

#### Connection Timeout
**Cause**: Network issues or slow API response
**Solution**:
1. Check your server's internet connection
2. Increase PHP `max_execution_time` to 300
3. Try test connection again
4. Contact your hosting provider if persistent

### Getting Help

1. **Check Plugin Logs**: Admin → AI Content → Logs & Monitoring
2. **Test API Directly**: Use provider's playground/console
3. **Review API Status**: Check provider status pages
4. **Contact Support**:
   - Gemini: Google Cloud Support
   - DeepSeek: Platform support
   - OpenAI: Help center

---

## Next Steps

After setting up your API:

1. ✅ Test connection in plugin
2. ✅ Configure basic settings (topic, style, etc.)
3. ✅ Generate your first test post manually
4. ✅ Review and edit the generated content
5. ✅ Adjust prompts if needed
6. ✅ Enable automatic scheduled generation
7. ✅ Monitor costs and quality regularly

---

**Last Updated**: 2024
**Plugin Version**: 1.0.0

For plugin support, see the main [README.md](README.md) file.
