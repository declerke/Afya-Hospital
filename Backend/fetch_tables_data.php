<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }


include 'db_connect.php';

try {
    // Fetch all appointments, sorted by status (Scheduled, Cancelled, Completed), then date and time
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
            p.date_of_birth AS patient_dob,
            p.address AS patient_location,
            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name,
            d.department
        FROM appointments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        ORDER BY 
            CASE 
                WHEN a.status = 'Scheduled' THEN 1
                WHEN a.status = 'Cancelled' THEN 2
                WHEN a.status = 'Completed' THEN 3
                ELSE 4 
            END,
            a.appointment_date ASC, 
            a.appointment_time ASC
    ");
    $stmt->execute();
    $appointments = $stmt->fetchAll();

    
    header('Content-Type: application/json');
    echo json_encode($appointments);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>