<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$doctorId = $_SESSION['doctor_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;

try {
    $stmt = $pdo->prepare("
        SELECT a.id, a.patient_id, p.first_name, p.last_name, p.date_of_birth, a.appointment_date AS date, a.appointment_time AS time, a.status
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = ? AND a.appointment_date >= CURDATE()
        ORDER BY a.appointment_date, a.appointment_time
        LIMIT ?
    ");
    $stmt->bindParam(1, $doctorId, PDO::PARAM_INT);
    $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $appointments = $stmt->fetchAll();

    $data = array_map(function($row) {
        // Calculate age from date_of_birth
        $dob = new DateTime($row['date_of_birth']);
        $now = new DateTime();
        $age = $now->diff($dob)->y;

        // Format Appointment ID as APTXXXX
        $formattedId = 'APT' . str_pad($row['id'], 4, '0', STR_PAD_LEFT);

        return [
            'id' => $row['id'], // Keep raw ID for links
            'formatted_id' => $formattedId, // Formatted ID for display
            'patient_id' => $row['patient_id'],
            'patient_name' => $row['first_name'] . ' ' . $row['last_name'],
            'age' => $age,
            'date' => $row['date'],
            'time' => $row['time'],
            'status' => $row['status']
        ];
    }, $appointments);

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>