<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include '../Backend/db_connect.php';

// Fetch doctor details using doctor_id (doctors.id)
$doctorId = $_SESSION['doctor_id'];
$doctor = [];
try {
    $stmt = $pdo->prepare("
        SELECT profile_pic, staff_id, first_name, last_name, department, date_of_birth, gender, address, email, contact_number
        FROM doctors
        WHERE id = ?
    ");
    $stmt->execute([$doctorId]);
    $doctor = $stmt->fetch();

    if (!$doctor) {
        error_log("No doctor found for doctor_id: $doctorId");
        $doctor = [];
    } else {
        // Format date_of_birth to DD/MM/YYYY
        if (!empty($doctor['date_of_birth'])) {
            $dateOfBirth = DateTime::createFromFormat('Y-m-d', $doctor['date_of_birth']);
            $doctor['date_of_birth'] = $dateOfBirth ? $dateOfBirth->format('d/m/Y') : 'N/A';
        } else {
            $doctor['date_of_birth'] = 'N/A';
        }

        // Construct the full profile picture path
        if (!empty($doctor['profile_pic'])) {
            $doctor['profile_pic'] = 'assets/img/' . $doctor['profile_pic'];
            // Check if the image file exists; if not, use the default
            if (!file_exists($doctor['profile_pic'])) {
                $doctor['profile_pic'] = 'assets/img/user.jpg';
            }
        } else {
            $doctor['profile_pic'] = 'assets/img/user.jpg';
        }
    }
} catch (PDOException $e) {
    error_log("Error fetching doctor details: " . $e->getMessage());
    $doctor = [];
}

// Fetch education details using staff_id
$educationList = [];
if (!empty($doctor['staff_id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT institution, starting_date, complete_date, degree, subject
            FROM education_informations
            WHERE staff_id = ?
            ORDER BY complete_date DESC
        ");
        $stmt->execute([$doctor['staff_id']]);
        $educationList = $stmt->fetchAll();

        // Format dates
        foreach ($educationList as &$edu) {
            $edu['starting_date'] = (new DateTime($edu['starting_date']))->format('Y');
            $edu['complete_date'] = (new DateTime($edu['complete_date']))->format('Y');
        }
    } catch (PDOException $e) {
        error_log("Error fetching education details: " . $e->getMessage());
    }
}

// Fetch experience details using staff_id
$experienceList = [];
if (!empty($doctor['staff_id'])) {
    try {
        $stmt = $pdo->prepare("
            SELECT company_name, job_position, period_from, period_to
            FROM experience_informations
            WHERE staff_id = ?
            ORDER BY period_to DESC
        ");
        $stmt->execute([$doctor['staff_id']]);
        $experienceList = $stmt->fetchAll();

        // Format dates
        foreach ($experienceList as &$exp) {
            $exp['period_from'] = (new DateTime($exp['period_from']))->format('Y');
            $exp['period_to'] = $exp['period_to'] === '0000-00-00' ? 'Present' : (new DateTime($exp['period_to']))->format('Y');
        }
    } catch (PDOException $e) {
        error_log("Error fetching experience details: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - My Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
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
                        <span class="user-img"><img class="rounded-circle" src="<?php echo htmlspecialchars($doctor['profile_pic']); ?>" width="24" alt="Doctor"><span class="status online"></span></span>
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
                        <li><a href="medicalrecords.php"><i class="fas fa-file-medical"></i> <span>Medical Records</span></a></li>
                        <li><a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a></li>
                        <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
                        <li><a href="feedback.php"><i class="fa fa-comment"></i> <span>Feedback</span></a></li>
                        <li class="active"><a href="profile.php"><i class="fa fa-user"></i> <span>Profile</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title" id="page-title">My Profile</h4>
                    </div>
                    <div class="col-sm-5 col-6 text-right m-b-30">
                        <a href="edit-profile.php" class="btn btn-primary btn-rounded" id="edit-profile-btn"><i class="fa fa-plus"></i> Edit Profile</a>
                    </div>
                </div>
                <div class="card-box profile-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                    <div class="profile-img">
                                        <a href="#"><img class="avatar" id="profile-pic" src="<?php echo htmlspecialchars($doctor['profile_pic']); ?>" alt=""></a>
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 mb-0" id="user-name">Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?></h3>
                                                <small class="text-muted" id="user-role"><?php echo htmlspecialchars($doctor['department'] ?? 'Doctor'); ?></small>
                                                <div class="staff-id" id="staff-id">Staff ID: <?php echo htmlspecialchars($doctor['staff_id'] ?? 'N/A'); ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">
                                                <li>
                                                    <span class="title">Phone:</span>
                                                    <span class="text"><a href="#" id="user-phone"><?php echo htmlspecialchars($doctor['contact_number'] ?? 'N/A'); ?></a></span>
                                                </li>
                                                <li>
                                                    <span class="title">Email:</span>
                                                    <span class="text"><a href="#" id="user-email"><?php echo htmlspecialchars($doctor['email'] ?? 'N/A'); ?></a></span>
                                                </li>
                                                <li>
                                                    <span class="title">Birthday:</span>
                                                    <span class="text" id="user-birthday"><?php echo htmlspecialchars($doctor['date_of_birth']); ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Address:</span>
                                                    <span class="text" id="user-address"><?php echo htmlspecialchars($doctor['address'] ?? 'N/A'); ?></span>
                                                </li>
                                                <li>
                                                    <span class="title">Gender:</span>
                                                    <span class="text" id="user-gender"><?php echo htmlspecialchars($doctor['gender'] ?? 'N/A'); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    </div>
                </div>
                <div class="profile-tabs">
                    <ul class="nav nav-tabs nav-tabs-bottom">
                        <li class="nav-item"><a class="nav-link active" href="#about-cont" data-toggle="tab">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#bottom-tab2" data-toggle="tab">Profile</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="about-cont">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <h3 class="card-title">Education Information</h3>
                                        <div class="experience-box">
                                            <ul class="experience-list" id="education-list">
                                                <?php if (empty($educationList)): ?>
                                                    <li>No education information available.</li>
                                                <?php else: ?>
                                                    <?php foreach ($educationList as $edu): ?>
                                                        <li>
                                                            <div class="experience-user">
                                                                <div class="before-circle"></div>
                                                            </div>
                                                            <div class="experience-content">
                                                                <div class="timeline-content">
                                                                    <a href="#" class="name"><?php echo htmlspecialchars($edu['institution']); ?></a>
                                                                    <div><?php echo htmlspecialchars($edu['degree'] . ', ' . $edu['subject']); ?></div>
                                                                    <span class="time"><?php echo htmlspecialchars($edu['starting_date'] . ' - ' . $edu['complete_date']); ?></span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-box mb-0">
                                        <h3 class="card-title">Experience</h3>
                                        <div class="experience-box">
                                            <ul class="experience-list" id="experience-list">
                                                <?php if (empty($experienceList)): ?>
                                                    <li>No experience information available.</li>
                                                <?php else: ?>
                                                    <?php foreach ($experienceList as $exp): ?>
                                                        <li>
                                                            <div class="experience-user">
                                                                <div class="before-circle"></div>
                                                            </div>
                                                            <div class="experience-content">
                                                                <div class="timeline-content">
                                                                    <a href="#" class="name"><?php echo htmlspecialchars($exp['job_position'] . ' - ' . $exp['company_name']); ?></a>
                                                                    <span class="time"><?php echo htmlspecialchars($exp['period_from'] . ' - ' . $exp['period_to']); ?></span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="bottom-tab2">
                            Tab content 2
                        </div>
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