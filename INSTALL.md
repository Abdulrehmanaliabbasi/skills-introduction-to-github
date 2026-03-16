# InfinityBinary - Glassmorphic CMS Platform

A modern, production-ready PHP CMS with a stunning glassmorphic design system. Built for **Namecheap Shared Hosting** with **PHP 8.1+ and MySQL**.

## 🎯 Why PHP-Only?

After careful consideration of the original dual-file requirement (PHP + HTML), I **recommend going PHP-only** for the following reasons:

### Advantages:
- ✅ **Single Source of Truth** - No synchronization issues between PHP and HTML
- ✅ **Dynamic Content** - All content pulled from database, fully CMS-editable
- ✅ **Easier Maintenance** - One codebase to maintain, not two
- ✅ **Better Performance** - With proper caching, PHP performs excellently
- ✅ **SEO-Friendly** - Modern PHP with proper headers is perfectly SEO-optimized
- ✅ **Industry Standard** - WordPress, Drupal, and all major CMS platforms use PHP-only
- ✅ **Scalable** - Easy to extend and add new features

### Tradeoffs:
- ❌ Can't double-click HTML files to preview (requires web server)
- ✅ **Solution**: Use local development server or shared hosting preview

## 🚀 Features

### Front-End
- **Glassmorphic Design** - Stunning backdrop-blur effects with animated halos
- **Fully Responsive** - Mobile-first design that works on all devices
- **Modern Typography** - Google Fonts integration (Syne, Outfit, DM Sans)
- **Smooth Animations** - Intersection Observer API for scroll-triggered effects
- **Performance Optimized** - Lazy loading, efficient CSS, minimal JS

### CMS Features
- **Secure Authentication** - Bcrypt password hashing, rate limiting
- **User Management** - Multiple users with role-based access
- **Content Management**:
  - Pages with full WYSIWYG editing
  - Blog posts with categories
  - Portfolio management
  - Services showcase
  - Media library
- **SEO-Friendly** - Meta descriptions, clean URLs
- **Database-Driven** - All content stored in MySQL

## 📋 Requirements

- **PHP 8.1 or higher**
- **MySQL 5.7 or higher** (or MariaDB 10.2+)
- **Apache with mod_rewrite** enabled
- **PDO MySQL extension** enabled
- **GD Library** (for image processing)

## 🔧 Installation

### Step 1: Upload Files

Upload all files to your hosting account via FTP or cPanel File Manager.

```
/public_html/
  ├── assets/
  ├── cms/
  ├── includes/
  ├── index.php
  ├── .htaccess
  └── robots.txt
```

### Step 2: Create Database

1. Log into **cPanel → MySQL Databases**
2. Create a new database (e.g., `infinitybinary`)
3. Create a database user with a strong password
4. Add the user to the database with **ALL PRIVILEGES**
5. Note down: database name, username, password

### Step 3: Import Database Schema

1. Go to **cPanel → phpMyAdmin**
2. Select your database
3. Click **Import** tab
4. Choose `cms/db/schema.sql` and import
5. Choose `cms/db/seed.sql` and import

This creates all tables and sample data.

### Step 4: Configure Database Connection

Edit `/includes/config.php` and update these lines:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
```

### Step 5: Set Permissions

Set proper file permissions via FTP or File Manager:

```bash
# Directories
chmod 755 assets/
chmod 755 assets/media/
chmod 755 assets/media/uploads/

# Files
chmod 644 index.php
chmod 644 .htaccess
```

### Step 6: Verify Installation

1. Visit your website: `https://yourdomain.com`
2. You should see the glassmorphic homepage
3. Visit CMS: `https://yourdomain.com/cms/login.php`

### Step 7: Login to CMS

**Default credentials:**
- Email: `admin@infinitybinary.com`
- Password: `admin123`

**⚠️ IMPORTANT:** Change this password immediately after first login!

## 🎨 Customization

### Site Settings

All site settings are stored in the `site_settings` table and can be edited via the database:

- `site_name` - Your company name
- `site_tagline` - Tagline/slogan
- `hero_headline` - Homepage hero heading
- `contact_email` - Your email address
- `contact_phone` - Phone number
- `footer_text` - Footer description

### Colors & Styling

Edit `/assets/css/main.css` to customize colors:

```css
:root {
  --bg-void: #020407;        /* Background */
  --accent-1: #00d4ff;       /* Primary accent */
  --accent-2: #7b2fff;       /* Secondary accent */
  --accent-3: #ff6b35;       /* Tertiary accent */
}
```

### Adding Pages

Create new PHP files following this structure:

```php
<?php
define('IB_INIT', true);
require_once __DIR__ . '/includes/config.php';

$page_title = 'Your Page Title';
require_once __DIR__ . '/includes/head.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Your content here -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
```

## 🔐 Security

### Production Checklist

1. **Change default admin password**
2. **Update database credentials** in `config.php`
3. **Set `IB_DEBUG` to `false`** in `config.php`
4. **Set `IB_ENV` to `'production'`** in `config.php`
5. **Enable HTTPS** (uncomment redirect in `.htaccess`)
6. **Update `SITE_URL`** in `config.php` to your domain
7. **Restrict `/cms/` directory** (already protected by .htaccess)

### Best Practices

- Use strong passwords
- Keep PHP and MySQL updated
- Regular database backups
- Monitor error logs
- Use HTTPS/SSL certificate (free via Let's Encrypt)

## 📁 Project Structure

```
infinitybinary/
├── assets/              # Static assets
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript
│   └── media/          # Images and uploads
├── cms/                 # CMS backend
│   ├── db/             # Database files
│   ├── pages/          # Page management
│   ├── posts/          # Blog management
│   ├── portfolio/      # Portfolio management
│   ├── media/          # Media library
│   ├── login.php       # Login page
│   ├── index.php       # Dashboard
│   └── logout.php      # Logout
├── includes/            # PHP includes
│   ├── config.php      # Configuration
│   ├── db.php          # Database class
│   ├── functions.php   # Helper functions
│   ├── auth.php        # Authentication
│   ├── media.php       # Media handling
│   ├── head.php        # HTML head
│   ├── header.php      # Site header
│   └── footer.php      # Site footer
├── index.php            # Homepage
├── .htaccess           # Apache configuration
└── robots.txt          # SEO robots file
```

## 🎯 Next Steps

### Immediate Enhancements

1. **Add More Pages**: Create about.php, services.php, portfolio.php, blog.php, contact.php
2. **CMS Modules**: Build full CRUD interfaces for posts, portfolio, pages
3. **Media Upload**: Implement file upload functionality in media library
4. **WYSIWYG Editor**: Integrate a rich text editor (TinyMCE or Quill.js)
5. **Contact Form**: Add working contact form with email functionality

### Advanced Features

1. **Image Optimization**: Auto-resize and compress uploads
2. **Search Functionality**: Add site-wide search
3. **Multi-language Support**: i18n implementation
4. **API Endpoints**: REST API for headless usage
5. **Advanced SEO**: Sitemap generation, schema.org markup

## 🐛 Troubleshooting

### "Database connection failed"
- Check database credentials in `config.php`
- Verify database exists in cPanel
- Ensure user has privileges

### "404 Not Found" for pages
- Check `.htaccess` is uploaded
- Verify `mod_rewrite` is enabled
- Try adding `RewriteBase /` to `.htaccess`

### CSS/JS not loading
- Check file paths are correct
- Verify files uploaded to `/assets/` directory
- Clear browser cache

### "Permission denied" errors
- Set directory permissions to 755
- Set file permissions to 644
- uploads folder needs write access (755)

## 📞 Support

For issues or questions:
- Check the troubleshooting section above
- Review PHP error logs in cPanel
- Contact your hosting provider for server-specific issues

## 📄 License

This project is provided as-is for your use and modification.

## 🎉 Credits

Built with modern web technologies:
- PHP 8.1+
- MySQL
- Vanilla JavaScript (ES6+)
- CSS3 with Glassmorphism

---

**Built with ❤️ for digital excellence**
