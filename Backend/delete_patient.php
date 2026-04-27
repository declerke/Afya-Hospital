<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
include 'db_connect.php'; 

header('Content-Type: application/json');

try {
    if (!isset($_POST['id'])) {
        throw new Exception('Patient ID is required');
    }

    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$id]);

    $response = ['success' => true, 'message' => 'Patient deleted successfully'];
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?>