<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

include 'db_connect.php';

try {
    // Fetch unread notifications
    $sql = "SELECT id, message, created_at FROM admin_notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5";
    $stmt = $pdo->query($sql);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get unread notification count
    $countSql = "SELECT COUNT(*) AS unread_count FROM admin_notifications WHERE is_read = 0";
    $countStmt = $pdo->query($countSql);
    $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);

    
    $response = [
        'notifications' => $notifications,
        'unread_count' => $countRow['unread_count'] ?? 0
    ];

    
    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>

