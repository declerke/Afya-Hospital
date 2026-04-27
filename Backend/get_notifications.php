<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
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
$isHeader = isset($_GET['header']) && $_GET['header'] === 'true'; // Flag for header dropdown

try {
    if ($isHeader) {
        // Query for header dropdown (latest 5 unread notifications)
        $query = "
            SELECT 
                an.id,
                an.user_id,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                an.message,
                an.notification_type,
                an.created_at,
                an.is_read
            FROM admin_notifications an
            LEFT JOIN users u ON an.user_id = u.id
            WHERE an.is_read = 0
            ORDER BY an.created_at DESC
            LIMIT 5
        ";

        $countQuery = "
            SELECT COUNT(*) 
            FROM admin_notifications
            WHERE is_read = 0
        ";
    } else {
        // Query for activities.php table (with pagination and search)
        $query = "
            SELECT 
                an.id,
                an.user_id,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                an.message,
                an.notification_type,
                an.created_at,
                an.is_read
            FROM admin_notifications an
            LEFT JOIN users u ON an.user_id = u.id
            WHERE (CONCAT(u.first_name, ' ', u.last_name) LIKE :search OR u.first_name IS NULL)
               OR an.message LIKE :search
               OR an.notification_type LIKE :search
               OR an.created_at LIKE :search
            ORDER BY an.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $countQuery = "
            SELECT COUNT(*) 
            FROM admin_notifications an
            LEFT JOIN users u ON an.user_id = u.id
            WHERE (CONCAT(u.first_name, ' ', u.last_name) LIKE :search OR u.first_name IS NULL)
               OR an.message LIKE :search
               OR an.notification_type LIKE :search
               OR an.created_at LIKE :search
        ";
    }

    // Prepare and execute the main query
    $stmt = $pdo->prepare($query);
    if (!$isHeader) {
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    $stmt->execute();
    $notifications = $stmt->fetchAll();

    // Get total count for pagination or unread count
    $countStmt = $pdo->prepare($countQuery);
    if (!$isHeader) {
        $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalRecords = $countStmt->fetchColumn();
    $totalPages = $isHeader ? 1 : ceil($totalRecords / $limit);

    // Format the response
    $response = [
        'notifications' => [],
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'unreadCount' => $totalRecords
    ];

    foreach ($notifications as $notification) {
        $response['notifications'][] = [
            'id' => $notification['id'],
            'user_name' => $notification['user_name'] ?? 'Unknown User',
            'message' => $notification['message'],
            'notification_type' => $notification['notification_type'],
            'created_at' => $notification['created_at'],
            'is_read' => (int)$notification['is_read']
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