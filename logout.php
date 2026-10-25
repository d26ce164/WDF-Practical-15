<?php
/**
 * Practical 10: Secure Logout
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db.php';

if (is_logged_in() && $db_connected) {
    log_audit_action($conn, $_SESSION['user_id'], "USER_LOGOUT", "User {$_SESSION['username']} logged out.");
}

// Invalidate session data
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear remember token cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

session_destroy();

header("Location: login.php?msg=logged_out");
exit();
?>
