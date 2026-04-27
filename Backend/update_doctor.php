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
    if (!isset($_POST['doctor_id']) || empty($_POST['doctor_id'])) {
        throw new Exception("Doctor ID is required.");
    }

    $doctorId = (int)$_POST['doctor_id'];
    $staffId = trim($_POST['staff_id'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contactNumber = trim($_POST['phone'] ?? '');
    $currentProfilePic = trim($_POST['current_profile_pic'] ?? ''); // Full path from hidden input

    // Validate required fields
    if (empty($firstName) || empty($email) || empty($department) || empty($contactNumber)|| empty($dateOfBirth)) {
        throw new Exception("First name, email, phone, Date Of Birth and department are required.");
    }

    // Validate contact number if provided
    if ($contactNumber && !preg_match('/^[0-9+\- ]*$/', $contactNumber)) {
        throw new Exception("Invalid phone number format. Use digits, +, or - only.");
    }

    // Determine if profile picture should be updated
    $profilePicPath = $currentProfilePic ? basename($currentProfilePic) : ''; // Extract filename only (e.g., 'filename.jpg')
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
        $newFileName = 'doctor_' . $doctorId . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception("Failed to upload the file.");
        }

        $profilePicPath = $newFileName; // Update to new filename only
    } else {
        // If no new file is uploaded, keep the existing filename (or empty if none)
        $profilePicPath = $currentProfilePic ? basename($currentProfilePic) : '';
    }

    $stmt = $pdo->prepare("UPDATE doctors SET first_name = :first_name, last_name = :last_name, email = :email, date_of_birth = :date_of_birth, gender = :gender, department = :department, address = :address, contact_number = :contact_number, profile_pic = :profile_pic WHERE id = :id");
    $stmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $lastName ? $lastName : null, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_birth', $dateOfBirth ? $dateOfBirth : null, PDO::PARAM_STR);
    $stmt->bindValue(':gender', $gender ? $gender : null, PDO::PARAM_STR);
    $stmt->bindValue(':department', $department, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address ? $address : null, PDO::PARAM_STR);
    $stmt->bindValue(':contact_number', $contactNumber ? $contactNumber : null, PDO::PARAM_STR);
    $stmt->bindValue(':profile_pic', $profilePicPath ? $profilePicPath : null, PDO::PARAM_STR); // Only update if changed
    $stmt->bindValue(':id', $doctorId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        throw new Exception("No changes made or doctor not found.");
    }

    $fullProfilePicPath = $profilePicPath ? 'assets/img/' . $profilePicPath : '';
    $response = [
        'success' => true,
        'message' => 'Doctor updated successfully.',
        'profile_pic' => $fullProfilePicPath 
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>