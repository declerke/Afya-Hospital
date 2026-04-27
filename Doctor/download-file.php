<?php
session_start();
include 'db_connect.php';

// Validate that the user is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

$doctorId = $_SESSION['doctor_id'];
$recordId = isset($_GET['record_id']) ? (int)$_GET['record_id'] : 0;
$fileIndex = isset($_GET['file_index']) ? (int)$_GET['file_index'] : -1;

try {
    // Step 1: Fetch the medical record
    $stmt = $pdo->prepare("
        SELECT patient_id, uploaded_files
        FROM patient_records 
        WHERE id = ?
    ");
    $stmt->execute([$recordId]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        throw new Exception("Medical record not found.");
    }

    // Step 2: Verify that the doctor has an appointment with this patient
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM appointments 
        WHERE doctor_id = ? AND patient_id = ?
    ");
    $stmt->execute([$doctorId, $record['patient_id']]);
    $hasAppointment = $stmt->fetchColumn();

    if ($hasAppointment == 0) {
        throw new Exception("You do not have permission to access this file.");
    }

    // Step 3: Log the action in data_access_logs
    $stmt = $pdo->prepare("
        INSERT INTO data_access_logs (user_id, patient_id, access_time, action)
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt->execute([$_SESSION['user_id'], $record['patient_id'], 'DOWNLOAD_PATIENT_FILE']);

    // Step 4: Get the file path
    if (!$record['uploaded_files']) {
        throw new Exception("No files found for this record.");
    }

    $files = explode(',', $record['uploaded_files']);
    if (!isset($files[$fileIndex])) {
        throw new Exception("Invalid file index.");
    }

    $filePath = $files[$fileIndex];
    if (!file_exists($filePath)) {
        throw new Exception("File not found on server.");
    }

    // Step 5: Decrypt the file
    $encryption_key = '0589121e755e38401cc2a3a7ed0a8ec9dc8c4db7e0f94ffc46623074b8f33525';
    $fileContent = file_get_contents($filePath);
    list($encryptedContent, $iv) = explode('::', $fileContent, 2);
    $iv = base64_decode($iv);
    $decryptedContent = openssl_decrypt(
        $encryptedContent,
        'aes-256-cbc',
        hex2bin($encryption_key),
        0,
        $iv
    );

    if ($decryptedContent === false) {
        throw new Exception("Failed to decrypt the file.");
    }

    // Step 6: Extract the original file name (without 'enc_' prefix and timestamp)
    $filePathParts = explode('/', $filePath);
    $fileName = end($filePathParts);
    $fileNameParts = explode('_', $fileName, 3);
    $originalFileName = isset($fileNameParts[2]) ? str_replace('.enc', '', $fileNameParts[2]) : $fileName;

    // Step 7: Force download of the decrypted file
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $originalFileName . '"');
    header('Content-Length: ' . strlen($decryptedContent));
    echo $decryptedContent;
    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>