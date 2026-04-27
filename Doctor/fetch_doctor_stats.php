<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$doctorId = $_SESSION['doctor_id']; // For doctors.id
$staffId = $_SESSION['staff_id'];   // For visit_records

try {
    // Appointments count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointments WHERE doctor_id = ? AND appointment_date >= CURDATE()");
    $stmt->execute([$doctorId]);
    $appointments = $stmt->fetch()['count'];

    // Schedule count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM doctor_schedule WHERE doctor_id = ? AND schedule_date >= CURDATE()");
    $stmt->execute([$doctorId]);
    $schedule = $stmt->fetch()['count'];

    // Medical Records count (uses staff_id)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM visit_records WHERE doctor_id = ?");
    $stmt->execute([$staffId]);
    $medical_records = $stmt->fetch()['count'];

    // Billing count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM billing b JOIN appointments a ON b.appointment_id = a.id WHERE a.doctor_id = ?");
    $stmt->execute([$doctorId]);
    $billing = $stmt->fetch()['count'];

    echo json_encode([
        'success' => true,
        'data' => [
            'appointments' => $appointments,
            'schedule' => $schedule,
            'medical_records' => $medical_records,
            'billing' => $billing
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>