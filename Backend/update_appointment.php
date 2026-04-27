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
    // Enable error reporting for debugging    // Get form data from POST
    $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
    $doctorId = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
    $appointmentDate = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
    $appointmentTime = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Log the incoming data for debugging
    // Validate required fields with detailed error messages
    if ($appointmentId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid appointment ID: ' . $appointmentId]);
        exit;
    }
    if ($patientId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid patient ID: ' . $patientId]);
        exit;
    }
    if ($doctorId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid doctor ID: ' . $doctorId]);
        exit;
    }
    if (empty($appointmentDate)) {
        http_response_code(400);
        echo json_encode(['error' => 'Appointment date is required']);
        exit;
    }
    if (empty($appointmentTime)) {
        http_response_code(400);
        echo json_encode(['error' => 'Appointment time is required']);
        exit;
    }
    if (empty($status)) {
        http_response_code(400);
        echo json_encode(['error' => 'Status is required']);
        exit;
    }

    // Validate date and time formats
    $dateTime = DateTime::createFromFormat('Y-m-d H:i', $appointmentDate . ' ' . $appointmentTime);
    if (!$dateTime || $dateTime->format('Y-m-d H:i') !== $appointmentDate . ' ' . $appointmentTime) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid date or time format: ' . $appointmentDate . ' ' . $appointmentTime]);
        exit;
    }

    // Update appointment in the database
    $stmt = $pdo->prepare("
        UPDATE appointments
        SET patient_id = ?, doctor_id = ?, appointment_date = ?, appointment_time = ?, status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$patientId, $doctorId, $appointmentDate, $appointmentTime, $status, $appointmentId]);

    // Sync doctor_schedule: Busy when Scheduled, Available otherwise
    $scheduleStatus = ($status === 'Scheduled') ? 'Busy' : 'Available';
    $endTime = (clone $dateTime)->modify('+30 minutes')->format('H:i:s');
    $scheduleStmt = $pdo->prepare("
        UPDATE doctor_schedule
        SET doctor_id = ?, schedule_date = ?, start_time = ?, end_time = ?, status = ?, updated_at = NOW()
        WHERE appointment_id = ?
    ");
    $scheduleStmt->execute([$doctorId, $appointmentDate, $appointmentTime, $endTime, $scheduleStatus, $appointmentId]);

    // Fetch patient name + users.id
    $patientStmt = $pdo->prepare("
        SELECT CONCAT(p.first_name,' ',p.last_name) AS name, u.id AS user_id
        FROM patients p JOIN users u ON u.patient_id = p.id
        WHERE p.id = ?");
    $patientStmt->execute([$patientId]);
    $patientRow    = $patientStmt->fetch();
    $patientName   = $patientRow['name'] ?? 'Patient';
    $patientUserId = $patientRow['user_id'] ?? null;

    // Fetch doctor name + users.id
    $doctorStmt = $pdo->prepare("
        SELECT CONCAT(d.first_name,' ',d.last_name) AS name, u.id AS user_id
        FROM doctors d JOIN users u ON u.staff_id = d.staff_id
        WHERE d.id = ?");
    $doctorStmt->execute([$doctorId]);
    $doctorRow    = $doctorStmt->fetch();
    $doctorName   = $doctorRow['name'] ?? 'Doctor';
    $doctorUserId = $doctorRow['user_id'] ?? null;

    $appointmentIdFormatted = 'APT' . str_pad($appointmentId, 4, '0', STR_PAD_LEFT);
    $dateFormatted = $dateTime->format('d M Y');
    $timeFormatted = $dateTime->format('H:i');

    $baseMessage       = "Your appointment $appointmentIdFormatted has been updated on $dateFormatted at $timeFormatted to $status.";
    $doctorBaseMessage = "Appointment $appointmentIdFormatted for $patientName has been updated on $dateFormatted at $timeFormatted to $status.";
    $patientMessage = $message ? "$baseMessage\n\nAdditional Note: $message" : $baseMessage;
    $doctorMessage  = $message ? "$doctorBaseMessage\n\nAdditional Note: $message" : $doctorBaseMessage;

    $notifyStmt = $pdo->prepare("
        INSERT INTO notifications (recipient_type, recipient_id, message, notification_type, is_read, created_at)
        VALUES (?, ?, ?, 'Alert', 0, NOW())
    ");
    if ($patientUserId) $notifyStmt->execute(['Patient', $patientUserId, $patientMessage]);
    if ($doctorUserId)  $notifyStmt->execute(['Doctor',  $doctorUserId,  $doctorMessage]);

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Appointment updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    error_log("PDOException in update_appointment.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("General Exception in update_appointment.php: " . $e->getMessage());
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>