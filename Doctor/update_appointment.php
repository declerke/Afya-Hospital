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
$patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
$appointmentDate = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
$appointmentTime = isset($_POST['appointment_time']) ? trim($_POST['appointment_time']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($appointmentId <= 0 || $patientId <= 0 || empty($appointmentDate) || empty($appointmentTime) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

try {
    // Convert UI date (DD/MM/YYYY) to database format (YYYY-MM-DD)
    $dateTime = DateTime::createFromFormat('d/m/Y', $appointmentDate);
    if (!$dateTime || $dateTime->format('d/m/Y') !== $appointmentDate) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format. Expected DD/MM/YYYY (e.g., 15/04/2025)']);
        exit();
    }
    $appointmentDate = $dateTime->format('Y-m-d'); // Convert to YYYY-MM-DD (e.g., 2025-04-15)

    // Validate time format (HH:MM) and convert to HH:MM:SS
    $timeParts = explode(':', $appointmentTime);
    if (count($timeParts) !== 2 || !is_numeric($timeParts[0]) || !is_numeric($timeParts[1]) ||
        (int)$timeParts[0] < 0 || (int)$timeParts[0] > 23 || (int)$timeParts[1] < 0 || (int)$timeParts[1] > 59) {
        echo json_encode(['success' => false, 'message' => 'Invalid time format. Expected HH:MM (e.g., 14:40)']);
        exit();
    }
    $appointmentTime = $appointmentTime . ':00'; // Convert HH:MM to HH:MM:SS (e.g., 14:40 to 14:40:00)

    // Begin transaction
    $pdo->beginTransaction();

    // Update the appointment
    $stmt = $pdo->prepare("
        UPDATE appointments
        SET patient_id = ?, appointment_date = ?, appointment_time = ?, status = ?, modified_by = ?
        WHERE id = ? AND doctor_id = ?
    ");
    $stmt->execute([
        $patientId,
        $appointmentDate,
        $appointmentTime,
        $status,
        'Doctor',
        $appointmentId,
        $_SESSION['doctor_id']
    ]);

    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Appointment not found or not authorized']);
        exit();
    }

    // Fetch patient and doctor names for notifications
    $patientStmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM patients WHERE id = ?");
    $patientStmt->execute([$patientId]);
    $patientName = $patientStmt->fetch()['name'];

    $doctorStmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM doctors WHERE id = ?");
    $doctorStmt->execute([$_SESSION['doctor_id']]);
    $doctorName = $doctorStmt->fetch()['name'];

    // Format the date and time for notifications (dd MMM yyyy at HH:mm)
    $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . $appointmentTime);
    if (!$dateTime) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Invalid date or time format']);
        exit();
    }
    $appointmentIdFormatted = 'APT' . str_pad($appointmentId, 4, '0', STR_PAD_LEFT);
    $dateFormatted = $dateTime->format('d M Y');
    $timeFormatted = $dateTime->format('H:i');

    // Prepare notification messages
    $baseMessage = "Your appointment $appointmentIdFormatted has been updated on $dateFormatted at $timeFormatted to $status.";
    $doctorBaseMessage = "Appointment $appointmentIdFormatted for $patientName has been updated on $dateFormatted at $timeFormatted to $status.";

    $patientMessage = $message ? "$baseMessage\n\nAdditional Note: $message" : $baseMessage;
    $doctorMessage = $message ? "$doctorBaseMessage\n\nAdditional Note: $message" : $doctorBaseMessage;

    // Resolve users.id for notifications
    $puStmt = $pdo->prepare("SELECT id FROM users WHERE patient_id = ?");
    $puStmt->execute([$patientId]);
    $patientUserId = $puStmt->fetchColumn();

    $duStmt = $pdo->prepare("SELECT u.id FROM users u JOIN doctors d ON u.staff_id = d.staff_id WHERE d.id = ?");
    $duStmt->execute([$_SESSION['doctor_id']]);
    $doctorUserId = $duStmt->fetchColumn();

    $notifyStmt = $pdo->prepare("
        INSERT INTO notifications (recipient_type, recipient_id, message, notification_type, is_read, created_at)
        VALUES (?, ?, ?, 'Alert', 0, NOW())
    ");
    if ($patientUserId) $notifyStmt->execute(['Patient', $patientUserId, $patientMessage]);
    if ($doctorUserId)  $notifyStmt->execute(['Doctor',  $doctorUserId,  $doctorMessage]);

    // Log the action in audit_logs
    $details = "Appointment updated by Doctor ID: {$_SESSION['doctor_id']}. New values: Patient ID: $patientId, Date: $appointmentDate, Time: $appointmentTime, Status: $status";
    if ($message) {
        $details .= ", Message: $message";
    }
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp, details)
        VALUES (?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([
        $userId,
        'Appointment Updated',
        'appointments',
        $appointmentId,
        $details
    ]);

    // Commit transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Appointment updated successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>