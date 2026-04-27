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
    $patientId  = isset($_POST['patient_id'])     ? (int)trim($_POST['patient_id'])       : 0;
    $firstName  = isset($_POST['first_name'])      ? trim($_POST['first_name'])             : '';
    $lastName   = isset($_POST['last_name'])       ? trim($_POST['last_name'])              : '';
    $email      = isset($_POST['email'])           ? trim($_POST['email'])                  : '';
    $dob        = isset($_POST['date_of_birth'])   ? trim($_POST['date_of_birth'])          : '';
    $gender     = isset($_POST['gender'])          ? trim($_POST['gender'])                 : '';
    $phone      = isset($_POST['contact_number'])  ? trim($_POST['contact_number'])         : '';
    $address    = isset($_POST['address'])         ? trim($_POST['address'])                : '';
    $insurance  = isset($_POST['insurance'])       ? trim($_POST['insurance'])              : '';

    if ($patientId <= 0) throw new Exception('Invalid patient ID.');
    if (empty($firstName))  throw new Exception('First name is required.');
    if (empty($email))      throw new Exception('Email is required.');

    // Convert DOB from DD/MM/YYYY to YYYY-MM-DD (nullable)
    $dobFormatted = null;
    if (!empty($dob)) {
        $dobDate = DateTime::createFromFormat('d/m/Y', $dob);
        if (!$dobDate || $dobDate->format('d/m/Y') !== $dob) {
            throw new Exception('Invalid date of birth format. Expected DD/MM/YYYY.');
        }
        $dobFormatted = $dobDate->format('Y-m-d');
    }

    $stmt = $pdo->prepare("
        UPDATE patients
        SET first_name = ?, last_name = ?, email = ?, date_of_birth = ?,
            gender = ?, contact_number = ?, address = ?, insurance = ?
        WHERE id = ?
    ");
    $stmt->execute([$firstName, $lastName, $email, $dobFormatted, $gender, $phone, $address, $insurance, $patientId]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Patient not found or no changes made.');
    }

    echo json_encode(['success' => true, 'message' => 'Patient updated successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
