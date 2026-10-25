<?php
/**
 * Practical 13: Asynchronous PHP RESTful JSON API
 * Handles GET, POST, PUT, DELETE for events and students
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];
if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
    if (!is_logged_in()) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Login required.']); exit(); }
    if (!is_admin()) { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Administrator access required.']); exit(); }
}

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Fallback to static JSON if DB is unavailable
function fallbackEvents() {
    $file = __DIR__ . '/../data/events.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return [];
}

try {
    if (!$db_connected) {
        if ($method === 'GET') { echo json_encode(fallbackEvents()); exit(); }
        http_response_code(503); echo json_encode(['success'=>false,'message'=>'Database unavailable; this operation requires MySQL.']); exit();
    }

    if ($method === 'GET') {
        $search = trim($_GET['search'] ?? '');
        $category = trim($_GET['category'] ?? '');

        $sql = "SELECT id, title, category, description, event_date, event_time, venue, poster_image, capacity, status FROM events WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR description LIKE ? OR venue LIKE ?)";
            $s = "%$search%";
            $params[] = $s; $params[] = $s; $params[] = $s;
            $types .= "sss";
        }
        if (!empty($category) && $category !== 'all') {
            $sql .= " AND category = ?";
            $params[] = $category;
            $types .= "s";
        }
        $sql .= " ORDER BY event_date ASC";

        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $events = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        echo json_encode($events);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $title = trim($input['title'] ?? '');
        $category = trim($input['category'] ?? 'General');
        $desc = trim($input['description'] ?? '');
        $date = trim($input['event_date'] ?? date('Y-m-d'));
        $time = trim($input['event_time'] ?? '10:00:00');
        $venue = trim($input['venue'] ?? 'Campus Auditorium');
        $capacity = (int)($input['capacity'] ?? 100);

        if (empty($title)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Event title is required.']);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO events (title, category, description, event_date, event_time, venue, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $title, $category, $desc, $date, $time, $venue, $capacity);
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Event created successfully via REST API.',
                'event_id' => $stmt->insert_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        $stmt->close();
    } elseif ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
        $title = trim($input['title'] ?? '');
        $category = trim($input['category'] ?? 'General');
        $desc = trim($input['description'] ?? '');
        $date = trim($input['event_date'] ?? '');
        $time = trim($input['event_time'] ?? '10:00:00');
        $venue = trim($input['venue'] ?? 'Campus Auditorium');
        $capacity = filter_var($input['capacity'] ?? 100, FILTER_VALIDATE_INT);
        $status = trim($input['status'] ?? 'upcoming');
        if (!$id || $id < 1 || $title === '' || !preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $date) || !preg_match('/^\\d{2}:\\d{2}(:\\d{2})?$/', $time) || $capacity === false || $capacity < 1 || !in_array($status, ['upcoming','completed','cancelled'], true)) {
            http_response_code(400); echo json_encode(['success'=>false,'message'=>'Valid ID, title, date, time, capacity and status are required.']); exit;
        }
        $stmt = $conn->prepare('UPDATE events SET title=?, category=?, description=?, event_date=?, event_time=?, venue=?, capacity=?, status=? WHERE id=?');
        // Six strings, integer capacity, string status, integer ID.
        $stmt->bind_param('ssssssisi', $title, $category, $desc, $date, $time, $venue, $capacity, $status, $id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows === 0 && $stmt->errno) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Event update failed.']); }
            else echo json_encode(['success'=>true,'message'=>'Event updated successfully.']);
        } else { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Event update failed.']); }
        $stmt->close();
    } elseif ($method === 'DELETE') {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid ID parameter is required.']);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => "Event {$id} deleted."]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        $stmt->close();
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
