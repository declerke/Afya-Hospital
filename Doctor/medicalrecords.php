<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

$doctorId = $_SESSION['doctor_id'];
$records = [];

try {
    // Step 1: Fetch all patient_ids associated with the doctor's appointments
    $stmt = $pdo->prepare("
        SELECT DISTINCT patient_id
        FROM appointments
        WHERE doctor_id = ?
    ");
    $stmt->execute([$doctorId]);
    $patientIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($patientIds)) {
        // No appointments found for this doctor
        $records = [];
    } else {
        // Step 2: Fetch medical records for these patients
        $placeholders = implode(',', array_fill(0, count($patientIds), '?'));
        $stmt = $pdo->prepare("
            SELECT pr.id, pr.patient_id, pr.medical_history_text, pr.uploaded_files, pr.submitted_at,
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name
            FROM patient_records pr
            JOIN patients p ON pr.patient_id = p.id
            WHERE pr.patient_id IN ($placeholders)
            ORDER BY pr.submitted_at DESC
        ");
        $stmt->execute($patientIds);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Step 3: Process the uploaded_files to extract the file name
        foreach ($records as &$record) {
            if ($record['uploaded_files']) {
                // Extract the file name from the path (e.g., enc_1742842576_Zetech University fee statement.pdf.enc)
                $filePathParts = explode('/', $record['uploaded_files']);
                $fileName = end($filePathParts);
                // Remove the 'enc_' prefix and timestamp
                $fileNameParts = explode('_', $fileName, 3);
                $record['display_file_name'] = isset($fileNameParts[2]) ? $fileNameParts[2] : $fileName;
            } else {
                $record['display_file_name'] = 'N/A';
            }
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Error: Unable to fetch medical records.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Medical Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .medical-record-widget {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            padding: 15px;
        }
        .record-info-left {
            flex: 1;
            display: flex;
            align-items: center;
        }
        .record-info-right {
            margin-left: auto;
        }
        .patient-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .patient-icon i {
            font-size: 30px;
            color: #007bff;
        }
        .record-info-cont h4 {
            margin-bottom: 5px;
            font-size: 1.25rem;
        }
        .record-info-cont p {
            margin-bottom: 5px;
            color: #666;
        }
        .record-details p {
            margin-bottom: 5px;
        }
        .record-details i {
            margin-right: 5px;
            color: #007bff;
        }
        .record-booking a {
            display: block;
            width: 120px;
            text-align: center;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .medical-record-widget {
                flex-direction: column;
                align-items: flex-start;
            }
            .record-info-right {
                margin-top: 15px;
                margin-left: 0;
                width: 100%;
            }
            .record-booking a {
                width: 100%;
            }
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
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Medical Records</h4>
                    </div>
                    <div class="col-sm-8 col-9 text-right m-b-20">
                        <a href="add-medical-record.php" class="btn btn-primary btn-rounded float-right"><i class="fa fa-plus"></i> Add Record</a>
                        <input type="text" id="recordSearchInput" class="form-control float-right mr-2" placeholder="Search records..." style="width: 200px;">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php if (empty($records)): ?>
                            <div class="card">
                                <div class="card-body">
                                    <p>No medical records found for patients associated with your appointments.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($records as $record): ?>
                                <!-- Medical Record Card -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="medical-record-widget">
                                            <div class="record-info-left">
                                                <div class="patient-icon">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div class="record-info-cont">
                                                    <h4 class="patient-name"><?php echo htmlspecialchars($record['patient_name']); ?></h4>
                                                    <div class="record-details">
                                                        <p><i class="fas fa-notes-medical"></i> Medical History: [Encrypted Data]</p>
                                                        <p><i class="fas fa-file-alt"></i> Uploaded Files: <?php echo htmlspecialchars($record['display_file_name']); ?></p>
                                                        <p><i class="far fa-clock"></i> Submitted: <?php echo htmlspecialchars($record['submitted_at']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="record-info-right">
                                                <div class="record-booking">
                                                    <a class="btn btn-primary view-pro-btn" href="view-medical-record.php?id=<?php echo htmlspecialchars($record['id']); ?>">View</a>
                                                    <a class="btn btn-success apt-btn" href="download-medical-record.php?id=<?php echo htmlspecialchars($record['id']); ?>">Download</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Medical Record Card -->
                            <?php endforeach; ?>
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
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>