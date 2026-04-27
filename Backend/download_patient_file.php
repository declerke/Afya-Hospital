<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in and has 'Doctor' role
if (!isset($_SESSION['user_id']) || strcasecmp($_SESSION['role'], 'Doctor') !== 0) {
    die("Access denied. Only doctors can download patient files.");
}

$user_id = $_SESSION['user_id'];
if (!isset($_SESSION['staff_id'])) {
    die("Error: Staff ID not set in session. Please log in again or contact support.");
}
$staff_id = $_SESSION['staff_id'];

// Validate staff_id exists in doctors table
$stmt = $pdo->prepare("SELECT id FROM doctors WHERE staff_id = ?");
$stmt->execute([$staff_id]);
if ($stmt->rowCount() === 0) {
    die("Invalid staff ID.");
}

// Check if record_id is provided
if (!isset($_GET['record_id']) || !is_numeric($_GET['record_id'])) {
    die("Invalid patient record ID.");
}
$record_id = (int)$_GET['record_id'];

// Decryption setup
$encryption_key = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6'; // 32-byte key

// Function to decrypt file content
function decryptFile($file_path, $key) {
    if (!file_exists($file_path)) {
        return false;
    }

    $encrypted_content = file_get_contents($file_path);
    if ($encrypted_content === false) {
        return false;
    }

    $data = base64_decode($encrypted_content);
    if ($data === false) {
        return false;
    }

    list($encrypted, $iv) = explode('::', $data, 2);
    $iv = base64_decode($iv);
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', hex2bin($key), 0, $iv);
    return $decrypted !== false ? $decrypted : false;
}

try {
    // Fetch the patient record to get the file path and patient_id
    $stmt = $pdo->prepare("
        SELECT patient_id, uploaded_files
        FROM patient_records
        WHERE id = ?
    ");
    $stmt->execute([$record_id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record || empty($record['uploaded_files'])) {
        die("Patient record or file not found.");
    }

    $patient_id = $record['patient_id'];
    $file_path = $record['uploaded_files'];

    // Validate file path to prevent directory traversal
    $base_dir = realpath('assets/fileuploads');
    $real_file_path = realpath($file_path);
    if ($real_file_path === false || strpos($real_file_path, $base_dir) !== 0) {
        die("Invalid file path.");
    }

    // Log the access to the file in data_access_logs
    $stmt = $pdo->prepare("
        INSERT INTO data_access_logs (user_id, patient_id, access_time, action)
        VALUES (?, ?, NOW(), 'DOWNLOAD_PATIENT_FILE')
    ");
    $stmt->execute([$user_id, $patient_id]);

    // Decrypt the file
    $decrypted_content = decryptFile($file_path, $encryption_key);
    if ($decrypted_content === false) {
        die("Error: Failed to decrypt the file.");
    }

    // Extract the original filename from the encrypted path
    $filename = basename($file_path); // e.g., enc_1742128276_Real-time-software-systems-assgnment 1.pdf.enc
    $original_filename = preg_replace('/^enc_\d+_(.*)\.enc$/', '$1', $filename); // e.g., Real-time-software-systems-assgnment 1.pdf

    // Serve the decrypted file as a download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $original_filename . '"');
    header('Content-Length: ' . strlen($decrypted_content));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $decrypted_content;
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>