<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

// Include the database connection
require_once 'db_connect.php';

// Fetch all audit logs
$query = "
    SELECT 
        CONCAT(u.first_name, ' ', u.last_name) AS user_name,
        al.action,
        al.table_name,
        al.record_id,
        al.timestamp
    FROM audit_logs al
    JOIN users u ON al.user_id = u.id
    ORDER BY al.timestamp DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$logs = $stmt->fetchAll();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="audit_logs.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['User', 'Action', 'Entity', 'Record ID', 'Timestamp']);

// Write data rows
foreach ($logs as $log) {
    fputcsv($output, [
        $log['user_name'],
        $log['action'],
        $log['table_name'],
        $log['record_id'],
        $log['timestamp']
    ]);
}

// Close the output stream
fclose($output);
exit;