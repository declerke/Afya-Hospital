<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

require_once 'db_connect.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid or missing user ID']);
    exit;
}

try {
    $query = "
        SELECT 
            id,
            profile_pic,
            first_name,
            last_name,
            role,
            email,
            contact_number,
            date_of_birth,
            gender,
            address,
            staff_id,
            patient_id
        FROM users
        WHERE id = :id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    // Handle profile_pic: prepend 'assets/img/' if not null, else use default
    $user['profile_pic'] = $user['profile_pic'] ? 'assets/img/' . $user['profile_pic'] : 'assets/img/user.jpg';

    // Handle nullable fields
    $user['staff_id'] = $user['staff_id'] ?? 'N/A';
    $user['patient_id'] = $user['patient_id'] ?? 'N/A';
    $user['contact_number'] = $user['contact_number'] ?? '';
    $user['date_of_birth'] = $user['date_of_birth'] ?? '';
    $user['gender'] = $user['gender'] ?? '';
    $user['address'] = $user['address'] ?? '';

    header('Content-Type: application/json');
    echo json_encode($user);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unexpected error: ' . $e->getMessage()]);
}
exit;