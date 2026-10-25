<?php
/**
 * Practical 10: Secure Login/Logout with Sessions, RBAC, Timeout, and Remember-Me
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db.php';

// If already logged in, redirect based on role
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$error = '';
$msg = $_GET['msg'] ?? '';
if ($msg === 'session_timeout') {
    $error = "Your session expired after 15 minutes of inactivity. Please log in again.";
} elseif ($msg === 'login_required') {
    $error = "Please sign in to access that protected portal area.";
} elseif ($msg === 'logged_out') {
    $info_msg = "You have been logged out securely.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($identifier) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        if ($db_connected) {
            $stmt = $conn->prepare("SELECT id, username, email, password, role, full_name, status FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $identifier, $identifier);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                // Verify status
                if ($user['status'] !== 'active') {
                    $error = "This account is inactive. Please contact the administrator.";
                } elseif (password_verify($password, $user['password'])) {
                    // Password verified! Regenerate session ID to prevent session fixation attacks
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['LAST_ACTIVITY'] = time();

                    // Update last_login in DB
                    $update_login = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $update_login->bind_param("i", $user['id']);
                    $update_login->execute();
                    $update_login->close();

                    log_audit_action($conn, $user['id'], "USER_LOGIN", "User {$user['username']} logged in successfully.");

                    // Handle Remember Me (Practical 10 Advanced)
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $token_hash = hash('sha256', $token);
                        $expires = date('Y-m-d H:i:s', time() + (86400 * 30)); // 30 days

                        $rem_stmt = $conn->prepare("INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
                        $rem_stmt->bind_param("iss", $user['id'], $token_hash, $expires);
                        $rem_stmt->execute();
                        $rem_stmt->close();

                        setcookie('remember_token', $token, [
                            'expires' => time() + (86400 * 30),
                            'path' => '/',
                            'httponly' => true,
                            'samesite' => 'Lax'
                        ]);
                    }

                    // Role-Based Redirection
                    if ($user['role'] === 'admin') {
                        header("Location: admin.php");
                    } else {
                        header("Location: dashboard.php");
                    }
                    exit();
                } else {
                    $error = "Invalid username/email or password.";
                    log_audit_action($conn, $user['id'], "LOGIN_FAIL", "Failed login attempt for {$identifier}");
                }
            } else {
                $error = "Invalid username/email or password.";
            }
            $stmt->close();
        } else {
            // Demo fallback if testing offline without running MySQL service
            if ($identifier === 'admin' && $password === 'admin123') {
                session_regenerate_id(true);
                $_SESSION['user_id'] = 1;
                $_SESSION['username'] = 'admin';
                $_SESSION['role'] = 'admin';
                $_SESSION['full_name'] = 'System Administrator';
                $_SESSION['LAST_ACTIVITY'] = time();
                header("Location: admin.php");
                exit();
            } else {
                $error = "Unable to connect to MySQL database. Default fallback: user 'admin' / pass 'admin123'";
            }
        }
    }
}

$page_title = "Sign In";
require_once __DIR__ . '/includes/header.php';
?>

<div class="form-card">
  <div style="text-align: center; margin-bottom: 1.5rem;">
    <span class="brand-badge">Practical 10</span>
    <h1 style="color: var(--primary-color); margin-top: 0.5rem; font-size: 1.8rem;">Welcome Back</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Sign in with your StudentHub or CHARUSAT credentials.</p>
  </div>

  <?php if (!empty($info_msg)): ?>
    <div class="alert alert-info"><span>ℹ️ <?php echo htmlspecialchars($info_msg); ?></span></div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><span>⚠️ <?php echo htmlspecialchars($error); ?></span></div>
  <?php endif; ?>

  <!-- Pre-seeded demo credentials banner for evaluation & viva testing -->
  <div style="background: rgba(15,76,129,0.06); border-radius: 6px; padding: 0.75rem; font-size: 0.85rem; margin-bottom: 1.25rem;">
    <strong>Quick Demo Credentials:</strong><br>
    • Admin: <code>admin</code> / <code>Password123!</code><br>
    • Student: <code>divyakumar</code> / <code>Password123!</code>
  </div>

  <form method="POST" action="login.php" novalidate>
    <div class="form-group">
      <label class="form-label" for="identifier">Username or Email *</label>
      <input type="text" id="identifier" name="identifier" class="form-control" required placeholder="admin or student email" value="<?php echo htmlspecialchars($_POST['identifier'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label class="form-label" for="password">Password *</label>
      <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; font-size: 0.88rem;">
      <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer;">
        <input type="checkbox" name="remember" value="1"> Remember me for 30 days
      </label>
      <a href="faq.php" style="color: var(--primary-color); text-decoration: none;">Forgot password?</a>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
  </form>

  <div style="text-align: center; margin-top: 1.25rem; font-size: 0.9rem;">
    Don't have an account? <a href="register.php" style="color: var(--primary-color); font-weight: 600;">Register now</a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
