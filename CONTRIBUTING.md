# Contributing to AI Auto Content Generator

Thank you for your interest in contributing to AI Auto Content Generator! This document provides guidelines and instructions for contributing to the project.

## 🎯 Ways to Contribute

There are many ways to contribute to this project:

1. **Report Bugs** - Help us identify and fix issues
2. **Suggest Features** - Share ideas for improvements
3. **Submit Code** - Fix bugs or add features via Pull Requests
4. **Improve Documentation** - Fix typos, clarify instructions, add examples
5. **Share Prompt Templates** - Contribute effective prompts for different use cases
6. **Test Beta Features** - Help test new features before release
7. **Answer Questions** - Help other users in Issues/Discussions
8. **Spread the Word** - Write reviews, tutorials, or blog posts

---

## 🐛 Reporting Bugs

### Before Submitting a Bug Report

1. **Check existing issues** - Your bug might already be reported
2. **Update to latest version** - The bug might be fixed
3. **Test in isolation** - Disable other plugins to rule out conflicts
4. **Gather information** - Prepare system details

### Bug Report Template

```markdown
**Describe the Bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Go to '...'
2. Click on '...'
3. Scroll down to '...'
4. See error

**Expected Behavior**
What you expected to happen.

**Screenshots**
If applicable, add screenshots.

**Environment:**
- Plugin Version: [e.g. 1.0.1]
- WordPress Version: [e.g. 6.4]
- PHP Version: [e.g. 7.4]
- Browser: [e.g. Chrome 98]
- Active API: [e.g. Gemini]

**Additional Context**
Any other context about the problem.

**Error Messages/Logs**
Copy from Tools → System Information and Logs tab
```

### Where to Report

- **GitHub Issues**: https://github.com/yourusername/ai-auto-content-generator/issues
- Label: `bug`

---

## 💡 Suggesting Features

### Before Suggesting a Feature

1. **Check existing feature requests** - It might already be suggested
2. **Consider the scope** - Does it fit the plugin's purpose?
3. **Think about implementation** - Is it technically feasible?

### Feature Request Template

```markdown
**Is your feature request related to a problem?**
A clear description of the problem. Ex. I'm frustrated when [...]

**Describe the solution you'd like**
Clear description of what you want to happen.

**Describe alternatives you've considered**
Other solutions or features you've considered.

**Use Case**
How would you use this feature? Who else would benefit?

**Additional context**
Screenshots, mockups, or examples.
```

### Where to Suggest

- **GitHub Issues**: https://github.com/yourusername/ai-auto-content-generator/issues
- Label: `enhancement`

---

## 🔨 Contributing Code

### Getting Started

1. **Fork the repository**
   ```bash
   git clone https://github.com/your-username/ai-auto-content-generator.git
   cd ai-auto-content-generator
   ```

2. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/bug-description
   ```

3. **Set up development environment**
   - Install WordPress locally (recommended: Local by Flywheel, XAMPP, or MAMP)
   - Symlink plugin folder to wp-content/plugins/
   - Activate the plugin
   - Configure at least one AI API for testing

### Development Guidelines

#### Code Style

Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/):

**PHP:**
- Use tabs for indentation
- Opening braces on the same line
- Yoda conditions for comparisons
- Single quotes for strings (unless interpolating)
- Proper doc blocks for all functions/methods

**JavaScript:**
- Use tabs for indentation
- Semicolons required
- Single quotes for strings
- jQuery wrapped in `(function($) { ... })(jQuery)`

**CSS:**
- Use tabs for indentation
- One selector per line
- Properties alphabetically sorted (optional but recommended)

#### Code Quality Checklist

- [ ] Follows WordPress Coding Standards
- [ ] All functions have doc blocks
- [ ] Code is properly indented and formatted
- [ ] No PHP errors or warnings
- [ ] No JavaScript console errors
- [ ] Security: All inputs sanitized, outputs escaped
- [ ] Security: Nonces for all forms/AJAX
- [ ] Security: Capability checks where needed
- [ ] Internationalization: All strings wrapped in `__()` or `_e()`
- [ ] Performance: No N+1 queries
- [ ] Performance: Database queries are optimized
- [ ] Backward compatibility maintained

#### File Structure

```
ai-auto-content-generator/
├── ai-auto-content-generator.php  # Main plugin file
├── includes/                       # Core classes
│   ├── class-*.php                # Class files
│   └── api/                       # API implementations
├── admin/                         # Admin interface
│   ├── class-admin-settings.php  # Settings controller
│   ├── views/                    # View templates
│   └── assets/                   # CSS/JS
└── languages/                    # Translation files
```

#### Naming Conventions

- **Classes**: `AIACG_Class_Name`
- **Functions**: `aiacg_function_name()`
- **Variables**: `$snake_case`
- **Constants**: `AIACG_CONSTANT_NAME`
- **Options**: `aiacg_option_name`
- **Hooks**: `aiacg_hook_name`

### Adding a New Feature

1. **Plan your changes**
   - Review existing code structure
   - Identify files that need modification
   - Plan database changes (if any)

2. **Write the code**
   - Follow coding standards
   - Add proper documentation
   - Include inline comments for complex logic

3. **Test thoroughly**
   - Test on fresh WordPress installation
   - Test with different PHP versions (7.4, 8.0, 8.1)
   - Test with different WordPress versions
   - Test with other popular plugins (Yoast, Rank Math, etc.)
   - Test all user flows
   - Test error scenarios

4. **Update documentation**
   - Update README.md if needed
   - Update relevant guide files
   - Add to CHANGELOG.md

### Submitting a Pull Request

1. **Ensure your code is ready**
   - All tests passing
   - Code follows standards
   - Documentation updated
   - Commits are clean and descriptive

2. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: add feature description"
   # or
   git commit -m "fix: bug description"
   ```

   **Commit Message Format:**
   - `feat:` New feature
   - `fix:` Bug fix
   - `docs:` Documentation changes
   - `style:` Code style changes (formatting, etc.)
   - `refactor:` Code refactoring
   - `perf:` Performance improvements
   - `test:` Adding tests
   - `chore:` Maintenance tasks

3. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

4. **Create Pull Request**
   - Go to GitHub repository
   - Click "New Pull Request"
   - Select your branch
   - Fill in the PR template:

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Documentation update
- [ ] Code refactoring
- [ ] Performance improvement

## Related Issue
Closes #issue_number

## Testing
- [ ] Tested on WordPress 5.0+
- [ ] Tested on PHP 7.4+
- [ ] No PHP errors/warnings
- [ ] No JavaScript errors
- [ ] All existing features still work

## Screenshots (if applicable)
Add screenshots of UI changes

## Checklist
- [ ] Code follows WordPress Coding Standards
- [ ] Self-reviewed code
- [ ] Commented complex code
- [ ] Updated documentation
- [ ] No breaking changes (or documented)
```

### Code Review Process

1. **Automatic checks** - GitHub Actions will run automated tests
2. **Manual review** - Maintainers will review your code
3. **Feedback** - You may receive requests for changes
4. **Approval** - Once approved, your PR will be merged
5. **Recognition** - You'll be credited in the changelog!

---

## 📝 Improving Documentation

Documentation is crucial! Even small improvements help.

### Types of Documentation

1. **Code Comments** - Inline explanations
2. **Doc Blocks** - Function/class documentation
3. **User Guides** - README, Quick Start, API Setup
4. **Developer Docs** - Plugin Structure, Contributing
5. **Examples** - Prompt templates, code snippets

### Documentation Guidelines

- Use clear, simple language
- Avoid jargon or explain it
- Include examples
- Keep it up-to-date with code
- Test all instructions
- Use proper Markdown formatting

### Where to Contribute

- Fix typos via Pull Request
- Improve clarity in existing docs
- Add missing information
- Create tutorials or guides
- Translate documentation

---

## 🎨 Contributing Prompt Templates

Share your effective prompts with the community!

### Template Contribution Process

1. Test your prompt thoroughly (at least 10 generated articles)
2. Document the use case
3. Note which AI model works best
4. Add to PROMPT-TEMPLATES.md via Pull Request

### Template Format

```markdown
### [Template Name]

**Best For:** [Industry/use case]
**Tested With:** [Gemini/DeepSeek/OpenAI]
**Quality Rating:** ⭐⭐⭐⭐⭐ (your assessment)

\```
[Your prompt template here]
\```

**Tips:**
- [Usage tip 1]
- [Usage tip 2]

**Example Output:** [Brief description or link]
```

---

## 🧪 Testing

### Manual Testing Checklist

**Basic Functionality:**
- [ ] Plugin activates without errors
- [ ] Settings save correctly
- [ ] Manual generation works
- [ ] Scheduled generation works
- [ ] API failover works
- [ ] History records correctly
- [ ] Logs display properly

**Edge Cases:**
- [ ] Empty settings handled gracefully
- [ ] Invalid API keys show proper errors
- [ ] Network failures handled correctly
- [ ] Large content generation (3000+ words)
- [ ] Multiple posts generation
- [ ] API rate limit handling

**Compatibility:**
- [ ] Fresh WordPress install
- [ ] WordPress Multisite
- [ ] Popular page builders (Elementor, Divi)
- [ ] Popular SEO plugins (Yoast, Rank Math)
- [ ] Different PHP versions (7.4, 8.0, 8.1)
- [ ] Different MySQL versions

**Security:**
- [ ] XSS prevention
- [ ] CSRF prevention (nonces)
- [ ] SQL injection prevention
- [ ] Capability checks working
- [ ] API keys not exposed

### Reporting Test Results

Include in your PR:
- WordPress version tested
- PHP version tested
- Other active plugins
- Any issues found
- Screenshots if relevant

---

## 🌍 Internationalization (i18n)

Help translate the plugin!

### Translation Process

1. **Generate POT file**
   ```bash
   wp i18n make-pot . languages/ai-auto-content-generator.pot
   ```

2. **Translate using Poedit or Loco Translate**

3. **Test translations**
   - Switch WordPress language
   - Verify all strings translated
   - Check formatting

4. **Submit translation**
   - Via Pull Request (add .po and .mo files)
   - Or via WordPress.org (if plugin is in directory)

### String Guidelines

- Use translation functions: `__()`, `_e()`, `_n()`, `esc_html__()`
- Include text domain: `'ai-auto-content-generator'`
- Provide context where ambiguous
- Use placeholders for variables:
  ```php
  sprintf(__('Generated %d posts', 'ai-auto-content-generator'), $count)
  ```

---

## 📋 Project Governance

### Decision Making

- **Bug Fixes**: Fast-track approval
- **Minor Features**: Community discussion, maintainer approval
- **Major Features**: RFC (Request for Comments), community vote
- **Breaking Changes**: Require strong justification

### Release Cycle

- **Patch Versions** (1.0.x): Bug fixes, every 1-2 weeks
- **Minor Versions** (1.x.0): New features, every 1-2 months
- **Major Versions** (x.0.0): Breaking changes, as needed

### Versioning

We follow [Semantic Versioning](https://semver.org/):
- **MAJOR**: Incompatible API changes
- **MINOR**: Backward-compatible new features
- **PATCH**: Backward-compatible bug fixes

---

## 🏆 Recognition

### Contributors

All contributors will be:
- Listed in CHANGELOG.md
- Credited in release notes
- Thanked in commit messages

### Core Contributors

Regular contributors may be invited to become core contributors with:
- Repository write access
- Decision-making participation
- Release management involvement

---

## 📞 Getting Help

### Questions About Contributing?

- **GitHub Discussions**: Ask the community
- **GitHub Issues**: Technical questions
- **Email**: [maintainer email]

### Stuck?

Don't hesitate to ask for help! We're here to assist:
- How to set up development environment
- Understanding the codebase
- Best practices
- Code review feedback

---

## 📜 Code of Conduct

### Our Pledge

We pledge to make participation in our project a harassment-free experience for everyone, regardless of age, body size, disability, ethnicity, gender identity and expression, level of experience, nationality, personal appearance, race, religion, or sexual identity and orientation.

### Our Standards

**Positive Behavior:**
- Using welcoming and inclusive language
- Being respectful of differing viewpoints
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards others

**Unacceptable Behavior:**
- Trolling, insulting/derogatory comments
- Public or private harassment
- Publishing others' private information
- Other conduct which could reasonably be considered inappropriate

### Enforcement

Instances of abusive, harassing, or otherwise unacceptable behavior may be reported to the project maintainers. All complaints will be reviewed and investigated.

---

## 📚 Resources

### WordPress Development
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress REST API](https://developer.wordpress.org/rest-api/)

### AI APIs
- [Google Gemini Docs](https://ai.google.dev/docs)
- [DeepSeek Docs](https://platform.deepseek.com/docs)
- [OpenAI Docs](https://platform.openai.com/docs)

### Tools
- [Local by Flywheel](https://localwp.com/) - WordPress development environment
- [phpcs](https://github.com/squizlabs/PHP_CodeSniffer) - PHP Code Sniffer
- [Poedit](https://poedit.net/) - Translation editor

---

## 🙏 Thank You!

Thank you for contributing to AI Auto Content Generator! Your help makes this project better for everyone.

**Every contribution matters**, whether it's:
- A one-line typo fix
- A detailed bug report
- A new feature
- Helping another user

We appreciate your time and effort! 🎉

---

**Questions?** Open an issue or start a discussion on GitHub.

**Last Updated:** November 2024
**Version:** 1.0.1
