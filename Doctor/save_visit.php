<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$patientId    = isset($_POST['patient_id'])    ? (int)$_POST['patient_id']        : 0;
$doctorId     = isset($_POST['doctor_id'])     ? (int)$_POST['doctor_id']         : 0;
$visitDate    = isset($_POST['visit_date'])    ? trim($_POST['visit_date'])        : '';
$reason       = isset($_POST['reason'])        ? trim($_POST['reason'])            : '';
$treatment    = isset($_POST['treatment'])     ? trim($_POST['treatment'])         : '';
$prescriptions= isset($_POST['prescriptions'])? trim($_POST['prescriptions'])     : '';
$procedure    = isset($_POST['procedure'])     ? trim($_POST['procedure'])         : '';
$note         = isset($_POST['note'])          ? trim($_POST['note'])              : '';

if ($patientId <= 0 || $doctorId <= 0 || empty($visitDate) || empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

try {
    $dateTime = DateTime::createFromFormat('d/m/Y', $visitDate);
    if (!$dateTime || $dateTime->format('d/m/Y') !== $visitDate) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format. Expected DD/MM/YYYY']);
        exit();
    }
    $visitDate = $dateTime->format('Y-m-d');

    // visit_records.doctor_id is varchar — store staff_id, not doctors.id
    $staffStmt = $pdo->prepare("SELECT staff_id FROM doctors WHERE id = ?");
    $staffStmt->execute([$doctorId]);
    $staffId = $staffStmt->fetchColumn();
    if (!$staffId) {
        echo json_encode(['success' => false, 'message' => 'Doctor staff ID not found']);
        exit();
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO visit_records
          (patient_id, doctor_id, visit_date, reason_for_visit, notes_outcome, treatment, prescriptions, `procedure`, note)
        VALUES (?, ?, ?, ?, '', ?, ?, ?, ?)
    ");
    $stmt->execute([$patientId, $staffId, $visitDate, $reason, $treatment, $prescriptions, $procedure, $note]);

    // Resolve users.id for notifications
    $patientUserId = $pdo->prepare("SELECT id FROM users WHERE patient_id = ?");
    $patientUserId->execute([$patientId]);
    $patientUserId = $patientUserId->fetchColumn();

    $doctorUserId = $pdo->prepare("SELECT id FROM users WHERE staff_id = ?");
    $doctorUserId->execute([$staffId]);
    $doctorUserId = $doctorUserId->fetchColumn();

    $patientNameStmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM patients WHERE id = ?");
    $patientNameStmt->execute([$patientId]);
    $patientName = $patientNameStmt->fetch()['name'] ?? 'Patient';

    $msg = "A new visit record has been added for $patientName on $visitDate.";
    $notifyStmt = $pdo->prepare("INSERT INTO notifications (recipient_type, recipient_id, message, notification_type, is_read, created_at) VALUES (?, ?, ?, 'Alert', 0, NOW())");
    if ($patientUserId) $notifyStmt->execute(['Patient', $patientUserId, $msg]);
    if ($doctorUserId)  $notifyStmt->execute(['Doctor',  $doctorUserId,  $msg]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Visit recorded successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
