<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
include 'db_connect.php';

try {
    // Start a transaction
    $pdo->beginTransaction();

    // Get form data
    $staffId = trim($_POST['staff_id'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contactNumber = trim($_POST['phone'] ?? '');

    // Validate required fields
    if (empty($firstName) || empty($department) || empty($dateOfBirth) || empty($email) || empty($contactNumber) || empty($gender)) {
        throw new Exception("First Name, Department, Date of Birth, Email, Phone, and Gender are required.");
    }

    // Validate phone number format
    if (!preg_match('/^[0-9+\- ]*$/', $contactNumber)) {
        throw new Exception("Invalid phone number format. Use digits, +, or - only.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format.");
    }

    // Validate staff_id format (DR-GG-SSS-YYYY)
    if (!preg_match('/^DR-\d{2}-\d{3}-\d{4}$/', $staffId)) {
        throw new Exception("Invalid Staff ID format.");
    }

    // Handle profile picture upload
    $profilePicPath = null;
    if (isset($_FILES['profile_pic_file']) && $_FILES['profile_pic_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_pic_file'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Only JPEG, PNG, and GIF files are allowed.");
        }

        if ($file['size'] > $maxFileSize) {
            throw new Exception("File size must be less than 5MB.");
        }

        $uploadDir = 'assets/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $profilePicPath = 'doctor_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $profilePicPath;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to upload the file.");
        }
    } else {
        throw new Exception("Avatar is required.");
    }

    // Insert into doctors table
    $stmt = $pdo->prepare("INSERT INTO doctors (staff_id, first_name, last_name, department, date_of_birth, gender, address, email, contact_number, profile_pic) VALUES (:staff_id, :first_name, :last_name, :department, :date_of_birth, :gender, :address, :email, :contact_number, :profile_pic)");
    $stmt->bindValue(':staff_id', $staffId, PDO::PARAM_STR);
    $stmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $lastName ? $lastName : null, PDO::PARAM_STR);
    $stmt->bindValue(':department', $department, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_birth', $dateOfBirth, PDO::PARAM_STR);
    $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address ? $address : null, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':contact_number', $contactNumber, PDO::PARAM_STR);
    $stmt->bindValue(':profile_pic', $profilePicPath, PDO::PARAM_STR);
    $stmt->execute();

    // Commit the transaction
    $pdo->commit();

    $response = [
        'success' => true,
        'message' => 'Doctor added successfully.'
    ];
} catch (Exception $e) {
    // Roll back the transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // If a profile picture was uploaded, delete it on error
    if (isset($uploadPath) && file_exists($uploadPath)) {
        unlink($uploadPath);
    }

    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>