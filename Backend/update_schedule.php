<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
include 'db_connect.php';

try {
    // Get form data
    $scheduleId = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : 0;
    $doctorId = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
    $scheduleDate = isset($_POST['schedule_date']) ? $_POST['schedule_date'] : '';
    $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : '';
    $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    // Validate required fields
    if ($scheduleId <= 0 || $doctorId <= 0 || empty($scheduleDate) || empty($startTime) || empty($status)) {
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

    // Fetch the current schedule data for comparison
    $stmt = $pdo->prepare("
        SELECT doctor_id, schedule_date, start_time, end_time, status, notes
        FROM doctor_schedule
        WHERE id = ?
    ");
    $stmt->execute([$scheduleId]);
    $currentSchedule = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$currentSchedule) {
        throw new Exception("Schedule not found.");
    }

    // Build the details message by comparing old and new values
    $details = "Updated schedule ID $scheduleId: ";
    $changes = [];

    if ($doctorId != $currentSchedule['doctor_id']) {
        $changes[] = "Doctor ID changed from {$currentSchedule['doctor_id']} to $doctorId";
    }
    if ($scheduleDateDb != $currentSchedule['schedule_date']) {
        $changes[] = "Date changed from {$currentSchedule['schedule_date']} to $scheduleDateDb";
    }
    if ($startTimeDb != $currentSchedule['start_time']) {
        $changes[] = "Start time changed from {$currentSchedule['start_time']} to $startTimeDb";
    }
    if ($endTimeDb != $currentSchedule['end_time']) {
        $oldEndTime = $currentSchedule['end_time'] ?: 'none';
        $newEndTime = $endTimeDb ?: 'none';
        $changes[] = "End time changed from $oldEndTime to $newEndTime";
    }
    if ($status != $currentSchedule['status']) {
        $changes[] = "Status changed from {$currentSchedule['status']} to $status";
    }
    if ($notes != $currentSchedule['notes']) {
        $oldNotes = $currentSchedule['notes'] ?: 'none';
        $newNotes = $notes ?: 'none';
        $changes[] = "Notes changed from '$oldNotes' to '$newNotes'";
    }

    if (empty($changes)) {
        $details .= "No changes detected.";
    } else {
        $details .= implode('; ', $changes) . ".";
    }

    // Update doctor_schedule table
    $stmt = $pdo->prepare("
        UPDATE doctor_schedule 
        SET doctor_id = ?, schedule_date = ?, start_time = ?, end_time = ?, status = ?, notes = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$doctorId, $scheduleDateDb, $startTimeDb, $endTimeDb, $status, $notes, $scheduleId]);

    // Log the action in audit_logs with details
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in. Unable to log action.");
    }
    $adminUserId = (int)$_SESSION['user_id'];
    $auditStmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, table_name, record_id, details, timestamp)
        VALUES (?, 'Update Schedule', 'doctor_schedule', ?, ?, NOW())
    ");
    $auditStmt->execute([$adminUserId, $scheduleId, $details]);

    // Commit the transaction
    $pdo->commit();

    $response = [
        'success' => true,
        'message' => 'Schedule updated successfully.'
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