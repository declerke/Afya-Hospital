<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

include 'db_connect.php';

try {
    $stmt = $pdo->query("SELECT staff_id FROM doctors ORDER BY staff_id DESC LIMIT 1");
    $latestStaffId = $stmt->fetchColumn();

    $response = [
        'success' => true,
        'latest_staff_id' => $latestStaffId ?: 'DR-01-001-2025' // Default if no records exist
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>