<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit(); }

// Include the database connection file
include 'db_connect.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

// Get the notification ID from the request
$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if (!$notification_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
    exit;
}

// Update the notification to mark as read
try {
    $query = "UPDATE notifications SET is_read = 1 WHERE id = :notification_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['notification_id' => $notification_id]);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read: ' . $e->getMessage()]);
}
?>