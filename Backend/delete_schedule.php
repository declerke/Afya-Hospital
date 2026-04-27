<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
// Include the database connection
require_once 'db_connect.php';

// Get the schedule ID from the POST request
$scheduleId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($scheduleId <= 0) {
    echo json_encode(['error' => 'Invalid schedule ID']);
    exit;
}

// Delete the schedule
try {
    $stmt = $pdo->prepare("DELETE FROM doctor_schedule WHERE id = :id");
    $stmt->bindValue(':id', $scheduleId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Schedule not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to delete schedule: ' . $e->getMessage()]);
}
exit;