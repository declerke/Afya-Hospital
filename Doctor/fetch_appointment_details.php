<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$appointmentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($appointmentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT a.id, a.patient_id, a.doctor_id, a.appointment_date, a.appointment_time, a.status,
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
               d.department AS department_name,
               CONCAT(d.first_name, ' ', d.last_name) AS doctor_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.id = ? AND a.doctor_id = ?
    ");
    $stmt->execute([$appointmentId, $_SESSION['doctor_id']]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or not authorized']);
        exit();
    }

    // Convert database date (YYYY-MM-DD) to UI format (DD/MM/YYYY)
    $appointmentDate = DateTime::createFromFormat('Y-m-d', $appointment['appointment_date']);
    $formattedDate = $appointmentDate ? $appointmentDate->format('d/m/Y') : $appointment['appointment_date'];

    // Convert database time (HH:MM:SS) to UI format (HH:MM)
    $appointmentTime = DateTime::createFromFormat('H:i:s', $appointment['appointment_time']);
    $formattedTime = $appointmentTime ? $appointmentTime->format('H:i') : substr($appointment['appointment_time'], 0, 5);

    // Fetch all patients for the dropdown
    $stmt = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM patients ORDER BY name");
    $patients = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'appointment' => [
            'id' => $appointment['id'],
            'patient_id' => $appointment['patient_id'],
            'patient_name' => $appointment['patient_name'],
            'doctor_id' => $appointment['doctor_id'],
            'doctor_name' => $appointment['doctor_name'],
            'department' => $appointment['department_name'],
            'appointment_date' => $formattedDate, // Formatted for UI (DD/MM/YYYY)
            'appointment_time' => $formattedTime, // Formatted for UI (HH:MM)
            'status' => $appointment['status']
        ],
        'patients' => $patients
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>