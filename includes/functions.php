<?php
// Core Helper Functions
if (!defined('INFINITYBINARY_INIT')) {
    die('Direct access not permitted');
}

/**
 * Escape HTML attribute
 */
function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape HTML output
 */
function esc_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape URL
 */
function esc_url($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

/**
 * Sanitize string
 */
function sanitize_text($text) {
    return trim(strip_tags($text));
}

/**
 * Sanitize email
 */
function sanitize_email($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

/**
 * Validate email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate slug from string
 */
function generate_slug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? uniqid() : $text;
}

/**
 * Format bytes to human readable
 */
function format_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Format date
 */
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Get time ago string
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

/**
 * Calculate reading time
 */
function calculate_reading_time($content) {
    $wordCount = str_word_count(strip_tags($content));
    $minutes = ceil($wordCount / 200);
    return $minutes . ' min read';
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    $text = strip_tags($text);
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get current page URL
 */
function current_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Redirect to URL
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require login
 */
function require_login() {
    if (!is_logged_in()) {
        redirect(BASE_URL . '/cms/login.php');
    }
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Get CSRF token
 */
function csrf_token() {
    return $_SESSION[CSRF_TOKEN_NAME] ?? '';
}

/**
 * Generate CSRF token field
 */
function csrf_field() {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . esc_attr(csrf_token()) . '">';
}

/**
 * Flash message functions
 */
function set_flash($key, $message, $type = 'info') {
    $_SESSION['flash'][$key] = [
        'message' => $message,
        'type' => $type
    ];
}

function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    return null;
}

function has_flash($key) {
    return isset($_SESSION['flash'][$key]);
}

/**
 * Get setting from database
 */
function get_setting($key, $default = '') {
    $setting = db()->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    return $setting ? $setting['setting_value'] : $default;
}

/**
 * Update setting in database
 */
function update_setting($key, $value) {
    $existing = db()->fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);

    if ($existing) {
        return db()->update('settings', ['setting_value' => $value], 'setting_key = :key', ['key' => $key]);
    } else {
        return db()->insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
    }
}

/**
 * Get page by slug
 */
function get_page_by_slug($slug) {
    return db()->fetchOne("SELECT * FROM pages WHERE slug = ? AND status = 'published'", [$slug]);
}

/**
 * Get post by slug
 */
function get_post_by_slug($slug) {
    return db()->fetchOne("SELECT p.*, c.name as category_name, c.slug as category_slug
                          FROM posts p
                          LEFT JOIN categories c ON p.category_id = c.id
                          WHERE p.slug = ? AND p.status = 'published'", [$slug]);
}

/**
 * Get portfolio item by slug
 */
function get_portfolio_by_slug($slug) {
    return db()->fetchOne("SELECT * FROM portfolio WHERE slug = ? AND status = 'published'", [$slug]);
}

/**
 * Get navigation items
 */
function get_nav_items($location = 'header') {
    return db()->fetchAll("SELECT * FROM navigation WHERE location = ? ORDER BY `order` ASC", [$location]);
}

/**
 * Send email
 */
function send_email($to, $subject, $message, $headers = []) {
    if (SMTP_ENABLED) {
        // SMTP sending logic would go here
        // For now, use PHP mail()
    }

    $defaultHeaders = [
        'From: ' . SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>',
        'Reply-To: ' . SMTP_FROM_EMAIL,
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8'
    ];

    $allHeaders = array_merge($defaultHeaders, $headers);

    return mail($to, $subject, $message, implode("\r\n", $allHeaders));
}

/**
 * JSON response helper
 */
function json_response($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Pagination helper
 */
function paginate($total, $perPage, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => $currentPage - 1,
        'next_page' => $currentPage + 1
    ];
}
