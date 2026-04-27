<?php
session_start();
require_once '../Backend/db_connect.php'; // Linking to your db_connect.php in Backend folder

// Check if patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../Backend/loginpage.php"); // Redirect to login if not a patient
    exit();
}

// Initialize variables
$patient_id = $_SESSION['patient_id']; // From session (set in login)
$patient_name = '';
$department = '';
$doctor_id = '';
$appointment_date = '';
$appointment_time = '';
$error_message = '';
$success_message = '';

// Fetch patient name from users table
$stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
if ($patient) {
    $patient_name = $patient['full_name'];
}

// Fetch departments from doctors table (distinct values)
$dept_sql = "SELECT DISTINCT department FROM doctors ORDER BY department";
$stmt = $pdo->query($dept_sql);
$departments = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch doctors based on selected department (if any)
$doctors = [];
if (!empty($_POST['department'])) {
    $department = filter_input(INPUT_POST, 'department', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $doctors_sql = "SELECT id AS doctor_id, first_name, last_name, department 
                    FROM doctors 
                    WHERE department = ? 
                    ORDER BY last_name, first_name";
    $stmt = $pdo->prepare($doctors_sql);
    $stmt->execute([$department]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $doctors[$row['doctor_id']] = $row['first_name'] . ' ' . $row['last_name'] . ' (' . $row['department'] . ')';
    }
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['department'])) {
    $department = filter_input(INPUT_POST, 'department', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $doctor_id = filter_input(INPUT_POST, 'doctor_id', FILTER_SANITIZE_NUMBER_INT);
    $appointment_date = filter_input(INPUT_POST, 'appointment_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $appointment_time = filter_input(INPUT_POST, 'appointment_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validate input
    if (empty($department)) {
        $error_message = "Please select a department.";
    } elseif (empty($doctor_id)) {
        $error_message = "Please select a doctor.";
    } elseif (!isset($doctors[$doctor_id])) {
        $error_message = "Invalid doctor selected.";
    } elseif (empty($appointment_date)) {
        $error_message = "Appointment date is required.";
    } elseif (empty($appointment_time)) {
        $error_message = "Appointment time is required.";
    } else {
        // Insert appointment record
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, modified_by, created_at)
                VALUES (?, ?, ?, ?, 'Scheduled', 'Patient', NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$patient_id, $doctor_id, $appointment_date, $appointment_time]);

        if ($stmt->rowCount() > 0) {
            $newId = $pdo->lastInsertId();
            $displayId = 'APT' . str_pad($newId, 4, '0', STR_PAD_LEFT);
            $pdo->prepare("UPDATE appointments SET appointment_id = ? WHERE id = ?")->execute([$displayId, $newId]);
            $success_message = "Appointment scheduled successfully!";
            $department = $doctor_id = $appointment_date = $appointment_time = ''; // Reset fields
            $doctors = []; // Clear doctors list
        } else {
            $error_message = "Error scheduling appointment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Afya Hospital</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f8f9fa;
            color: #343a40;
            line-height: 1.6;
        }
        .nav-links {
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .nav-links a {
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
            margin-right: 15px;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #0056b3;
        }
        .container {
            max-width: 700px;
            margin: 30px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        h2 {
            text-align: center;
            color: #007bff;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #1a3c6d;
        }
        input[type="text"],
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="text"]:disabled {
            background-color: #e9ecef;
            color: #6c757d;
        }
        select {
            appearance: none;
            background: url('data:image/svg+xml;charset=UTF-8,%3csvg fill="%23343a40" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"%3e%3cpath d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/%3e%3c/svg%3e') no-repeat right 1rem center;
            background-size: 1rem;
            cursor: pointer;
        }
        select:invalid {
            color: #6c757d;
        }
        select option {
            color: #343a40;
        }
        input:focus, select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            outline: none;
        }
        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 10px;
            text-align: center;
        }
        .success {
            color: #28a745;
            font-size: 0.9rem;
            margin-top: 10px;
            text-align: center;
        }
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        @media (max-width: 600px) {
            .nav-links {
                padding: 15px;
            }
            .nav-links a {
                display: inline-block;
                margin-bottom: 10px;
            }
            .container {
                margin: 20px;
                padding: 20px;
            }
            h2 {
                font-size: 1.5rem;
            }
            button[type="submit"] {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="index.html">Home</a> / 
        <a href="../Backend/loginpage.php">Log out</a>
    </div>

    <div class="container">
        <h2>Book a Medical Appointment</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="appointmentForm">
            <div class="form-group">
                <label for="patient_name">Patient Name:</label>
                <input type="text" id="patient_name" name="patient_name" value="<?php echo htmlspecialchars($patient_name); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="department">Department:</label>
                <select id="department" name="department" required>
                    <option value="" disabled selected>Select a Department</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo htmlspecialchars($dept); ?>" <?php if ($department === $dept) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($dept); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="doctor_id">Select Doctor:</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="" disabled selected>Select a Doctor</option>
                    <?php foreach ($doctors as $id => $name): ?>
                        <option value="<?php echo htmlspecialchars($id); ?>" <?php if ($doctor_id == $id) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="appointment_date">Appointment Date:</label>
                <input type="date" id="appointment_date" name="appointment_date" value="<?php echo htmlspecialchars($appointment_date); ?>" required>
            </div>

            <div class="form-group">
                <label for="appointment_time">Appointment Time:</label>
                <input type="time" id="appointment_time" name="appointment_time" value="<?php echo htmlspecialchars($appointment_time); ?>" required>
            </div>

            <button type="submit">Book Appointment</button>
        </form>
    </div>

    <script>
        document.getElementById('department').addEventListener('change', function() {
            document.getElementById('appointmentForm').submit(); // Auto-submit to refresh doctor options
        });
    </script>
</body>
</html>