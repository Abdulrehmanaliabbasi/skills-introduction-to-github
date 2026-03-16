<?php
/**
 * InfinityBinary - Authentication System
 */

if (!defined('IB_INIT')) {
    die('Direct access not permitted');
}

/**
 * Authenticate user login
 */
function authenticate_user($email, $password) {
    // Rate limiting check
    if (!check_rate_limit('login_attempt', MAX_LOGIN_ATTEMPTS, LOGIN_TIMEOUT)) {
        log_login_attempt($email, false);
        return [
            'success' => false,
            'error' => 'Too many login attempts. Please try again in ' . (LOGIN_TIMEOUT / 60) . ' minutes.'
        ];
    }

    // Validate input
    $email = sanitize($email, 'email');

    if (!is_valid_email($email)) {
        return ['success' => false, 'error' => 'Invalid email address'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Invalid password'];
    }

    // Fetch user
    $user = db()->fetch(
        "SELECT * FROM users WHERE email = ? AND status = 'active'",
        [$email]
    );

    if (!$user) {
        log_login_attempt($email, false);
        return ['success' => false, 'error' => 'Invalid credentials'];
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        log_login_attempt($email, false);
        return ['success' => false, 'error' => 'Invalid credentials'];
    }

    // Update last login
    db()->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    // Clear rate limiting
    unset($_SESSION['rate_limit']['login_attempt_' . get_client_ip()]);

    log_login_attempt($email, true);

    return [
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role']
        ]
    ];
}

/**
 * Log login attempt
 */
function log_login_attempt($email, $success) {
    db()->insert('login_attempts', [
        'email' => $email,
        'ip_address' => get_client_ip(),
        'success' => $success ? 1 : 0
    ]);
}

/**
 * Logout user
 */
function logout_user() {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Check if user has role
 */
function has_role($role) {
    if (!is_logged_in()) {
        return false;
    }

    $user_role = $_SESSION['user_role'] ?? '';

    $roles = ['author' => 1, 'editor' => 2, 'admin' => 3];

    return isset($roles[$user_role]) &&
           isset($roles[$role]) &&
           $roles[$user_role] >= $roles[$role];
}

/**
 * Require authentication
 */
function require_auth() {
    if (!is_logged_in()) {
        redirect('/cms/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

/**
 * Require specific role
 */
function require_role($role) {
    require_auth();

    if (!has_role($role)) {
        set_flash('error', 'Access denied. Insufficient permissions.');
        redirect('/cms/index.php');
    }
}

/**
 * Create new user
 */
function create_user($email, $password, $name, $role = 'author') {
    // Validate input
    $email = sanitize($email, 'email');
    $name = sanitize($name);

    if (!is_valid_email($email)) {
        return ['success' => false, 'error' => 'Invalid email address'];
    }

    if (strlen($password) < 8) {
        return ['success' => false, 'error' => 'Password must be at least 8 characters'];
    }

    if (!in_array($role, ['author', 'editor', 'admin'])) {
        return ['success' => false, 'error' => 'Invalid role'];
    }

    // Check if user exists
    if (db()->exists('users', 'email = ?', [$email])) {
        return ['success' => false, 'error' => 'Email already exists'];
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_HASH_ALGO, [
        'cost' => PASSWORD_HASH_COST
    ]);

    // Insert user
    $user_id = db()->insert('users', [
        'email' => $email,
        'password' => $password_hash,
        'name' => $name,
        'role' => $role,
        'status' => 'active'
    ]);

    if ($user_id) {
        return [
            'success' => true,
            'user_id' => $user_id,
            'message' => 'User created successfully'
        ];
    }

    return ['success' => false, 'error' => 'Failed to create user'];
}

/**
 * Update user password
 */
function update_password($user_id, $old_password, $new_password) {
    if (strlen($new_password) < 8) {
        return ['success' => false, 'error' => 'Password must be at least 8 characters'];
    }

    // Fetch user
    $user = db()->fetch("SELECT password FROM users WHERE id = ?", [$user_id]);

    if (!$user) {
        return ['success' => false, 'error' => 'User not found'];
    }

    // Verify old password
    if (!password_verify($old_password, $user['password'])) {
        return ['success' => false, 'error' => 'Current password is incorrect'];
    }

    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_HASH_ALGO, [
        'cost' => PASSWORD_HASH_COST
    ]);

    // Update password
    if (db()->update('users', ['password' => $password_hash], 'id = ?', [$user_id])) {
        return ['success' => true, 'message' => 'Password updated successfully'];
    }

    return ['success' => false, 'error' => 'Failed to update password'];
}

/**
 * Reset user password (admin only)
 */
function reset_user_password($user_id, $new_password) {
    if (!has_role('admin')) {
        return ['success' => false, 'error' => 'Access denied'];
    }

    if (strlen($new_password) < 8) {
        return ['success' => false, 'error' => 'Password must be at least 8 characters'];
    }

    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_HASH_ALGO, [
        'cost' => PASSWORD_HASH_COST
    ]);

    // Update password
    if (db()->update('users', ['password' => $password_hash], 'id = ?', [$user_id])) {
        return ['success' => true, 'message' => 'Password reset successfully'];
    }

    return ['success' => false, 'error' => 'Failed to reset password'];
}

/**
 * Generate password reset token
 */
function generate_reset_token($email) {
    $email = sanitize($email, 'email');

    if (!is_valid_email($email)) {
        return ['success' => false, 'error' => 'Invalid email address'];
    }

    $user = db()->fetch("SELECT id, name FROM users WHERE email = ? AND status = 'active'", [$email]);

    if (!$user) {
        // Don't reveal if email exists
        return ['success' => true, 'message' => 'If the email exists, a reset link has been sent'];
    }

    // Generate token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store token (would need a password_resets table)
    // For now, return success
    // TODO: Implement password reset table and email sending

    return ['success' => true, 'message' => 'Password reset link sent'];
}

/**
 * Check session timeout
 */
function check_session_timeout() {
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
        return;
    }

    $elapsed = time() - $_SESSION['last_activity'];

    if ($elapsed > SESSION_LIFETIME) {
        logout_user();
        redirect('/cms/login.php?timeout=1');
    }

    $_SESSION['last_activity'] = time();
}

/**
 * Regenerate session ID
 */
function regenerate_session() {
    if (is_logged_in()) {
        session_regenerate_id(true);
    }
}
