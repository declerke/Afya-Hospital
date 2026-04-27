<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }


include 'db_connect.php';

try {
    // Enable error reporting for debugging (remove in production)    // Fetch dropdown options for patients
    $patientsStmt = $pdo->query("
        SELECT id, first_name, last_name
        FROM patients
        ORDER BY last_name, first_name
    ");
    $patients = $patientsStmt->fetchAll();

    // Fetch dropdown options for doctors
    $doctorsStmt = $pdo->query("
        SELECT id, first_name, last_name, department
        FROM doctors
        ORDER BY last_name, first_name
    ");
    $doctors = $doctorsStmt->fetchAll();

    // Fetch unique department options from doctors
    $departmentsStmt = $pdo->query("
        SELECT DISTINCT department
        FROM doctors
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ");
    $departments = $departmentsStmt->fetchAll(PDO::FETCH_COLUMN);

    // Combine data into response
    $response = [
        'patients' => $patients,
        'departments' => $departments,
        'doctors' => $doctors
    ];

    
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    error_log("PDOException in fetch_add_form_data.php: " . $e->getMessage()); // Log database errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("General Exception in fetch_add_form_data.php: " . $e->getMessage()); // Log general errors
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>