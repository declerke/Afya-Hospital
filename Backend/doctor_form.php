<?php
session_start();

// Include database connection
require_once 'db_connect.php';

// Check if user is logged in and has 'Doctor' role
if (!isset($_SESSION['user_id']) || strcasecmp($_SESSION['role'], 'Doctor') !== 0) {
    die("Access denied. Only doctors can add visit records.");
}

$user_id = $_SESSION['user_id']; // From users.id
if (!isset($_SESSION['staff_id'])) {
    error_log("Session staff_id not set for user_id: " . ($_SESSION['user_id'] ?? 'unknown'));
    die("Error: Staff ID not set in session. Please log in again or contact support.");
}
$staff_id = $_SESSION['staff_id']; // From users.staff_id, linked to doctors.staff_id

try {
    // Validate staff_id exists in doctors table
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    if ($stmt->rowCount() === 0) {
        die("Invalid staff ID.");
    }

    // Fetch all patients for the dropdown
    $stmt = $pdo->prepare("SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM patients ORDER BY first_name");
    $stmt->execute();
    $patients = $stmt->fetchAll();

    // Fetch recent visit records for this doctor
    $stmt = $pdo->prepare("
        SELECT vr.id, vr.visit_date, CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM visit_records vr
        JOIN patients p ON vr.patient_id = p.id
        WHERE vr.doctor_id = ?
        ORDER BY vr.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$staff_id]);
    $recent_records = $stmt->fetchAll();

    // Encryption setup
    $encryption_key = '0589121e755e38401cc2a3a7ed0a8ec9dc8c4db7e0f94ffc46623074b8f33525'; // 32-byte key (same as patient_form.php)
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $patient_id = $_POST['patient_id'];
        $visit_date = $_POST['visit_date'];
        $reason_for_visit = $_POST['reason_for_visit'];
        $notes_outcome = $_POST['notes_outcome'];

        // Validate inputs
        if (empty($patient_id) || empty($visit_date) || empty($reason_for_visit) || empty($notes_outcome)) {
            die("Please fill in all fields.");
        }

        // Encrypt sensitive fields
        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted_reason = openssl_encrypt(
            $reason_for_visit,
            'aes-256-cbc',
            hex2bin($encryption_key),
            0,
            $iv
        );
        $encrypted_reason = base64_encode($encrypted_reason . '::' . base64_encode($iv));

        $iv = openssl_random_pseudo_bytes($iv_length);
        $encrypted_notes = openssl_encrypt(
            $notes_outcome,
            'aes-256-cbc',
            hex2bin($encryption_key),
            0,
            $iv
        );
        $encrypted_notes = base64_encode($encrypted_notes . '::' . base64_encode($iv));

        // Insert into visit_records
        $stmt = $pdo->prepare("INSERT INTO visit_records (patient_id, doctor_id, visit_date, reason_for_visit, notes_outcome, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$patient_id, $staff_id, $visit_date, $encrypted_reason, $encrypted_notes]);
        $record_id = $pdo->lastInsertId();

        // Log the action in audit_logs
        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp) VALUES (?, 'INSERT', 'visit_records', ?, NOW())");
        $stmt->execute([$user_id, $record_id]);

        // Redirect to PDF generation after successful insert
        echo "<script>
                alert('Visit record added successfully! Redirecting to download PDF...');
                window.location.href='generate_visit_pdf.php?record_id=$record_id';
              </script>";
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Visit Record Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        select, input[type="date"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
        .recent-records {
            margin-top: 20px;
        }
        .recent-records table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-records th, .recent-records td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .recent-records th {
            background-color: #f2f2f2;
        }
        .recent-records a {
            color: #28a745;
            text-decoration: none;
        }
        .recent-records a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Visit Record</h2>
        <form method="POST" onsubmit="return validateForm()">
            <label for="patient_id">Select Patient</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">-- Select Patient --</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?php echo htmlspecialchars($patient['id']); ?>">
                        <?php echo htmlspecialchars($patient['full_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="visit_date">Visit Date</label>
            <input type="date" id="visit_date" name="visit_date" required>

            <label for="reason_for_visit">Reason for Visit</label>
            <textarea id="reason_for_visit" name="reason_for_visit" rows="3" required></textarea>

            <label for="notes_outcome">Notes/Outcome</label>
            <textarea id="notes_outcome" name="notes_outcome" rows="3" required></textarea>

            <button type="submit">Submit</button>
        </form>

        <!-- Recent Visit Records -->
        <?php if (!empty($recent_records)): ?>
            <div class="recent-records">
                <h3>Recent Visit Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Visit Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['visit_date']); ?></td>
                                <td><a href="generate_visit_pdf.php?record_id=<?php echo $record['id']; ?>">Download PDF</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function validateForm() {
            const patientId = document.getElementById('patient_id').value;
            const visitDate = document.getElementById('visit_date').value;
            const reason = document.getElementById('reason_for_visit').value.trim();
            const notes = document.getElementById('notes_outcome').value.trim();

            if (!patientId || !visitDate || !reason || !notes) {
                alert('Please fill in all fields.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>