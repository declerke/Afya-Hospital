<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}
require_once '../Backend/db_connect.php';

// Fetch doctor's name and profile pic
$doctor_display_name = 'Doctor';
$doctor_profile_pic  = 'assets/img/user.jpg';
try {
    $stmt = $pdo->prepare("SELECT CONCAT(first_name,' ',last_name) AS name, profile_pic FROM doctors WHERE id = ?");
    $stmt->execute([$_SESSION['doctor_id']]);
    $doc = $stmt->fetch();
    if ($doc) {
        $doctor_display_name = htmlspecialchars($doc['name']);
        if (!empty($doc['profile_pic']) && file_exists(__DIR__ . '/assets/img/' . $doc['profile_pic'])) {
            $doctor_profile_pic = 'assets/img/' . htmlspecialchars($doc['profile_pic']);
        }
    }
} catch (PDOException $e) {}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
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
                        <i class="fa fa-bell-o"></i> 
                        <span class="badge badge-pill bg-danger float-right" id="notification-badge">0</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span>Notifications</span>
                        </div>
                        <div class="drop-scroll">
                            <ul class="notification-list" id="notification-list">
                                <li class="notification-message">
                                    <p class="text-center">Loading notifications...</p>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="notifications.php">View all Notifications</a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="<?php echo $doctor_profile_pic; ?>" width="24" alt="Doctor">
                            <span class="status online"></span>
                        </span>
                        <span><?php echo $doctor_display_name; ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="profile.php">Edit Profile</a>
                    <a class="dropdown-item" href="profile.php">Settings</a>
                    <a class="dropdown-item" href="loginpage.php">Logout</a>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li class="active">
                            <a href="doctordashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
                        <li>
                            <a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a>
                        </li>
                        <li>
                            <a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Schedule</span></a>
                        </li>
                        <li>
                            <a href="medicalrecords.php"><i class="fas fa-file-medical"></i> <span>Medical Records</span></a>
                        </li>
                        <li>
                            <a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Dashboard Wrapper -->
        <div class="page-wrapper">
            <div class="content">
                <!-- 1st Row: Doctor-Specific Widgets -->
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg1"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3 class="appointment-count">Loading...</h3>
                                <span class="widget-title1">Appointments <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg2"><i class="fa fa-calendar-check-o"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3 class="schedule-count">Loading...</h3>
                                <span class="widget-title2">My Schedule <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg3"><i class="fas fa-file-medical"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3 class="records-count">Loading...</h3>
                                <span class="widget-title3">Records <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
                        <div class="dash-widget">
                            <span class="dash-widget-bg4"><i class="fa fa-money"></i></span>
                            <div class="dash-widget-info text-right">
                                <h3 class="billing-count">Loading...</h3>
                                <span class="widget-title4">Billing <i class="fa fa-check" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2nd Row: Appointments and Schedule -->
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">Upcoming Appointments</h4>
                                <a href="appointments.php" class="btn btn-primary float-right">View All</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="upcoming-appointments-table">
                                        <thead>
                                            <tr>
                                                <th>Patient Name</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">My Schedule</h4>
                                <a href="schedule.php" class="btn btn-primary float-right">View Full</a>
                            </div>
                            <div class="card-body">
                                <ul class="contact-list" id="schedule-list">
                                    <li class="notification-message">
                                        <p class="text-center">Loading schedule...</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3rd Row: Medical Records and Billing -->
                <div class="row">
                    
                <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">Recent Billing</h4>
                                <a href="billing.php" class="btn btn-primary float-right">View All</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table mb-0" id="billing-table">
                                        <thead>
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Patient</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block">Recent Medical Records</h4>
                                <a href="medicalrecords.php" class="btn btn-primary float-right">View All</a>
                            </div>
                            <div class="card-body">
                                <ul class="contact-list" id="medical-records-list">
                                    <li class="notification-message">
                                        <p class="text-center">Loading medical records...</p>
                                    </li>
                                </ul>
                            </div>
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
    <script src="assets/js/doctor-dashboard.js"></script>
    <script>
    window.doctorId = <?php echo json_encode($_SESSION['doctor_id']); ?>;
</script>
</body>
</html>