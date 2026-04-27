<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
require_once 'db_connect.php'; 

// Check if it’s a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get form data
$user_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$date_of_birth = trim($_POST['date_of_birth'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$address = trim($_POST['address'] ?? '');

// Basic validation
if ($user_id <= 0 || empty($first_name) || empty($email)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required fields: User ID, First Name, or Email']);
    exit;
}

// Handle profile picture upload
$profile_pic = null;
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['profile_pic']['tmp_name'];
    $file_name = $_FILES['profile_pic']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($file_ext, $allowed_exts)) {
        // Generate unique filename (e.g., user_123_1743431397.png)
        $profile_pic = "user_{$user_id}_" . time() . "." . $file_ext;
        $upload_path = "assets/img/" . $profile_pic;

        if (!move_uploaded_file($file_tmp, $upload_path)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Failed to upload profile picture']);
            exit;
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid file type. Allowed: jpg, jpeg, png, gif']);
        exit;
    }
}

try {
    // Build the update query (only editable fields)
    $query = "
        UPDATE users
        SET 
            first_name = :first_name,
            last_name = :last_name,
            email = :email,
            contact_number = :contact_number,
            date_of_birth = :date_of_birth,
            gender = :gender,
            address = :address
            " . ($profile_pic ? ", profile_pic = :profile_pic" : "") . "
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':first_name', $first_name, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $last_name ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':contact_number', $contact_number ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_birth', $date_of_birth ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':gender', $gender ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address ?: null, PDO::PARAM_STR);
    if ($profile_pic) {
        $stmt->bindValue(':profile_pic', $profile_pic, PDO::PARAM_STR);
    }
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);

    $stmt->execute();

    // Check if any rows were affected
    if ($stmt->rowCount() > 0 || $profile_pic) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No changes detected or user not found']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unexpected error: ' . $e->getMessage()]);
}
exit;