# Contributing to Interactive Decision Tree

First off, thank you for considering contributing to Interactive Decision Tree! It's people like you that make this tool better for everyone.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Getting Started](#getting-started)
- [Development Process](#development-process)
- [Style Guidelines](#style-guidelines)
- [Commit Messages](#commit-messages)
- [Pull Request Process](#pull-request-process)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to [your-email@example.com].

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** (code snippets, XML examples, etc.)
- **Describe the behavior you observed** and what you expected to see
- **Include screenshots** if applicable
- **Include your environment details** (PHP version, browser, OS)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the suggested enhancement
- **Explain why this enhancement would be useful** to most users
- **List any similar features** in other projects, if applicable

### Your First Code Contribution

Unsure where to begin? Look for issues labeled:
- `good first issue` - Good for newcomers
- `help wanted` - Extra attention needed

### Pull Requests

- Fill in the required template
- Follow the style guidelines
- Include screenshots and animated GIFs in your pull request whenever possible
- End all files with a newline
- Avoid platform-dependent code

## Getting Started

1. **Fork the repository** and clone your fork:
```bash
   git clone https://github.com/YOUR-USERNAME/interactive-decision-tree.git
   cd interactive-decision-tree

Create a branch for your changes:

bash   git checkout -b feature/my-new-feature

Set up your development environment:

Ensure PHP 7.4+ is installed
Set up a local web server (Apache, Nginx, or PHP's built-in server)
Make the xml/ directory writable


Make your changes and test thoroughly
Commit your changes:

bash   git add .
   git commit -m "Add some feature"

Push to your fork:

bash   git push origin feature/my-new-feature

Submit a pull request

Development Process
Setting Up Local Development
bash# Start PHP's built-in server for quick testing
php -S localhost:8000
Then navigate to http://localhost:8000/editTree.php
Testing Your Changes
Before submitting a pull request:

Test the editor - Create, edit, and delete decision trees
Test the viewer - Ensure decision trees display correctly
Test XML generation - Verify XML files are properly formatted
Test across browsers - Chrome, Firefox, Safari, Edge
Test file permissions - Ensure the xml/ directory remains writable

Style Guidelines
PHP Code Style

Use 4 spaces for indentation (no tabs)
Follow PSR-12 coding standards where possible
Use meaningful variable and function names
Add comments for complex logic
Keep functions focused and small

Example:
php<?php
// Good
function createDecisionNode($question, $options) {
    // Implementation
}

// Avoid
function cdn($q, $o) {
    // Implementation
}
?>
JavaScript Code Style

Use 2 spaces for indentation
Use const and let instead of var
Use semicolons
Use meaningful variable names
Add JSDoc comments for functions

Example:
javascript/**
 * Loads a decision tree from XML
 * @param {string} treeId - The ID of the tree to load
 * @returns {Object} Parsed tree data
 */
const loadDecisionTree = (treeId) => {
  // Implementation
};
CSS Code Style

Use 2 spaces for indentation
One selector per line in multi-selector rulesets
Use meaningful class names (BEM methodology preferred)
Group related properties together

Example:
css.decision-tree__node {
  display: flex;
  flex-direction: column;
  padding: 1rem;
}
XML Structure

Use 2 spaces for indentation
Always include XML declaration
Use meaningful element and attribute names
Keep structure consistent

Commit Messages

Use the present tense ("Add feature" not "Added feature")
Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
Limit the first line to 72 characters or less
Reference issues and pull requests liberally after the first line

Examples:
Add support for multiple choice questions

- Implement multi-select option type
- Update XML schema for new question type
- Add UI controls in editor

Fixes #123
Pull Request Process

Update the README.md with details of changes if applicable
Update documentation if you're changing functionality
Ensure all tests pass (once we have automated testing)
Request review from maintainers
Address feedback promptly and professionally
Squash commits if requested before merging

Pull Request Template
When you create a PR, please include:
markdown## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
Describe how you tested your changes

## Screenshots (if applicable)
Add screenshots here

## Checklist
- [ ] My code follows the style guidelines
- [ ] I have commented my code where necessary
- [ ] I have updated the documentation
- [ ] My changes generate no new warnings
- [ ] I have tested my changes thoroughly
Questions?
Feel free to open an issue with the question label, or reach out to the maintainers.
Recognition
Contributors will be recognized in the project README and release notes
