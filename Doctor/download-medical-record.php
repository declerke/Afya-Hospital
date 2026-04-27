<?php
session_start();
include 'db_connect.php';

// Validate that the user is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

$doctorId = $_SESSION['doctor_id'];
$userId = $_SESSION['user_id'];
$recordId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$record = null;
$patient = null;
$doctor = null;
$decryptedHistory = 'N/A';
$fileLinks = [];

try {
    // Step 1: Fetch the medical record
    $stmt = $pdo->prepare("
        SELECT pr.id, pr.patient_id, pr.medical_history_text, pr.uploaded_files, pr.submitted_at
        FROM patient_records pr
        WHERE pr.id = ?
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
        throw new Exception("You do not have permission to view this patient's medical record.");
    }

    // Step 3: Fetch patient details
    $stmt = $pdo->prepare("
        SELECT CONCAT(first_name, ' ', last_name) AS name, email, contact_number, address
        FROM patients 
        WHERE id = ?
    ");
    $stmt->execute([$record['patient_id']]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        throw new Exception("Patient not found.");
    }

    // Step 4: Fetch the logged-in doctor's details
    $stmt = $pdo->prepare("
        SELECT CONCAT(first_name, ' ', last_name) AS name, email, department, staff_id
        FROM doctors 
        WHERE id = ?
    ");
    $stmt->execute([$doctorId]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctor) {
        throw new Exception("Doctor not found.");
    }

    // Step 5: Decrypt the medical history
    $encryption_key = '0589121e755e38401cc2a3a7ed0a8ec9dc8c4db7e0f94ffc46623074b8f33525';
    if ($record['medical_history_text']) {
        $encryptedData = base64_decode($record['medical_history_text']);
        list($encryptedHistory, $iv) = explode('::', $encryptedData, 2);
        $iv = base64_decode($iv);
        $decryptedHistory = openssl_decrypt(
            $encryptedHistory,
            'aes-256-cbc',
            hex2bin($encryption_key),
            0,
            $iv
        );
        if ($decryptedHistory === false) {
            throw new Exception("Failed to decrypt medical history.");
        }
    }

    // Step 6: Process and prepare uploaded files for download links
    if ($record['uploaded_files']) {
        $files = explode(',', $record['uploaded_files']);
        foreach ($files as $index => $file) {
            $filePathParts = explode('/', $file);
            $fileName = end($filePathParts);
            $fileNameParts = explode('_', $fileName, 3);
            $displayFileName = isset($fileNameParts[2]) ? $fileNameParts[2] : $fileName;
            $displayFileName = str_replace('.enc', '', $displayFileName);
            $fileLinks[] = [
                'display_name' => $displayFileName,
                'path' => $file,
                'index' => $index
            ];
        }
    }

    // Step 7: Log the action in data_access_logs
    $stmt = $pdo->prepare("
        INSERT INTO data_access_logs (user_id, patient_id, access_time, action)
        VALUES (?, ?, NOW(), ?)
    ");
    $stmt->execute([$userId, $record['patient_id'], 'DOWNLOAD_MEDICAL_RECORD']);

    // Step 8: Generate the PDF using TCPDF
    require_once 'tcpdf/tcpdf.php';

    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Afya Hospital');
    $pdf->SetTitle('Medical Record #' . $record['id']);
    $pdf->SetSubject('Medical Record');
    $pdf->SetKeywords('Medical, Record, Afya Hospital');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'Afya Hospital', "123 Hospital Road, Nairobi, Kenya\n+254 712 345 678\ninfo@afyahospital.com");

    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Build the HTML content for the PDF
    $html = '
    <h1>Medical Record #' . htmlspecialchars($record['id']) . '</h1>
    <p><strong>Submitted:</strong> ' . htmlspecialchars($record['submitted_at']) . '</p>
    
    <h2>Patient Information</h2>
    <p><strong>Name:</strong> ' . htmlspecialchars($patient['name']) . '</p>
    <p><strong>Address:</strong> ' . htmlspecialchars($patient['address'] ?: 'P.O. Box 12345-00100, Nairobi, Kenya') . '</p>
    <p><strong>Contact Number:</strong> ' . htmlspecialchars($patient['contact_number'] ?: '+254 723 456 789') . '</p>
    <p><strong>Email:</strong> <a href="mailto:' . htmlspecialchars($patient['email']) . '">' . htmlspecialchars($patient['email']) . '</a></p>
    
    <h2>Doctor Information</h2>
    <p><strong>Name:</strong> ' . htmlspecialchars($doctor['name']) . '</p>
    <p><strong>Specialty:</strong> ' . htmlspecialchars($doctor['department']) . '</p>
    <p><strong>Staff ID:</strong> ' . htmlspecialchars($doctor['staff_id']) . '</p>
    <p><strong>Email:</strong> <a href="mailto:' . htmlspecialchars($doctor['email']) . '">' . htmlspecialchars($doctor['email']) . '</a></p>
    
    <h2>Medical Record Details</h2>
    <table border="1" cellpadding="4">
        <tr>
            <th>#</th>
            <th>Detail</th>
            <th>Description</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Medical History</td>
            <td>' . htmlspecialchars($decryptedHistory) . '</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Uploaded Files</td>
            <td>';

    if (empty($fileLinks)) {
        $html .= 'No files uploaded.';
    } else {
        $html .= '<ul>';
        foreach ($fileLinks as $fileLink) {
            // Construct the full URL for the download link
            $baseUrl = 'http://localhost/FINALPROJECT2025/Doctor/'; // Adjust this to your server's base URL
            $downloadUrl = $baseUrl . 'download-file.php?record_id=' . htmlspecialchars($recordId) . '&file_index=' . htmlspecialchars($fileLink['index']);
            $html .= '<li><a href="' . $downloadUrl . '">' . htmlspecialchars($fileLink['display_name']) . '</a></li>';
        }
        $html .= '</ul>';
    }

    $html .= '
            </td>
        </tr>
    </table>
    
    <h2>Additional Notes</h2>
    <p>This medical record is confidential and intended for use by authorized medical personnel only. For any inquiries, please contact Afya Hospital at +254 712 345 678 or email info@afyahospital.com.</p>';

    // Write the HTML content to the PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('medical_record_' . $record['id'] . '.pdf', 'D');

    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>