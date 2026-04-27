<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

// Include the database connection
require_once 'db_connect.php';

// Fetch all chatbot logs
$query = "
    SELECT 
        CONCAT(u.first_name, ' ', u.last_name) AS user_name,
        cl.message,
        cl.response,
        cl.timestamp
    FROM chatbot_logs cl
    JOIN users u ON cl.user_id = u.id
    ORDER BY cl.timestamp DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$logs = $stmt->fetchAll();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="chatbot_logs.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['User', 'Message', 'Response', 'Timestamp']);

// Write data rows
foreach ($logs as $log) {
    fputcsv($output, [
        $log['user_name'],
        $log['message'],
        $log['response'],
        $log['timestamp']
    ]);
}

// Close the output stream
fclose($output);
exit;