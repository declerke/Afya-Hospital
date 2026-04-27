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
    // Build the query with LEFT JOIN to handle missing patients
    $query = "
        SELECT 
            f.id,
            f.patient_id,
            CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
            f.feedback_text,
            f.rating,
            f.submitted_at
        FROM feedback f
        LEFT JOIN patients p ON f.patient_id = p.id
        WHERE (CONCAT(p.first_name, ' ', p.last_name) LIKE :search OR p.first_name IS NULL)
           OR f.feedback_text LIKE :search
           OR f.rating LIKE :search
           OR f.submitted_at LIKE :search
        ORDER BY f.submitted_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $countQuery = "
        SELECT COUNT(*) 
        FROM feedback f
        LEFT JOIN patients p ON f.patient_id = p.id
        WHERE (CONCAT(p.first_name, ' ', p.last_name) LIKE :search OR p.first_name IS NULL)
           OR f.feedback_text LIKE :search
           OR f.rating LIKE :search
           OR f.submitted_at LIKE :search
    ";

    // Prepare and execute the main query
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $feedbacks = $stmt->fetchAll();

    // Get total count for pagination
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);

    // Format the response
    $response = [
        'feedbacks' => [],
        'totalPages' => $totalPages,
        'currentPage' => $page
    ];

    foreach ($feedbacks as $feedback) {
        $response['feedbacks'][] = [
            'id' => $feedback['id'],
            'patient_name' => $feedback['patient_name'] ?? 'Unknown Patient',
            'feedback_text' => $feedback['feedback_text'],
            'rating' => $feedback['rating'],
            'submitted_at' => $feedback['submitted_at']
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