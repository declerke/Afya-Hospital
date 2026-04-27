<?php
session_start();

// Include database connection
require_once 'db_connect.php';

// Check if user is logged in and has 'Patient' role
if (!isset($_SESSION['user_id']) || strcasecmp($_SESSION['role'], 'Patient') !== 0) {
    die("Access denied. Only patients can submit medical history.");
}

// Verify patient_id matches the logged-in user
$user_id = $_SESSION['user_id']; // From users.id
if (!isset($_SESSION['patient_id'])) {
    die("Error: Patient ID not set in session. Please log in again or contact support.");
}
$patient_id = $_SESSION['patient_id']; // From users.patient_id

try {
    // Validate patient_id against users and patients tables
    $stmt = $pdo->prepare("SELECT p.id FROM users u LEFT JOIN patients p ON u.patient_id = p.id WHERE u.id = ? AND u.patient_id = ?");
    $stmt->execute([$user_id, $patient_id]);
    if ($stmt->rowCount() === 0) {
        die("Invalid patient ID or user mismatch.");
    }

    // Encryption setup
    $encryption_key = '0589121e755e38401cc2a3a7ed0a8ec9dc8c4db7e0f94ffc46623074b8f33525'; // 32-byte key (replace with your generated key)
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $medical_history = $_POST['medical_history'];

        // Encrypt medical history
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted_history = openssl_encrypt(
            $medical_history,
            'aes-256-cbc',
            hex2bin($encryption_key),
            0,
            $iv
        );
        $encrypted_history = base64_encode($encrypted_history . '::' . base64_encode($iv)); // Store encrypted data with IV

        // Handle file uploads and encryption
        $file_paths = [];
        $upload_dir = "assets/fileuploads/patient_$patient_id/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Debug: Check if files are being received
        var_dump($_FILES); // Remove after debugging

        if (!empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['tmp_name'] as $index => $tmp_name) {
                if ($_FILES['documents']['error'][$index] === UPLOAD_ERR_OK) {
                    $file_name = $_FILES['documents']['name'][$index];
                    $original_file = $upload_dir . time() . "_" . $file_name;
                    if (move_uploaded_file($tmp_name, $original_file)) {
                        // Encrypt the file
                        $file_content = file_get_contents($original_file);
                        $iv_file = openssl_random_pseudo_bytes($iv_length);
                        $encrypted_content = openssl_encrypt(
                            $file_content,
                            'aes-256-cbc',
                            hex2bin($encryption_key),
                            0,
                            $iv_file
                        );
                        $encrypted_file = $upload_dir . 'enc_' . time() . "_" . $file_name . '.enc';
                        file_put_contents($encrypted_file, $encrypted_content . '::' . base64_encode($iv_file));
                        unlink($original_file); // Remove unencrypted file
                        $file_paths[] = $encrypted_file;
                    } else {
                        // Debug: Log if move_uploaded_file fails
                        error_log("Failed to move uploaded file: $original_file");
                    }
                } else {
                    // Debug: Log upload errors
                    error_log("Upload error for file $index: " . $_FILES['documents']['error'][$index]);
                }
            }
        } else {
            // Debug: Log if no files were uploaded
            error_log("No files uploaded.");
        }
        $file_paths_str = !empty($file_paths) ? implode(',', $file_paths) : NULL; // Explicitly set to NULL if no files

        // Debug: Check file paths before insertion
        var_dump($file_paths_str); // Remove after debugging

        // Insert into patient_records
        $stmt = $pdo->prepare("INSERT INTO patient_records (patient_id, medical_history_text, uploaded_files, submitted_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$patient_id, $encrypted_history, $file_paths_str]);
        $record_id = $pdo->lastInsertId();

        // Log the action in audit_logs
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp) VALUES (?, 'INSERT', 'patient_records', ?, NOW())");
        $stmt->execute([$user_id, $record_id]);

        echo "<script>alert('Medical history submitted successfully!'); window.location.href='patient_form.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Medical History Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        textarea, input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Submit Medical History</h2>
        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="medical_history">Medical History</label>
            <textarea id="medical_history" name="medical_history" rows="5" required></textarea>

            <label for="documents">Upload Documents (if any)</label>
            <input type="file" id="documents" name="documents[]" multiple>

            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        function validateForm() {
            const medicalHistory = document.getElementById('medical_history').value.trim();
            if (!medicalHistory) {
                alert('Please enter your medical history.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>