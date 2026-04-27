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
    // Get the patient_id from the GET request
    $patientId = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

    if ($patientId <= 0) {
        throw new Exception("Invalid patient ID.");
    }

    // Fetch patient data
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, date_of_birth, gender, contact_number, email, address, insurance FROM patients WHERE id = :patient_id");
    $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        throw new Exception("Patient not found.");
    }

    // Format date_of_birth for display (from YYYY-MM-DD to DD/MM/YYYY)
    if ($patient['date_of_birth']) {
        $patient['date_of_birth'] = date('d/m/Y', strtotime($patient['date_of_birth']));
    }

    $response = [
        'success' => true,
        'patient' => $patient
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>