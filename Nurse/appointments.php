<?php
require_once 'session_check.php';
require_once '../Backend/db_connect.php';

$filter_date  = trim($_GET['date'] ?? date('Y-m-d'));
$appointments = [];
try {
    $stmt = $pdo->prepare(
        "SELECT a.id, a.appointment_date, a.appointment_time, a.status,
                CONCAT(p.first_name,' ',p.last_name) AS patient_name,
                CONCAT(d.first_name,' ',d.last_name) AS doctor_name, d.department
         FROM appointments a
         JOIN patients p ON a.patient_id = p.id
         JOIN doctors  d ON a.doctor_id  = d.id
         WHERE a.appointment_date = ?
         ORDER BY a.appointment_time ASC"
    );
    $stmt->execute([$filter_date]);
    $appointments = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Afya Hospital Nurse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/style.css">
    <style>
        .badge-scheduled { background:#fef3c7;color:#92400e; }
        .badge-confirmed { background:#d1fae5;color:#065f46; }
        .badge-cancelled { background:#fee2e2;color:#991b1b; }
        .badge-completed { background:#dbeafe;color:#1e40af; }
        .appt-badge { padding:.2rem .6rem; border-radius:10px; font-size:.75rem; font-weight:600; }
    </style>
</head>
<body>
<div class="main-wrapper">
    <div class="header">
        <div class="header-left">
            <a href="nursedashboard.php" class="logo">
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
                    <li><a href="nursedashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
                    <li><a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a></li>
                    <li class="active"><a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="content">
            <div class="row page-titles">
                <div class="col-md-5 col-8 align-self-center">
                    <h3 class="text-themecolor">Appointments</h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="input-group" style="max-width:280px;">
                            <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Time</th><th>Patient</th><th>Doctor</th><th>Department</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr><td colspan="5" class="text-center text-muted">No appointments for this date.</td></tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $a): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(date('g:i A', strtotime($a['appointment_time']))); ?></td>
                                    <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
                                    <td>Dr. <?php echo htmlspecialchars($a['doctor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($a['department'] ?? '—'); ?></td>
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
