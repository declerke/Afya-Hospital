<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

require_once 'db_connect.php';

// Get parameters from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$role = isset($_GET['role']) ? trim($_GET['role']) : '';

$roleTables = [
    'Doctor' => 'doctors',
    'Nurse' => 'nurses',
    'Receptionist' => 'receptionists',
    'Admin' => 'admins'
];

if (!$id || !array_key_exists($role, $roleTables)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid ID or role']);
    exit;
}

$table = $roleTables[$role];
$query = "
    SELECT 
        id,
        profile_pic,
        staff_id,
        first_name,
        last_name,
        " . ($role === 'Doctor' ? 'department,' : '') . "
        date_of_birth,
        gender,
        address,
        email,
        contact_number,
        created_at
    FROM $table
    WHERE id = :id
";

try {
    // Fetch employee data
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Employee not found']);
        exit;
    }

    // Format the employee response
    $response = [
        'id' => $employee['id'],
        'profile_pic' => $employee['profile_pic'] ? 'assets/img/' . $employee['profile_pic'] : 'assets/img/user.jpg',
        'staff_id' => $employee['staff_id'],
        'first_name' => $employee['first_name'],
        'last_name' => $employee['last_name'],
        'date_of_birth' => $employee['date_of_birth'] ? date('d-m-Y', strtotime($employee['date_of_birth'])) : '',
        'gender' => $employee['gender'],
        'address' => $employee['address'],
        'email' => $employee['email'],
        'contact_number' => $employee['contact_number']
    ];

    if ($role === 'Doctor') {
        $response['department'] = $employee['department'];

        // Fetch all unique departments from doctors table
        $deptQuery = "SELECT DISTINCT department FROM doctors WHERE department IS NOT NULL ORDER BY department";
        $deptStmt = $pdo->prepare($deptQuery);
        $deptStmt->execute();
        $departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);
        $response['departments'] = $departments;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
exit;