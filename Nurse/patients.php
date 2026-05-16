<?php
require_once 'session_check.php';
require_once '../Backend/db_connect.php';

$search   = strip_tags(trim($_GET['search'] ?? ''));
$patients = [];
try {
    if ($search) {
        $stmt = $pdo->prepare(
            "SELECT id, first_name, last_name, gender, date_of_birth, contact_number, email
             FROM patients
             WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?
             ORDER BY created_at DESC LIMIT 50"
        );
        $s = "%$search%";
        $stmt->execute([$s, $s, $s]);
    } else {
        $stmt = $pdo->query("SELECT id, first_name, last_name, gender, date_of_birth, contact_number, email FROM patients ORDER BY created_at DESC LIMIT 50");
    }
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients - Afya Hospital Nurse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../Backend/assets/css/style.css">
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
                    <li class="active"><a href="patients.php"><i class="fa fa-wheelchair"></i> <span>Patients</span></a></li>
                    <li><a href="appointments.php"><i class="fa fa-calendar"></i> <span>Appointments</span></a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> <span>Logout</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="content">
            <div class="row page-titles">
                <div class="col-md-5 col-8 align-self-center">
                    <h3 class="text-themecolor">Patients</h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="input-group" style="max-width:400px;">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search by name or email..."
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>#</th><th>Name</th><th>Gender</th><th>DOB</th><th>Phone</th><th>Email</th></tr>
                            </thead>
                            <tbody>
                            <?php if (empty($patients)): ?>
                                <tr><td colspan="6" class="text-center text-muted">No patients found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($patients as $p): ?>
                                <tr>
                                    <td><?php echo (int)$p['id']; ?></td>
                                    <td><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['gender'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($p['date_of_birth'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($p['contact_number'] ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($p['email'] ?? '—'); ?></td>
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
