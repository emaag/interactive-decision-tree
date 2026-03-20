# Interactive Decision Tree

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue.svg)](https://www.php.net/)
[![GitHub Stars](https://img.shields.io/github/stars/emaag/interactive-decision-tree.svg)](https://github.com/emaag/interactive-decision-tree/stargazers)
[![GitHub Forks](https://img.shields.io/github/forks/emaag/interactive-decision-tree.svg)](https://github.com/emaag/interactive-decision-tree/network)

A web-based tool that guides users through decision-making processes using an interactive, user-friendly flow chart interface. Think of it as a choose-your-own-adventure for decisions.

## Features

- **XML-Based Data Storage** - Decision tree data stored as standard XML for easy editing and portability
- **Client-Side Viewer** - Built with HTML, CSS, and JavaScript for universal compatibility
- **Cross-Platform** - Host on any web server or run locally
- **Visual Editor** - PHP-driven editor for creating decision trees without manual XML editing
- **No Database Required** - Simple file-based system
- **Responsive Design** - Works on desktop and mobile devices

## Table of Contents

- [Demo](#demo)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [File Structure](#file-structure)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## Demo

<!-- Add your demo link or animated GIF here -->
<!-- ![Decision Tree Demo](path/to/demo.gif) -->

[Watch Video Tutorial](https://www.youtube.com/watch?v=ngcjYuJHZ4Q)

## Requirements

- **Web Server** (Apache, Nginx, or any HTTP server)
- **PHP 7.4+** (PHP 8.x recommended)
- **Write permissions** on the `xml/` directory

## Installation

### Quick Start

1. **Clone the repository**
   ```bash
   git clone https://github.com/emaag/interactive-decision-tree.git
   cd interactive-decision-tree
   ```

2. **Set directory permissions**
   ```bash
   chmod 755 xml/
   # Or, if necessary:
   chmod 777 xml/
   ```

3. **Configure your web server** to point to the repository directory

4. **Access the editor** in your web browser:
   ```
   http://your-domain.com/interactive-decision-tree/editTree.php
   ```

### Docker Installation

```bash
docker compose up --build
```

Then open `http://localhost:8080` in your browser.

## Usage

### Creating a Decision Tree

1. Navigate to `editTree.php` in your browser
2. Click "Create New Decision Tree"
3. Add questions and decision paths using the visual editor
4. Save your decision tree (stored as XML in the `xml/` directory)

### Viewing a Decision Tree

1. Navigate to `index.php` to see a list of all available trees
2. Click **View** next to any tree to open it in the interactive viewer

### Setting an Editor Password

By default the editor is unprotected. To require a password:

```bash
php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
```

Paste the output into `config.php`:

```php
define( 'EDITOR_PASSWORD_HASH', '$2y$10$...' );
```

### Example XML Structure

```xml
<?xml version="1.0"?>
<tree>
  <title>My Decision Tree</title>
  <description>A short description</description>
  <branch id="1">
    <content>What is your question?</content>
    <fork target="1.1">Answer A</fork>
    <fork target="1.2">Answer B</fork>
  </branch>
  <branch id="1.1">
    <content>You chose A. Another question?</content>
    <fork target="1.1.1">Yes</fork>
    <fork target="1.1.2">No</fork>
  </branch>
  <!-- Branch id="1.2" etc. -->
</tree>
```

## File Structure

```
interactive-decision-tree/
├── xml/                  # Decision tree XML files (needs write permission)
├── css/                  # Stylesheets
├── js/                   # JavaScript files
├── index.php             # Public listing of all trees
├── showTree.html         # Interactive viewer for a single tree
├── editTree.php          # Editor for creating/editing trees (password-protected)
├── login.php             # Editor login page
├── logout.php            # Editor logout
├── config.php            # App configuration (password hash)
├── class.decisiontree.php  # DecisionTree and Branch classes
├── inc.general.php       # Shared functions and session/auth helpers
├── Dockerfile            # Docker image definition
├── docker-compose.yml    # Docker Compose setup
├── LICENSE               # MIT License
└── README.md             # This file
```

## Contributing

Contributions are welcome! Please see CONTRIBUTING.md for details.

### How to Contribute

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Bug Reports

Found a bug? Please open an issue on GitHub with:

- Description of the problem
- Steps to reproduce
- Expected behavior
- Screenshots (if applicable)

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

Found a bug or have a question? [Open an issue](https://github.com/emaag/interactive-decision-tree/issues) on GitHub.

## Author

**Eric Maag**

- GitHub: [@emaag](https://github.com/emaag)

## Acknowledgments

- Thanks to all contributors who have helped improve this project
- Inspired by the need for simple, accessible decision-making tools

## Project Stats

This project has been forked by organizations including legal service providers and educational institutions to help users navigate complex decision-making processes.

---

⭐ Star this repository if you find it useful!
