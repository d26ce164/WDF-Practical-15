<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_auth = isset($_SESSION['user_id']);
$user_name = $_SESSION['full_name'] ?? 'User';
$user_role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="StudentHub - CHARUSAT Academic & Event Management Portal">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | StudentHub' : 'StudentHub - CHARUSAT'; ?></title>
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="icon" href="data:,">
</head>
<body>
  <!-- Accessibility: Skip to Content -->
  <a href="#main-content" class="skip-link">Skip to main content</a>

  <!-- Navigation Header -->
  <header class="navbar">
    <div class="nav-container">
      <a href="index.php" class="brand-logo" aria-label="StudentHub Homepage">
        🎓 Student<span>Hub</span>
        <span class="brand-badge">CHARUSAT</span>
      </a>

      <button class="hamburger" id="nav-toggle" aria-label="Toggle navigation menu">☰</button>

      <nav aria-label="Primary Navigation">
        <ul class="nav-menu" id="nav-menu">
          <li><a href="index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">Home</a></li>
          <li><a href="about.php" class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>">About</a></li>
          <li><a href="events.php" class="nav-link <?php echo $current_page === 'events.php' ? 'active' : ''; ?>">Events</a></li>
          <li><a href="faq.php" class="nav-link <?php echo $current_page === 'faq.php' ? 'active' : ''; ?>">FAQ</a></li>
          <li><a href="contact.php" class="nav-link <?php echo $current_page === 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
          <li><a href="feedback.php" class="nav-link <?php echo $current_page === 'feedback.php' ? 'active' : ''; ?>">Feedback</a></li>

          <?php if ($is_auth): ?>
            <li><a href="dashboard.php" class="nav-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <?php if ($user_role === 'admin'): ?>
              <li><a href="admin.php" class="nav-link <?php echo $current_page === 'admin.php' ? 'active' : ''; ?>" style="color: var(--secondary-color); font-weight:700;">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="profile.php" class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>">Profile (<?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>)</a></li>
            <li><a href="logout.php" class="btn btn-outline btn-sm">Logout</a></li>
          <?php else: ?>
            <li><a href="login.php" class="nav-link <?php echo $current_page === 'login.php' ? 'active' : ''; ?>">Login</a></li>
            <li><a href="register.php" class="btn btn-primary btn-sm">Register</a></li>
          <?php endif; ?>
        </ul>
      </nav>

      <div class="nav-actions">
        <button id="theme-toggle" class="theme-toggle-btn" aria-label="Switch between light and dark theme">🌙 Dark</button>
      </div>
    </div>
  </header>

  <main id="main-content" class="container">
