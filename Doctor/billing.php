<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include 'db_connect.php';

$doctorId = $_SESSION['doctor_id'];
$billingRecords = [];
$searchInvoice = isset($_GET['invoice_number']) ? trim($_GET['invoice_number']) : '';
$searchPatient = isset($_GET['patient']) ? trim($_GET['patient']) : '';
$searchStatus = isset($_GET['status']) ? trim($_GET['status']) : '';

// Handle Delete Action
if (isset($_GET['delete_invoice']) && !empty($_GET['delete_invoice'])) {
    $invoiceToDelete = trim($_GET['delete_invoice']);
    try {
        $deleteQuery = "
            DELETE b FROM billing b
            WHERE b.invoice_number = :invoice_number
            AND EXISTS (
                SELECT 1 FROM appointments a 
                WHERE a.patient_id = b.patient_id 
                AND a.doctor_id = :doctor_id
            )
        ";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->execute([
            ':invoice_number' => $invoiceToDelete,
            ':doctor_id' => $doctorId
        ]);
        // Redirect to refresh the page after deletion
        header("Location: billing.php?" . http_build_query([
            'invoice_number' => $searchInvoice,
            'patient' => $searchPatient,
            'status' => $searchStatus
        ]));
        exit();
    } catch (Exception $e) {
        $error = "Error deleting invoice: " . $e->getMessage();
    }
}

// Handle PDF Download
if (isset($_GET['download_invoice']) && !empty($_GET['download_invoice'])) {
    $invoiceNumber = trim($_GET['download_invoice']);
    try {
        $query = "
            SELECT b.id, b.invoice_number, b.amount, b.payment_status, b.transaction_date,
                   CONCAT(p.first_name, ' ', p.last_name) AS patient_name, p.contact_number, p.email AS patient_email,
                   CONCAT(d.first_name, ' ', d.last_name) AS doctor_name
            FROM billing b
            JOIN patients p ON b.patient_id = p.id
            JOIN appointments a ON a.patient_id = b.patient_id
            JOIN doctors d ON d.id = a.doctor_id
            WHERE b.invoice_number = :invoice_number
            AND a.doctor_id = :doctor_id
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':invoice_number' => $invoiceNumber,
            ':doctor_id' => $doctorId
        ]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            die("Invoice not found or you don’t have permission to download it.");
        }

        // Static items
        $totalAmount = $invoice['amount'];
        $itemCount = 3;
        $baseAmount = $totalAmount / $itemCount;
        $items = [
            ['name' => 'Consultation Fee', 'description' => 'Doctor consultation', 'unit_cost' => $baseAmount, 'quantity' => 1],
            ['name' => 'Lab Test', 'description' => 'Basic diagnostic test', 'unit_cost' => $baseAmount, 'quantity' => 1],
            ['name' => 'Medication', 'description' => 'Prescribed drugs', 'unit_cost' => $baseAmount, 'quantity' => 1]
        ];

        // Generate PDF
        require_once 'tcpdf/tcpdf.php';
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Afya Hospital');
        $pdf->SetTitle('Invoice #' . $invoice['invoice_number']);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $html = '
        <h1>Invoice #' . htmlspecialchars($invoice['invoice_number']) . '</h1>
        <p><strong>Afya Hospital</strong><br>123 Hospital Road, Nairobi, Kenya<br>+254 712 345 678<br>info@afyahospital.com</p>
        <p><strong>Invoice to:</strong><br>' . htmlspecialchars($invoice['patient_name']) . '<br>' . htmlspecialchars($invoice['contact_number']) . '<br>' . htmlspecialchars($invoice['patient_email']) . '<br>Attending Doctor: ' . htmlspecialchars($invoice['doctor_name']) . '</p>
        <p><strong>Date:</strong> ' . htmlspecialchars($invoice['transaction_date']) . '<br><strong>Due Date:</strong> ' . date('Y-m-d', strtotime($invoice['transaction_date'] . ' +30 days')) . '<br><strong>Payment Status:</strong> ' . htmlspecialchars($invoice['payment_status']) . '</p>
        <table border="1" cellpadding="5">
            <tr><th>#</th><th>Item</th><th>Description</th><th>Unit Cost (Kshs)</th><th>Quantity</th><th>Total (Kshs)</th></tr>';
        foreach ($items as $index => $item) {
            $html .= '<tr><td>' . ($index + 1) . '</td><td>' . htmlspecialchars($item['name']) . '</td><td>' . htmlspecialchars($item['description']) . '</td><td>' . number_format($item['unit_cost'], 2) . '</td><td>' . $item['quantity'] . '</td><td>' . number_format($item['unit_cost'] * $item['quantity'], 2) . '</td></tr>';
        }
        $html .= '</table>
        <p><strong>Total Due:</strong> Kshs ' . number_format($totalAmount, 2) . '</p>
        <p><strong>Payment Details:</strong><br>Bank: Equity Bank Kenya<br>Branch: Nairobi CBD<br>Account No: 1234567890123<br>SWIFT: EQBLKENA</p>';

        $pdf->writeHTML($html);
        $pdf->Output('invoice_' . $invoice['invoice_number'] . '.pdf', 'D');
        exit();
    } catch (Exception $e) {
        die("Error generating PDF: " . $e->getMessage());
    }
}

// Fetch billing records
try {
    $query = "
        SELECT b.id, b.patient_id, b.appointment_id, b.invoice_number, b.amount, 
               b.payment_status, b.transaction_date, 
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM billing b
        JOIN patients p ON b.patient_id = p.id
        WHERE EXISTS (
            SELECT 1 
            FROM appointments a 
            WHERE a.patient_id = b.patient_id 
            AND a.doctor_id = :doctor_id
        )
    ";
    $params = [':doctor_id' => $doctorId];
    if ($searchInvoice) {
        $query .= " AND b.invoice_number LIKE :invoice_number";
        $params[':invoice_number'] = '%' . $searchInvoice . '%';
    }
    if ($searchPatient) {
        $query .= " AND CONCAT(p.first_name, ' ', p.last_name) LIKE :patient_name";
        $params[':patient_name'] = '%' . $searchPatient . '%';
    }
    if ($searchStatus) {
        $query .= " AND b.payment_status = :payment_status";
        $params[':payment_status'] = $searchStatus;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $billingRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error fetching billing records: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Billing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
    .custom-badge.status-green { background-color: #28a745; color: #fff; }
    .custom-badge.status-blue { background-color: #007bff; color: #fff; }
    .custom-badge.status-red { background-color: #dc3545; color: #fff; }
    .custom-badge.status-orange { background-color: #fd7e14; color: #fff; }
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
                        <li class="active"><a href="billing.php"><i class="fa fa-money"></i> <span>Billing</span></a></li>
                        <li><a href="notifications.php"><i class="fa fa-bell"></i> <span>Notifications</span></a></li>
                        <li><a href="feedback.php"><i class="fa fa-comment"></i> <span>Feedback</span></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-sm-5 col-4">
                        <h4 class="page-title">Billing</h4>
                    </div>
                    <div class="col-sm-7 col-8 text-right m-b-30">
                        <a href="create-invoice.php" class="btn btn-primary btn-rounded"><i class="fa fa-plus"></i> Create New Invoice</a>
                    </div>
                </div>
                <div class="row filter-row">
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group form-focus">
                            <input type="text" class="form-control floating" id="invoice_number_search">
                            <label class="focus-label">Invoice Number</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group form-focus">
                            <input type="text" class="form-control floating" id="patient_search">
                            <label class="focus-label">Patient</label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group form-focus select-focus">
                            <label class="focus-label">Status</label>
                            <select class="select floating" id="status_search">
                                <option value="">Select Status</option>
                                <option value="Paid">Paid</option>
                                <option value="Pending">Pending</option>
                                <option value="Failed">Failed</option>
                                <option value="Refunded">Refunded</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <a href="#" class="btn btn-success btn-block" id="search_btn"> Search </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Invoice Number</th>
                                        <th>Patient</th>
                                        <th>Appointment ID</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Transaction Date</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($error)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center"><?php echo htmlspecialchars($error); ?></td>
                                        </tr>
                                    <?php elseif (empty($billingRecords)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No billing records found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($billingRecords as $index => $record): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><a href="invoice-view.php?invoice=<?php echo urlencode($record['invoice_number']); ?>"><?php echo htmlspecialchars($record['invoice_number']); ?></a></td>
                                                <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                                <td><?php echo $record['appointment_id'] ? 'APT' . str_pad($record['appointment_id'], 3, '0', STR_PAD_LEFT) : '-'; ?></td>
                                                <td>Kshs <?php echo number_format($record['amount'], 2); ?></td>
                                                <td>
                                                    <span class="custom-badge status-<?php echo strtolower($record['payment_status']) == 'paid' ? 'green' : (strtolower($record['payment_status']) == 'pending' ? 'blue' : (strtolower($record['payment_status']) == 'failed' ? 'red' : 'orange')); ?>">
                                                        <?php echo htmlspecialchars($record['payment_status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($record['transaction_date']); ?></td>
                                                <td class="text-right">
                                                    <div class="dropdown dropdown-action">
                                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="edit-invoice.php?invoice=<?php echo urlencode($record['invoice_number']); ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                                                            <a class="dropdown-item" href="invoice-view.php?invoice=<?php echo urlencode($record['invoice_number']); ?>"><i class="fa fa-eye m-r-5"></i> View</a>
                                                            <a class="dropdown-item" href="billing.php?download_invoice=<?php echo urlencode($record['invoice_number']); ?>"><i class="fa fa-file-pdf-o m-r-5"></i> Download</a>
                                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete_invoice_<?php echo htmlspecialchars($record['id']); ?>"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- Delete Modal for this record -->
                                            <div id="delete_invoice_<?php echo htmlspecialchars($record['id']); ?>" class="modal fade delete-modal" role="dialog">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-body text-center">
                                                            <img src="assets/img/sent.png" alt="" width="50" height="46">
                                                            <h3>Are you sure you want to delete this Invoice?</h3>
                                                            <div class="m-t-20">
                                                                <a href="#" class="btn btn-white" data-dismiss="modal">Close</a>
                                                                <a href="billing.php?delete_invoice=<?php echo urlencode($record['invoice_number']); ?>" class="btn btn-danger">Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
    $(document).ready(function() {
        function submitSearch() {
            var invoiceNumber = $('#invoice_number_search').val();
            var patient = $('#patient_search').val();
            var status = $('#status_search').val();
            var query = [];
            if (invoiceNumber) query.push('invoice_number=' + encodeURIComponent(invoiceNumber));
            if (patient) query.push('patient=' + encodeURIComponent(patient));
            if (status) query.push('status=' + encodeURIComponent(status));
            window.location.href = 'billing.php?' + query.join('&');
        }

        $('#search_btn').click(function(e) {
            e.preventDefault();
            submitSearch();
        });

        $('#invoice_number_search, #patient_search').on('input', function() {
            submitSearch();
        });

        $('#status_search').change(function() {
            submitSearch();
        });
    });
    </script>
</body>
</html>