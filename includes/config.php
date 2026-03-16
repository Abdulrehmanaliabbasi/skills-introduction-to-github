<?php
/**
 * InfinityBinary - Configuration File
 * Core configuration settings for the application
 */

// Prevent direct access
if (!defined('IB_INIT')) {
    define('IB_INIT', true);
}

// Environment Configuration
define('IB_ENV', 'production'); // production, development
define('IB_DEBUG', false);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'infinitybinary');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'InfinityBinary');
define('SITE_TAGLINE', 'Digital Excellence Delivered');
define('ADMIN_EMAIL', 'admin@infinitybinary.com');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('MEDIA_PATH', ASSETS_PATH . '/media');
define('UPLOADS_PATH', MEDIA_PATH . '/uploads');
define('CMS_PATH', ROOT_PATH . '/cms');

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);

// Upload Configuration
define('MAX_UPLOAD_SIZE', 10485760); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg']);

// Pagination Configuration
define('POSTS_PER_PAGE', 9);
define('PORTFOLIO_PER_PAGE', 12);
define('COMMENTS_PER_PAGE', 20);

// Email Configuration (SMTP)
define('SMTP_ENABLED', false);
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls'); // tls or ssl

// Cache Configuration
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 hour

// SEO Configuration
define('META_DESCRIPTION', 'InfinityBinary - Expert Digital Solutions. Web Development, Mobile Applications, AI Integration, Cloud Solutions, and UI/UX Design.');
define('META_KEYWORDS', 'web development, mobile apps, AI, cloud solutions, UI/UX design, digital agency');
define('OG_IMAGE', SITE_URL . '/assets/media/og-image.jpg');

// Error Reporting
if (IB_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('UTC');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
ini_set('session.cookie_samesite', 'Lax');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-load includes
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/db.php';
