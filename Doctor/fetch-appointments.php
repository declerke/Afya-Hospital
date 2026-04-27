<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

include 'db_connect.php';

$doctorId = $_SESSION['doctor_id'];
$patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;

try {
    $stmt = $pdo->prepare("
        SELECT id, appointment_date
        FROM appointments
        WHERE patient_id = :patient_id AND doctor_id = :doctor_id
        ORDER BY appointment_date DESC
    ");
    $stmt->execute([':patient_id' => $patientId, ':doctor_id' => $doctorId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the appointments as JSON
    header('Content-Type: application/json');
    echo json_encode($appointments);

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Error fetching appointments: ' . $e->getMessage()]);
}
?>