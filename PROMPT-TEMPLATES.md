# Example Prompt Templates

这是一个示例提示词模板库，包含针对不同行业和内容类型的优化提示词。您可以直接使用或根据需要修改。

## 如何使用

1. 进入 WordPress后台 → AI Content → Content Templates
2. 复制下面适合您的模板
3. 粘贴到"系统提示词"或"用户提示词模板"字段
4. 根据需要调整变量和要求
5. 保存设置并测试生成

---

## 系统提示词模板

### 通用专业写作

```
You are a professional content writer with expertise in creating engaging, informative, and SEO-optimized articles. Write in a clear, authoritative style with proper structure including introduction, body paragraphs, and conclusion. Use headings (H2, H3) to organize content. Include relevant examples and actionable insights.
```

### 科技博客

```
You are a technology journalist and content creator specializing in explaining complex technical concepts to general audiences. Write in an engaging, accessible style that balances technical accuracy with readability. Use analogies, real-world examples, and maintain enthusiasm for technology while being objective about pros and cons.
```

### 健康与健身

```
You are a certified health and fitness writer with expertise in wellness, nutrition, and exercise science. Write evidence-based content that is informative yet motivational. Always include disclaimers where appropriate (e.g., "consult your doctor"). Use clear language to explain health concepts, cite reputable sources, and focus on practical, actionable advice.
```

### 商业与金融

```
You are a business analyst and financial writer with expertise in market trends, entrepreneurship, and personal finance. Write authoritative, data-driven content that provides valuable insights for business professionals and investors. Use clear examples, cite recent statistics, and maintain a professional yet accessible tone.
```

### 旅游与生活方式

```
You are a travel writer and lifestyle blogger with extensive experience exploring different cultures and destinations. Write vivid, engaging content that transports readers to the locations you describe. Include practical tips, personal anecdotes, and cultural insights. Maintain an enthusiastic yet authentic voice.
```

### 教育与学习

```
You are an experienced educator and instructional designer who specializes in creating clear, comprehensive learning materials. Write in a supportive, encouraging tone that breaks down complex topics into manageable parts. Use step-by-step explanations, examples, and emphasize practical application of knowledge.
```

---

## 用户提示词模板

### 标准文章模板（多用途）

```
Write a comprehensive article with the following specifications:

Title: {title}
Topic: {topic}
Target Word Count: {word_count} words
Writing Style: {style}
Angle: {angle}
Date Context: {date}
Target Audience: {audience}

Requirements:
- Use proper HTML formatting (p, h2, h3, ul, ol tags)
- Include 3-5 subheadings (H2 or H3)
- Write an engaging introduction that hooks the reader
- Develop each main point in separate sections
- Include practical examples and actionable insights
- Write a strong conclusion that summarizes key points
- Optimize for SEO without keyword stuffing
- Maintain consistent tone throughout

Structure:
1. Introduction (10% of content)
2. Main Body (75% of content, divided into 3-5 sections)
3. Conclusion (10% of content)
4. Optional: Call-to-action

Return ONLY the article content in HTML format, without the title.
```

### How-To Guide模板

```
Write a detailed how-to guide on: {title}

Topic: {topic}
Target Length: {word_count} words
Audience Level: {audience}
Today's Date: {date}

Requirements:
- Start with a brief introduction explaining what readers will learn
- Break down the process into clear, numbered steps
- For each step:
  * Provide detailed instructions
  * Explain WHY the step is important
  * Include tips or warnings where relevant
- Use bullet points for sub-steps or options
- Include a "What You'll Need" section if applicable
- Add troubleshooting tips for common issues
- End with a conclusion summarizing the process

Format in HTML with:
- H2 for major sections
- H3 for step headings
- Ordered lists (ol) for sequential steps
- Unordered lists (ul) for tips or requirements
- Emphasis (strong/em) for key points

Return ONLY the formatted guide content, without the title.
```

### 产品评测模板

```
Write an unbiased, comprehensive product review for: {title}

Topic: {topic}
Word Count: {word_count} words
Writing Style: {style}
Date: {date}

Review Structure:
1. Introduction
   - Brief overview of the product
   - Who it's for
   - Key verdict (1-2 sentences)

2. What We Like (Pros)
   - List 3-5 major advantages
   - Explain each with details

3. What Could Be Better (Cons)
   - List 2-4 disadvantages
   - Be fair and balanced

4. Key Features
   - Detail the main features
   - Explain how they work in practice

5. Performance
   - Real-world usage experience
   - Comparisons where relevant

6. Value for Money
   - Pricing discussion
   - Is it worth it?

7. Final Verdict
   - Summary of pros/cons
   - Recommendation for buyers

Requirements:
- Be honest and balanced
- Include specific details, not vague praise
- Use HTML formatting (h2, h3, ul, strong)
- Maintain objective tone
- Support claims with reasons

Return the review in HTML format, without the title.
```

### 新闻文章模板

```
Write a news article about: {title}

Topic: {topic}
Word Count: {word_count} words
Date: {date}
Style: Objective journalism

Structure:
1. Lede (Opening Paragraph)
   - Answer Who, What, When, Where, Why, How
   - Most important information first

2. Body
   - Expand on lede with details
   - Include quotes (fictional but realistic)
   - Add context and background
   - Present multiple perspectives if applicable

3. Conclusion
   - Future implications
   - What's next
   - Any calls to action

Journalistic Requirements:
- Inverted pyramid style (most important info first)
- Neutral, objective tone
- Short paragraphs (2-3 sentences)
- Active voice preferred
- Attribute information properly
- Include relevant statistics or data points

Format:
- Use H2 for major sections
- Use H3 for subsections
- Emphasize key terms with <strong>
- Use quotes with proper attribution

Return in HTML format, without the title.
```

### 列表文章（Listicle）模板

```
Create an engaging listicle: {title}

Topic: {topic}
Number of Items: 7-12 items
Word Count: {word_count} words total
Style: {style}
Audience: {audience}

Structure for Each Item:
1. Catchy subheading (H3)
2. Detailed explanation (150-200 words)
3. Why it matters
4. Pro tip or example

Overall Requirements:
- Engaging introduction (explain the value of the list)
- Each item should be substantial, not just a sentence
- Include relevant examples or case studies
- Use varied language (avoid repetitive phrasing)
- Add a conclusion that ties everything together
- Optional: Include a summary box at the end

HTML Formatting:
- H2 for introduction
- H3 for each list item heading
- Paragraphs for explanations
- Bold (<strong>) for key points
- Bullet points (<ul>) for sub-lists if needed

Make it scannable but detailed enough to be valuable.

Return in HTML format, without the main title.
```

### 对比文章模板

```
Write a detailed comparison article: {title}

Topic: {topic}
Word Count: {word_count} words
Items Being Compared: [Extract from topic]
Angle: {angle}

Structure:
1. Introduction
   - What's being compared and why
   - Who should care about this comparison
   - Brief overview of conclusions

2. Quick Comparison Table
   - Create a simple HTML table
   - Compare 5-7 key attributes side by side

3. Detailed Comparison
   - Feature 1: Compare both sides
   - Feature 2: Compare both sides
   - Feature 3: Compare both sides
   (Continue for 5-7 major features)

4. Pros and Cons Summary
   - Option A: Pros and Cons
   - Option B: Pros and Cons

5. Which Should You Choose?
   - Scenarios where Option A is better
   - Scenarios where Option B is better
   - Final recommendation

Requirements:
- Fair and balanced analysis
- Specific details, not generalities
- Support claims with reasons
- Include use cases and examples
- HTML formatting (tables, lists, headings)

Return in HTML format, without the title.
```

### SEO优化模板（注重关键词）

```
Write an SEO-optimized article on: {title}

Primary Topic: {topic}
Target Keywords: {keywords} (derived from sub_topics)
Word Count: {word_count} words
Writing Style: {style}
Date: {date}

SEO Requirements:
- Include primary keyword in first paragraph
- Use related keywords naturally throughout
- Create descriptive H2 and H3 headings
- Aim for keyword density of 1-2%
- Include internal link opportunities (mention related topics)
- Write meta-friendly introduction (can be used as excerpt)

Content Structure:
1. Introduction (150-200 words)
   - Include primary keyword
   - Hook the reader
   - Preview what's covered

2. Main Content Sections (3-5 sections)
   - Each section with H2 heading
   - Include keywords naturally
   - Provide value and depth
   - Use examples and data

3. FAQ Section (Optional)
   - 3-5 common questions
   - Use H3 for each question
   - Provide concise answers

4. Conclusion
   - Summarize key points
   - Include call-to-action
   - Reinforce main keyword

HTML Formatting:
- Semantic headings (H2, H3)
- Short paragraphs (3-4 sentences)
- Bullet points and numbered lists
- Bold key phrases
- Well-structured content

Return in HTML format, without the title.
```

---

## 写作角度（Writing Angles）示例

您可以在"Content Templates"页面查看和自定义写作角度。以下是一些额外的角度建议：

### 技术深度文章
"Technical Deep Dive - Comprehensive exploration with code examples and implementation details"

### 初学者指南
"Beginner's Guide - Friendly, jargon-free explanation for newcomers"

### 专家圆桌
"Expert Roundup - Insights and quotes from industry professionals"

### 数据驱动分析
"Data-Driven Analysis - Statistical insights and research-based conclusions"

### 常见错误解析
"Common Mistakes - What to avoid and how to correct them"

### 未来展望
"Future Outlook - Predictions and emerging trends"

### 实战经验分享
"Lessons from the Trenches - Real-world experiences and practical wisdom"

### 争议话题探讨
"Controversial Take - Challenging conventional wisdom with well-reasoned arguments"

---

## 变量说明

在提示词模板中，您可以使用以下变量，系统会自动替换：

- `{title}` - 生成的文章标题
- `{topic}` - 您设置的主题
- `{word_count}` - 目标字数
- `{style}` - 写作风格 (professional/casual/formal/conversational)
- `{angle}` - 当前使用的写作角度
- `{date}` - 当前日期
- `{audience}` - 目标受众
- `{keywords}` - 从子主题生成的关键词

---

## 优化建议

### 提高内容质量

1. **具体化要求**：不要只说"写好"，要说"包含3个实例"、"引用最新数据"
2. **设定结构**：明确要求章节划分、段落长度
3. **增加约束**：如"避免使用行话"、"使用日常语言"
4. **要求多样性**：让AI使用不同的句式、过渡词

### 控制输出格式

1. **明确HTML要求**：指定使用哪些标签
2. **长度控制**：为每个部分设定字数范围
3. **风格一致性**：在系统提示词中设定总体基调

### 提升SEO效果

1. **关键词整合**：自然融入，避免堆砌
2. **结构优化**：使用语义化的标题层级
3. **内链机会**：要求AI提及相关主题（可手动添加链接）

---

## 测试与迭代

1. **生成测试文章**：使用模板生成几篇文章
2. **评估质量**：检查是否符合预期
3. **调整模板**：根据结果优化提示词
4. **保存成功模板**：将效果好的模板保存到文档中
5. **持续优化**：根据长期效果不断改进

---

## 社区分享

如果您创建了特别有效的提示词模板，欢迎：

1. 在GitHub上分享（创建Issue或PR）
2. 加入WordPress社区讨论
3. 在您的博客分享经验

**记住**：好的提示词是不断实验和优化的结果。不要害怕尝试不同的方法！

---

**版本**: 1.0.0
**最后更新**: 2024年11月
**维护者**: AI Auto Content Generator团队
