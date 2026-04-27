<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include 'db_connect.php';

// Get invoice number from URL
$invoiceNumber = isset($_GET['invoice']) ? trim($_GET['invoice']) : '';
if (empty($invoiceNumber)) {
    die("No invoice number provided.");
}

try {
    // Fetch invoice details with patient and doctor info
    $query = "
        SELECT b.id, b.invoice_number, b.amount, b.payment_status, b.transaction_date,
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name, p.contact_number, p.email AS patient_email,
               CONCAT(d.first_name, ' ', d.last_name) AS doctor_name
        FROM billing b
        JOIN patients p ON b.patient_id = p.id
        JOIN doctors d ON d.id = :doctor_id
        WHERE b.invoice_number = :invoice_number
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':invoice_number' => $invoiceNumber,
        ':doctor_id' => $_SESSION['doctor_id']
    ]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        die("Invoice not found or you don’t have permission to view it.");
    }

    // Static items with amounts divided from billing.amount
    $totalAmount = $invoice['amount'];
    $itemCount = 3; // Consultation, Lab Test, Medication
    $baseAmount = $totalAmount / $itemCount;
    $items = [
        ['name' => 'Consultation Fee', 'description' => 'Doctor consultation', 'unit_cost' => $baseAmount, 'quantity' => 1],
        ['name' => 'Lab Test', 'description' => 'Basic diagnostic test', 'unit_cost' => $baseAmount, 'quantity' => 1],
        ['name' => 'Medication', 'description' => 'Prescribed drugs', 'unit_cost' => $baseAmount, 'quantity' => 1]
    ];

} catch (Exception $e) {
    die("Error fetching invoice: " . $e->getMessage());
}

// PDF Generation
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    require_once 'tcpdf/tcpdf.php'; // Ensure TCPDF is in this directory or adjust path
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Invoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .inv-logo { max-width: 150px; }
        .invoice-details h3 { font-size: 24px; }
        .invoice-payment-details h5 { font-size: 18px; }
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
                        <h4 class="page-title">Invoice</h4>
                    </div>
                    <div class="col-sm-7 col-8 text-right m-b-30">
                        <div class="btn-group btn-group-sm">
                            <a href="?invoice=<?php echo urlencode($invoice['invoice_number']); ?>&download=pdf" class="btn btn-white">PDF</a>
                            <button class="btn btn-white" onclick="window.print();"><i class="fa fa-print fa-lg"></i> Print</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row custom-invoice">
                                    <div class="col-6 col-sm-6 m-b-20">
                                        <img src="assets/img/logo-dark.png" class="inv-logo" alt="Afya Hospital">
                                        <ul class="list-unstyled">
                                            <li>Afya Hospital</li>
                                            <li>123 Hospital Road,</li>
                                            <li>Nairobi, Kenya</li>
                                            <li>+254 712 345 678</li>
                                            <li><a href="mailto:info@afyahospital.com">info@afyahospital.com</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-6 col-sm-6 m-b-20">
                                        <div class="invoice-details">
                                            <h3 class="text-uppercase">Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></h3>
                                            <ul class="list-unstyled">
                                                <li>Date: <span><?php echo htmlspecialchars($invoice['transaction_date']); ?></span></li>
                                                <li>Due date: <span><?php echo date('Y-m-d', strtotime($invoice['transaction_date'] . ' +30 days')); ?></span></li>
                                                <li>Status: <span class="custom-badge status-<?php echo strtolower($invoice['payment_status']) == 'paid' ? 'green' : (strtolower($invoice['payment_status']) == 'pending' ? 'blue' : (strtolower($invoice['payment_status']) == 'failed' ? 'red' : 'orange')); ?>">
                                                    <?php echo htmlspecialchars($invoice['payment_status']); ?>
                                                </span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6 col-lg-6 m-b-20">
                                        <h5>Invoice to:</h5>
                                        <ul class="list-unstyled">
                                            <li><h5><strong><?php echo htmlspecialchars($invoice['patient_name']); ?></strong></h5></li>
                                            <li><?php echo htmlspecialchars($invoice['contact_number']); ?></li>
                                            <li><a href="mailto:<?php echo htmlspecialchars($invoice['patient_email']); ?>"><?php echo htmlspecialchars($invoice['patient_email']); ?></a></li>
                                            <li>Attending Doctor: <strong><?php echo htmlspecialchars($invoice['doctor_name']); ?></strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6 col-lg-6 m-b-20">
                                        <div class="invoices-view">
                                            <span class="text-muted">Payment Details:</span>
                                            <ul class="list-unstyled invoice-payment-details">
                                                <li><h5>Total Due: <span class="text-right">Kshs <?php echo number_format($totalAmount, 2); ?></span></h5></li>
                                                <li>Bank name: <span>Equity Bank Kenya</span></li>
                                                <li>Branch: <span>Nairobi CBD</span></li>
                                                <li>Account No: <span>1234567890123</span></li>
                                                <li>SWIFT code: <span>EQBLKENA</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ITEM</th>
                                                <th>DESCRIPTION</th>
                                                <th>UNIT COST (Kshs)</th>
                                                <th>QUANTITY</th>
                                                <th>TOTAL (Kshs)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $index => $item): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['description']); ?></td>
                                                    <td><?php echo number_format($item['unit_cost'], 2); ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td><?php echo number_format($item['unit_cost'] * $item['quantity'], 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <div class="row invoice-payment">
                                        <div class="col-sm-7"></div>
                                        <div class="col-sm-5">
                                            <div class="m-b-20">
                                                <h6>Total due</h6>
                                                <div class="table-responsive no-border">
                                                    <table class="table mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <th>Total:</th>
                                                                <td class="text-right text-primary"><h5>Kshs <?php echo number_format($totalAmount, 2); ?></h5></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invoice-info">
                                        <h5>Other Information</h5>
                                        <p class="text-muted">Payment is due within 30 days. For inquiries, contact Afya Hospital at +254 712 345 678 or info@afyahospital.com.</p>
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
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>