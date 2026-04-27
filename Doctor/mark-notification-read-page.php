<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

$doctorId = $_SESSION['doctor_id'] ?? null;
$notificationId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : null;

if (!$doctorId || !$notificationId) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized or invalid notification ID']);
    exit();
}

try {
    // Update the notification's is_read status
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE id = :notification_id AND recipient_type = 'Doctor' AND recipient_id = :doctor_id
    ");
    $stmt->execute([
        ':notification_id' => $notificationId,
        ':doctor_id' => $doctorId
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Notification not found or already marked as read']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error marking notification as read: ' . $e->getMessage()]);
}
?>