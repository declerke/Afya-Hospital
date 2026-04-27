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
    // User details
    $first_name = "Ian";
    $last_name = "Mboya";
    $date_of_birth = "2003-05-21";
    $gender = "Male";
    $address = "0100, Nairobi";
    $password = "12345678"; 
    $role = "Admin";
    $email = "ianmboya@gmail.com";
    $contact_number = "0712346540";
    $staff_id = "AD-476321";
    $created_at = date("Y-m-d H:i:s"); 

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $sql = "INSERT INTO users (first_name, last_name, date_of_birth, gender, address, password_hash, role, email, contact_number, created_at, staff_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$first_name, $last_name, $date_of_birth, $gender, $address, $password_hash, $role, $email, $contact_number, $created_at, $staff_id]);

    echo "Admin user added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
