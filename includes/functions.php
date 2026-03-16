<?php
/**
 * InfinityBinary - Core Helper Functions
 */

if (!defined('IB_INIT')) {
    die('Direct access not permitted');
}

/**
 * Sanitize string input
 */
function sanitize($data, $type = 'string') {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitize($item, $type);
        }, $data);
    }

    $data = trim($data);

    switch ($type) {
        case 'email':
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($data, FILTER_SANITIZE_URL);
        case 'int':
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'html':
            return $data; // Allow HTML but escape output
        default:
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Escape output for HTML
 */
function esc($data) {
    if (is_array($data)) {
        return array_map('esc', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate slug from string
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

/**
 * Format date
 */
function format_date($date, $format = 'F j, Y') {
    if (empty($date)) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Calculate reading time
 */
function calculate_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    return max(1, ceil($word_count / 200)); // Average 200 words per minute
}

/**
 * Truncate text
 */
function truncate($text, $length = 100, $suffix = '...') {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Generate CSRF token
 */
function csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 */
function verify_csrf($token) {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Generate random string
 */
function random_string($length = 16) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Get current user
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }

    static $user = null;

    if ($user === null) {
        $user = db()->fetch(
            "SELECT id, email, name, role, avatar_source, bio FROM users WHERE id = ? AND status = 'active'",
            [$_SESSION['user_id']]
        );
    }

    return $user ?: null;
}

/**
 * Redirect to URL
 */
function redirect($url, $status_code = 302) {
    header('Location: ' . $url, true, $status_code);
    exit;
}

/**
 * Get setting value
 */
function get_setting($key, $default = '') {
    static $settings = [];

    if (empty($settings)) {
        $results = db()->fetchAll("SELECT setting_key, setting_value FROM site_settings");
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $settings[$key] ?? $default;
}

/**
 * Update setting value
 */
function update_setting($key, $value) {
    $exists = db()->exists('site_settings', 'setting_key = ?', [$key]);

    if ($exists) {
        return db()->update('site_settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
    } else {
        return db()->insert('site_settings', [
            'setting_key' => $key,
            'setting_value' => $value
        ]);
    }
}

/**
 * Parse JSON safely
 */
function json_parse($json, $assoc = true) {
    if (empty($json)) {
        return $assoc ? [] : null;
    }

    $data = json_decode($json, $assoc);
    return json_last_error() === JSON_ERROR_NONE ? $data : ($assoc ? [] : null);
}

/**
 * Encode to JSON safely
 */
function json_encode_safe($data) {
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * Paginate results
 */
function paginate($table, $where = '1', $params = [], $per_page = 10, $page = 1) {
    $offset = ($page - 1) * $per_page;

    $total = db()->count($table, $where, $params);
    $items = db()->fetchAll(
        "SELECT * FROM `{$table}` WHERE {$where} LIMIT {$per_page} OFFSET {$offset}",
        $params
    );

    return [
        'items' => $items,
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $page,
        'total_pages' => ceil($total / $per_page),
        'has_prev' => $page > 1,
        'has_next' => $page < ceil($total / $per_page)
    ];
}

/**
 * Send email
 */
function send_email($to, $subject, $message, $from = null) {
    $from = $from ?: ADMIN_EMAIL;
    $headers = [
        'From: ' . $from,
        'Reply-To: ' . $from,
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8'
    ];

    if (SMTP_ENABLED) {
        // TODO: Implement SMTP sending
        return false;
    }

    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Validate email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 */
function is_valid_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Get client IP address
 */
function get_client_ip() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

    foreach ($ip_keys as $key) {
        if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
            return $_SERVER[$key];
        }
    }

    return '0.0.0.0';
}

/**
 * Log activity (for debugging)
 */
function log_activity($message, $level = 'info') {
    if (!IB_DEBUG) {
        return;
    }

    $log_file = ROOT_PATH . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[{$timestamp}] [{$level}] {$message}\n";

    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Get excerpt from content
 */
function get_excerpt($content, $length = 160) {
    $content = strip_tags($content);
    $content = preg_replace('/\s+/', ' ', $content);
    return truncate($content, $length);
}

/**
 * Format number
 */
function format_number($number) {
    if ($number >= 1000000) {
        return number_format($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return number_format($number / 1000, 1) . 'K';
    }
    return number_format($number);
}

/**
 * Check if request is AJAX
 */
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Return JSON response
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode_safe($data);
    exit;
}

/**
 * Flash message system
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Honeypot check
 */
function check_honeypot($field_name = 'website') {
    return !empty($_POST[$field_name]);
}

/**
 * Rate limiting check
 */
function check_rate_limit($action, $limit = 5, $window = 3600) {
    $key = $action . '_' . get_client_ip();

    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 0, 'reset' => time() + $window];
    }

    $data = &$_SESSION['rate_limit'][$key];

    if (time() > $data['reset']) {
        $data['count'] = 0;
        $data['reset'] = time() + $window;
    }

    $data['count']++;

    return $data['count'] <= $limit;
}
