<?php
session_start();
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

include 'db_connect.php';

$doctorId = $_SESSION['doctor_id'];
$invoiceNumber = isset($_GET['invoice']) ? trim($_GET['invoice']) : '';
$invoice = null;
$patients = [];
$appointments = [];
$successMessage = '';
$errorMessage = '';

try {
    // Validate the invoice number format
    if ($invoiceNumber && !preg_match('/^#INV-\d{2}-\d{3}-\d{4}$/', $invoiceNumber)) {
        throw new Exception("Invalid invoice number format.");
    }

    // Fetch the invoice details if an invoice number is provided
    if ($invoiceNumber) {
        $stmt = $pdo->prepare("
            SELECT b.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name
            FROM billing b
            JOIN patients p ON b.patient_id = p.id
            WHERE b.invoice_number = :invoice_number
            AND EXISTS (
                SELECT 1 
                FROM appointments a 
                WHERE a.patient_id = b.patient_id 
                AND a.doctor_id = :doctor_id
            )
        ");
        $stmt->execute([':invoice_number' => $invoiceNumber, ':doctor_id' => $doctorId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            throw new Exception("Invoice not found or you do not have permission to edit this invoice.");
        }
    } else {
        throw new Exception("No invoice number provided.");
    }

    // Format the transaction_date for display
    $transactionDateTime = new DateTime($invoice['transaction_date']);
    $transactionDateFormatted = $transactionDateTime->format('d/m/Y'); // e.g., 01/04/2025
    $transactionTimeFormatted = $transactionDateTime->format('H:i:s'); // e.g., 14:30:00

    // Fetch patients the doctor has appointments with
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.id, CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM patients p
        JOIN appointments a ON a.patient_id = p.id
        WHERE a.doctor_id = :doctor_id
        ORDER BY patient_name
    ");
    $stmt->execute([':doctor_id' => $doctorId]);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch appointments for the selected patient
    $selectedPatientId = $invoice['patient_id'];
    $stmt = $pdo->prepare("
        SELECT id, appointment_date
        FROM appointments
        WHERE patient_id = :patient_id AND doctor_id = :doctor_id
        ORDER BY appointment_date DESC
    ");
    $stmt->execute([':patient_id' => $selectedPatientId, ':doctor_id' => $doctorId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $patientId = $_POST['patient_id'] ?? '';
        $appointmentId = $_POST['appointment_id'] ?? null;
        $amount = $_POST['amount'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? '';
        $paymentStatus = $_POST['payment_status'] ?? '';
        $transactionToken = $_POST['transaction_token'] ?? '';
        $transactionDatePart = $_POST['transaction_date_part'] ?? '';
        $transactionTimePart = $_POST['transaction_time_part'] ?? '';

        // Validate inputs
        if (empty($patientId) || empty($amount) || empty($paymentMethod) || empty($paymentStatus) || empty($transactionDatePart) || empty($transactionTimePart)) {
            throw new Exception("All required fields must be filled.");
        }

        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception("Amount must be a positive number.");
        }

        // Combine the date and time into a single DATETIME value
        $combinedDateTimeString = $transactionDatePart . ' ' . $transactionTimePart; // e.g., "01/04/2025 14:30:00"
        $dateTime = DateTime::createFromFormat('d/m/Y H:i:s', $combinedDateTimeString);
        if ($dateTime === false) {
            throw new Exception("Invalid transaction date or time format. Please use the date and time pickers to select valid values.");
        }
        $transactionDate = $dateTime->format('Y-m-d H:i:s'); // e.g., "2025-04-01 14:30:00"

        // Update the invoice in the billing table
        $stmt = $pdo->prepare("
            UPDATE billing
            SET patient_id = :patient_id,
                appointment_id = :appointment_id,
                amount = :amount,
                payment_method = :payment_method,
                payment_status = :payment_status,
                transaction_token = :transaction_token,
                transaction_date = :transaction_date,
                updated_at = NOW()
            WHERE invoice_number = :invoice_number
        ");
        $stmt->execute([
            ':patient_id' => $patientId,
            ':appointment_id' => $appointmentId ?: null,
            ':amount' => $amount,
            ':payment_method' => $paymentMethod,
            ':payment_status' => $paymentStatus,
            ':transaction_token' => $transactionToken,
            ':transaction_date' => $transactionDate,
            ':invoice_number' => $invoiceNumber
        ]);

        // Log the action in audit_logs
        $actionDetails = json_encode([
            'invoice_number' => $invoiceNumber,
            'patient_id' => $patientId,
            'amount' => $amount,
            'payment_status' => $paymentStatus,
            'transaction_date' => $transactionDate
        ]);
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, action, table_name, record_id, details, timestamp)
            VALUES (:user_id, :action, :table_name, :record_id, :details, NOW())
        ");
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'],
            ':action' => 'Updated invoice',
            ':table_name' => 'billing',
            ':record_id' => $invoice['id'],
            ':details' => $actionDetails
        ]);

        // Fetch the doctor's name for the notification
        $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) AS doctor_name FROM doctors WHERE id = :doctor_id");
        $stmt->execute([':doctor_id' => $doctorId]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        $doctorName = $doctor['doctor_name'];

        // Create a notification for the patient (recipient_id must be users.id)
        $puStmt = $pdo->prepare("SELECT id FROM users WHERE patient_id = ?");
        $puStmt->execute([$patientId]);
        $patientUserId = $puStmt->fetchColumn();

        $notificationMessage = "Your invoice {$invoiceNumber} has been updated by Dr. {$doctorName}. New amount: Kshs " . number_format($amount, 2) . ", Status: {$paymentStatus}.";
        if ($patientUserId) {
            $stmt = $pdo->prepare("INSERT INTO notifications (recipient_id, recipient_type, message, notification_type, is_read, created_at) VALUES (?, 'Patient', ?, 'Alert', 0, NOW())");
            $stmt->execute([$patientUserId, $notificationMessage]);
        }

        $successMessage = "Invoice updated successfully.";
        // Refresh the invoice data
        $stmt = $pdo->prepare("
            SELECT b.*, CONCAT(p.first_name, ' ', p.last_name) AS patient_name
            FROM billing b
            JOIN patients p ON b.patient_id = p.id
            WHERE b.invoice_number = :invoice_number
        ");
        $stmt->execute([':invoice_number' => $invoiceNumber]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update the formatted transaction_date after refresh
        $transactionDateTime = new DateTime($invoice['transaction_date']);
        $transactionDateFormatted = $transactionDateTime->format('d/m/Y');
        $transactionTimeFormatted = $transactionDateTime->format('H:i:s');

        // Refresh appointments for the updated patient
        $selectedPatientId = $patientId;
        $stmt = $pdo->prepare("
            SELECT id, appointment_date
            FROM appointments
            WHERE patient_id = :patient_id AND doctor_id = :doctor_id
            ORDER BY appointment_date DESC
        ");
        $stmt->execute([':patient_id' => $selectedPatientId, ':doctor_id' => $doctorId]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Exception $e) {
    $errorMessage = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>Afya Hospital - Edit Invoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <style>
        .error-message { color: red; }
        .success-message { color: green; }
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
                    <div class="col-sm-12">
                        <h4 class="page-title">Edit Invoice - <?php echo htmlspecialchars($invoiceNumber); ?></h4>
                    </div>
                </div>
                <?php if ($errorMessage): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($successMessage): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST">
                            <div class="row">
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Patient <span class="text-danger">*</span></label>
                                        <select class="select" name="patient_id" id="patient_id">
                                            <option value="">Select Patient</option>
                                            <?php foreach ($patients as $patient): ?>
                                                <option value="<?php echo htmlspecialchars($patient['id']); ?>" <?php echo $patient['id'] == $selectedPatientId ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($patient['patient_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Appointment ID</label>
                                        <select class="select" name="appointment_id" id="appointment_id">
                                            <option value="">No Appointment</option>
                                            <?php foreach ($appointments as $appointment): ?>
                                                <option value="<?php echo htmlspecialchars($appointment['id']); ?>" <?php echo $appointment['id'] == $invoice['appointment_id'] ? 'selected' : ''; ?>>
                                                    APT<?php echo str_pad($appointment['id'], 3, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($appointment['appointment_date']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Invoice Number</label>
                                        <input class="form-control" type="text" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Amount (Kshs) <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" name="amount" value="<?php echo htmlspecialchars($invoice['amount']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Payment Method <span class="text-danger">*</span></label>
                                        <select class="select" name="payment_method" required>
                                            <option value="Card" <?php echo $invoice['payment_method'] == 'Card' ? 'selected' : ''; ?>>Card</option>
                                            <option value="Mpesa" <?php echo $invoice['payment_method'] == 'Mpesa' ? 'selected' : ''; ?>>Mpesa</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Payment Status <span class="text-danger">*</span></label>
                                        <select class="select" name="payment_status" required>
                                            <option value="Paid" <?php echo $invoice['payment_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="Pending" <?php echo $invoice['payment_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Failed" <?php echo $invoice['payment_status'] == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                                            <option value="Refunded" <?php echo $invoice['payment_status'] == 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Transaction Token</label>
                                        <input class="form-control" type="text" name="transaction_token" value="<?php echo htmlspecialchars($invoice['transaction_token']); ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Transaction Date <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control datepicker" type="text" name="transaction_date_part" value="<?php echo htmlspecialchars($transactionDateFormatted); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label>Transaction Time <span class="text-danger">*</span></label>
                                        <div class="cal-icon">
                                            <input class="form-control timepicker" type="text" name="transaction_time_part" value="<?php echo htmlspecialchars($transactionTimeFormatted); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Other Information</label>
                                        <textarea class="form-control" rows="4" name="other_information"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center m-t-20">
                                <button class="btn btn-primary submit-btn" type="submit">Save Invoice</button>
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
    $(document).ready(function() {
        // Configure the date picker (only date)
        $('.datepicker').datetimepicker({
            format: 'DD/MM/YYYY',
            useCurrent: false,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            }
        });

        // Configure the time picker (only time)
        $('.timepicker').datetimepicker({
            format: 'HH:mm:ss',
            useCurrent: false,
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
                today: 'fa fa-crosshairs',
                clear: 'fa fa-trash',
                close: 'fa fa-times'
            }
        });

        // Debugging: Log when the pickers are initialized
        console.log('Date picker initialized with format DD/MM/YYYY');
        console.log('Time picker initialized with format HH:mm:ss');

        // Dynamically update the appointment dropdown when the patient changes
        $('#patient_id').change(function() {
            var patientId = $(this).val();
            if (patientId) {
                $.ajax({
                    url: 'fetch-appointments.php',
                    type: 'POST',
                    data: { patient_id: patientId, doctor_id: <?php echo $doctorId; ?> },
                    dataType: 'json',
                    success: function(data) {
                        var $appointmentSelect = $('#appointment_id');
                        $appointmentSelect.empty();
                        $appointmentSelect.append('<option value="">No Appointment</option>');
                        $.each(data, function(index, appointment) {
                            $appointmentSelect.append(
                                '<option value="' + appointment.id + '">' +
                                'APT' + String(appointment.id).padStart(3, '0') + ' - ' + appointment.appointment_date +
                                '</option>'
                            );
                        });
                    },
                    error: function() {
                        alert('Error fetching appointments.');
                    }
                });
            } else {
                $('#appointment_id').empty().append('<option value="">No Appointment</option>');
            }
        });
    });
    </script>
</body>
</html>