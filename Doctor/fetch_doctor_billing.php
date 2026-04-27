<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$doctorId = $_SESSION['doctor_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 9;

try {
    $stmt = $pdo->prepare("
        SELECT b.invoice_number, p.first_name, p.last_name, b.amount, b.payment_status AS status
        FROM billing b
        JOIN appointments a ON b.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = ?
        ORDER BY b.transaction_date DESC
        LIMIT ?
    ");
    $stmt->bindParam(1, $doctorId, PDO::PARAM_INT);
    $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $billing = $stmt->fetchAll();

    $data = array_map(function($row) {
        return [
            'invoice_number' => $row['invoice_number'],
            'patient_name' => $row['first_name'] . ' ' . $row['last_name'],
            'amount' => $row['amount'],
            'status' => $row['status']
        ];
    }, $billing);

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>