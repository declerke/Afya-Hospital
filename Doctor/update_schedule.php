<?php
include 'db_connect.php';
session_start();

header('Content-Type: application/json');

// Validate that the user is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$schedule_id = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;
$doctor_id = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
$schedule_date = isset($_POST['schedule_date']) ? $_POST['schedule_date'] : '';
$start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';
$notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
$status = isset($_POST['status']) ? $_POST['status'] : '';
$user_id = $_SESSION['user_id'];

// Validate required fields
if (!$schedule_id || !$doctor_id || !$schedule_date || !$start_time || !$status) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit();
}

// Validate status
$valid_statuses = ['Available', 'Busy', 'On-Call', 'Blocked'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Convert date and time formats
try {
    $scheduleDate = DateTime::createFromFormat('d/m/Y', $schedule_date);
    if (!$scheduleDate) {
        throw new Exception('Invalid schedule date format');
    }
    $formattedScheduleDate = $scheduleDate->format('Y-m-d');

    $startTime = DateTime::createFromFormat('h:i A', $start_time);
    if (!$startTime) {
        throw new Exception('Invalid start time format');
    }
    $formattedStartTime = $startTime->format('H:i:s');

    $formattedEndTime = null;
    if ($end_time) {
        $endTime = DateTime::createFromFormat('h:i A', $end_time);
        if (!$endTime) {
            throw new Exception('Invalid end time format');
        }
        $formattedEndTime = $endTime->format('H:i:s');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}

try {
    // Start a transaction
    $pdo->beginTransaction();

    // Step 1: Update the doctor_schedule table
    $stmt = $pdo->prepare("
        UPDATE doctor_schedule
        SET schedule_date = ?, start_time = ?, end_time = ?, notes = ?, status = ?, updated_at = NOW()
        WHERE id = ? AND doctor_id = ?
    ");
    $stmt->execute([
        $formattedScheduleDate,
        $formattedStartTime,
        $formattedEndTime,
        $notes,
        $status,
        $schedule_id,
        $doctor_id
    ]);

    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Schedule not found or you do not have permission to update it']);
        exit();
    }

    // Step 2: Check if the schedule has an appointment_id
    $stmt = $pdo->prepare("SELECT appointment_id FROM doctor_schedule WHERE id = ?");
    $stmt->execute([$schedule_id]);
    $appointment_id = $stmt->fetchColumn();

    if ($appointment_id) {
        // Fetch the patient_id from the appointments table
        $stmt = $pdo->prepare("SELECT patient_id FROM appointments WHERE id = ?");
        $stmt->execute([$appointment_id]);
        $patient_id = $stmt->fetchColumn();

        if ($patient_id) {
            // Fetch the patient's user_id from the users table
            $stmt = $pdo->prepare("SELECT id FROM users WHERE patient_id = ?");
            $stmt->execute([$patient_id]);
            $patient_user_id = $stmt->fetchColumn();

            if ($patient_user_id) {
                // Create a notification for the patient
                $message = "Your appointment schedule (APT" . str_pad($appointment_id, 4, '0', STR_PAD_LEFT) . ") has been updated. New date: $formattedScheduleDate, Time: $start_time" . ($end_time ? " - $end_time" : "");
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (recipient_type, recipient_id, message, notification_type, is_read, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    'Patient',
                    $patient_user_id,
                    $message,
                    'Alert',
                    0
                ]);
            }
        }
    }

    // Step 3: Log the update in audit_logs
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp, details)
        VALUES (?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([
        $user_id,
        'Update',
        'doctor_schedule',
        $schedule_id,
        "Updated schedule with ID $schedule_id: Date=$formattedScheduleDate, Start Time=$start_time, End Time=" . ($end_time ?: 'N/A') . ", Status=$status"
    ]);

    // Commit the transaction
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Schedule updated successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error updating schedule: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>