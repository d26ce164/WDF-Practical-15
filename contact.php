<?php
$page_title = "Contact Us & Campus Inquiry";
require_once __DIR__ . '/includes/header.php';

$success_msg = '';
$errors = [];

// Practical 7: PHP Form Processing with Server-Side Validation & CSV/JSON Storage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitization
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $subject = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS));
    $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS));

    // Validation
    if (empty($name) || strlen($name) < 2) {
        $errors['name'] = "Name must be at least 2 characters.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please provide a valid university or personal email address.";
    }
    if (empty($subject)) {
        $errors['subject'] = "Subject line is required.";
    }
    if (empty($message) || strlen($message) < 10) {
        $errors['message'] = "Message must be at least 10 characters long.";
    }

    if (empty($errors)) {
        $record = [
            'timestamp' => date('Y-m-d H:i:s'),
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        // 1. Save to JSON file
        $json_file = __DIR__ . '/data/contact_submissions.json';
        $existing = [];
        if (file_exists($json_file)) {
            $existing = json_decode(file_get_contents($json_file), true) ?: [];
        }
        $existing[] = $record;
        file_put_contents($json_file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // 2. Save to CSV file
        $csv_file = __DIR__ . '/data/contact_submissions.csv';
        $is_new_csv = !file_exists($csv_file);
        $fp = fopen($csv_file, 'a');
        if ($is_new_csv) {
            fputcsv($fp, ['Timestamp', 'Name', 'Email', 'Subject', 'Message', 'IP']);
        }
        fputcsv($fp, [$record['timestamp'], $record['name'], $record['email'], $record['subject'], $record['message'], $record['ip']]);
        fclose($fp);

        $success_msg = "Thank you, {$name}! Your message has been safely received and stored.";
    }
}
?>

<section style="margin-bottom: 2rem;">
  <div style="max-width: 800px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 2rem;">
      <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">Contact StudentHub Support</h1>
      <p style="color: var(--text-muted);">Have questions or technical issues? Reach out directly to the course lab faculty and support team.</p>
      <span class="brand-badge" style="margin-top: 0.5rem; display: inline-block;">Practical 7: Server-side PHP Processing & File Storage</span>
    </div>

    <?php if ($success_msg): ?>
      <div class="alert alert-success">
        <span>✅ <?php echo htmlspecialchars($success_msg); ?></span>
      </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
      <!-- Contact Info Card -->
      <div class="card" style="height: fit-content;">
        <h3 style="color: var(--primary-color); margin-bottom: 1rem;">Campus Department</h3>
        <p style="font-size: 0.95rem; line-height: 1.8; color: var(--text-muted); margin-bottom: 1.5rem;">
          Faculty of Technology & Engineering (FTE)<br>
          Chandubhai S Patel Institute of Technology (CSPIT)<br>
          Devang Patel Institute of Advance Technology and Research (DEPSTAR)<br>
          CHARUSAT Campus, Changa - 388421
        </p>
        <div style="font-size: 0.9rem; line-height: 2;">
          <div>📞 <strong>Phone:</strong> +91 2697 265011 / 265021</div>
          <div>✉️ <strong>Email:</strong> info@charusat.edu.in</div>
          <div>⏰ <strong>Lab Hours:</strong> Monday - Saturday, 9:00 AM - 4:45 PM</div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="card">
        <form method="POST" action="contact.php" novalidate>
          <div class="form-group">
            <label class="form-label" for="contact-name">Full Name *</label>
            <input type="text" id="contact-name" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            <?php if (isset($errors['name'])): ?><span class="error-text"><?php echo $errors['name']; ?></span><?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label" for="contact-email">Email Address *</label>
            <input type="email" id="contact-email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <?php if (isset($errors['email'])): ?><span class="error-text"><?php echo $errors['email']; ?></span><?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label" for="contact-subject">Subject *</label>
            <input type="text" id="contact-subject" name="subject" class="form-control" required value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
            <?php if (isset($errors['subject'])): ?><span class="error-text"><?php echo $errors['subject']; ?></span><?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label" for="contact-message">Message *</label>
            <textarea id="contact-message" name="message" class="form-control" rows="4" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
            <?php if (isset($errors['message'])): ?><span class="error-text"><?php echo $errors['message']; ?></span><?php endif; ?>
          </div>

          <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
