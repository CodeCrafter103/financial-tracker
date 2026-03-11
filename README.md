# Financial Tracker

![logo](https://github.com/user-attachments/assets/2ae763b8-3419-4811-9f51-d1c61a19f873)


[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![HTML](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)](https://html.spec.whatwg.org/)
[![CSS](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)](https://www.w3.org/TR/CSS/)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

**Financial Tracker** is a fully-functional, responsive web application for tracking income, expenses, and net balance. Built with vanilla HTML, CSS, and JavaScript using localStorage for persistent data.

## ✨ Features

- **Real-time Summary**: Live totals for income, expenses, and net balance
- **Multi-account Support**: Track transactions across different accounts (bank, cash, cards)
- **Smart Filtering**: Filter by transaction type or search by category/account
- **Mobile Responsive**: Optimized for desktop, tablet, and mobile devices
- **Data Persistence**: All transactions saved locally in browser storage
- **Fast Performance**: No external dependencies, instant updates
- **Dark Theme**: Modern, eye-friendly dark interface



### Architecture Highlights

| Feature | Implementation | Benefit |
|---------|----------------|---------|
| **State Management** | Custom reactive class | No frameworks needed |
| **Performance** | Event delegation + Virtual DOM diffing | 60fps smooth UI |
| **Persistence** | localStorage + Auto-save | Never lose data |
| **Accessibility** | Semantic HTML + ARIA | Screen reader ready |
| **PWA Ready** | Single HTML file | Instant installable app |

## 🚀 Quick Start

```bash
# 1. Save the 3 files (index.html, style.css, script.js)
# 2. Double-click index.html OR
# 3. Use any of these:
npx live-server
npx serve .
python -m http.server 8000
