<?php
$page_title = "Home - Campus Portal";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section with CSS Grid / Flexbox -->
<section style="background: linear-gradient(135deg, rgba(15,76,129,0.08) 0%, rgba(245,130,32,0.08) 100%); padding: 3rem 2rem; border-radius: var(--radius); margin-bottom: 2.5rem; text-align: center;">
  <span class="brand-badge" style="margin-bottom: 1rem; display: inline-block;">Semester 3 - ITUE203</span>
  <h1 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 1rem;">Welcome to StudentHub</h1>
  <p style="font-size: 1.15rem; color: var(--text-muted); max-width: 700px; margin: 0 auto 1.5rem;">
    Your centralized platform for student lifecycle tracking, event registrations, campus announcements, and academic collaboration at CHARUSAT.
  </p>
  <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
    <a href="register.php" class="btn btn-primary">Join StudentHub</a>
    <a href="events.php" class="btn btn-outline">Explore Campus Events</a>
    <a href="faq.php" class="btn btn-secondary">Frequently Asked Questions</a>
  </div>
</section>

<!-- Quick Stats Feature Row -->
<section style="margin-bottom: 3rem;">
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; text-align: center;">
    <div class="card">
      <h3 style="font-size: 2rem; color: var(--primary-color); margin-bottom: 0.25rem;">1500+</h3>
      <p style="color: var(--text-muted); font-size: 0.9rem;">Enrolled Students</p>
    </div>
    <div class="card">
      <h3 style="font-size: 2rem; color: var(--secondary-color); margin-bottom: 0.25rem;">30+</h3>
      <p style="color: var(--text-muted); font-size: 0.9rem;">Annual Events & Fests</p>
    </div>
    <div class="card">
      <h3 style="font-size: 2rem; color: var(--accent-color); margin-bottom: 0.25rem;">15</h3>
      <p style="color: var(--text-muted); font-size: 0.9rem;">Course Practical Modules</p>
    </div>
    <div class="card">
      <h3 style="font-size: 2rem; color: var(--warning-color); margin-bottom: 0.25rem;">100%</h3>
      <p style="color: var(--text-muted); font-size: 0.9rem;">Accessible & Responsive</p>
    </div>
  </div>
</section>

<!-- Key Modules Overview (Grid) -->
<section style="margin-bottom: 3rem;">
  <h2 style="text-align: center; margin-bottom: 0.5rem; color: var(--primary-color);">Key Features & Capabilities</h2>
  <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">Designed and engineered according to modern web development standards.</p>

  <div class="card-grid">
    <div class="card">
      <h3 style="margin-bottom: 0.5rem;">🔐 Secure Authentication</h3>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Complete with BCRYPT password hashing, session regeneration, remember-me token management, and role-based access control.
      </p>
    </div>
    <div class="card">
      <h3 style="margin-bottom: 0.5rem;">👥 Student Management</h3>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        CRUD interface with live search, department filtering, pagination, and data export in both CSV and JSON formats.
      </p>
    </div>
    <div class="card">
      <h3 style="margin-bottom: 0.5rem;">🎉 Event Lifecycle & Posters</h3>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Dynamic event showcase, seat reservation, server-side validated poster image upload with size and MIME type checks.
      </p>
    </div>
    <div class="card">
      <h3 style="margin-bottom: 0.5rem;">⚡ Modern Fetch API & REST</h3>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Zero full-page reloads using asynchronous JavaScript Fetch API and backend PHP JSON micro-services.
      </p>
    </div>
    <div class="card">
      <h3 style="margin-bottom: 0.5rem;">📊 Administrative Dashboard</h3>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        Comprehensive administrative panel with metrics summary cards, system audit logging, and dynamic role-based navigation.
      </p>
    </div>
    <div class="card">
      <h3 style="margin-bottom: 0.5rem;">♿ Accessibility & UX</h3>
      <p style="color: var(--text-muted); font-size: 0.95rem;">
        WCAG-compliant semantic HTML5 markup, skip-links, high-contrast dark/light mode, and responsive layout for all screens.
      </p>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
