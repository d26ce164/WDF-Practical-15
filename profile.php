<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db.php';

require_login();

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Load student profile data
$profile = [
    'full_name' => $_SESSION['full_name'],
    'email' => $_SESSION['email'],
    'student_id' => '23IT001',
    'department' => 'Information Technology',
    'phone' => '9876543210',
    'gender' => 'Male'
];

if ($db_connected) {
    $stmt = $conn->prepare("SELECT s.*, u.full_name, u.email FROM users u LEFT JOIN students s ON u.id = s.user_id WHERE u.id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $profile['full_name'] = $row['full_name'] ?: $profile['full_name'];
        $profile['email'] = $row['email'] ?: $profile['email'];
        if (!empty($row['student_id'])) {
            $profile['student_id'] = $row['student_id'];
            $profile['department'] = $row['department'];
            $profile['phone'] = $row['phone'];
            $profile['gender'] = $row['gender'];
        }
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS));
    $new_phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS));

    if (empty($new_name) || strlen($new_name) < 3) {
        $error_msg = "Please enter a valid full name.";
    } else {
        if ($db_connected) {
            $u_stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
            $u_stmt->bind_param("si", $new_name, $user_id);
            $u_stmt->execute();
            $u_stmt->close();

            $s_stmt = $conn->prepare("UPDATE students SET phone = ? WHERE user_id = ?");
            $s_stmt->bind_param("si", $new_phone, $user_id);
            $s_stmt->execute();
            $s_stmt->close();

            $_SESSION['full_name'] = $new_name;
            $profile['full_name'] = $new_name;
            $profile['phone'] = $new_phone;
            $success_msg = "Profile updated successfully!";
            log_audit_action($conn, $user_id, "PROFILE_UPDATE", "User updated personal profile details.");
        } else {
            $_SESSION['full_name'] = $new_name;
            $profile['full_name'] = $new_name;
            $profile['phone'] = $new_phone;
            $success_msg = "Profile updated locally!";
        }
    }
}

$page_title = "User Profile";
require_once __DIR__ . '/includes/header.php';
?>

<div class="form-card" style="max-width: 650px;">
  <div style="text-align: center; margin-bottom: 1.5rem;">
    <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--primary-color); color: #fff; font-size: 2rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
      <?php echo strtoupper(substr($profile['full_name'], 0, 1)); ?>
    </div>
    <h1 style="color: var(--primary-color); font-size: 1.6rem;"><?php echo htmlspecialchars($profile['full_name']); ?></h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">CHARUSAT Student Identification: <strong><?php echo htmlspecialchars($profile['student_id']); ?></strong></p>
  </div>

  <?php if ($success_msg): ?>
    <div class="alert alert-success"><span>✅ <?php echo htmlspecialchars($success_msg); ?></span></div>
  <?php endif; ?>
  <?php if ($error_msg): ?>
    <div class="alert alert-danger"><span>⚠️ <?php echo htmlspecialchars($error_msg); ?></span></div>
  <?php endif; ?>

  <form method="POST" action="profile.php">
    <div class="form-group">
      <label class="form-label" for="full_name">Full Name</label>
      <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($profile['full_name']); ?>" required>
    </div>

    <div class="form-group">
      <label class="form-label" for="email">University Email (Read-only)</label>
      <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($profile['email']); ?>" disabled style="opacity: 0.7;">
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
      <div class="form-group">
        <label class="form-label" for="department">Department</label>
        <input type="text" id="department" class="form-control" value="<?php echo htmlspecialchars($profile['department']); ?>" disabled style="opacity: 0.7;">
      </div>
      <div class="form-group">
        <label class="form-label" for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile['phone']); ?>">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Gender</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($profile['gender']); ?>" disabled style="opacity: 0.7;">
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">Save Changes</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
