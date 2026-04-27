<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$patientId = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

try {
    $stmt = $pdo->prepare("SELECT visit_date, reason, treatment, prescriptions, procedure, note 
                           FROM visit_records WHERE patient_id = ? ORDER BY visit_date DESC");
    $stmt->execute([$patientId]);
    $visits = $stmt->fetchAll();

    echo json_encode(['success' => true, 'visits' => $visits]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>