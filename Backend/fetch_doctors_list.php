<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

include 'db_connect.php'; 

try {
    // Get pagination parameters from GET request (default to 0 offset and 12 limit)
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;

    // Get total number of doctors for frontend to know if more data exists
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM doctors");
    $totalDoctors = $countStmt->fetch()['total'];

    // Query to get doctors with pagination
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, department, address, profile_pic FROM doctors LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add the full path to profile_pic
    $formattedDoctors = array_map(function($doctor) {
        
        $doctor['profile_pic'] = !empty($doctor['profile_pic']) ? 'assets/img/' . $doctor['profile_pic'] : 'assets/img/doc1.jpg';
        return $doctor;
    }, $doctors);

    
    $response = [
        'doctors' => $formattedDoctors,
        'total' => $totalDoctors,
        'offset' => $offset,
        'limit' => $limit,
        'error' => null
    ];
} catch (PDOException $e) {
    $response = ['error' => "Database error: " . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
?>