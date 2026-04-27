<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }


include 'db_connect.php'; 

try {
    // Query to count doctors
    $stmt = $pdo->prepare("SELECT COUNT(*) AS doctor_count FROM doctors");
    $stmt->execute();
    $doctor = $stmt->fetch();

    // Query to count nurses
    $stmt = $pdo->prepare("SELECT COUNT(*) AS nurse_count FROM nurses");
    $stmt->execute();
    $nurse = $stmt->fetch();

    // Query to count patients
    $stmt = $pdo->prepare("SELECT COUNT(*) AS patient_count FROM patients");
    $stmt->execute();
    $patient = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT COUNT(*) AS staff_count FROM hospital_staffs");
    $stmt->execute();
    $staff = $stmt->fetch();

    
    echo json_encode([
        'doctor_count' => $doctor['doctor_count'],
        'nurse_count' => $nurse['nurse_count'],
        'patient_count' => $patient['patient_count'],
        'staff_count' => $staff['staff_count']
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
}
?>
