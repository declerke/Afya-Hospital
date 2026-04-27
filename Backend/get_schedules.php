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
        ds.id,
        ds.doctor_id,
        CONCAT(d.first_name, ' ', d.last_name) AS doctor_name,
        d.department,
        ds.schedule_date,
        ds.start_time,
        ds.end_time,
        ds.status
    FROM doctor_schedule ds
    JOIN doctors d ON ds.doctor_id = d.id
    WHERE CONCAT(d.first_name, ' ', d.last_name) LIKE :search
       OR ds.schedule_date LIKE :search
       OR ds.status LIKE :search
    LIMIT :limit OFFSET :offset
";

$countQuery = "
    SELECT COUNT(*) 
    FROM doctor_schedule ds
    JOIN doctors d ON ds.doctor_id = d.id
    WHERE CONCAT(d.first_name, ' ', d.last_name) LIKE :search
       OR ds.schedule_date LIKE :search
       OR ds.status LIKE :search
";

// Prepare and execute the main query
$stmt = $pdo->prepare($query);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$schedules = $stmt->fetchAll();

// Get total count for pagination
$countStmt = $pdo->prepare($countQuery);
$countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Format the response
$response = [
    'schedules' => [],
    'totalPages' => $totalPages,
    'currentPage' => $page
];

foreach ($schedules as $schedule) {
    $availableTime = ($schedule['status'] === 'Available' && $schedule['start_time'] && $schedule['end_time'])
        ? date('h:i A', strtotime($schedule['start_time'])) . ' - ' . date('h:i A', strtotime($schedule['end_time']))
        : 'N/A';

    $response['schedules'][] = [
        'id' => $schedule['id'],
        'doctor_name' => $schedule['doctor_name'],
        'department' => $schedule['department'] ?? 'N/A',
        'schedule_date' => $schedule['schedule_date'],
        'status' => $schedule['status'],
        'available_time' => $availableTime
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;