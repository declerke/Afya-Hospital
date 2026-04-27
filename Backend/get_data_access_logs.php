<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

require_once 'db_connect.php';

// Get pagination and search parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Records per page
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Main query with joins
    $query = "
        SELECT 
            dal.id,
            dal.user_id,
            CONCAT(u.first_name, ' ', u.last_name) AS user_name,
            dal.patient_id,
            CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
            dal.access_time,
            dal.action
        FROM data_access_logs dal
        LEFT JOIN users u ON dal.user_id = u.id
        LEFT JOIN patients p ON dal.patient_id = p.id
        WHERE CONCAT(u.first_name, ' ', u.last_name) LIKE :search
           OR CONCAT(p.first_name, ' ', p.last_name) LIKE :search
           OR dal.action LIKE :search
           OR dal.access_time LIKE :search
        ORDER BY dal.access_time DESC
        LIMIT :limit OFFSET :offset
    ";

    // Count query for pagination
    $countQuery = "
        SELECT COUNT(*) 
        FROM data_access_logs dal
        LEFT JOIN users u ON dal.user_id = u.id
        LEFT JOIN patients p ON dal.patient_id = p.id
        WHERE CONCAT(u.first_name, ' ', u.last_name) LIKE :search
           OR CONCAT(p.first_name, ' ', p.last_name) LIKE :search
           OR dal.action LIKE :search
           OR dal.access_time LIKE :search
    ";

    // Prepare and execute main query
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // Format response
    $response = [
        'logs' => $logs,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unexpected error: ' . $e->getMessage()]);
}
exit;