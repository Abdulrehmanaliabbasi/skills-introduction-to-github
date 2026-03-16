<?php
// INFINITYBINARY Configuration
// Master configuration file for the application

// Prevent direct access
if (!defined('INFINITYBINARY_INIT')) {
    define('INFINITYBINARY_INIT', true);
}

// Environment
define('ENVIRONMENT', 'development'); // 'development' or 'production'
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/'));

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'infinitybinary');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Security
define('SESSION_NAME', 'infinitybinary_session');
define('SESSION_LIFETIME', 7200); // 2 hours in seconds
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Upload Configuration
define('UPLOAD_PATH', BASE_PATH . '/assets/media/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/media/uploads/');
define('MAX_UPLOAD_SIZE', 10485760); // 10MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg']);

// Pagination
define('POSTS_PER_PAGE', 9);
define('PORTFOLIO_PER_PAGE', 12);
define('COMMENTS_PER_PAGE', 20);

// Email Configuration
define('SMTP_ENABLED', false); // Set to true to use SMTP
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@infinitybinary.com');
define('SMTP_PASS', '');
define('SMTP_FROM_EMAIL', 'noreply@infinitybinary.com');
define('SMTP_FROM_NAME', 'InfinityBinary');
define('ADMIN_EMAIL', 'admin@infinitybinary.com');

// Site Configuration (Default values, can be overridden in CMS)
define('SITE_NAME', 'InfinityBinary');
define('SITE_TAGLINE', 'Innovation Beyond Limits');
define('SITE_DESCRIPTION', 'We build what others imagine. Expert digital solutions for the modern world.');
define('DEFAULT_META_KEYWORDS', 'web development, mobile applications, AI integration, cloud solutions, digital agency');

// Timezone
date_default_timezone_set('UTC');

// Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/php-error.log');
}

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0));
ini_set('session.cookie_samesite', 'Strict');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

// Auto-load required files
require_once BASE_PATH . '/includes/db.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/media.php';
