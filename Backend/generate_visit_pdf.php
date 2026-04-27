<?php
session_start();
require_once 'db_connect.php';

// Include TCPDF library
require_once 'tcpdf/tcpdf.php';

// Check if user is logged in and has 'Doctor' role
if (!isset($_SESSION['user_id']) || strcasecmp($_SESSION['role'], 'Doctor') !== 0) {
    die("Access denied. Only doctors can generate visit record PDFs.");
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
    die("Invalid visit record ID.");
}
$record_id = (int)$_GET['record_id'];

// Decryption setup (same key as in doctor_form.php)
$encryption_key = '0589121e755e38401cc2a3a7ed0a8ec9dc8c4db7e0f94ffc46623074b8f33525'; // 32-byte key

// Function to decrypt data
function decryptData($encrypted_data, $key) {
    if (empty($encrypted_data)) {
        return 'N/A';
    }
    $data = base64_decode($encrypted_data);
    if ($data === false) {
        return 'Error: Failed to decode encrypted data';
    }
    list($encrypted, $iv) = explode('::', $data, 2);
    $iv = base64_decode($iv);
    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', hex2bin($key), 0, $iv);
    return $decrypted !== false ? $decrypted : 'Error: Decryption failed';
}

try {
    // Fetch the visit record with patient and doctor details
    $stmt = $pdo->prepare("
        SELECT vr.patient_id, vr.doctor_id, vr.visit_date, vr.reason_for_visit, vr.notes_outcome, vr.created_at,
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
               CONCAT(d.first_name, ' ', d.last_name) AS doctor_name
        FROM visit_records vr
        JOIN patients p ON vr.patient_id = p.id
        JOIN doctors d ON vr.doctor_id = d.staff_id
        WHERE vr.id = ?
    ");
    $stmt->execute([$record_id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        die("Visit record not found.");
    }

    // Get patient_id for logging and fetching patient records
    $patient_id = $record['patient_id'];

    // Log the access to patient records in data_access_logs
    $stmt = $pdo->prepare("
        INSERT INTO data_access_logs (user_id, patient_id, access_time, action)
        VALUES (?, ?, NOW(), 'VIEW_PATIENT_RECORDS')
    ");
    $stmt->execute([$user_id, $patient_id]);

    // Fetch patient records for this patient
    $stmt = $pdo->prepare("
        SELECT id, submitted_at, medical_history_text, uploaded_files
        FROM patient_records
        WHERE patient_id = ?
        ORDER BY submitted_at DESC
    ");
    $stmt->execute([$patient_id]);
    $patient_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decrypt sensitive fields from visit record
    $reason_for_visit = decryptData($record['reason_for_visit'], $encryption_key);
    $notes_outcome = decryptData($record['notes_outcome'], $encryption_key);

    // Decrypt patient records fields
    foreach ($patient_records as &$pr) {
        $pr['medical_history_text'] = decryptData($pr['medical_history_text'], $encryption_key);
        // uploaded_files will be a link to download the decrypted file
    }
    unset($pr); // Unset the reference after the loop

    // Create new TCPDF instance
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('AFYA Hospital');
    $pdf->SetTitle('Visit Record Report');
    $pdf->SetSubject('Visit Record Details');
    $pdf->SetKeywords('Visit, Record, AFYA Hospital');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'AFYA Hospital', 'Visit Record Report', array(0,64,255), array(0,64,128));
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

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

    // Create HTML content for the PDF
    $html = '
    <h1 style="text-align: center;">Visit Record Report</h1>
    <h2>Patient Medical Records</h2>';

    // Add patient records (if any)
    if (!empty($patient_records)) {
        $html .= '
        <table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Submitted At</th>
                    <th>Medical History</th>
                    <th>Uploaded Files</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($patient_records as $pr) {
            $download_link = $pr['uploaded_files'] ? 
                '<a href="http://localhost/FINALPROJECT2025/Backend/download_patient_file.php?record_id=' . $pr['id'] . '">Download File</a>' : 
                'N/A';
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($pr['submitted_at']) . '</td>
                    <td>' . htmlspecialchars($pr['medical_history_text']) . '</td>
                    <td>' . $download_link . '</td>
                </tr>';
        }
        $html .= '
            </tbody>
        </table>';
    } else {
        $html .= '<p>No medical records found for this patient.</p>';
    }

    // Add visit record details
    $html .= '
    <h2 style="margin-top: 20px;">Visit Record Details</h2>
    <table border="0" cellpadding="5">
        <tr>
            <td><strong>Patient Name:</strong></td>
            <td>' . htmlspecialchars($record['patient_name']) . '</td>
        </tr>
        <tr>
            <td><strong>Doctor Name:</strong></td>
            <td>' . htmlspecialchars($record['doctor_name']) . '</td>
        </tr>
        <tr>
            <td><strong>Visit Date:</strong></td>
            <td>' . htmlspecialchars($record['visit_date']) . '</td>
        </tr>
        <tr>
            <td><strong>Reason for Visit:</strong></td>
            <td>' . htmlspecialchars($reason_for_visit) . '</td>
        </tr>
        <tr>
            <td><strong>Notes/Outcome:</strong></td>
            <td>' . htmlspecialchars($notes_outcome) . '</td>
        </tr>
        <tr>
            <td><strong>Created At:</strong></td>
            <td>' . htmlspecialchars($record['created_at']) . '</td>
        </tr>
    </table>';

    // Write HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Log the action in audit_logs (for PDF generation)
    $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp) VALUES (?, 'GENERATE_PDF', 'visit_records', ?, NOW())");
    $stmt->execute([$user_id, $record_id]);

    // Output the PDF
    $pdf->Output('visit_record_' . $record_id . '.pdf', 'D'); // 'D' forces download
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>