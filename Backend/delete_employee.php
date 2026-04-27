<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
// Include the database connection
require_once 'db_connect.php';

// Get the employee ID and role from the POST request
$employeeId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$role = isset($_POST['role']) ? $_POST['role'] : '';

if ($employeeId <= 0 || empty($role)) {
    echo json_encode(['error' => 'Invalid employee ID or role']);
    exit;
}

// Determine the table to delete from based on the role
$table = '';
switch ($role) {
    case 'Doctor':
        $table = 'doctors';
        break;
    case 'Nurse':
        $table = 'nurses';
        break;
    case 'Receptionist':
        $table = 'receptionists';
        break;
    case 'Admin':
        $table = 'admins';
        break;
    default:
        echo json_encode(['error' => 'Invalid role']);
        exit;
}

// Delete the employee
try {
    $stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
    $stmt->bindValue(':id', $employeeId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Employee not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to delete employee: ' . $e->getMessage()]);
}
exit;