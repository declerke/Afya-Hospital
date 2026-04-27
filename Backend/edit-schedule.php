<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: loginpage.php'); exit();
}
include 'db_connect.php';

// Get the schedule_id from the URL
$schedule_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the schedule data
$schedule = null;
$doctors = [];
try {
    // Fetch schedule details
    $stmt = $pdo->prepare("
        SELECT ds.*, CONCAT(d.first_name, ' ', d.last_name) AS doctor_name
        FROM doctor_schedule ds
        LEFT JOIN doctors d ON ds.doctor_id = d.id
        WHERE ds.id = ?
    ");
    $stmt->execute([$schedule_id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        die("Schedule not found.");
    }

    // Fetch all doctors for the dropdown
    $stmt = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM doctors ORDER BY first_name");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <!-- Header section -->
        <div class="header">
            <div class="header-left">
                <a href="admindashboard.php" class="logo">
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
                            <a href="activities.php">View all Notifications</a>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                            <span class="status online"></span>
                        </span>
                        <span>Admin</span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile.php">My Profile</a>
                        <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                    <a class="dropdown-item" href="profile.php">Settings</a>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <!-- Sidebar menu -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li class="active">
                            <a href="admindashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                        </li>
                        <li>
                            <a href="doctors.php"><i class="fa fa-user-md"></i> <span>Doctors</span></a>
                        </li>
                        <li>
                            <a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a>
                        </li>
                        <li>
                            <a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a>
                        </li>
                        <li>
                            <a href="schedule.php"><i class="fa fa-calendar-check-o"></i> <span>Doctor Schedule</span></a>
                        </li>
                        <li>
                            <a href="auditlogs.php"><i class="fa fa-clipboard"></i> <span>Audit Logs</span></a>
                        </li>
                        <li>
                            <a href="chatbotlogs.php"><i class="fas fa-robot"></i> <span>Chatbot Logs</span></a>
                        </li>
                        <li>
                            <a href="employees.php"><i class="fa fa-user"></i> <span>Employees</span></a>
                        </li>
                        <li>
                            <a href="dataaccesslogs.php"><i class="fa fa-database"></i> <span>Data Access Logs</span></a>
                        </li>
                        <li>
                            <a href="feedback.php"><i class="fa fa-star"></i> <span>Feedback</span></a>
                        </li>
                        <li>
                            <a href="users.php"><i class="fa fa-users"></i> <span>Users</span></a>
                        </li>
                        <li>
                            <a href="activities.php"><i class="fa fa-bell-o"></i> <span>Notifications</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Edit Schedule</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <form id="edit-schedule-form" action="update_schedule.php" method="POST">
                            <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule_id); ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Doctor Name</label>
                                        <select class="select" id="doctor_id" name="doctor_id" required>
                                            <option value="">Select</option>
                                            <?php foreach ($doctors as $doctor): ?>
                                                <option value="<?php echo htmlspecialchars($doctor['id']); ?>" 
                                                        <?php echo $doctor['id'] == $schedule['doctor_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($doctor['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Schedule Date</label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" id="schedule_date" name="schedule_date" 
                                                   value="<?php echo htmlspecialchars(date('d/m/Y', strtotime($schedule['schedule_date']))); ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Appointment ID</label>
                                        <input type="text" class="form-control" id="appointment_id" name="appointment_id" 
                                               value="<?php echo $schedule['appointment_id'] ? htmlspecialchars('APT' . str_pad($schedule['appointment_id'], 4, '0', STR_PAD_LEFT)) : 'N/A'; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <div class="time-icon">
                                            <input type="text" class="form-control timepicker" id="start_time" name="start_time" 
                                                   value="<?php echo htmlspecialchars(date('h:i A', strtotime($schedule['start_time']))); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <div class="time-icon">
                                            <input type="text" class="form-control timepicker" id="end_time" name="end_time" 
                                                   value="<?php echo $schedule['end_time'] ? htmlspecialchars(date('h:i A', strtotime($schedule['end_time']))) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea cols="30" rows="4" class="form-control" id="notes" name="notes"><?php echo htmlspecialchars($schedule['notes'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="display-block">Schedule Status</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_available" value="Available" 
                                           <?php echo $schedule['status'] === 'Available' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status_available">Available</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_busy" value="Busy" 
                                           <?php echo $schedule['status'] === 'Busy' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status_busy">Busy</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_oncall" value="On-Call" 
                                           <?php echo $schedule['status'] === 'On-Call' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status_oncall">On-Call</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_blocked" value="Blocked" 
                                           <?php echo $schedule['status'] === 'Blocked' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status_blocked">Blocked</label>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit">Save</button>
                            </div>
                        </form>
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
    <script>
        $(function () {
            // Initialize datetimepicker for schedule date
            $('#schedule_date').datetimepicker({
                format: 'DD/MM/YYYY',
                useCurrent: false
            });

            // Initialize datetimepicker for start and end times
            $('#start_time').datetimepicker({
                format: 'hh:mm A',
                useCurrent: false
            });
            $('#end_time').datetimepicker({
                format: 'hh:mm A',
                useCurrent: false
            });

            // Add class to time inputs for styling consistency
            $('#start_time').addClass('timepicker');
            $('#end_time').addClass('timepicker');

            // Handle form submission via AJAX
            $('#edit-schedule-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'update_schedule.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = 'schedule.php'; // Redirect to schedule list page
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('An error occurred while updating the schedule: ' + error);
                    }
                });
            });
        });
    </script>
</body>
</html>