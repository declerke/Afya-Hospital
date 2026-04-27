<?php
require_once 'session_check.php';
require_once 'config/db.php';

$patient_id      = $_SESSION['patient_id'];
$error_message   = '';
$success_message = '';

// Fetch doctors for the dropdown
$doctors = [];
try {
    $stmt = $pdo->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name, department FROM doctors ORDER BY first_name");
    $doctors = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Could not load doctors list.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id        = filter_input(INPUT_POST, 'doctor_id',        FILTER_VALIDATE_INT);
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $appointment_time = trim($_POST['appointment_time'] ?? '');
    $status           = 'Scheduled';

    if (!$doctor_id || $doctor_id <= 0) {
        $error_message = "Please select a doctor.";
    } elseif (empty($appointment_date)) {
        $error_message = "Please select an appointment date.";
    } elseif (empty($appointment_time)) {
        $error_message = "Please select an appointment time.";
    } else {
        // Validate date is not in the past
        $appt_dt = DateTime::createFromFormat('Y-m-d', $appointment_date);
        if (!$appt_dt || $appt_dt < new DateTime('today')) {
            $error_message = "Please select a future date.";
        } else {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$patient_id, $doctor_id, $appointment_date, $appointment_time, $status]);
                $newId = $pdo->lastInsertId();
                $displayId = 'APT' . str_pad($newId, 4, '0', STR_PAD_LEFT);
                $pdo->prepare("UPDATE appointments SET appointment_id = ? WHERE id = ?")->execute([$displayId, $newId]);
                $success_message = "Appointment booked successfully!";
            } catch (PDOException $e) {
                $error_message = "Could not book appointment. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Afya Hospital</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .booking-wrapper { max-width: 600px; margin: 3rem auto; padding: 0 1rem; }
        .booking-card { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); padding:2rem; }
        .booking-card h2 { margin-bottom:1.5rem; color:#1a1a2e; }
        .form-group { margin-bottom:1.2rem; }
        .form-group label { display:block; font-weight:500; margin-bottom:.4rem; color:#374151; }
        .form-group input, .form-group select {
            width:100%; padding:.6rem .9rem; border:1px solid #d1d5db;
            border-radius:6px; font-size:.95rem; transition:border-color .2s;
        }
        .form-group input:focus, .form-group select:focus {
            outline:none; border-color:#0080ff; box-shadow:0 0 0 2px rgba(0,128,255,.15);
        }
        .btn-submit { width:100%; padding:.8rem; background:#0080ff; color:#fff;
            border:none; border-radius:6px; font-size:1rem; font-weight:600; cursor:pointer; transition:background .2s; }
        .btn-submit:hover { background:#0066cc; }
        .alert { padding:.9rem 1rem; border-radius:6px; margin-bottom:1rem; font-size:.9rem; }
        .alert-error   { background:#fef2f2; color:#b91c1c; border:1px solid #fee2e2; }
        .alert-success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
        .back-link { display:inline-block; margin-top:1rem; color:#0080ff; text-decoration:none; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <a href="index.html"><span class="logo-blue">Afya</span><span class="logo-dark">Hospital</span></a>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="medical-history.php">Medical History</a></li>
                <li><a href="billing.php">Billing</a></li>
                <li><a href="feedback.php">Feedback</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <a href="logout.php" class="btn btn-secondary" style="background:#e5e7eb;color:#374151;padding:.5rem 1rem;border-radius:6px;text-decoration:none;">Logout</a>
        </div>
    </div>
</header>

<div class="booking-wrapper">
    <div class="booking-card">
        <h2><i class="fas fa-calendar-plus" style="color:#0080ff;margin-right:.5rem;"></i>Book an Appointment</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="doctor_id">Select Doctor</label>
                <select id="doctor_id" name="doctor_id" required>
                    <option value="">-- Choose a Doctor --</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?php echo (int)$doc['id']; ?>"
                            <?php echo (isset($_POST['doctor_id']) && $_POST['doctor_id'] == $doc['id']) ? 'selected' : ''; ?>>
                            Dr. <?php echo htmlspecialchars($doc['name']); ?>
                            <?php if (!empty($doc['department'])): ?>
                                — <?php echo htmlspecialchars($doc['department']); ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="appointment_date">Date</label>
                <input type="date" id="appointment_date" name="appointment_date"
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo htmlspecialchars($_POST['appointment_date'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="appointment_time">Time</label>
                <input type="time" id="appointment_time" name="appointment_time"
                       value="<?php echo htmlspecialchars($_POST['appointment_time'] ?? ''); ?>" required>
            </div>

            <button type="submit" class="btn-submit">Confirm Appointment</button>
        </form>

        <a href="index.html" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
</div>
</body>
</html>
