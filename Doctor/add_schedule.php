<?php
session_start();
include 'db_connect.php';

// Validate that the user is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    // Get form data
    $doctorId = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
    $scheduleDate = isset($_POST['schedule_date']) ? $_POST['schedule_date'] : '';
    $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : '';
    $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $userId = $_SESSION['user_id'];

    // Validate required fields
    if ($doctorId <= 0 || empty($scheduleDate) || empty($startTime) || empty($status)) {
        throw new Exception("Missing required fields.");
    }

    // Validate date format (DD/MM/YYYY)
    $scheduleDateObj = DateTime::createFromFormat('d/m/Y', $scheduleDate);
    if (!$scheduleDateObj || $scheduleDateObj->format('d/m/Y') !== $scheduleDate) {
        throw new Exception("Invalid schedule date format. Use DD/MM/YYYY.");
    }
    $scheduleDateDb = $scheduleDateObj->format('Y-m-d');

    // Validate start time (e.g., "10:00 AM")
    $startTimeObj = DateTime::createFromFormat('h:i A', $startTime);
    if (!$startTimeObj || $startTimeObj->format('h:i A') !== $startTime) {
        throw new Exception("Invalid start time format. Use HH:MM AM/PM.");
    }
    $startTimeDb = $startTimeObj->format('H:i:s');

    // Validate end time (if provided)
    $endTimeDb = null;
    if (!empty($endTime)) {
        $endTimeObj = DateTime::createFromFormat('h:i A', $endTime);
        if (!$endTimeObj || $endTimeObj->format('h:i A') !== $endTime) {
            throw new Exception("Invalid end time format. Use HH:MM AM/PM.");
        }
        $endTimeDb = $endTimeObj->format('H:i:s');

        // Ensure end time is after start time
        if ($endTimeObj <= $startTimeObj) {
            throw new Exception("End time must be after start time.");
        }
    }

    // Validate status
    if (!in_array($status, ['Available', 'Busy', 'On-Call', 'Blocked'])) {
        throw new Exception("Invalid status value.");
    }

    // Start a transaction
    $pdo->beginTransaction();

    // Insert into doctor_schedule with appointment_id as NULL and created_at set to NOW()
    $stmt = $pdo->prepare("
        INSERT INTO doctor_schedule (doctor_id, schedule_date, start_time, end_time, status, appointment_id, notes, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NULL, ?, NOW(), NULL)
    ");
    $stmt->execute([$doctorId, $scheduleDateDb, $startTimeDb, $endTimeDb, $status, $notes]);

    // Get the ID of the newly inserted schedule
    $scheduleId = $pdo->lastInsertId();

    // Build the details message for audit_logs
    $details = "Added new schedule ID $scheduleId: Doctor ID $doctorId; Date $scheduleDateDb; Start time $startTimeDb; ";
    $details .= "End time " . ($endTimeDb ?: 'none') . "; Status $status; Notes '" . ($notes ?: 'none') . "'.";

    // Log the action in audit_logs
    $auditStmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, table_name, record_id, details, timestamp)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $auditStmt->execute([$userId, 'Add', 'doctor_schedule', $scheduleId, $details]);

    // Create a notification in admin_notifications
    $message = "Doctor (ID: $doctorId) added a new schedule (ID: $scheduleId) on $scheduleDateDb from $startTime to " . ($endTime ?: 'N/A') . ".";
    $notificationStmt = $pdo->prepare("
        INSERT INTO admin_notifications (user_id, message, notification_type, is_read, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $notificationStmt->execute([$userId, $message, 'CRUD', 0]);

    // Commit the transaction
    $pdo->commit();

    $response = [
        'success' => true,
        'message' => 'Schedule added successfully.'
    ];
} catch (Exception $e) {
    $pdo->rollBack();
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>