<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include '../Backend/db_connect.php';

// Fetch patient details based on patient_id from URL
$patientId = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;
$patient = [];
if ($patientId > 0) {
    try {
        $stmt = $pdo->prepare("SELECT p.id, p.first_name, p.last_name, p.contact_number, p.email, p.address, p.gender, p.date_of_birth, u.id AS user_id 
                               FROM patients p 
                               LEFT JOIN users u ON u.patient_id = p.id 
                               WHERE p.id = ?");
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch();

        // Format date_of_birth to DD/MM/YYYY
        if (!empty($patient['date_of_birth'])) {
            $dateOfBirth = DateTime::createFromFormat('Y-m-d', $patient['date_of_birth']);
            $patient['date_of_birth'] = $dateOfBirth ? $dateOfBirth->format('d/m/Y') : 'N/A';
        } else {
            $patient['date_of_birth'] = 'N/A';
        }
    } catch (PDOException $e) {
        $patient = [];
    }
}

$doctorId = $_SESSION['doctor_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Patient Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .profile-header { background: linear-gradient(135deg, #e0f7fa, #ffffff); border-radius: 10px; }
        .nav-tabs .nav-link { color: #007bff; background: #f8f9fa; border: none; }
        .nav-tabs .nav-link.active { background: #007bff; color: #fff; border-radius: 5px 5px 0 0; }
        .card-title { font-size: 1.5rem; font-weight: 600; color: #333; }
        .download-btn { background: #28a745; color: #fff; border-radius: 20px; padding: 8px 20px; }
        .download-btn:hover { background: #218838; }
        .visit-form { background: #f1f1f1; padding: 20px; border-radius: 10px; }
        .table-responsive { margin-top: 20px; }
        @media (max-width: 768px) {
            .profile-header { flex-direction: column; text-align: center; }
            .nav-tabs { flex-wrap: nowrap; overflow-x: auto; }
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
                        <a class="dropdown-item" href="profile.php">Edit Profile</a>
                        <a class="dropdown-item" href="../Backend/loginpage.php">Logout</a>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="profile.php">My Profile</a>
                    <a class="dropdown-item" href="profile.php">Edit Profile</a>
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
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-7 col-6">
                        <h4 class="page-title">Patient Details</h4>
                    </div>
                    <div class="col-sm-5 col-6 text-right m-b-30">
                        <a href="#" class="btn btn-primary btn-rounded download-btn" id="downloadMedicalHistory"><i class="fa fa-download"></i> Download Medical History</a>
                    </div>
                </div>
                <div class="card-box profile-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-view">
                                <div class="profile-img-wrap">
                                    <div class="profile-img">
                                        <img class="avatar" src="assets/img/user.jpg" alt="Patient Avatar">
                                    </div>
                                </div>
                                <div class="profile-basic">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="profile-info-left">
                                                <h3 class="user-name m-t-0 mb-0"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h3>
                                                <small class="text-muted">Patient ID: <?php echo $patientId; ?></small>
                                                <div class="staff-msg"><a href="#" class="btn btn-primary">Send Message</a></div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <ul class="personal-info">
                                                <li><span class="title">Contact:</span><span class="text"><?php echo htmlspecialchars($patient['contact_number'] ?? 'N/A'); ?></span></li>
                                                <li><span class="title">Email:</span><span class="text"><?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?></span></li>
                                                <li><span class="title">Date of Birth:</span><span class="text"><?php echo htmlspecialchars($patient['date_of_birth']); ?></span></li>
                                                <li><span class="title">Address:</span><span class="text"><?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></span></li>
                                                <li><span class="title">Gender:</span><span class="text"><?php echo htmlspecialchars($patient['gender'] ?? 'N/A'); ?></span></li>
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
                        <li class="nav-item"><a class="nav-link active" href="#patient-info" data-toggle="tab">Patient Info</a></li>
                        <li class="nav-item"><a class="nav-link" href="#visit-record" data-toggle="tab">Record Visit</a></li>
                        <li class="nav-item"><a class="nav-link" href="#payment-request" data-toggle="tab">Payment Request</a></li>
                        <li class="nav-item"><a class="nav-link" href="#visit-history" data-toggle="tab">Visit History</a></li>
                    </ul>
                    <div class="tab-content">
                        <!-- Patient Info Tab -->
                        <div class="tab-pane show active" id="patient-info">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <h3 class="card-title">Patient Overview</h3>
                                        <p class="text-muted">Detailed information about the patient is displayed below.</p>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Patient ID:</strong> <?php echo $patientId; ?></li>
                                            <li class="list-group-item"><strong>User ID:</strong> <?php echo $patient['user_id'] ?? 'N/A'; ?></li>
                                            <li class="list-group-item"><strong>Full Name:</strong> <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></li>
                                            <li class="list-group-item"><strong>Contact:</strong> <?php echo htmlspecialchars($patient['contact_number'] ?? 'N/A'); ?></li>
                                            <li class="list-group-item"><strong>Email:</strong> <?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?></li>
                                            <li class="list-group-item"><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient['date_of_birth']); ?></li>
                                            <li class="list-group-item"><strong>Address:</strong> <?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></li>
                                            <li class="list-group-item"><strong>Gender:</strong> <?php echo htmlspecialchars($patient['gender'] ?? 'N/A'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Visit Recording Tab -->
                        <div class="tab-pane" id="visit-record">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-box visit-form">
                                        <h3 class="card-title">Record Patient Visit</h3>
                                        <form id="visitForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Patient Name</label>
                                                        <input type="text" class="form-control" name="patient_name" value="<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>" readonly>
                                                        <input type="hidden" name="patient_id" value="<?php echo $patientId; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Staff ID (Doctor)</label>
                                                        <input type="text" class="form-control" name="staff_id" value="<?php echo $doctorId; ?>" readonly>
                                                        <input type="hidden" name="doctor_id" value="<?php echo $doctorId; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Visit Date</label>
                                                        <div class="cal-icon">
                                                            <input type="text" class="form-control datetimepicker" name="visit_date">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Reason for Visit</label>
                                                        <textarea class="form-control" name="reason" rows="3" placeholder="Enter reason for visit"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Treatment</label>
                                                        <textarea class="form-control" name="treatment" rows="3" placeholder="Enter treatment details"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Prescriptions</label>
                                                        <textarea class="form-control" name="prescriptions" rows="3" placeholder="Enter prescriptions"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Procedure</label>
                                                        <textarea class="form-control" name="procedure" rows="3" placeholder="Enter procedure details"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Additional Note</label>
                                                        <textarea class="form-control" name="note" rows="3" placeholder="Enter additional notes"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary">Save Visit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Payment Request Tab -->
                        <div class="tab-pane" id="payment-request">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-box visit-form">
                                        <h3 class="card-title">Request Payment Invoice</h3>
                                        <form id="paymentRequestForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Invoice Amount</label>
                                                        <input type="number" class="form-control" name="amount" placeholder="Enter amount (e.g., 100.00)" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Reason for Payment</label>
                                                        <textarea class="form-control" name="payment_reason" rows="3" placeholder="Enter reason for payment"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-success">Request Invoice</button>
                                            </div>
                                        </form>
                                        <p class="text-muted mt-3">Note: This request will notify the billing department. Invoice generation is pending integration with the billing system.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Visit History Tab -->
                        <div class="tab-pane" id="visit-history">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <h3 class="card-title">Visit History</h3>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" id="visitHistoryTable">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Reason</th>
                                                        <th>Treatment</th>
                                                        <th>Prescriptions</th>
                                                        <th>Procedure</th>
                                                        <th>Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Populated via JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <div id="successModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="assets/img/sent1.png" alt="" width="50" height="46">
                    <h3>Success</h3>
                    <p id="successMessage"></p>
                    <div class="m-t-20"><a href="#" class="btn btn-white" data-dismiss="modal">Close</a></div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/patient-details.js"></script>
    <script>
        $(function () {
            $('.datetimepicker').datetimepicker({
                format: 'DD/MM/YYYY'
            });
            $('#visitHistoryTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 5
            });
        });
    </script>
</body>
</html>