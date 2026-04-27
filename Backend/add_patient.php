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
    // Get form data
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $insurance = trim($_POST['insurance'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $medicalHistory = trim($_POST['medical_history'] ?? '');

    // Validate required fields
    if (empty($firstName)) {
        throw new Exception("First Name is required.");
    }
    if (empty($email)) {
        throw new Exception("Email is required.");
    }
    if (empty($dateOfBirth)) {
        throw new Exception("Date of Birth is required.");
    }
    if (empty($contactNumber)) {
        throw new Exception("Phone is required.");
    }
    if (empty($medicalHistory)) {
        throw new Exception("Medical Issue is required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format.");
    }

    // Validate phone number format
    if (!preg_match('/^[0-9+\- ]*$/', $contactNumber)) {
        throw new Exception("Invalid phone number format. Use digits, +, or - only.");
    }

    // Validate gender (if provided)
    if (!empty($gender) && !in_array($gender, ['Male', 'Female'])) {
        throw new Exception("Invalid gender value.");
    }

    // Convert date_of_birth from DD/MM/YYYY to YYYY-MM-DD for the database
    $date = DateTime::createFromFormat('d/m/Y', $dateOfBirth);
    if ($date === false) {
        throw new Exception("Invalid date of birth format. Use DD/MM/YYYY.");
    }
    $dateOfBirth = $date->format('Y-m-d');

    // Check if the email is already in use
    $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetch()) {
        throw new Exception("Email is already in use by another patient.");
    }

    // Insert the new patient into the database
    $stmt = $pdo->prepare("INSERT INTO patients (first_name, last_name, email, date_of_birth, insurance, gender, address, contact_number, medical_history, created_at) VALUES (:first_name, :last_name, :email, :date_of_birth, :insurance, :gender, :address, :contact_number, :medical_history, NOW())");
    $stmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $lastName ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':date_of_birth', $dateOfBirth, PDO::PARAM_STR);
    $stmt->bindValue(':insurance', $insurance ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':gender', $gender ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address ?: null, PDO::PARAM_STR);
    $stmt->bindValue(':contact_number', $contactNumber, PDO::PARAM_STR);
    $stmt->bindValue(':medical_history', $medicalHistory, PDO::PARAM_STR);
    $stmt->execute();

    $response = [
        'success' => true,
        'message' => 'Patient added successfully.'
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