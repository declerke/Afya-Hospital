<?php
require_once 'session_check.php';
require_once '../Backend/db_connect.php';

$stats = ['patients' => 0, 'appointments_today' => 0, 'doctors' => 0, 'nurses' => 0];
try {
    $stats['patients']           = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $stats['appointments_today'] = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetchColumn();
    $stats['doctors']            = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $stats['nurses']             = $pdo->query("SELECT COUNT(*) FROM nurses")->fetchColumn();
} catch (PDOException $e) {}

$staff_name = 'Staff';
try {
    $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    if ($row) $staff_name = htmlspecialchars($row['name']);
} catch (PDOException $e) {}

// Recent appointments
$appointments = [];
try {
    $stmt = $pdo->query(
        "SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                CONCAT(p.first_name,' ',p.last_name) AS patient_name,
                CONCAT(d.first_name,' ',d.last_name) AS doctor_name
         FROM appointments a
         JOIN patients p ON a.patient_id = p.id
         JOIN doctors  d ON a.doctor_id  = d.id
         ORDER BY a.appointment_date DESC, a.appointment_time DESC
         LIMIT 10"
    );
    $appointments = $stmt->fetchAll();
} catch (PDOException $e) {}

// Doctor schedules
$schedules = [];
try {
    $stmt = $pdo->query(
        "SELECT s.*, CONCAT(d.first_name,' ',d.last_name) AS doctor_name
         FROM schedules s
         JOIN doctors d ON s.doctor_id = d.id
         ORDER BY s.available_date ASC
         LIMIT 10"
    );
    $schedules = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../Backend/assets/img/favicon.ico">
    <title>Afya Hospital - Hospital Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/style.css">
    <style>
        .stat-card { background:#fff; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.07); display:flex; align-items:center; gap:1rem; }
        .stat-icon { width:3rem; height:3rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:#fff; }
        .stat-icon.blue   { background:#0080ff; }
        .stat-icon.green  { background:#10b981; }
        .stat-icon.amber  { background:#f59e0b; }
        .stat-icon.purple { background:#8b5cf6; }
        .stat-num { font-size:2rem; font-weight:700; color:#1a1a2e; line-height:1; }
        .stat-label { color:#6b7280; font-size:.875rem; }
        .appt-badge { padding:.2rem .6rem; border-radius:10px; font-size:.75rem; font-weight:600; }
        .badge-scheduled { background:#fef3c7;color:#92400e; }
        .badge-confirmed { background:#d1fae5;color:#065f46; }
        .badge-cancelled { background:#fee2e2;color:#991b1b; }
        .badge-completed { background:#dbeafe;color:#1e40af; }
    </style>
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
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="../Backend/assets/img/user.jpg" width="24" alt="Staff">
                        <span class="status online"></span>
                    </span>
                    <span><?php echo $staff_name; ?></span>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li class="active"><a href="hospitalstaffdashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                    <li><a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a></li>
                    <li><a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
                    <li><a href="schedules.php"><i class="fa fa-calendar-check-o"></i> <span>Doctor Schedules</span></a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="content">
            <div class="row page-titles">
                <div class="col-md-5 col-8 align-self-center">
                    <h3 class="text-themecolor">Welcome, <?php echo $staff_name; ?></h3>
                    <p class="text-muted"><?php echo date('l, d F Y'); ?></p>
                </div>
            </div>

            <!-- Stats -->
            <div class="row" style="margin-bottom:1.5rem;">
                <?php
                $stat_items = [
                    ['icon'=>'fa-wheelchair','class'=>'blue','value'=>$stats['patients'],'label'=>'Total Patients'],
                    ['icon'=>'fa-calendar-check-o','class'=>'green','value'=>$stats['appointments_today'],'label'=>"Today's Appointments"],
                    ['icon'=>'fa-user-md','class'=>'amber','value'=>$stats['doctors'],'label'=>'Doctors'],
                    ['icon'=>'fa-stethoscope','class'=>'purple','value'=>$stats['nurses'],'label'=>'Nurses'],
                ];
                foreach ($stat_items as $s): ?>
                <div class="col-md-3 col-sm-6" style="padding:.5rem;">
                    <div class="stat-card">
                        <div class="stat-icon <?php echo $s['class']; ?>"><i class="fa <?php echo $s['icon']; ?>"></i></div>
                        <div>
                            <div class="stat-num"><?php echo $s['value']; ?></div>
                            <div class="stat-label"><?php echo $s['label']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="row">
                <!-- Recent Appointments -->
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Recent Appointments</h4>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr><th>Date</th><th>Patient</th><th>Doctor</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($appointments)): ?>
                                        <tr><td colspan="4" class="text-muted text-center">No appointments.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($appointments as $a): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($a['appointment_date']); ?></td>
                                            <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
                                            <td>Dr. <?php echo htmlspecialchars($a['doctor_name']); ?></td>
                                            <td>
                                                <span class="appt-badge badge-<?php echo strtolower(htmlspecialchars($a['status'])); ?>">
                                                    <?php echo htmlspecialchars($a['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="appointments.php" class="btn btn-sm btn-outline-primary mt-2">View All</a>
                        </div>
                    </div>
                </div>

                <!-- Doctor Schedules -->
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Upcoming Schedules</h4>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr><th>Doctor</th><th>Date</th><th>Shift</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($schedules)): ?>
                                        <tr><td colspan="3" class="text-muted text-center">No schedules.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($schedules as $sc): ?>
                                        <tr>
                                            <td>Dr. <?php echo htmlspecialchars($sc['doctor_name']); ?></td>
                                            <td><?php echo htmlspecialchars($sc['available_date'] ?? '—'); ?></td>
                                            <td><?php echo htmlspecialchars($sc['shift'] ?? '—'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="schedules.php" class="btn btn-sm btn-outline-primary mt-2">View All</a>
                        </div>
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
