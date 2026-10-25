<?php
$page_title = "Student Feedback & Course Rating";
require_once __DIR__ . '/includes/header.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim(filter_input(INPUT_POST, 'student_name', FILTER_SANITIZE_SPECIAL_CHARS));
    $course_code = trim(filter_input(INPUT_POST, 'course_code', FILTER_SANITIZE_SPECIAL_CHARS));
    $rating = (int)($_POST['rating'] ?? 5);
    $comments = trim(filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_SPECIAL_CHARS));

    if (!empty($student_name) && !empty($course_code)) {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'student_name' => $student_name,
            'course' => $course_code,
            'rating' => $rating,
            'comments' => $comments
        ];
        $file = __DIR__ . '/data/feedback_submissions.json';
        $all = file_exists($file) ? json_decode(file_get_contents($file), true) ?: [] : [];
        $all[] = $entry;
        file_put_contents($file, json_encode($all, JSON_PRETTY_PRINT));
        $msg = "Thank you! Your feedback has been recorded.";
    }
}
?>

<section style="margin-bottom: 2rem;">
  <div style="max-width: 650px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 2rem;">
      <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">Academic Course Feedback</h1>
      <p style="color: var(--text-muted);">Share your feedback on laboratory experiments, framework tutorials, and learning outcomes.</p>
    </div>

    <?php if ($msg): ?>
      <div class="alert alert-success"><span>✅ <?php echo htmlspecialchars($msg); ?></span></div>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="feedback.php">
        <div class="form-group">
          <label class="form-label" for="student_name">Your Name / Student ID</label>
          <input type="text" id="student_name" name="student_name" class="form-control" required placeholder="e.g., 23IT001 or Divyakumar">
        </div>

        <div class="form-group">
          <label class="form-label" for="course_code">Subject / Framework Module</label>
          <select id="course_code" name="course_code" class="form-control" required>
            <option value="ITUE203 - Web Development Frameworks">ITUE203 - Web Development Frameworks</option>
            <option value="HTML5 & Accessibility Practical">HTML5 & Accessibility Practical</option>
            <option value="JavaScript DOM & Event Handling">JavaScript DOM & Event Handling</option>
            <option value="PHP Server-Side & File Storage">PHP Server-Side & File Storage</option>
            <option value="MySQL Database & PDO/MySQLi">MySQL Database & PDO/MySQLi</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Overall Experience Rating</label>
          <div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.5rem;">
            <label><input type="radio" name="rating" value="5" checked> ⭐⭐⭐⭐⭐ (Excellent)</label>
            <label><input type="radio" name="rating" value="4"> ⭐⭐⭐⭐ (Very Good)</label>
            <label><input type="radio" name="rating" value="3"> ⭐⭐⭐ (Good)</label>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="comments">Suggestions & Comments</label>
          <textarea id="comments" name="comments" class="form-control" rows="4" placeholder="What did you learn? Any areas for improvement?"></textarea>
        </div>

        <button type="submit" class="btn btn-secondary" style="width: 100%;">Submit Feedback</button>
      </form>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
