<?php
session_start();
include 'db_connect.php';

$response = ['success' => false, 'data' => null];

try {
    // Determine if we're fetching the admin's profile or a doctor's profile
    $userType = isset($_GET['type']) ? $_GET['type'] : 'admin'; // 'admin' or 'doctor'
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($userType === 'admin') {
        // Fetch admin's ID from session
        $id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        if (!$id) {
            throw new Exception('Admin not logged in');
        }

        // Fetch admin details from users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'Admin'");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $response['success'] = true;
            $response['data'] = [
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role'],
                'staff_id' => $user['staff_id'],
                'phone' => $user['contact_number'],
                'email' => $user['email'],
                'birthday' => date('jS F', strtotime($user['date_of_birth'])), // Format as "21st May"
                'address' => $user['address'],
                'gender' => $user['gender'],
                'profile_pic' => $user['profile_pic']
            ];
        }
    } elseif ($userType === 'doctor' && $id) {
        // Fetch doctor details from doctors table
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor) {
            $response['success'] = true;
            $response['data'] = [
                'first_name' => $doctor['first_name'],
                'last_name' => $doctor['last_name'],
                'role' => $doctor['department'], // Using department as role for doctors
                'staff_id' => $doctor['staff_id'],
                'phone' => $doctor['contact_number'],
                'email' => $doctor['email'],
                'birthday' => date('jS F', strtotime($doctor['date_of_birth'])),
                'address' => $doctor['address'],
                'gender' => $doctor['gender'],
                'profile_pic' => $doctor['profile_pic']
            ];
        }
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>