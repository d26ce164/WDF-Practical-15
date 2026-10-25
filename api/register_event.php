<?php
require_once __DIR__ . '/../includes/auth_guard.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (!is_logged_in()) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Please log in before registering for an event.']); exit; }
if (!is_student()) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Only student accounts can register for events.']); exit; }
require_once __DIR__ . '/../includes/db.php';
if (!$db_connected) { http_response_code(503); echo json_encode(['success'=>false,'message'=>'The database is unavailable. Please try again later.']); exit; }
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$eventId = filter_var($input['event_id'] ?? null, FILTER_VALIDATE_INT);
if (isset($input['email']) && strcasecmp(trim((string)$input['email']), (string)($_SESSION['email'] ?? '')) !== 0) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Use the email address associated with your signed-in account.']); exit; }
if (isset($input['name']) && strcasecmp(trim((string)$input['name']), (string)($_SESSION['full_name'] ?? '')) !== 0) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Use the full name associated with your signed-in account.']); exit; }
if (!$eventId || $eventId < 1) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'A valid event is required.']); exit; }
try {
  $studentStmt = $conn->prepare('SELECT id FROM students WHERE user_id = ? OR email = ? LIMIT 1');
  $studentStmt->bind_param('is', $_SESSION['user_id'], $_SESSION['email']);
  $studentStmt->execute(); $student = $studentStmt->get_result()->fetch_assoc(); $studentStmt->close();
  if (!$student) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'Your account has no student profile. Contact the administrator.']); exit; }
  $eventStmt = $conn->prepare("SELECT id, title, capacity, status, event_date, (SELECT COUNT(*) FROM registrations r WHERE r.event_id = events.id AND r.status IN ('confirmed','waitlisted')) AS registered_count FROM events WHERE id = ? LIMIT 1");
  $eventStmt->bind_param('i', $eventId); $eventStmt->execute(); $event = $eventStmt->get_result()->fetch_assoc(); $eventStmt->close();
  if (!$event || $event['status'] !== 'upcoming' || $event['event_date'] < date('Y-m-d')) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'This event is unavailable for registration.']); exit; }
  if ((int)$event['registered_count'] >= (int)$event['capacity']) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'This event is full.']); exit; }
  $stmt = $conn->prepare("INSERT INTO registrations (student_id, event_id, status) VALUES (?, ?, 'confirmed')");
  $sid=(int)$student['id']; $stmt->bind_param('ii',$sid,$eventId);
  if (!$stmt->execute()) {
    if ($stmt->errno === 1062) { http_response_code(409); echo json_encode(['success'=>false,'message'=>'You are already registered for this event.']); }
    else { throw new RuntimeException('Could not save registration.'); }
    $stmt->close(); exit;
  }
  $stmt->close();
  log_audit_action($conn, (int)$_SESSION['user_id'], 'EVENT_REGISTER', 'Registered for event ID '.$eventId.' ('.$event['title'].')');
  echo json_encode(['success'=>true,'message'=>'Registration confirmed for '.$event['title'].'. It is now saved to your dashboard.']);
} catch (Throwable $e) { error_log('Event registration error: '.$e->getMessage()); http_response_code(500); echo json_encode(['success'=>false,'message'=>'Registration could not be completed. Please try again.']); }
?>