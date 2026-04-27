<?php
include 'db_connect.php';

// Start the session
session_start();

try {
    // Enable error reporting for debugging    // Get the logged-in user's ID from the session
    $loggedInUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    if ($loggedInUserId <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'User not logged in or invalid session']);
        exit;
    }

    // Get form data
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $staffId = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : null;
    $patientId = isset($_POST['patient_id']) ? trim($_POST['patient_id']) : null;
    $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $contactNumber = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $birthDate = isset($_POST['birth_date']) ? trim($_POST['birth_date']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';

    // Validate that the user is editing their own profile
    if ($userId !== $loggedInUserId) {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'You can only edit your own profile']);
        exit;
    }

    // Log incoming data
    // Validate required fields
    if ($userId <= 0 || empty($role) || empty($firstName) || empty($lastName) || empty($email) || empty($contactNumber) || empty($address) || empty($birthDate) || empty($gender)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    // Validate birth date format (YYYY-MM-DD)
    $dateTime = DateTime::createFromFormat('Y-m-d', $birthDate);
    if (!$dateTime || $dateTime->format('Y-m-d') !== $birthDate) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid birth date format: ' . $birthDate]);
        exit;
    }

    // Handle profile picture upload
    $profilePictureFileName = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.']);
            exit;
        }

        // Generate a unique file name
        $fileExtension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;

        // Delete the old profile picture if it exists
        $oldPictureStmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
        $oldPictureStmt->execute([$userId]);
        $oldPicture = $oldPictureStmt->fetchColumn();
        if ($oldPicture && file_exists($uploadDir . $oldPicture)) {
            unlink($uploadDir . $oldPicture);
        }

        // Upload the new file
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath)) {
            $profilePictureFileName = $fileName;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload profile picture']);
            exit;
        }
    }

    // Update the users table
    $updateUserStmt = $pdo->prepare("
        UPDATE users
        SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, date_of_birth = ?, gender = ?, profile_pic = COALESCE(?, profile_pic)
        WHERE id = ?
    ");
    $updateUserStmt->execute([$firstName, $lastName, $email, $contactNumber, $address, $birthDate, $gender, $profilePictureFileName, $userId]);

    // Update the role-specific table
    switch ($role) {
        case 'Admin':
            if ($staffId) {
                $updateStmt = $pdo->prepare("
                    UPDATE admins
                    SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, date_of_birth = ?, gender = ?, profile_pic = COALESCE(?, profile_pic)
                    WHERE staff_id = ?
                ");
                $updateStmt->execute([$firstName, $lastName, $email, $contactNumber, $address, $birthDate, ucfirst($gender), $profilePictureFileName, $staffId]);
            }
            break;

        case 'Doctor':
            $updateStmt = $pdo->prepare("
                UPDATE doctors
                SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, date_of_birth = ?, gender = ?, profile_pic = COALESCE(?, profile_pic)
                WHERE id = ?
            ");
            $updateStmt->execute([$firstName, $lastName, $email, $contactNumber, $address, $birthDate, $gender, $profilePictureFileName, $userId]);
            break;

        case 'Nurse':
            $updateStmt = $pdo->prepare("
                UPDATE nurses
                SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, date_of_birth = ?, gender = ?, profile_pic = COALESCE(?, profile_pic)
                WHERE id = ?
            ");
            $updateStmt->execute([$firstName, $lastName, $email, $contactNumber, $address, $birthDate, $gender, $profilePictureFileName, $userId]);
            break;

        case 'Receptionist':
            $updateStmt = $pdo->prepare("
                UPDATE receptionists
                SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, date_of_birth = ?, gender = ?, profile_pic = COALESCE(?, profile_pic)
                WHERE id = ?
            ");
            $updateStmt->execute([$firstName, $lastName, $email, $contactNumber, $address, $birthDate, $gender, $profilePictureFileName, $userId]);
            break;

        case 'Patient':
            if ($patientId) {
                $updateStmt = $pdo->prepare("
                    UPDATE patients
                    SET first_name = ?, last_name = ?, email = ?, contact_number = ?, address = ?, date_of_birth = ?, gender = ?, profile_pic = COALESCE(?, profile_pic)
                    WHERE patient_id = ?
                ");
                $updateStmt->execute([$firstName, $lastName, $email, $contactNumber, $address, $birthDate, $gender, $profilePictureFileName, $patientId]);
            }
            break;

        default:
            // If the role doesn't match any known role, just update the users table
            break;
    }

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    error_log("PDOException in update-adminprofile.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("General Exception in update-adminprofile.php: " . $e->getMessage());
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>