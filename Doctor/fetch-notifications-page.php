<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

$doctorId = $_SESSION['doctor_id'] ?? null;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$perPage = isset($_POST['per_page']) ? (int)$_POST['per_page'] : 10;
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$showAll = isset($_POST['show_all']) && $_POST['show_all'] == 1;

if (!$doctorId) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Build the query
    $query = "SELECT id, message, notification_type, is_read, created_at 
              FROM notifications 
              WHERE recipient_type = 'Doctor' AND recipient_id = :doctor_id";
    
    // Add search filter
    if (!empty($search)) {
        $query .= " AND (message LIKE :search OR notification_type LIKE :search)";
    }

    // Add is_read filter if not showing all
    if (!$showAll) {
        $query .= " AND is_read = 0";
    }

    // Count total notifications for pagination
    $countStmt = $pdo->prepare($query);
    $countStmt->bindValue(':doctor_id', $doctorId, PDO::PARAM_INT);
    if (!empty($search)) {
        $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalNotifications = $countStmt->rowCount();
    $totalPages = ceil($totalNotifications / $perPage);

    // Fetch notifications for the current page
    $query .= " ORDER BY created_at DESC LIMIT :offset, :per_page";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':doctor_id', $doctorId, PDO::PARAM_INT);
    if (!empty($search)) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
    $stmt->execute();

    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'notifications' => $notifications,
        'total_pages' => $totalPages
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching notifications: ' . $e->getMessage()]);
}
?>