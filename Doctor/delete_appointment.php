<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id']; // From users.id
$appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;

if ($appointmentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
    exit();
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Delete the appointment
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ? AND doctor_id = ?");
    $stmt->execute([$appointmentId, $_SESSION['doctor_id']]);

    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Appointment not found or not authorized']);
        exit();
    }

    // Log the action in audit_logs
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp, details)
        VALUES (?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([
        $userId,
        'Appointment Deleted',
        'appointments',
        $appointmentId,
        'Appointment ID: ' . $appointmentId . ' deleted by Doctor ID: ' . $_SESSION['doctor_id']
    ]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>