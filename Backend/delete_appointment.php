<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

include 'db_connect.php';

try {
    // Get the appointment ID from the POST request
    $appointmentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($appointmentId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid appointment ID']);
        exit;
    }

    // Delete the appointment from the database
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);

    // Check if deletion was successful
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Appointment not found']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>