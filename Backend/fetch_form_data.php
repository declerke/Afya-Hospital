<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }


include 'db_connect.php';

try {
    // Enable error reporting for debugging (remove in production)    // Get appointment ID from URL parameter and debug it
    $appointmentId = isset($_GET['id']) ? trim($_GET['id']) : null;
    
    // Log the entire request for debugging (remove in production)
    // Validate and convert to integer, ensuring it's positive
    if ($appointmentId === null || !is_numeric($appointmentId) || (int)$appointmentId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid appointment ID: ' . ($appointmentId ?? 'null')]);
        exit;
    }

    $appointmentId = (int)$appointmentId;

    // Log the appointment ID being queried
    // Fetch appointment data with related patient and doctor info
    $stmt = $pdo->prepare("
        SELECT 
            a.id AS appointment_id,
            a.patient_id,
            a.doctor_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            p.first_name AS patient_first_name,
            p.last_name AS patient_last_name,
            d.department,
            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name
        FROM appointments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        WHERE a.id = ?
    ");
    $stmt->execute([$appointmentId]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        http_response_code(404);
        echo json_encode(['error' => 'Appointment not found for ID: ' . $appointmentId]);
        exit;
    }

    // Log successful appointment fetch
    // Fetch dropdown options for patients (unchanged from Step 2)
    $patientsStmt = $pdo->query("
        SELECT id, first_name, last_name
        FROM patients
        ORDER BY last_name, first_name
    ");
    $patients = $patientsStmt->fetchAll();

    // Fetch dropdown options for doctors (unchanged from Step 4)
    $doctorsStmt = $pdo->query("
        SELECT id, first_name, last_name, department
        FROM doctors
        ORDER BY last_name, first_name
    ");
    $doctors = $doctorsStmt->fetchAll();

    // Fetch unique department options from doctors (unchanged from Step 3)
    $departmentsStmt = $pdo->query("
        SELECT DISTINCT department
        FROM doctors
        WHERE department IS NOT NULL AND department != ''
        ORDER BY department
    ");
    $departments = $departmentsStmt->fetchAll(PDO::FETCH_COLUMN);

    // Combine data into response
    $response = [
        'appointment' => $appointment,
        'patients' => $patients,
        'departments' => $departments,
        'doctors' => $doctors
    ];

    
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    error_log("PDOException in fetch_form_data.php: " . $e->getMessage()); // Log database errors
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("General Exception in fetch_form_data.php: " . $e->getMessage()); // Log general errors
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>