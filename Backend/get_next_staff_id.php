<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

require_once 'db_connect.php';

$role = isset($_GET['role']) ? trim($_GET['role']) : '';
$roleTables = [
    'Doctor' => 'doctors',
    'Nurse' => 'nurses',
    'Receptionist' => 'receptionists',
    'Admin' => 'admins'
];

if (!array_key_exists($role, $roleTables)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid role']);
    exit;
}

$table = $roleTables[$role];
$prefix = ($role === 'Doctor' ? 'DR' : ($role === 'Nurse' ? 'NR' : ($role === 'Receptionist' ? 'REC' : 'AD')));
$year = date('Y'); // e.g., 2025

try {
    // Fetch the latest staff_id by numerically sorting the xxx part
    $query = "
        SELECT staff_id 
        FROM $table 
        WHERE staff_id LIKE :prefix 
        ORDER BY CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(staff_id, '-', -2), '-', 1) AS UNSIGNED) DESC 
        LIMIT 1
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':prefix', "$prefix-01-%-$year", PDO::PARAM_STR);
    $stmt->execute();
    $lastStaffId = $stmt->fetchColumn();

    if ($lastStaffId === false) {
        // No records found, start at 001
        $nextNumber = '001';
    } else {
        // Extract the numeric part (e.g., "002" from "NR-01-002-2025")
        $parts = explode('-', $lastStaffId);
        $lastNumber = (int)$parts[2]; // e.g., "002"
        $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    $nextStaffId = "$prefix-01-$nextNumber-$year";
    header('Content-Type: application/json');
    echo json_encode(['staff_id' => $nextStaffId]);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
exit;