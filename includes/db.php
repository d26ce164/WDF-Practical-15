<?php
/**
 * Database Connection & Global Configuration
 * StudentHub - CHARUSAT ITUE203 Practical 8 / 9 / 10 / 11 / 12 / 13 / 14
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'studenthub');
define('DB_PORT', 3306);

// Establish MySQLi connection
$conn = null;
$db_connected = false;
if (class_exists('mysqli')) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        error_log("StudentHub DB Connection Error: " . $conn->connect_error);
    } else {
        $conn->set_charset("utf8mb4");
        $db_connected = true;
    }
} else {
    error_log('StudentHub requires the PHP mysqli extension. Enable mysqli in php.ini.');
}

/**
 * Helper to log audit actions
 */
function log_audit_action($conn, $userId, $action, $details = '') {
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_error) return false;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isss", $userId, $action, $details, $ip);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}
?>
