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
    // Check if doctor_id is provided
    if (!isset($_POST['doctor_id']) || empty($_POST['doctor_id'])) {
        throw new Exception("Doctor ID is required.");
    }

    $doctorId = (int)$_POST['doctor_id'];

    // Delete from doctors table
    $stmtDoctor = $pdo->prepare("DELETE FROM doctors WHERE id = :id");
    $stmtDoctor->bindValue(':id', $doctorId, PDO::PARAM_INT);
    $stmtDoctor->execute();

    // Check if the doctor was actually deleted
    if ($stmtDoctor->rowCount() === 0) {
        throw new Exception("Doctor not found.");
    }

    
    $response = [
        'success' => true,
        'message' => 'Doctor deleted successfully.',
        'doctor_id' => $doctorId
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Error deleting doctor: " . $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>