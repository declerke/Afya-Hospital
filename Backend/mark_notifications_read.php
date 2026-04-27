<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
include 'db_connect.php';

$response = ["status" => "error", "message" => ""];

try {
    // Check if notification_id is provided
    if (!isset($_POST['notification_id']) || empty($_POST['notification_id'])) {
        throw new Exception("Notification ID not provided.");
    }

    $notification_id = intval($_POST['notification_id']);

    // Update the specific notification to mark it as read
    $sql = "UPDATE admin_notifications SET is_read = 1 WHERE id = ? AND is_read = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$notification_id]);

    // Check if any rows were affected (i.e., the notification was unread and updated)
    if ($stmt->rowCount() > 0) {
        $response["status"] = "success";
        $response["message"] = "Notification marked as read.";
    } else {
        $response["message"] = "Notification already read or not found.";
    }
} catch (Exception $e) {
    $response["message"] = "Error: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>