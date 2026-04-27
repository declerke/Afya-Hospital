<?php
session_start();
include '../Backend/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$staffId = $_SESSION['staff_id']; // visit_records uses staff_id
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

// Encryption setup (same as visit_record_form.php)
$encryption_key = '0589121e755e38401cc2a3a7ed0a8ec9dc8c4db7e0f94ffc46623074b8f33525'; // 32-byte key

try {
    $stmt = $pdo->prepare("
        SELECT vr.id, vr.patient_id, p.first_name, p.last_name, vr.visit_date, vr.reason_for_visit, vr.notes_outcome
        FROM visit_records vr
        JOIN patients p ON vr.patient_id = p.id
        WHERE vr.doctor_id = ?
        ORDER BY vr.visit_date DESC
        LIMIT ?
    ");
    $stmt->bindParam(1, $staffId, PDO::PARAM_INT);
    $stmt->bindParam(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll();

    $data = array_map(function($row) use ($encryption_key) {
        // Decrypt reason_for_visit
        list($encrypted_reason, $iv) = explode('::', base64_decode($row['reason_for_visit']));
        $decrypted_reason = openssl_decrypt(
            $encrypted_reason,
            'aes-256-cbc',
            hex2bin($encryption_key),
            0,
            base64_decode($iv)
        );

        // Decrypt notes_outcome
        list($encrypted_notes, $iv) = explode('::', base64_decode($row['notes_outcome']));
        $decrypted_notes = openssl_decrypt(
            $encrypted_notes,
            'aes-256-cbc',
            hex2bin($encryption_key),
            0,
            base64_decode($iv)
        );

        return [
            'patient_id' => $row['patient_id'], // Add patient_id for linking
            'patient_name' => $row['first_name'] . ' ' . $row['last_name'],
            'visit_date' => $row['visit_date'],
            'reason' => $decrypted_reason ?: 'Decryption failed',
            'notes' => $decrypted_notes ?: 'Decryption failed'
        ];
    }, $records);

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>