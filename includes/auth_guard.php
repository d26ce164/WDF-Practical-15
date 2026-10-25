<?php
/**
 * Session Management, Role-Based Access Control, and Timeout Check
 * Practical 10 requirement:
 * - Session timeout after 15 minutes (900 seconds)
 * - Session regeneration
 * - Role redirection
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set true if HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Session Timeout Check (15 minutes = 900 seconds)
$timeout_duration = 900; 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    // Session expired
    session_unset();
    session_destroy();
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    header("Location: login.php?msg=session_timeout");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if current user is admin
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if current user is student
 */
function is_student() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

/**
 * Require Login Guard
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php?msg=login_required");
        exit();
    }
}

/**
 * Require Admin Guard
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        header("Location: dashboard.php?msg=unauthorized_admin");
        exit();
    }
}
?>
