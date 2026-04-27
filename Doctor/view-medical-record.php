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

    // Step 6: Process and prepare uploaded files for download
    if ($record['uploaded_files']) {
        $files = explode(',', $record['uploaded_files']);
        foreach ($files as $index => $file) {
            $filePathParts = explode('/', $file);
            $fileName = end($filePathParts);
            $fileNameParts = explode('_', $fileName, 3);
            $displayFileName = isset($fileNameParts[2]) ? $fileNameParts[2] : $fileName;
            // Remove the '.enc' suffix for display
            $displayFileName = str_replace('.enc', '', $displayFileName);
            // Create a download link
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
    $stmt->execute([$userId, $record['patient_id'], 'VIEW_PATIENT_RECORDS']);

    // Step 8: Create a notification in admin_notifications
    $message = "Doctor (ID: $doctorId) viewed medical record (ID: $recordId) for patient ID {$record['patient_id']}.";
    $stmt = $pdo->prepare("
        INSERT INTO admin_notifications (user_id, message, notification_type, is_read, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $message, 'medical_record', 0]);

} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - View Medical Record</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .custom-record {
            margin-bottom: 20px;
        }
        .record-details {
            text-align: right;
        }
        .record-details h3 {
            color: #007bff;
        }
        .inv-logo {
            max-width: 150px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .record-info {
            margin-top: 20px;
        }
        .record-info h5 {
            color: #333;
            margin-bottom: 10px;
        }
        .text-muted {
            color: #666 !important;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <a href="doctordashboard.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>Afya Hospital</span>
                </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown d-none d-sm-block">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i> <span class="badge badge-pill bg-danger float-right" id="notification-badge">0</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header"><span>Notifications</span></div>
                        <div class="drop-scroll"><ul class="notification-list" id="notification-list"><li class="notification-message"><p class="text-center">Loading notifications...</p></li></ul></div>
                        <div class="topnav-dropdown-footer"><a href="notifications.php">View all Notifications</a></div>
                    </div>
                </li>
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img"><img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Doctor"><span class="status online"></span></span>
                        <span>Doctor</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php">My Profile</a>
                        <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                        <a class="dropdown-item" href="../Backend/loginpage.php">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                    <a class="dropdown-item" href="../Backend/loginpage.php">Logout</a>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li><a href="doctordashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                        <li><a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
                        <li><a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Schedule</span></a></li>
                        <li class="active"><a href="medicalrecords.php"><i class="fas fa-file-medical"></i> <span>Medical Records</span></a></li>
                        <li><a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a></li>
                        <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
                        <li><a href="feedback.php"><i class="fa fa-comment"></i> <span>Feedback</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-5 col-4">
                        <h4 class="page-title">Medical Record</h4>
                    </div>
                    <div class="col-sm-7 col-8 text-right m-b-30">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-white" onclick="window.print()"><i class="fa fa-print fa-lg"></i> Print</button>
                            <a href="download-medical-record.php?id=<?php echo htmlspecialchars($recordId); ?>" class="btn btn-white"><i class="fa fa-download"></i> Download PDF</a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row custom-record">
                                        <div class="col-6 col-sm-6 m-b-20">
                                            <img src="assets/img/logo.png" class="inv-logo" alt="Afya Hospital">
                                            <ul class="list-unstyled">
                                                <li>Afya Hospital</li>
                                                <li>123 Hospital Road,</li>
                                                <li>Nairobi, Kenya</li>
                                                <li>+254 712 345 678</li>
                                                <li>info@afyahospital.com</li>
                                            </ul>
                                        </div>
                                        <div class="col-6 col-sm-6 m-b-20">
                                            <div class="record-details">
                                                <h3 class="text-uppercase">Record #<?php echo htmlspecialchars($record['id']); ?></h3>
                                                <ul class="list-unstyled">
                                                    <li>Submitted: <span><?php echo htmlspecialchars($record['submitted_at']); ?></span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-lg-6 m-b-20">
                                            <h5>Patient Information:</h5>
                                            <ul class="list-unstyled">
                                                <li><h5><strong><?php echo htmlspecialchars($patient['name']); ?></strong></h5></li>
                                                <li><?php echo htmlspecialchars($patient['address'] ?: 'P.O. Box 12345-00100, Nairobi, Kenya'); ?></li>
                                                <li><?php echo htmlspecialchars($patient['contact_number'] ?: '+254 723 456 789'); ?></li>
                                                <li><a href="mailto:<?php echo htmlspecialchars($patient['email']); ?>"><?php echo htmlspecialchars($patient['email']); ?></a></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-6 col-lg-6 m-b-20">
                                            <div class="record-view">
                                                <span class="text-muted">Doctor Information:</span>
                                                <ul class="list-unstyled">
                                                    <li><h5><strong><?php echo htmlspecialchars($doctor['name']); ?></strong></h5></li>
                                                    <li>Specialty: <?php echo htmlspecialchars($doctor['department']); ?></li>
                                                    <li>Staff ID: <?php echo htmlspecialchars($doctor['staff_id']); ?></li>
                                                    <li>Email: <a href="mailto:<?php echo htmlspecialchars($doctor['email']); ?>"><?php echo htmlspecialchars($doctor['email']); ?></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Detail</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Medical History</td>
                                                    <td><?php echo htmlspecialchars($decryptedHistory); ?></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>Uploaded Files</td>
                                                    <td>
                                                        <?php if (empty($fileLinks)): ?>
                                                            No files uploaded.
                                                        <?php else: ?>
                                                            <ul>
                                                                <?php foreach ($fileLinks as $fileLink): ?>
                                                                    <li>
                                                                        <a href="download-file.php?record_id=<?php echo htmlspecialchars($recordId); ?>&file_index=<?php echo htmlspecialchars($fileLink['index']); ?>" target="_blank">
                                                                            <?php echo htmlspecialchars($fileLink['display_name']); ?>
                                                                        </a>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="record-info">
                                        <h5>Additional Notes</h5>
                                        <p class="text-muted">
                                            This medical record is confidential and intended for use by authorized medical personnel only. 
                                            For any inquiries, please contact Afya Hospital at +254 712 345 678 or email info@afyahospital.com.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>