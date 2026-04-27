<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

// Enable error reporting for debugging// Include the database connection
try {
    require_once 'db_connect.php';
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get pagination and search parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of records per page
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Build the query with the corrected column name 'contact_number'
    $query = "
        SELECT 
            id,
            CONCAT(first_name, ' ', last_name) AS full_name,
            role,
            contact_number,
            email
        FROM users
        WHERE CONCAT(first_name, ' ', last_name) LIKE :search
           OR role LIKE :search
           OR contact_number LIKE :search
           OR email LIKE :search
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $countQuery = "
        SELECT COUNT(*) 
        FROM users
        WHERE CONCAT(first_name, ' ', last_name) LIKE :search
           OR role LIKE :search
           OR contact_number LIKE :search
           OR email LIKE :search
    ";

    // Prepare and execute the main query
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll();

    // Get total count for pagination
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // Format the response
    $response = [
        'users' => [],
        'totalPages' => $totalPages,
        'currentPage' => $page
    ];

    foreach ($users as $user) {
        $response['users'][] = [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'contact_number' => $user['contact_number'], // Updated key to match column name
            'email' => $user['email']
        ];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Query failed: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
    exit;
}
exit;