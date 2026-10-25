<?php
$page_title = "Student Registration";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = (int)($_POST['year'] ?? 2);
    $gender = trim($_POST['gender'] ?? 'Male');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);

    // Server-side validation
    if (empty($full_name) || strlen($full_name) < 3) {
        $error = "Full Name must be at least 3 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Valid university email is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Mobile phone number must be exactly 10 digits.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
        $error = "Password must be at least 8 characters and include uppercase, numeric, and special characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!$terms) {
        $error = "You must accept the terms and conditions.";
    } else {
        // Practical 9: Database duplicate check & password_hash()
        if ($db_connected) {
            // Check duplicate email or username
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "An account with this email address or username already exists!";
                $stmt->close();
            } else {
                $stmt->close();
                // Securely hash password
                $hashed_pw = password_hash($password, PASSWORD_BCRYPT);
                $role = 'student';

                $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role, full_name, status) VALUES (?, ?, ?, ?, ?, 'active')");
                $insert_stmt->bind_param("sssss", $username, $email, $hashed_pw, $role, $full_name);

                if ($insert_stmt->execute()) {
                    $new_user_id = $insert_stmt->insert_id;
                    $insert_stmt->close();

                    // Also create record in students table
                    $student_id = "23IT" . str_pad(rand(50, 999), 3, '0', STR_PAD_LEFT);
                    $name_parts = explode(' ', $full_name);
                    $first_name = $name_parts[0];
                    $last_name = count($name_parts) > 1 ? end($name_parts) : 'Student';
                    $sem = $year * 2 - 1;

                    $st_insert = $conn->prepare("INSERT INTO students (user_id, student_id, first_name, last_name, email, phone, department, semester, gender, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");
                    $st_insert->bind_param("issssssis", $new_user_id, $student_id, $first_name, $last_name, $email, $phone, $course, $sem, $gender);
                    $st_insert->execute();
                    $st_insert->close();

                    log_audit_action($conn, $new_user_id, "USER_REGISTER", "New student {$full_name} ({$username}) registered.");
                    $success = "Registration completed successfully! Your generated Student ID is {$student_id}. You can now proceed to login.";
                } else {
                    $error = "Database error: " . $conn->error;
                }
            }
        } else {
            // Fallback storage in CSV/JSON if database service is starting up
            $record = ['username' => $username, 'email' => $email, 'full_name' => $full_name, 'created' => date('Y-m-d H:i:s')];
            $json_file = __DIR__ . '/data/offline_registrations.json';
            $existing = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) ?: [] : [];
            $existing[] = $record;
            file_put_contents($json_file, json_encode($existing, JSON_PRETTY_PRINT));
            $success = "Registration saved locally! Database connection is currently being established.";
        }
    }
}
?>

<div class="form-card">
  <div style="text-align: center; margin-bottom: 1.5rem;">
    <span class="brand-badge">Practical 5 & 9</span>
    <h1 style="color: var(--primary-color); margin-top: 0.5rem; font-size: 1.8rem;">Student Registration</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Create your StudentHub account with client-side & server-side validation.</p>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success"><span>✅ <?php echo htmlspecialchars($success); ?></span></div>
    <div style="text-align: center; margin-top: 1rem;">
      <a href="login.php" class="btn btn-primary">Go to Login</a>
    </div>
  <?php else: ?>
    <?php if ($error): ?>
      <div class="alert alert-danger"><span>⚠️ <?php echo htmlspecialchars($error); ?></span></div>
    <?php endif; ?>

    <form id="registrationForm" method="POST" action="register.php" onsubmit="return validateFrontendForm(event)" novalidate>
      <div class="form-group">
        <label class="form-label" for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" class="form-control" required placeholder="e.g., Divyakumar Shukla" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
        <span id="name-error" class="error-text"></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="username">Username (Alphanumeric) *</label>
        <input type="text" id="username" name="username" class="form-control" required placeholder="e.g., divya2026" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        <span id="username-error" class="error-text"></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">University Email *</label>
        <input type="email" id="email" name="email" class="form-control" required placeholder="id@charusat.edu.in" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        <span id="email-error" class="error-text"></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="phone">Mobile Phone (10 Digits) *</label>
        <input type="tel" id="phone" name="phone" class="form-control" required placeholder="9876543210" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
        <span id="phone-error" class="error-text"></span>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="course">Department *</label>
          <select id="course" name="course" class="form-control" required>
            <option value="Information Technology">Information Technology</option>
            <option value="Computer Engineering">Computer Engineering</option>
            <option value="Computer Science">Computer Science</option>
            <option value="AI & ML">AI & ML</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="year">Academic Year *</label>
          <select id="year" name="year" class="form-control" required>
            <option value="1">1st Year</option>
            <option value="2" selected>2nd Year (Semester 3)</option>
            <option value="3">3rd Year</option>
            <option value="4">4th Year</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Gender</label>
        <div style="display: flex; gap: 1.5rem; margin-top: 0.35rem;">
          <label><input type="radio" name="gender" value="Male" checked> Male</label>
          <label><input type="radio" name="gender" value="Female"> Female</label>
          <label><input type="radio" name="gender" value="Other"> Other</label>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password (Min 8 chars, 1 uppercase, 1 digit, 1 special char) *</label>
        <input type="password" id="password" name="password" class="form-control" required oninput="evaluatePasswordStrength(this.value)">
        <!-- Password Strength Meter -->
        <div style="margin-top: 0.5rem; background: #e2e8f0; height: 6px; border-radius: 3px; overflow: hidden;">
          <div id="password-strength-bar" style="width: 0%; height: 100%; transition: width 0.3s, background-color 0.3s;"></div>
        </div>
        <span id="password-strength-label" style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 0.25rem;">Strength: Empty</span>
        <span id="password-error" class="error-text"></span>
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm_password">Confirm Password *</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        <span id="confirm-password-error" class="error-text"></span>
      </div>

      <div class="form-group">
        <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.88rem; cursor: pointer;">
          <input type="checkbox" id="terms" name="terms" required style="margin-top: 3px;">
          <span>I accept the student portal guidelines and confirm the provided academic information is accurate.</span>
        </label>
        <span id="terms-error" class="error-text"></span>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">Complete Registration</button>
    </form>
    <div style="text-align: center; margin-top: 1.25rem; font-size: 0.9rem;">
      Already registered? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Sign in here</a>
    </div>
  <?php endif; ?>
</div>

<script>
// Practical 5: Frontend Regular Expression Validation & UX Feedback
function evaluatePasswordStrength(val) {
  const bar = document.getElementById('password-strength-bar');
  const label = document.getElementById('password-strength-label');
  let score = 0;

  if (val.length >= 8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[\W]/.test(val)) score++;

  if (score === 0) {
    bar.style.width = '0%';
    label.textContent = 'Strength: Empty';
  } else if (score <= 2) {
    bar.style.width = '35%';
    bar.style.backgroundColor = '#ef4444';
    label.textContent = 'Strength: Weak';
  } else if (score === 3) {
    bar.style.width = '70%';
    bar.style.backgroundColor = '#f59e0b';
    label.textContent = 'Strength: Moderate';
  } else {
    bar.style.width = '100%';
    bar.style.backgroundColor = '#10b981';
    label.textContent = 'Strength: Strong 🔒';
  }
}

function validateFrontendForm(e) {
  let isValid = true;

  // Clear previous errors
  document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

  const name = document.getElementById('full_name').value.trim();
  const username = document.getElementById('username').value.trim();
  const email = document.getElementById('email').value.trim();
  const phone = document.getElementById('phone').value.trim();
  const password = document.getElementById('password').value;
  const confirmPw = document.getElementById('confirm_password').value;
  const terms = document.getElementById('terms').checked;

  if (name.length < 3) {
    document.getElementById('name-error').textContent = 'Please enter a valid full name (min 3 chars).';
    isValid = false;
  }

  const userRegex = /^[a-zA-Z0-9_]{3,20}$/;
  if (!userRegex.test(username)) {
    document.getElementById('username-error').textContent = 'Username must be 3-20 alphanumeric characters or underscores.';
    isValid = false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    document.getElementById('email-error').textContent = 'Please enter a valid email address.';
    isValid = false;
  }

  const phoneRegex = /^[0-9]{10}$/;
  if (!phoneRegex.test(phone)) {
    document.getElementById('phone-error').textContent = 'Phone number must be exactly 10 digits.';
    isValid = false;
  }

  const pwRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W]).{8,}$/;
  if (!pwRegex.test(password)) {
    document.getElementById('password-error').textContent = 'Password must have at least 8 chars, 1 uppercase letter, 1 number, and 1 special symbol.';
    isValid = false;
  }

  if (password !== confirmPw) {
    document.getElementById('confirm-password-error').textContent = 'Passwords do not match.';
    isValid = false;
  }

  if (!terms) {
    document.getElementById('terms-error').textContent = 'You must accept the terms before continuing.';
    isValid = false;
  }

  if (!isValid) {
    e.preventDefault();
  }
  return isValid;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
