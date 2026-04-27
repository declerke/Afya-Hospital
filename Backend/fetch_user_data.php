<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
require_once 'db_connect.php';

try {
    $userId = (int)$_SESSION['user_id'];

    // Fetch user data
    $stmt = $pdo->prepare("
        SELECT id, profile_pic, first_name, last_name, date_of_birth, gender, address, email, contact_number, role, staff_id, patient_id
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    // Prepend the profile picture path
    $profilePicture = $user['profile_pic'] ? 'assets/img/' . $user['profile_pic'] : 'assets/img/user.jpg';

    $response = [
        'user' => [
            'id' => $user['id'],
            'profile_pic' => $profilePicture,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'date_of_birth' => $user['date_of_birth'],
            'gender' => $user['gender'],
            'address' => $user['address'],
            'email' => $user['email'],
            'contact_number' => $user['contact_number'],
            'role' => $user['role'], // Will be "Admin", "Doctor", etc.
            'staff_id' => $user['staff_id'],
            'patient_id' => $user['patient_id']
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    error_log("PDOException in fetch_user_data.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("General Exception in fetch_user_data.php: " . $e->getMessage());
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>