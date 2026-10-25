<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db.php';

require_login();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];
$user_role = $_SESSION['role'];

// Fetch registered events for this student if DB connected
$my_events = [];
if ($db_connected) {
    // Find matching student record
    $st_stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ? OR email = ?");
    $st_stmt->bind_param("is", $user_id, $_SESSION['email']);
    $st_stmt->execute();
    $st_res = $st_stmt->get_result();
    if ($st_row = $st_res->fetch_assoc()) {
        $student_db_id = $st_row['id'];
        $ev_stmt = $conn->prepare("
            SELECT e.id, e.title, e.category, e.event_date, e.venue, r.registered_at, r.status
            FROM registrations r
            JOIN events e ON r.event_id = e.id
            WHERE r.student_id = ?
            ORDER BY e.event_date ASC
        ");
        $ev_stmt->bind_param("i", $student_db_id);
        $ev_stmt->execute();
        $my_events = $ev_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $ev_stmt->close();
    }
    $st_stmt->close();
}

$page_title = "Student Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

<section style="margin-bottom: 2rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
    <div>
      <h1 style="color: var(--primary-color); margin-bottom: 0.25rem;">Student Dashboard</h1>
      <p style="color: var(--text-muted);">Welcome back, <strong><?php echo htmlspecialchars($user_name); ?></strong>! Role: <span class="brand-badge"><?php echo htmlspecialchars(strtoupper($user_role)); ?></span></p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
      <a href="events.php" class="btn btn-primary btn-sm">Explore More Events</a>
      <a href="profile.php" class="btn btn-outline btn-sm">Edit Profile</a>
    </div>
  </div>

  <!-- Metric Badges -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--primary-color);">
      <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Enrolled Events</div>
      <div style="font-size: 1.8rem; font-weight: 700; color: var(--primary-color); margin-top: 0.25rem;"><?php echo count($my_events); ?></div>
    </div>
    <div class="card" style="border-left: 4px solid var(--accent-color);">
      <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Account Status</div>
      <div style="font-size: 1.8rem; font-weight: 700; color: var(--accent-color); margin-top: 0.25rem;">Active ✅</div>
    </div>
    <div class="card" style="border-left: 4px solid var(--secondary-color);">
      <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Current Term</div>
      <div style="font-size: 1.8rem; font-weight: 700; color: var(--secondary-color); margin-top: 0.25rem;">Sem 3 (2026-27)</div>
    </div>
  </div>

  <!-- Registered Events Section -->
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h2 style="font-size: 1.3rem; color: var(--primary-color);">My Registered Campus Events</h2>
      <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo count($my_events); ?> entries</span>
    </div>

    <?php if (empty($my_events)): ?>
      <div style="text-align: center; padding: 2.5rem 1rem;">
        <p style="color: var(--text-muted); margin-bottom: 1rem;">You haven't registered for any campus events yet.</p>
        <a href="events.php" class="btn btn-outline">Browse Campus Events</a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Event Title</th>
              <th>Category</th>
              <th>Event Date</th>
              <th>Venue</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($my_events as $item): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                <td><span class="brand-badge"><?php echo htmlspecialchars($item['category']); ?></span></td>
                <td><?php echo htmlspecialchars($item['event_date']); ?></td>
                <td><?php echo htmlspecialchars($item['venue']); ?></td>
                <td><span style="color: var(--accent-color); font-weight: 600;">Confirmed</span></td>
                <td>
                  <button class="btn btn-outline btn-sm" onclick="showToast('Badge pass downloaded for <?php echo htmlspecialchars(addslashes($item['title'])); ?>', 'success')">Download Pass</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
