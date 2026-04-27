<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include 'db_connect.php';

$doctorId = $_SESSION['doctor_id'];

// Fetch all feedback entries with patient names
try {
    $query = "
        SELECT f.patient_id, f.feedback_text, f.rating, f.submitted_at, 
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM feedback f
        LEFT JOIN patients p ON f.patient_id = p.id
        ORDER BY f.submitted_at DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $feedbackEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $feedbackEntries = [];
    error_log("Error fetching feedback: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Feedback</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                        <li><a href="medicalrecords.php"><i class="fas fa-file-medical"></i> <span>Medical Records</span></a></li>
                        <li><a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a></li>
                        <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
                        <li class="active"><a href="feedback.php"><i class="fa fa-comment"></i> <span>Feedback</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-4 col-3">
                        <h4 class="page-title">Feedback</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-border table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Feedback</th>
                                        <th>Rating</th>
                                        <th>Submitted At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($feedbackEntries)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No feedback found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($feedbackEntries as $feedback): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($feedback['patient_name'] ?? 'Unknown Patient'); ?></td>
                                                <td><?php echo htmlspecialchars($feedback['feedback_text']); ?></td>
                                                <td><?php echo htmlspecialchars($feedback['rating']); ?></td>
                                                <td><?php echo htmlspecialchars($feedback['submitted_at']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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