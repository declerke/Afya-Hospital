<?php
require_once 'session_check.php';
require_once '../Backend/db_connect.php';

$schedules = [];
try {
    $stmt = $pdo->query(
        "SELECT s.*, CONCAT(d.first_name,' ',d.last_name) AS doctor_name, d.department
         FROM schedules s
         JOIN doctors d ON s.doctor_id = d.id
         ORDER BY s.available_date ASC
         LIMIT 100"
    );
    $schedules = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedules - Afya Hospital Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/style.css">
</head>
<body>
<div class="main-wrapper">
    <div class="header">
        <div class="header-left">
            <a href="hospitalstaffdashboard.php" class="logo">
                <img src="../Backend/assets/img/logo.png" width="35" height="35" alt=""> <span>Afya Hospital</span>
            </a>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
        <ul class="nav user-menu float-right">
            <li class="nav-item">
                <a href="logout.php" class="nav-link"><i class="fa fa-sign-out"></i> Logout</a>
            </li>
        </ul>
    </div>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li><a href="hospitalstaffdashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                    <li><a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a></li>
                    <li><a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
                    <li class="active"><a href="schedules.php"><i class="fa fa-calendar-check-o"></i> <span>Doctor Schedules</span></a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="content">
            <div class="row page-titles">
                <div class="col-md-5 col-8 align-self-center">
                    <h3 class="text-themecolor">Doctor Schedules</h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Doctor</th><th>Department</th><th>Date</th><th>Shift</th><th>Start</th><th>End</th></tr>
                            </thead>
                            <tbody>
                            <?php if (empty($schedules)): ?>
                                <tr><td colspan="6" class="text-center text-muted">No schedules found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($schedules as $s): ?>
                                <tr>
                                    <td>Dr. <?php echo htmlspecialchars($s['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($s['department'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($s['available_date'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($s['shift'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($s['start_time'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($s['end_time'] ?? '—'); ?></td>
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
<script src="../Backend/assets/js/jquery-3.2.1.min.js"></script>
<script src="../Backend/assets/js/bootstrap.min.js"></script>
<script src="../Backend/assets/js/app.js"></script>
</body>
</html>
