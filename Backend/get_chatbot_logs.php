<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

// Include the database connection
require_once 'db_connect.php';

// Get pagination and search parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of records per page
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$query = "
    SELECT 
        cl.id,
        cl.user_id,
        CONCAT(u.first_name, ' ', u.last_name) AS user_name,
        cl.message,
        cl.response,
        cl.timestamp
    FROM chatbot_logs cl
    JOIN users u ON cl.user_id = u.id
    WHERE CONCAT(u.first_name, ' ', u.last_name) LIKE :search
       OR cl.message LIKE :search
       OR cl.response LIKE :search
       OR cl.timestamp LIKE :search
    ORDER BY cl.timestamp DESC
    LIMIT :limit OFFSET :offset
";

$countQuery = "
    SELECT COUNT(*) 
    FROM chatbot_logs cl
    JOIN users u ON cl.user_id = u.id
    WHERE CONCAT(u.first_name, ' ', u.last_name) LIKE :search
       OR cl.message LIKE :search
       OR cl.response LIKE :search
       OR cl.timestamp LIKE :search
";

// Prepare and execute the main query
$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();

// Get total count for pagination
$countStmt = $pdo->prepare($countQuery);
$countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Format the response
$response = [
    'logs' => [],
    'totalPages' => $totalPages,
    'currentPage' => $page
];

foreach ($logs as $log) {
    $response['logs'][] = [
        'id' => $log['id'],
        'user_name' => $log['user_name'],
        'message' => $log['message'],
        'response' => $log['response'],
        'timestamp' => $log['timestamp']
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;