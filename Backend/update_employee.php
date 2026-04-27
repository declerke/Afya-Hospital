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

// Get POST data
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$role = isset($_POST['role']) ? trim($_POST['role']) : '';
$first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
$last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : ''; // Only for Doctors
$date_of_birth = isset($_POST['date_of_birth']) ? trim($_POST['date_of_birth']) : '';
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';

$roleTables = [
    'Doctor' => 'doctors',
    'Nurse' => 'nurses',
    'Receptionist' => 'receptionists',
    'Admin' => 'admins'
];

if (!$id || !array_key_exists($role, $roleTables) || !$first_name || !$email) {
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
        $newFileName = $role . '_' . $id . '_' . time() . '.' . $fileExt; // e.g., Doctor_123_1743346593.png
        $uploadPath = 'assets/img/' . $newFileName;
        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            $profile_pic = $newFileName;
        } else {
            echo json_encode(['error' => 'Failed to upload profile picture']);
            exit;
        }
    } else {
        echo json_encode(['error' => 'Invalid file type. Allowed: jpg, jpeg, png, gif']);
        exit;
    }
}

// Prepare the update query
$query = "
    UPDATE $table
    SET 
        first_name = :first_name,
        last_name = :last_name,
        " . ($role === 'Doctor' ? 'department = :department,' : '') . "
        date_of_birth = :date_of_birth,
        gender = :gender,
        address = :address,
        email = :email,
        contact_number = :contact_number
        " . ($profile_pic ? ', profile_pic = :profile_pic' : '') . "
    WHERE id = :id
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_birth', $date_of_birth ? date('Y-m-d', strtotime($date_of_birth)) : null, $date_of_birth ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':contact_number', $contact_number, PDO::PARAM_STR);
    if ($role === 'Doctor') {
        $stmt->bindValue(':department', $department, PDO::PARAM_STR);
    }
    if ($profile_pic) {
        $stmt->bindValue(':profile_pic', $profile_pic, PDO::PARAM_STR);
    }
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
exit;