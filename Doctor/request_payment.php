<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$reason = isset($_POST['payment_reason']) ? trim($_POST['payment_reason']) : '';

if ($patientId <= 0 || $amount <= 0 || empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

try {
    $pdo->beginTransaction();
    $invoiceId = 'INV' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO payment_requests (invoice_id, patient_id, amount, reason, status, created_at) VALUES (?, ?, ?, ?, 'Pending', NOW())");
    $stmt->execute([$invoiceId, $patientId, $amount, $reason]);

    $patientStmt = $pdo->prepare("SELECT id FROM users WHERE patient_id = ?");
    $patientStmt->execute([$patientId]);
    $patientUserId = $patientStmt->fetchColumn();

    $adminStmt = $pdo->prepare("SELECT id FROM users WHERE role = 'Admin' LIMIT 1");
    $adminStmt->execute();
    $adminUserId = $adminStmt->fetchColumn();

    $patientNameStmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM patients WHERE id = ?");
    $patientNameStmt->execute([$patientId]);
    $patientName = $patientNameStmt->fetch()['name'];

    $message = "A payment invoice ($invoiceId) for $patientName amounting to $amount has been requested.";
    $notifyStmt = $pdo->prepare("INSERT INTO notifications (recipient_type, recipient_id, message, notification_type, is_read, created_at) VALUES (?, ?, ?, 'Alert', 0, NOW())");
    if ($patientUserId) $notifyStmt->execute(['Patient', $patientUserId, $message]);
    if ($adminUserId)   $notifyStmt->execute(['Admin',   $adminUserId,   $message]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Payment request submitted successfully. Invoice ID: ' . $invoiceId]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>