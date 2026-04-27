<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
require_once 'db_connect.php';

header('Content-Type: application/json');

$role = isset($_POST['role']) ? trim($_POST['role']) : '';
$staff_id = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$date_of_birth = isset($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : '';
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';

$roleTables = [
    'Doctor' => 'doctors',
    'Nurse' => 'nurses',
    'Receptionist' => 'receptionists',
    'Admin' => 'admins'
];

if (!$role || !array_key_exists($role, $roleTables) || !$staff_id || !$first_name || !$email) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$table = $roleTables[$role];

// Handle profile picture upload
$profile_pic = null;
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
    $fileName = $_FILES['profile_pic']['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowedExts)) {
        $newFileName = $role . '_' . str_replace('-', '_', $staff_id) . '_' . time() . '.' . $fileExt;
        $uploadDir = 'assets/img/';
        $uploadPath = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($fileTmpPath, $uploadPath)) {
            echo json_encode(['error' => 'Failed to upload profile picture']);
            exit;
        }
        $profile_pic = $newFileName;
    } else {
        echo json_encode(['error' => 'Invalid file type. Allowed: jpg, jpeg, png, gif']);
        exit;
    }
}

try {
    // Start a transaction
    $pdo->beginTransaction();

    // Insert into role-specific table
    $query = "
        INSERT INTO $table (
            staff_id, first_name, last_name, " . ($role === 'Doctor' ? 'department,' : '') . "
            email, contact_number, date_of_birth, gender, address, profile_pic, created_at
        ) VALUES (
            :staff_id, :first_name, :last_name, " . ($role === 'Doctor' ? ':department,' : '') . "
            :email, :contact_number, :date_of_birth, :gender, :address, :profile_pic, NOW()
        )
    ";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':staff_id', $staff_id, PDO::PARAM_STR);
    $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $last_name ? $last_name : null, PDO::PARAM_STR);
    if ($role === 'Doctor') {
        $stmt->bindValue(':department', $department ? $department : null, PDO::PARAM_STR);
    }
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':contact_number', $contact_number ? $contact_number : null, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_birth', $date_of_birth ? date('Y-m-d', strtotime($date_of_birth)) : null, $date_of_birth ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':gender', $gender ? $gender : null, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address ? $address : null, PDO::PARAM_STR);
    $stmt->bindValue(':profile_pic', $profile_pic, $profile_pic ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->execute();

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => "$role added successfully"]);
} catch (Exception $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Delete uploaded file if it exists
    if (isset($uploadPath) && file_exists($uploadPath)) {
        unlink($uploadPath);
    }

    echo json_encode(['error' => $e->getMessage()]);
}
exit;