<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header('Location: ../Backend/loginpage.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid record ID.");
}

$record_id = (int)$_GET['id'];
$patient_id = (int)$_SESSION['patient_id'];

try {
    // Fetch record and verify it belongs to the logged-in patient
    $stmt = $pdo->prepare("
        SELECT pr.* FROM patient_records pr
        WHERE pr.id = ? AND pr.patient_id = ?
    ");
    $stmt->execute([$record_id, $patient_id]);
    $record = $stmt->fetch();

    if (!$record) {
        die("Record not found or access denied.");
    }

    if (empty($record['uploaded_files']) || !file_exists($record['uploaded_files'])) {
        die("File not found.");
    }

    // Log the access
    $pdo->prepare("INSERT INTO data_access_logs (user_id, patient_id, action) VALUES (?, ?, 'DOWNLOAD_PATIENT_FILE')")
        ->execute([$_SESSION['user_id'], $patient_id]);

} catch (PDOException $e) {
    die("Error retrieving record.");
}

$encrypted_content = file_get_contents($record['uploaded_files']);
$encryption_key = getenv('ENCRYPTION_KEY') ?: 'default_encryption_key';
$decrypted_content = openssl_decrypt(
    $encrypted_content, 'AES-256-CBC', $encryption_key, 0,
    substr(hash('sha256', $encryption_key), 0, 16)
);

$filename = basename($record['uploaded_files']);
$original_filename = preg_replace('/^enc_\d+_(.+)\.enc$/', '$1', $filename);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $original_filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($decrypted_content));

echo $decrypted_content;
exit;
?>
