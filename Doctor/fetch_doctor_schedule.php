<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$doctorId = $_SESSION['doctor_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

try {
    $stmt = $pdo->prepare("
        SELECT ds.id, ds.schedule_date, ds.start_time, ds.end_time, ds.appointment_id, ds.notes, ds.status,
               a.patient_id,
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM doctor_schedule ds
        LEFT JOIN appointments a ON ds.appointment_id = a.id
        LEFT JOIN patients p ON a.patient_id = p.id
        WHERE ds.doctor_id = ? AND ds.schedule_date >= CURDATE()
        ORDER BY ds.schedule_date, ds.start_time
        LIMIT ?
    ");
    $stmt->bindParam(1, $doctorId, PDO::PARAM_INT);
    $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $schedule = $stmt->fetchAll();

    $data = array_map(function($row) {
        // Determine the reason (either patient name or notes)
        $reason = $row['appointment_id']
            ? "Appointment with " . ($row['patient_name'] ?: 'Unknown Patient')
            : ($row['notes'] ?: 'No Notes');

        return [
            'schedule_date' => $row['schedule_date'],
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'reason' => $reason,
            'status' => $row['status'] ?: 'Unknown'
        ];
    }, $schedule);

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>