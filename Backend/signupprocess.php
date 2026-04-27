<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = ucfirst(strtolower(trim($_POST['firstName'] ?? '')));
    $last_name  = ucfirst(strtolower(trim($_POST['lastName'] ?? '')));
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role      = trim($_POST['role'] ?? '');
    $staff_id  = isset($_POST['staff_id']) ? trim($_POST['staff_id']) : NULL;

    if (empty($first_name) || empty($email) || empty($password)) {
        die("Error: Required fields are missing.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email address.");
    }

    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    if (strlen($password) < 8) {
        die("Error: Password must be at least 8 characters.");
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $pdo->rollBack();
        die("Error: Email is already registered in the system.");
    }

    $table = null;
    if ($role === 'hospital_staff') {
        $role  = 'Hospital Staff';
        $table = 'hospital_staffs';
    } elseif ($role === 'doctor') {
        $role  = 'Doctor';
        $table = 'doctors';
    } elseif ($role === 'nurse') {
        $role  = 'Nurse';
        $table = 'nurses';
    }

    if (in_array($role, ['Doctor', 'Nurse', 'Hospital Staff']) && !empty($staff_id) && $table) {
        $stmt = $pdo->prepare("SELECT first_name, last_name, email FROM $table WHERE staff_id = ?");
        $stmt->execute([$staff_id]);
        $staff = $stmt->fetch();

        if (!$staff
            || strtolower($staff['first_name']) !== strtolower($first_name)
            || strtolower($staff['last_name'])  !== strtolower($last_name)
            || strtolower($staff['email'])       !== $email) {
            $pdo->rollBack();
            die("Error: Invalid Staff ID or details do not match.");
        }
    }

    $patient_id = NULL;
    if (strtolower($role) === 'patient') {
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient && isset($patient['id'])) {
            $patient_id = $patient['id'];
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO patients (first_name, last_name, date_of_birth, gender, contact_number, email, address, medical_history, insurance, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
            );
            try {
                $stmt->execute([$first_name, $last_name, $date_of_birth, $gender, $phone, $email, $address, NULL, NULL]);
            } catch (PDOException $e) {
                $pdo->rollBack();
                die("Error: Failed to create patient record.");
            }
            $patient_id = $pdo->lastInsertId();
            if (!$patient_id) {
                $pdo->rollBack();
                die("Error: Failed to retrieve new patient ID.");
            }
        }
    }

    $stmt = $pdo->prepare(
        "INSERT INTO users (first_name, last_name, date_of_birth, gender, address, password_hash, role, email, contact_number, created_at, staff_id, patient_id)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)"
    );
    $success = $stmt->execute([
        $first_name, $last_name, $date_of_birth, $gender, $address,
        password_hash($password, PASSWORD_BCRYPT),
        $role, $email, $phone, $staff_id, $patient_id
    ]);

    if ($success) {
        $user_id = $pdo->lastInsertId();

        if (in_array($role, ['Doctor', 'Nurse', 'Hospital Staff'])) {
            if (empty($staff_id)) {
                $pdo->rollBack();
                die("Error: Staff ID is required for $role role.");
            }
            $stmt = $pdo->prepare("SELECT id FROM $table WHERE staff_id = ?");
            $stmt->execute([$staff_id]);
            if ($stmt->rowCount() === 0) {
                $pdo->rollBack();
                die("Error: Invalid Staff ID for $role.");
            }
        }

        $display_name = htmlspecialchars($first_name . ' ' . $last_name);
        $stmt = $pdo->prepare("INSERT INTO admin_notifications (user_id, message, notification_type) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, "New user signed up: $display_name", "signup"]);

        $pdo->commit();
        header("Location: loginpage.php?success=1");
        exit();
    } else {
        $pdo->rollBack();
        die("Error: Registration failed.");
    }
}
?>
