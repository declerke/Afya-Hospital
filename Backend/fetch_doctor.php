<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

include 'db_connect.php'; 

try {
    if (!isset($_GET['doctor_id']) || empty($_GET['doctor_id'])) {
        throw new Exception("Doctor ID is required.");
    }

    $doctorId = (int)$_GET['doctor_id'];

    $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = :id");
    $stmt->bindValue(':id', $doctorId, PDO::PARAM_INT);
    $stmt->execute();

    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctor) {
        throw new Exception("Doctor not found.");
    }

    $profilePic = $doctor['profile_pic'] ? 'assets/img/' . $doctor['profile_pic'] : '';

    $response = [
        'success' => true,
        'doctor' => [
            'id' => $doctor['id'],
            'staff_id' => $doctor['staff_id'],
            'first_name' => $doctor['first_name'],
            'last_name' => $doctor['last_name'],
            'email' => $doctor['email'] ?? null,
            'date_of_birth' => $doctor['date_of_birth'] ?? null,
            'gender' => $doctor['gender'] ?? null,
            'department' => $doctor['department'],
            'address' => $doctor['address'],
            'contact_number' => $doctor['contact_number'] ?? null,
            'profile_pic' => $profilePic // Full path
        ]
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>