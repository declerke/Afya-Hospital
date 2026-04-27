<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
include 'db_connect.php';

$doctor_id = (int)$_SESSION['doctor_id'];

try {
    $stmt = $pdo->prepare("
        SELECT id, message, created_at
        FROM notifications
        WHERE recipient_type = 'Doctor' AND recipient_id = ? AND is_read = 0
        ORDER BY created_at DESC
    ");
    $stmt->execute([$doctor_id]);
    $notifications = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'notifications' => $notifications]);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to fetch notifications.']);
}
?>
