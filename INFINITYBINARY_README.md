# INFINITYBINARY — Glassmorphic Website + Full CMS

**Production-Ready PHP CMS with Dual File System**

## Project Overview

This is a complete implementation of the INFINITYBINARY website specifications, featuring:

- **Dual File System**: Every page exists as both `.php` (database-driven) and `.html` (static) versions
- **Full CMS**: Complete content management system with CRUD operations for all content types
- **Triple-Source Media**: Upload files, use external URLs, or paste SVG/icon code
- **Glassmorphic Design**: Modern frosted-glass aesthetic with animated halo effects
- **Zero Dependencies**: Pure PHP 8.1, Vanilla JS ES6, and CSS3 - no frameworks

## Directory Structure

```
infinitybinary/
├── index.php + index.html          # Homepage (dual files)
├── about.php + about.html          # About page
├── services.php + services.html    # Services page
├── portfolio.php + portfolio.html  # Portfolio listing
├── portfolio-detail.php + .html    # Portfolio detail
├── blog.php + blog.html            # Blog listing
├── blog-post.php + blog-post.html  # Blog post detail
├── contact.php + contact.html      # Contact page
├── privacy.php + privacy.html      # Privacy policy
├── terms.php + terms.html          # Terms of service
├── 404.php + 404.html              # 404 error page
├── maintenance.php                 # Maintenance mode
├── .htaccess                       # URL rewriting
├── robots.txt                      # SEO robots file
├── sitemap.php                     # Dynamic sitemap
│
├── assets/
│   ├── css/
│   │   ├── main.css                # Base styles
│   │   ├── glass.css               # Glassmorphism system
│   │   ├── typography.css          # Typography classes
│   │   ├── animations.css          # Animations
│   │   ├── components.css          # UI components
│   │   ├── nav.css                 # Navigation
│   │   ├── footer.css              # Footer styles
│   │   └── cms-theme.css           # CMS-generated theme (DO NOT EDIT)
│   │
│   ├── js/
│   │   ├── core.js                 # Core utilities
│   │   ├── halo.js                 # Halo orb effects
│   │   ├── scroll.js               # Scroll animations
│   │   ├── parallax.js             # Parallax effects
│   │   ├── hero.js                 # Hero animations
│   │   ├── nav.js                  # Navigation functionality
│   │   ├── counter.js              # Counter animations
│   │   ├── marquee.js              # Marquee scroll
│   │   └── modal.js                # Modal system
│   │
│   ├── fonts/README.txt            # Font loading instructions
│   │
│   └── media/
│       ├── hero/                   # Hero section media
│       └── uploads/                # User uploads
│           └── .htaccess           # Upload security
│
├── includes/
│   ├── config.php                  # Configuration
│   ├── db.php                      # Database class
│   ├── functions.php               # Helper functions
│   ├── media.php                   # Triple-source media handler
│   ├── auth.php                    # Authentication
│   ├── head.php                    # HTML head
│   ├── header.php                  # Site header
│   ├── footer.php                  # Site footer
│   └── schema.php                  # Database schema installer
│
└── cms/                            # CMS Backend
    ├── login.php                   # CMS login
    ├── logout.php                  # Logout
    ├── index.php                   # Dashboard
    │
    ├── pages/
    │   ├── index.php               # Pages list
    │   ├── edit.php                # Page editor
    │   └── delete.php              # Delete page
    │
    ├── posts/
    │   ├── index.php               # Posts list
    │   ├── edit.php                # Post editor
    │   └── delete.php              # Delete post
    │
    ├── portfolio/
    │   ├── index.php               # Portfolio list
    │   ├── edit.php                # Portfolio editor
    │   └── delete.php              # Delete portfolio
    │
    ├── team/
    │   ├── index.php               # Team list
    │   └── edit.php                # Team editor
    │
    ├── categories/
    │   └── index.php               # Category management
    │
    ├── media/
    │   └── index.php               # Media library
    │
    ├── navigation/
    │   └── index.php               # Navigation manager
    │
    ├── theme/
    │   └── index.php               # Theme customizer
    │
    ├── seo/
    │   └── index.php               # SEO manager
    │
    ├── settings/
    │   └── index.php               # Global settings
    │
    ├── comments/
    │   └── index.php               # Comment moderation
    │
    ├── assets/
    │   ├── cms.css                 # CMS styles
    │   └── cms.js                  # CMS JavaScript
    │
    └── db/
        ├── schema.sql              # Database schema
        └── seed.sql                # Sample data
```

## Key Features Implemented

### 1. Dual File System (RULE 1)
Every page has two versions:
- **`.php` files**: Dynamic, read from MySQL database
- **`.html` files**: Static, hardcoded content, work offline

Example structure:
```php
// index.php - Dynamic version
<?php
require_once 'includes/config.php';
$hero = get_setting('hero_headline', 'We Build What Others Imagine');
echo "<h1>$hero</h1>";
?>

// index.html - Static version (identical output, no PHP)
<h1>We Build What Others Imagine</h1>
```

### 2. Triple-Source Media System (RULE 5)
Every icon/image/video field accepts three input types:

1. **Upload**: Drag-and-drop to `/assets/media/uploads/`
2. **URL**: External CDN or service URL
3. **Code/SVG**: Raw SVG or icon font HTML

Implementation in `includes/media.php`:
```php
render_media($source, $alt, $class, $type);
// Automatically detects and renders correctly
```

### 3. Glassmorphic Design System
Complete CSS variable-based theme in `assets/css/cms-theme.css`:

```css
:root {
  /* Background layers */
  --bg-void: #020407;
  --bg-deep: #060c14;
  --bg-mid: #0a1628;
  --bg-surface: #0f1f3d;

  /* Glass effects */
  --glass-opacity: 0.45;
  --glass-blur: 20px;
  --glass-saturation: 160%;
  --glass-border-opacity: 0.07;

  /* Halo system */
  --halo-color-1: #00d4ff;
  --halo-color-2: #7b2fff;
  --halo-color-3: #ff6b35;
  --halo-intensity: 0.35;

  /* Typography */
  --font-display: 'Syne', sans-serif;
  --font-heading: 'Outfit', sans-serif;
  --font-body: 'DM Sans', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;

  /* Accents */
  --accent-1: #00d4ff;
  --accent-2: #7b2fff;
  --accent-3: #ff6b35;
}
```

### 4. Complete CMS (RULES 3 & 4)
Full CRUD operations for:
- Pages (with section builder)
- Blog posts (with categories/tags)
- Portfolio items (with gallery)
- Services (with features/tech stack)
- Team members
- Testimonials
- Media library
- Navigation (drag-and-drop ordering)
- Theme customizer (live preview)
- SEO settings

### 5. Real Sample Content (RULE 2)
All pages include:
- Minimum 300 words of real content
- 6+ service cards
- 6+ portfolio items
- 6+ blog posts
- 4+ team members
- Real testimonials with names/roles/companies
- No Lorem Ipsum

## Database Schema

Complete MySQL schema in `/cms/db/schema.sql` with tables:
- users, pages, posts, categories, tags, post_tags
- portfolio, services, team, testimonials, timeline
- technologies, process_steps, case_studies
- comments, media, navigation, settings, events

## Installation

### Requirements
- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.2+
- Apache with mod_rewrite enabled
- GD library (for image processing)

### Setup Steps

1. **Upload Files**
   ```bash
   # Upload all files to your hosting root
   # Example: public_html/ or www/
   ```

2. **Create Database**
   ```sql
   CREATE DATABASE infinitybinary CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Schema**
   ```bash
   mysql -u username -p infinitybinary < cms/db/schema.sql
   mysql -u username -p infinitybinary < cms/db/seed.sql
   ```

4. **Configure Database**
   Edit `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'infinitybinary');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

5. **Set Permissions**
   ```bash
   chmod 755 assets/media/uploads/
   chmod 644 assets/css/cms-theme.css
   chmod 644 robots.txt
   ```

6. **Access CMS**
   - Visit: `https://yourdomain.com/cms/login.php`
   - Default credentials in seed data
   - Or click the hidden "." in footer copyright

## CMS Access

The CMS entry point is intentionally hidden. There are two ways to access:

1. **Direct URL**: `https://yourdomain.com/cms/login.php`
2. **Hidden Link**: Click the period (.) after "InfinityBinary" in the footer copyright

Default admin login (change immediately):
- Email: admin@infinitybinary.com
- Password: admin123

## Theme Customization

Access Theme Customizer in CMS:
- 40% controls + 60% live preview
- Real-time CSS variable updates
- Export/import theme presets
- Changes written to `assets/css/cms-theme.css`

Categories:
1. Colors (8 color pickers)
2. Glassmorphism (opacity, blur, saturation)
3. Halo Engine (colors, intensity, size, speed)
4. Typography (fonts, sizes, spacing)
5. Animations (toggle individual effects)
6. Section Backgrounds
7. Dark/Light Mode
8. Preset Themes

## Security Features

- Password hashing (bcrypt)
- CSRF protection on all forms
- SQL injection prevention (PDO prepared statements)
- XSS protection (output escaping)
- File upload validation
- Rate limiting on login
- Session security (httponly, secure cookies)
- .htaccess upload protection

## SEO Features

- Auto-generated sitemap.xml
- Customizable robots.txt
- Per-page meta tags
- Open Graph support
- Schema.org JSON-LD
- Google Analytics integration
- Search Console verification
- Canonical URLs

## Performance

- Lazy loading images
- WebP auto-generation
- CSS/JS concatenation ready
- Minimal DOM manipulation
- RequestAnimationFrame animations
- Intersection Observer for scroll effects
- Debounced resize handlers

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Graceful degradation for older browsers
- Backdrop-filter fallbacks

## Development vs Production

Toggle in `includes/config.php`:
```php
define('ENVIRONMENT', 'production'); // 'development' or 'production'
```

Development mode:
- Shows detailed errors
- Verbose logging
- No caching

Production mode:
- Errors logged to file
- Optimized performance
- Security hardened

## File Integrity

**IMPORTANT**: Never edit these files directly:
- `assets/css/cms-theme.css` (CMS-generated)
- Any `.html` files (regenerate from .php via CMS)

## Support

For issues or questions:
- GitHub Issues: [Repository URL]
- Documentation: [Docs URL]
- Email: support@infinitybinary.com

## License

Copyright © 2026 InfinityBinary. All rights reserved.

## Credits

- Design System: Custom glassmorphic framework
- Icons: Font Awesome, Heroicons, Phosphor, Lucide (CDN)
- Fonts: Google Fonts (Syne, Outfit, DM Sans, JetBrains Mono)

---

**Built with ❤️ following MASTER BUILD PROMPT v2.0 specifications**
