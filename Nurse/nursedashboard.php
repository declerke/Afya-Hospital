<?php
require_once 'session_check.php';
require_once '../Backend/db_connect.php';

// Fetch stats
$stats = ['patients' => 0, 'appointments_today' => 0, 'doctors' => 0];
try {
    $stats['patients']           = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $stats['appointments_today'] = $pdo->query("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()")->fetchColumn();
    $stats['doctors']            = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
} catch (PDOException $e) {
    // Continue with zeroes
}

// Fetch nurse name from session
$nurse_name = 'Nurse';
try {
    $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    if ($row) $nurse_name = htmlspecialchars($row['name']);
} catch (PDOException $e) {}

// Fetch today's appointments
$appointments = [];
try {
    $stmt = $pdo->prepare(
        "SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                CONCAT(p.first_name,' ',p.last_name) AS patient_name,
                CONCAT(d.first_name,' ',d.last_name) AS doctor_name, d.department
         FROM appointments a
         JOIN patients p ON a.patient_id = p.id
         JOIN doctors  d ON a.doctor_id  = d.id
         WHERE a.appointment_date = CURDATE()
         ORDER BY a.appointment_time ASC
         LIMIT 20"
    );
    $stmt->execute();
    $appointments = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../Backend/assets/img/favicon.ico">
    <title>Afya Hospital - Nurse Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/style.css">
    <style>
        .stat-card { background:#fff; border-radius:10px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.07); display:flex; align-items:center; gap:1rem; }
        .stat-icon { width:3rem; height:3rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.4rem; color:#fff; }
        .stat-icon.blue   { background:#0080ff; }
        .stat-icon.green  { background:#10b981; }
        .stat-icon.purple { background:#8b5cf6; }
        .stat-num { font-size:2rem; font-weight:700; color:#1a1a2e; line-height:1; }
        .stat-label { color:#6b7280; font-size:.875rem; }
        .appt-badge { padding:.25rem .65rem; border-radius:12px; font-size:.75rem; font-weight:600; }
        .badge-scheduled  { background:#fef3c7; color:#92400e; }
        .badge-confirmed  { background:#d1fae5; color:#065f46; }
        .badge-cancelled  { background:#fee2e2; color:#991b1b; }
        .badge-completed  { background:#dbeafe; color:#1e40af; }
    </style>
</head>
<body>
<div class="main-wrapper">
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <a href="nursedashboard.php" class="logo">
                <img src="../Backend/assets/img/logo.png" width="35" height="35" alt=""> <span>Afya Hospital</span>
            </a>
        </div>
        <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
        <ul class="nav user-menu float-right">
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                    <span class="user-img">
                        <img class="rounded-circle" src="../Backend/assets/img/user.jpg" width="24" alt="Nurse">
                        <span class="status online"></span>
                    </span>
                    <span><?php echo $nurse_name; ?></span>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </li>
        </ul>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li class="active">
                        <a href="nursedashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
                    </li>
                    <li>
                        <a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a>
                    </li>
                    <li>
                        <a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a>
                    </li>
                    <li>
                        <a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-wrapper">
        <div class="content">
            <div class="row page-titles">
                <div class="col-md-5 col-8 align-self-center">
                    <h3 class="text-themecolor">Welcome, <?php echo $nurse_name; ?></h3>
                    <p class="text-muted"><?php echo date('l, d F Y'); ?></p>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="row" style="gap:0;margin-bottom:1.5rem;">
                <div class="col-md-4" style="padding:.5rem;">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fa fa-wheelchair"></i></div>
                        <div>
                            <div class="stat-num"><?php echo $stats['patients']; ?></div>
                            <div class="stat-label">Total Patients</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="padding:.5rem;">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fa fa-calendar-check-o"></i></div>
                        <div>
                            <div class="stat-num"><?php echo $stats['appointments_today']; ?></div>
                            <div class="stat-label">Appointments Today</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="padding:.5rem;">
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fa fa-user-md"></i></div>
                        <div>
                            <div class="stat-num"><?php echo $stats['doctors']; ?></div>
                            <div class="stat-label">Doctors on Staff</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Today's Appointments</h4>
                            <?php if (empty($appointments)): ?>
                                <p class="text-muted">No appointments scheduled for today.</p>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Time</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($appointments as $appt): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(date('g:i A', strtotime($appt['appointment_time']))); ?></td>
                                            <td><?php echo htmlspecialchars($appt['patient_name']); ?></td>
                                            <td>Dr. <?php echo htmlspecialchars($appt['doctor_name']); ?></td>
                                            <td><?php echo htmlspecialchars($appt['department'] ?? '—'); ?></td>
                                            <td>
                                                <span class="appt-badge badge-<?php echo strtolower(htmlspecialchars($appt['status'])); ?>">
                                                    <?php echo htmlspecialchars($appt['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
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
