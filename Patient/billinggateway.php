<?php
session_start();
require_once '../Backend/db_connect.php'; // Linking to your db_connect.php in Backend folder

// Check if patient is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: ../Backend/loginpage.php");
    exit();
}

// Initialize variables
$patient_id = $_SESSION['patient_id'];
$billing_id = isset($_GET['billing_id']) ? filter_input(INPUT_GET, 'billing_id', FILTER_SANITIZE_NUMBER_INT) : null;
$billing_info = null;
$error_message = '';
$success_message = '';

// Fetch billing information
if ($billing_id) {
    $sql = "SELECT b.id AS billing_id, b.invoice_number, b.amount, b.payment_status, 
                   a.appointment_date, a.appointment_time
            FROM billing b
            JOIN appointments a ON b.appointment_id = a.id
            WHERE b.id = ? AND b.patient_id = ? AND b.payment_status = 'Pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$billing_id, $patient_id]);
    $billing_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$billing_info) {
        $error_message = "Billing record not found or already paid.";
    }
}

// Process M-Pesa payment confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_mpesa']) && isset($_POST['billing_id']) && isset($_POST['phone_number'])) {
    $billing_id = filter_input(INPUT_POST, 'billing_id', FILTER_SANITIZE_NUMBER_INT);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!preg_match('/^\+?\d{10,12}$/', $phone_number)) {
        $error_message = "Please enter a valid phone number.";
    } else {
        // Tokenize the phone number (for demo purposes; in production, use a secure method)
        $transaction_token = hash('sha256', $phone_number); // Hash the phone number as the transaction token
        $sql_update = "UPDATE billing 
                       SET payment_method = 'M-Pesa', payment_status = 'Paid', transaction_token = ?, transaction_date = NOW(), updated_at = NOW() 
                       WHERE id = ? AND patient_id = ? AND payment_status = 'Pending'";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$transaction_token, $billing_id, $patient_id]);

        if ($stmt_update->rowCount() > 0) {
            $success_message = "Payment confirmed successfully!";
            header("Location: billing.php"); // Redirect back to billing page
            exit();
        } else {
            $error_message = "Error confirming payment. The bill may already be paid or does not exist.";
        }
    }
}

// Process Card payment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['card_payment']) && isset($_POST['billing_id'])) {
    $billing_id = filter_input(INPUT_POST, 'billing_id', FILTER_SANITIZE_NUMBER_INT);
    $card_number = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $expiration_date = filter_input(INPUT_POST, 'expiration_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cvv = filter_input(INPUT_POST, 'cvv', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cardholder_name = filter_input(INPUT_POST, 'cardholder_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Basic validation for card details
    if (!preg_match('/^\d{16}$/', str_replace(' ', '', $card_number))) {
        $error_message = "Invalid card number. Must be 16 digits.";
    } elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiration_date)) {
        $error_message = "Invalid expiration date. Use MM/YY format.";
    } elseif (!preg_match('/^\d{3}$/', $cvv)) {
        $error_message = "Invalid CVV. Must be 3 digits.";
    } elseif (empty($cardholder_name)) {
        $error_message = "Cardholder name is required.";
    } else {
        $transaction_token = bin2hex(random_bytes(8)); // Generate transaction token
        $sql_update = "UPDATE billing 
                       SET payment_method = 'Card', payment_status = 'Paid', transaction_token = ?, transaction_date = NOW(), updated_at = NOW() 
                       WHERE id = ? AND patient_id = ? AND payment_status = 'Pending'";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$transaction_token, $billing_id, $patient_id]);

        if ($stmt_update->rowCount() > 0) {
            $success_message = "Payment processed successfully!";
            header("Location: billing.php"); // Redirect back to billing page
            exit();
        } else {
            $error_message = "Error processing payment.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - Afya Hospital</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f8f9fa;
            color: #343a40;
            line-height: 1.6;
        }
        .nav-links {
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .nav-links a {
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
            margin-right: 15px;
            transition: color 0.3s ease;
        }
        .nav-links a:hover {
            color: #0056b3;
        }
        .container {
            margin: 30px auto;
            max-width: 900px;
            display: flex;
            gap: 20px;
            padding: 20px;
        }
        .card.box1 {
            width: 350px;
            background-color: #007bff;
            color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .card.box2 {
            width: 550px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .error, .success {
            font-size: 0.9rem;
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }
        .error {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .success {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .box1 .amount {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .box1 .d-flex {
            margin-bottom: 15px;
        }
        .box1 .border-bottom {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin: 20px 0;
        }
        .box1 .text {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        .box1 .btn-primary {
            background-color: #0056b3;
            border: none;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .box1 .btn-primary:hover {
            background-color: #003d82;
        }
        .box2 .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            border-bottom: 1px solid #dee2e6;
        }
        .box2 .h5 {
            font-weight: 600;
            margin: 0;
        }
        .box2 .nav-tabs {
            border: none;
            border-bottom: 2px solid #dee2e6;
            padding: 0 30px;
        }
        .box2 .nav-tabs .nav-link {
            border: none;
            color: #343a40;
            font-size: 0.9rem;
            padding: 10px 15px;
        }
        .box2 .nav-tabs .nav-link:hover {
            border-bottom: 2px solid #007bff;
            color: #007bff;
        }
        .box2 .nav-tabs .nav-link.active {
            border: none;
            border-bottom: 2px solid #007bff;
            color: #007bff;
        }
        .box2 .tab-content {
            padding: 30px;
        }
        .box2 .form-control {
            border: none;
            border-bottom: 1px solid #dee2e6;
            border-radius: 0;
            padding: 10px 0;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .box2 .form-control:focus {
            box-shadow: none;
            border-bottom: 1px solid #007bff;
        }
        .box2 .inputWithIcon {
            position: relative;
        }
        .box2 .inputWithIcon span {
            position: absolute;
            right: 0;
            bottom: 10px;
            color: #007bff;
            font-size: 1rem;
        }
        .box2 .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .box2 .btn-primary:hover {
            background-color: #0056b3;
        }
        .mpesa-form .form-group {
            margin-bottom: 20px;
        }
        .mpesa-form label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #1a3c6d;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }
        .modal-content p {
            margin-bottom: 20px;
            font-size: 1rem;
        }
        .modal-content .btn-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-content .btn {
            padding: 6px 12px; /* Reduced padding to fit text size */
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .modal-content .btn-cancel {
            background-color: #dc3545;
            color: #fff;
        }
        .modal-content .btn-cancel:hover {
            background-color: #c82333;
        }
        .modal-content .btn-confirm {
            background-color: #28a745;
            color: #fff;
        }
        .modal-content .btn-confirm:hover {
            background-color: #218838;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                margin: 20px;
                padding: 10px;
            }
            .card.box1, .card.box2 {
                width: 100%;
            }
            .box1 {
                padding: 20px;
            }
            .box2 .header, .box2 .tab-content {
                padding: 20px;
            }
            .box2 .nav-tabs .nav-link {
                font-size: 0.8rem;
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="index.html">Home</a> / 
        <a href="../Backend/loginpage.php">Log out</a>
    </div>

    <div class="container">
        <?php if ($billing_info): ?>
            <div class="card box1">
                <div class="amount">
                    KES <?php echo htmlspecialchars(number_format($billing_info['amount'], 2)); ?>
                </div>
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between text">
                        <span>Commission</span>
                        <span>KES 0.00</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text mb-4">
                        <span>Total</span>
                        <span>KES <?php echo htmlspecialchars(number_format($billing_info['amount'], 2)); ?></span>
                    </div>
                    <div class="border-bottom mb-4"></div>
                    <div class="d-flex flex-column mb-4">
                        <span class="text"><i class="far fa-file-alt"></i> Invoice ID:</span>
                        <span class="ps-3"><?php echo htmlspecialchars($billing_info['invoice_number']); ?></span>
                    </div>
                    <div class="d-flex flex-column mb-5">
                        <span class="text"><i class="far fa-calendar-alt"></i> Appointment Date:</span>
                        <span class="ps-3"><?php echo htmlspecialchars($billing_info['appointment_date'] . ' ' . $billing_info['appointment_time']); ?></span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text mt-5">
                        <div class="d-flex flex-column text">
                            <span>Customer Support:</span>
                            <span>online chat 24/7</span>
                        </div>
                        <div class="btn btn-primary rounded-circle">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card box2">
                <div class="header">
                    <span class="h5">Payment Methods</span>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-tab="mpesa">M-Pesa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-tab="card">Credit Card</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- M-Pesa Tab -->
                    <div class="tab-pane active" id="mpesa">
                        <form class="mpesa-form" id="mpesaForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?billing_id=' . htmlspecialchars($billing_id); ?>">
                            <input type="hidden" name="billing_id" value="<?php echo htmlspecialchars($billing_info['billing_id']); ?>">
                            <input type="hidden" name="confirm_mpesa" value="1">
                            <div class="form-group">
                                <label for="phone_number">Phone Number:</label>
                                <div class="inputWithIcon">
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="+254712345678" required>
                                    <span><i class="fas fa-phone"></i></span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary">Pay KES <?php echo htmlspecialchars(number_format($billing_info['amount'], 2)); ?></button>
                        </form>
                    </div>

                    <!-- Card Tab -->
                    <div class="tab-pane" id="card">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?billing_id=' . htmlspecialchars($billing_id); ?>" method="POST">
                            <input type="hidden" name="billing_id" value="<?php echo htmlspecialchars($billing_info['billing_id']); ?>">
                            <input type="hidden" name="card_payment" value="1">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex flex-column mb-4">
                                        <span>Credit Card</span>
                                        <div class="inputWithIcon">
                                            <input class="form-control" type="text" name="card_number" placeholder="1234 5678 9012 3456" required>
                                            <span><i class="fas fa-credit-card"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex flex-column mb-4">
                                        <span>Expiration Date</span>
                                        <div class="inputWithIcon">
                                            <input type="text" class="form-control" name="expiration_date" placeholder="MM/YY" required>
                                            <span><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex flex-column mb-4">
                                        <span>Code CVV</span>
                                        <div class="inputWithIcon">
                                            <input type="password" class="form-control" name="cvv" placeholder="123" required>
                                            <span><i class="fas fa-lock"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column mb-4">
                                        <span>Cardholder Name</span>
                                        <div class="inputWithIcon">
                                            <input class="form-control text-uppercase" type="text" name="cardholder_name" placeholder="John Doe" required>
                                            <span><i class="far fa-user"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Pay KES <?php echo htmlspecialchars(number_format($billing_info['amount'], 2)); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="error">Invalid billing record.</div>
        <?php endif; ?>
    </div>

    <!-- M-Pesa Confirmation Popup -->
    <div class="modal" id="mpesaModal">
        <div class="modal-content">
            <p>A request prompt has been sent to your phone. Please complete the payment, then press Confirm to finalize the transaction.</p>
            <div class="btn-container">
                <button class="btn btn-cancel" onclick="closeMpesaPopup()">Cancel</button>
                <button class="btn btn-confirm" onclick="confirmMpesaPayment()">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.getAttribute('data-tab')).classList.add('active');
            });
        });

        // M-Pesa Popup
        document.querySelector('.mpesa-form .btn-primary').addEventListener('click', showMpesaPopup);

        function showMpesaPopup() {
            const phoneNumber = document.getElementById('phone_number').value;
            if (!phoneNumber.match(/^\+?\d{10,12}$/)) {
                alert('Please enter a valid phone number.');
                return;
            }
            document.getElementById('mpesaModal').style.display = 'block';
        }

        function closeMpesaPopup() {
            document.getElementById('mpesaModal').style.display = 'none';
        }

        function confirmMpesaPayment() {
            document.getElementById('mpesaForm').submit();
        }
    </script>
</body>
</html>