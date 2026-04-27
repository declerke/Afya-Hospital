<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
// Enable error reporting for debugging// Include the database connection
try {
    require_once 'db_connect.php';
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the notification ID from the POST data
$notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;

if ($notification_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
    exit;
}

try {
    // Prepare and execute the update query
    $query = "UPDATE admin_notifications SET is_read = 1 WHERE id = :notification_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':notification_id', $notification_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->rowCount() > 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Notification not found']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
    exit;
}
exit;