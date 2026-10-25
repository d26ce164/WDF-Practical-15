<?php
/**
 * Practical 11, 12, 14: Comprehensive Admin Panel
 * - RBAC enforcement (admin only)
 * - Dynamic metrics summary (users, students, events, registrations)
 * - Student CRUD (Practical 11) with search, filter, add, edit, delete
 * - Event CRUD with poster file upload & validation (Practical 12)
 * - System Audit Logs display (Practical 14)
 * - CSV export
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db.php';

require_admin();

$action = $_GET['action'] ?? 'dashboard';
$notice = '';
$error = '';

// --- Handle CSV Export for Students (Practical 11 extension) ---
if ($action === 'export_students' && $db_connected) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=studenthub_students_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Student ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Department', 'Semester', 'Gender', 'Status']);
    $res = $conn->query("SELECT id, student_id, first_name, last_name, email, phone, department, semester, gender, status FROM students WHERE status != 'deleted' ORDER BY id ASC");
    while ($row = $res->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// --- Handle Student CRUD Actions ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_action'])) {
    $st_action = $_POST['student_action'];
    if ($st_action === 'add' && $db_connected) {
        $sid = trim($_POST['student_id']);
        $fn = trim($_POST['first_name']);
        $ln = trim($_POST['last_name']);
        $em = trim($_POST['email']);
        $ph = trim($_POST['phone']);
        $dept = trim($_POST['department']);
        $sem = (int)$_POST['semester'];
        $gender = trim($_POST['gender']);

        $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name, email, phone, department, semester, gender, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("ssssssis", $sid, $fn, $ln, $em, $ph, $dept, $sem, $gender);
        if ($stmt->execute()) {
            $notice = "Student record {$sid} added successfully.";
            log_audit_action($conn, $_SESSION['user_id'], "STUDENT_ADD", "Added student {$sid} - {$fn} {$ln}");
        } else {
            $error = "Error adding student: " . $conn->error;
        }
        $stmt->close();
    } elseif ($st_action === 'update' && $db_connected) {
        $st_id = (int)($_POST['id'] ?? 0); $sid = trim($_POST['student_id'] ?? '');
        $fn = trim($_POST['first_name'] ?? ''); $ln = trim($_POST['last_name'] ?? '');
        $em = trim($_POST['email'] ?? ''); $ph = trim($_POST['phone'] ?? '');
        $dept = trim($_POST['department'] ?? ''); $sem = (int)($_POST['semester'] ?? 0); $gender = trim($_POST['gender'] ?? '');
        if ($st_id < 1 || $sid === '' || $fn === '' || $ln === '' || !filter_var($em, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10}$/', $ph) || $sem < 1 || $sem > 8 || !in_array($gender, ['Male','Female','Other'], true)) {
            $error = 'Please provide valid student details.';
        } else {
            $stmt = $conn->prepare("UPDATE students SET student_id=?, first_name=?, last_name=?, email=?, phone=?, department=?, semester=?, gender=? WHERE id=? AND status != 'deleted'");
            $stmt->bind_param('ssssssisi', $sid, $fn, $ln, $em, $ph, $dept, $sem, $gender, $st_id);
            if ($stmt->execute()) { $notice = 'Student record updated successfully.'; log_audit_action($conn, $_SESSION['user_id'], 'STUDENT_UPDATE', 'Updated student ID '.$st_id); }
            else { $error = 'Could not update student. Check that student ID and email are unique.'; }
            $stmt->close();
        }
    } elseif ($st_action === 'delete' && $db_connected) {
        $st_id = (int)($_POST['id'] ?? 0);
        $del_stmt = $conn->prepare("UPDATE students SET status = 'deleted' WHERE id = ? AND status != 'deleted'");
        $del_stmt->bind_param("i", $st_id);
        if ($del_stmt->execute() && $del_stmt->affected_rows > 0) { $notice = 'Student record marked as deleted.'; log_audit_action($conn, $_SESSION['user_id'], 'STUDENT_DELETE', "Soft deleted student ID {$st_id}"); }
        else { $error = 'Student record could not be deleted.'; }
        $del_stmt->close();
    }
}

// --- Handle Event Creation with Poster Upload (Practical 12) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_action']) && $_POST['event_action'] === 'add') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $desc = trim($_POST['description']);
    $ev_date = $_POST['event_date'];
    $ev_time = $_POST['event_time'];
    $venue = trim($_POST['venue']);
    $capacity = (int)$_POST['capacity'];
    $poster_filename = 'default_event.jpg';

    // File Upload Validation (MIME type & Size check)
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['poster']['tmp_name'];
        $file_name = $_FILES['poster']['name'];
        $file_size = $_FILES['poster']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        if (!in_array($file_ext, $allowed_exts) || !in_array($mime_type, $allowed_mimes)) {
            $error = "Invalid file type. Only JPG, PNG, and WEBP images are allowed.";
        } elseif ($file_size > 2 * 1024 * 1024) { // 2MB Max
            $error = "File size exceeds 2MB limit.";
        } else {
            // Sanitize file name with unique timestamp
            $poster_filename = 'event_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
            $upload_path = __DIR__ . '/assets/uploads/' . $poster_filename;
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Success
            } else {
                $error = "Failed to save uploaded poster file.";
            }
        }
    }

    if (empty($error) && $db_connected) {
        $stmt = $conn->prepare("INSERT INTO events (title, category, description, event_date, event_time, venue, poster_image, capacity, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssii", $title, $category, $desc, $ev_date, $ev_time, $venue, $poster_filename, $capacity, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $notice = "Event '{$title}' published successfully with poster.";
            log_audit_action($conn, $_SESSION['user_id'], "EVENT_CREATE", "Created event '{$title}' with poster {$poster_filename}");
        } else {
            $error = "Database error: " . $conn->error;
        }
        $stmt->close();
    }
}

// --- Fetch Aggregated Metrics for Practical 14 ---
$stats = ['students' => 20, 'events' => 5, 'registrations' => 7, 'users' => 3];
$student_list = [];
$event_list = [];
$audit_list = [];

if ($db_connected) {
    // Counts
    $res = $conn->query("SELECT COUNT(*) AS c FROM students WHERE status != 'deleted'");
    if ($res) $stats['students'] = $res->fetch_assoc()['c'];

    $res = $conn->query("SELECT COUNT(*) AS c FROM events");
    if ($res) $stats['events'] = $res->fetch_assoc()['c'];

    $res = $conn->query("SELECT COUNT(*) AS c FROM registrations");
    if ($res) $stats['registrations'] = $res->fetch_assoc()['c'];

    $res = $conn->query("SELECT COUNT(*) AS c FROM users");
    if ($res) $stats['users'] = $res->fetch_assoc()['c'];

    // Search and filter for students
    $search_query = trim($_GET['st_search'] ?? '');
    $filter_dept = trim($_GET['st_dept'] ?? '');

    $sql = "SELECT * FROM students WHERE status != 'deleted'";
    $params = [];
    $types = "";

    if (!empty($search_query)) {
        $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR student_id LIKE ? OR email LIKE ?)";
        $searchTerm = "%{$search_query}%";
        $params[] = $searchTerm; $params[] = $searchTerm; $params[] = $searchTerm; $params[] = $searchTerm;
        $types .= "ssss";
    }
    if (!empty($filter_dept) && $filter_dept !== 'all') {
        $sql .= " AND department = ?";
        $params[] = $filter_dept;
        $types .= "s";
    }
    $sql .= " ORDER BY id DESC LIMIT 50";

    $st_stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $st_stmt->bind_param($types, ...$params);
    }
    $st_stmt->execute();
    $student_list = $st_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $st_stmt->close();

    // Events
    $ev_res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
    if ($ev_res) $event_list = $ev_res->fetch_all(MYSQLI_ASSOC);

    // Audit Logs
    $aud_res = $conn->query("SELECT a.*, u.username FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 15");
    if ($aud_res) $audit_list = $aud_res->fetch_all(MYSQLI_ASSOC);
}

$edit_student = null;
if (isset($_GET['edit_student']) && $db_connected) {
    $edit_id = (int)$_GET['edit_student'];
    $edit_stmt = $conn->prepare("SELECT * FROM students WHERE id = ? AND status != 'deleted'");
    $edit_stmt->bind_param('i', $edit_id);
    $edit_stmt->execute();
    $edit_student = $edit_stmt->get_result()->fetch_assoc();
    $edit_stmt->close();
}

$page_title = "Admin Dashboard & Control Center";
require_once __DIR__ . '/includes/header.php';
?>

<section style="margin-bottom: 2rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
    <div>
      <span class="brand-badge" style="background: var(--secondary-color);">Practicals 11, 12 & 14</span>
      <h1 style="color: var(--primary-color); margin-top: 0.25rem;">Administrator Control Center</h1>
      <p style="color: var(--text-muted);">Manage students, events, uploaded assets, and track administrative audit logs.</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
      <a href="admin.php?action=export_students" class="btn btn-outline btn-sm">📥 Export Students CSV</a>
      <button class="btn btn-primary btn-sm" onclick="openModal('addStudentModal')">➕ Add Student</button>
      <button class="btn btn-secondary btn-sm" onclick="openModal('addEventModal')">📅 Post Event</button>
    </div>
  </div>

  <?php if ($notice): ?><div class="alert alert-success"><span>✅ <?php echo htmlspecialchars($notice); ?></span></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><span>⚠️ <?php echo htmlspecialchars($error); ?></span></div><?php endif; ?>

  <!-- Aggregated Metric Cards (Practical 14) -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="card" style="border-top: 4px solid var(--primary-color);">
      <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">Total Students</div>
      <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color); margin-top: 0.25rem;"><?php echo $stats['students']; ?></div>
      <div style="font-size: 0.8rem; color: var(--accent-color);">Active in registry</div>
    </div>
    <div class="card" style="border-top: 4px solid var(--secondary-color);">
      <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">Campus Events</div>
      <div style="font-size: 2rem; font-weight: 700; color: var(--secondary-color); margin-top: 0.25rem;"><?php echo $stats['events']; ?></div>
      <div style="font-size: 0.8rem; color: var(--text-muted);">Upcoming & scheduled</div>
    </div>
    <div class="card" style="border-top: 4px solid var(--accent-color);">
      <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">Registrations</div>
      <div style="font-size: 2rem; font-weight: 700; color: var(--accent-color); margin-top: 0.25rem;"><?php echo $stats['registrations']; ?></div>
      <div style="font-size: 0.8rem; color: var(--accent-color);">Total participations</div>
    </div>
    <div class="card" style="border-top: 4px solid var(--info-color);">
      <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600;">System Users</div>
      <div style="font-size: 2rem; font-weight: 700; color: var(--info-color); margin-top: 0.25rem;"><?php echo $stats['users']; ?></div>
      <div style="font-size: 0.8rem; color: var(--text-muted);">Admin & Student roles</div>
    </div>
  </div>

  <!-- Student Management Section (Practical 11) -->
  <div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
      <h2 style="font-size: 1.3rem; color: var(--primary-color);">Student Directory (CRUD, Search & Filter)</h2>
      
      <!-- Search & Filter Form -->
      <form method="GET" action="admin.php" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <input type="text" name="st_search" class="form-control" style="width: 200px; padding: 0.4rem 0.75rem;" placeholder="Search name/ID..." value="<?php echo htmlspecialchars($_GET['st_search'] ?? ''); ?>">
        <select name="st_dept" class="form-control" style="width: 170px; padding: 0.4rem 0.75rem;">
          <option value="all">All Depts</option>
          <option value="Information Technology" <?php if(($_GET['st_dept'] ?? '') === 'Information Technology') echo 'selected'; ?>>Information Technology</option>
          <option value="Computer Engineering" <?php if(($_GET['st_dept'] ?? '') === 'Computer Engineering') echo 'selected'; ?>>Computer Engineering</option>
          <option value="Computer Science" <?php if(($_GET['st_dept'] ?? '') === 'Computer Science') echo 'selected'; ?>>Computer Science</option>
          <option value="AI & ML" <?php if(($_GET['st_dept'] ?? '') === 'AI & ML') echo 'selected'; ?>>AI & ML</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <?php if (!empty($_GET['st_search']) || !empty($_GET['st_dept'])): ?>
          <a href="admin.php" class="btn btn-outline btn-sm">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Semester</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($student_list)): ?>
            <tr><td colspan="7" style="text-align:center; color:var(--text-muted);">No student records found.</td></tr>
          <?php else: ?>
            <?php foreach ($student_list as $st): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($st['student_id']); ?></strong></td>
                <td><?php echo htmlspecialchars($st['first_name'] . ' ' . $st['last_name']); ?></td>
                <td><?php echo htmlspecialchars($st['email']); ?></td>
                <td><?php echo htmlspecialchars($st['phone']); ?></td>
                <td><span class="brand-badge" style="background: rgba(15,76,129,0.15); color: var(--primary-color);"><?php echo htmlspecialchars($st['department']); ?></span></td>
                <td>Sem <?php echo htmlspecialchars($st['semester']); ?></td>
                <td>
                  <a class="btn btn-outline btn-sm" style="padding:2px 8px;font-size:.78rem;" href="admin.php?edit_student=<?php echo (int)$st['id']; ?>">Edit</a>
                  <form method="POST" action="admin.php" onsubmit="return confirm('Confirm deleting student <?php echo htmlspecialchars($st['student_id']); ?>?');" style="display:inline;">
                    <input type="hidden" name="student_action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $st['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 2px 8px; font-size: 0.78rem;">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Campus Events Management (Practical 12) -->
  <div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h2 style="font-size: 1.3rem; color: var(--primary-color);">Campus Events (With Poster Validation)</h2>
      <button class="btn btn-secondary btn-sm" onclick="openModal('addEventModal')">➕ New Event</button>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Poster</th>
            <th>Title</th>
            <th>Category</th>
            <th>Date & Time</th>
            <th>Venue</th>
            <th>Capacity</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($event_list as $ev): ?>
            <tr>
              <td>
                <div style="width: 64px; height: 48px; border-radius: 4px; background: #e2e8f0; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                  <img src="<?php echo htmlspecialchars((!empty($ev['poster_image']) && file_exists(__DIR__ . '/assets/uploads/' . basename($ev['poster_image'])) ? 'assets/uploads/' . basename($ev['poster_image']) : 'assets/images/default_event.jpg')); ?>" alt="Event poster" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.src='assets/images/default_event.jpg';">
                </div>
              </td>
              <td><strong><?php echo htmlspecialchars($ev['title']); ?></strong></td>
              <td><span class="brand-badge"><?php echo htmlspecialchars($ev['category']); ?></span></td>
              <td><?php echo htmlspecialchars($ev['event_date']); ?> @ <?php echo htmlspecialchars($ev['event_time']); ?></td>
              <td><?php echo htmlspecialchars($ev['venue']); ?></td>
              <td><?php echo htmlspecialchars($ev['capacity']); ?></td>
              <td><span style="color: var(--accent-color); font-weight:600;"><?php echo htmlspecialchars($ev['status']); ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Audit Logs (Practical 14) -->
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h2 style="font-size: 1.3rem; color: var(--primary-color);">System Audit Logs (Traceability)</h2>
      <span style="font-size: 0.85rem; color: var(--text-muted);">Real-time Action History</span>
    </div>

    <div class="table-responsive">
      <table class="table" style="font-size: 0.88rem;">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>User</th>
            <th>Action Code</th>
            <th>Details</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($audit_list as $log): ?>
            <tr>
              <td><?php echo htmlspecialchars($log['created_at']); ?></td>
              <td><strong><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></strong></td>
              <td><code><?php echo htmlspecialchars($log['action']); ?></code></td>
              <td><?php echo htmlspecialchars($log['details']); ?></td>
              <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- Edit Student Modal -->
<?php if ($edit_student): ?>
<div id="editStudentModal" class="modal-backdrop" style="display:flex;">
 <div class="modal-content" style="max-width:600px; max-height:90vh; overflow:auto;">
  <h3 style="color:var(--primary-color);margin-bottom:1rem;">Edit Student Record</h3>
  <form method="POST" action="admin.php">
   <input type="hidden" name="student_action" value="update"><input type="hidden" name="id" value="<?php echo (int)$edit_student['id']; ?>">
   <div class="form-group"><label class="form-label">Student ID</label><input class="form-control" name="student_id" required value="<?php echo htmlspecialchars($edit_student['student_id']); ?>"></div>
   <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
    <div class="form-group"><label class="form-label">First Name</label><input class="form-control" name="first_name" required value="<?php echo htmlspecialchars($edit_student['first_name']); ?>"></div>
    <div class="form-group"><label class="form-label">Last Name</label><input class="form-control" name="last_name" required value="<?php echo htmlspecialchars($edit_student['last_name']); ?>"></div>
   </div>
   <div class="form-group"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required value="<?php echo htmlspecialchars($edit_student['email']); ?>"></div>
   <div class="form-group"><label class="form-label">Phone (10 digits)</label><input class="form-control" name="phone" required pattern="[0-9]{10}" value="<?php echo htmlspecialchars($edit_student['phone']); ?>"></div>
   <div class="form-group"><label class="form-label">Department</label><select class="form-control" name="department"><option>Information Technology</option><option>Computer Engineering</option><option>Computer Science</option><option>AI &amp; ML</option></select></div>
   <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
    <div class="form-group"><label class="form-label">Semester</label><input type="number" min="1" max="8" class="form-control" name="semester" required value="<?php echo (int)$edit_student['semester']; ?>"></div>
    <div class="form-group"><label class="form-label">Gender</label><select class="form-control" name="gender"><option <?php echo $edit_student['gender']==='Male'?'selected':''; ?>>Male</option><option <?php echo $edit_student['gender']==='Female'?'selected':''; ?>>Female</option><option <?php echo $edit_student['gender']==='Other'?'selected':''; ?>>Other</option></select></div>
   </div>
   <div style="display:flex;justify-content:flex-end;gap:.75rem;"><a class="btn btn-outline" href="admin.php">Cancel</a><button class="btn btn-primary" type="submit">Save Changes</button></div>
  </form>
 </div>
</div>
<?php endif; ?>

<!-- Add Student Modal (Practical 11) -->
<div id="addStudentModal" class="modal-backdrop">
  <div class="modal-content" style="max-width: 600px;">
    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Add New Student Record</h3>
    <form method="POST" action="admin.php">
      <input type="hidden" name="student_action" value="add">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="new_sid">Student ID *</label>
          <input type="text" id="new_sid" name="student_id" class="form-control" required placeholder="23IT021">
        </div>
        <div class="form-group">
          <label class="form-label" for="new_dept">Department *</label>
          <select id="new_dept" name="department" class="form-control" required>
            <option value="Information Technology">Information Technology</option>
            <option value="Computer Engineering">Computer Engineering</option>
            <option value="Computer Science">Computer Science</option>
            <option value="AI & ML">AI & ML</option>
          </select>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="new_fn">First Name *</label>
          <input type="text" id="new_fn" name="first_name" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="new_ln">Last Name *</label>
          <input type="text" id="new_ln" name="last_name" class="form-control" required>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="new_em">Email Address *</label>
          <input type="email" id="new_em" name="email" class="form-control" required placeholder="id@charusat.edu.in">
        </div>
        <div class="form-group">
          <label class="form-label" for="new_ph">Phone (10 Digits) *</label>
          <input type="tel" id="new_ph" name="phone" class="form-control" required pattern="[0-9]{10}">
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="new_sem">Semester</label>
          <select id="new_sem" name="semester" class="form-control">
            <option value="1">Sem 1</option>
            <option value="2">Sem 2</option>
            <option value="3" selected>Sem 3</option>
            <option value="4">Sem 4</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="new_gender">Gender</label>
          <select id="new_gender" name="gender" class="form-control">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="button" class="btn btn-outline" onclick="closeModal('addStudentModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Student</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Event Modal with Poster Upload (Practical 12) -->
<div id="addEventModal" class="modal-backdrop">
  <div class="modal-content" style="max-width: 600px;">
    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Post New Campus Event</h3>
    <form method="POST" action="admin.php" enctype="multipart/form-data">
      <input type="hidden" name="event_action" value="add">
      <div class="form-group">
        <label class="form-label" for="ev_title">Event Title *</label>
        <input type="text" id="ev_title" name="title" class="form-control" required placeholder="e.g. Web Development Hackathon">
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="ev_cat">Category *</label>
          <select id="ev_cat" name="category" class="form-control" required>
            <option value="Festival">Festival</option>
            <option value="Workshop">Workshop</option>
            <option value="Hackathon">Hackathon</option>
            <option value="Seminar">Seminar</option>
            <option value="Competition">Competition</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="ev_cap">Capacity *</label>
          <input type="number" id="ev_cap" name="capacity" class="form-control" value="100" min="10" required>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="ev_date">Event Date *</label>
          <input type="date" id="ev_date" name="event_date" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="ev_time">Event Time *</label>
          <input type="time" id="ev_time" name="event_time" class="form-control" required value="10:00">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="ev_venue">Venue Location *</label>
        <input type="text" id="ev_venue" name="venue" class="form-control" required placeholder="Auditorium or Lab Name">
      </div>
      <div class="form-group">
        <label class="form-label" for="ev_desc">Description *</label>
        <textarea id="ev_desc" name="description" class="form-control" rows="3" required placeholder="Event description, rules, and prerequisites..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label" for="ev_poster">Upload Event Poster (JPG, PNG, WEBP max 2MB) *</label>
        <input type="file" id="ev_poster" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
        <button type="button" class="btn btn-outline" onclick="closeModal('addEventModal')">Cancel</button>
        <button type="submit" class="btn btn-secondary">Publish Event</button>
      </div>
    </form>
  </div>
</div>

<?php if ($edit_student): ?><script>document.querySelector('#editStudentModal select[name=department]').value = <?php echo json_encode($edit_student['department']); ?>;</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
