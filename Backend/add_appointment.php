<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    header("Content-Type: application/json");
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}
include 'db_connect.php';

try {    if (isset($_GET['action']) && $_GET['action'] === 'get_next_id') {
        $stmt = $pdo->query("SELECT MAX(id) AS max_id FROM appointments");
        $result = $stmt->fetch();
        $maxId = $result['max_id'] ? (int)$result['max_id'] : 0;
        $nextId = $maxId + 1;
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'nextId' => $nextId]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        $patientId = isset($_POST['patient_id']) ? (int)$_POST['patient_id'] : 0;
        $doctorId = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : 0;
        $appointmentDate = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
        $appointmentTime = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '';
        $status = isset($_POST['status']) && in_array($_POST['status'], ['Scheduled','Completed','Cancelled'])
                  ? $_POST['status'] : 'Scheduled';
        $message = isset($_POST['message']) ? trim($_POST['message']) : ''; // grab message for notifications

        // Log incoming data
        // Validate required fields for appointments
        if ($appointmentId <= 0 || $patientId <= 0 || $doctorId <= 0 || empty($appointmentDate) || empty($appointmentTime) || empty($status)) {
            $missing = [];
            if ($appointmentId <= 0) $missing[] = 'appointment_id';
            if ($patientId <= 0) $missing[] = 'patient_id';
            if ($doctorId <= 0) $missing[] = 'doctor_id';
            if (empty($appointmentDate)) $missing[] = 'appointment_date';
            if (empty($appointmentTime)) $missing[] = 'appointment_time';
            if (empty($status)) $missing[] = 'status';
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields: ' . implode(', ', $missing)]);
            exit;
        }

        $dateTime = DateTime::createFromFormat('Y-m-d H:i', "$appointmentDate $appointmentTime");
        if (!$dateTime || $dateTime->format('Y-m-d H:i') !== "$appointmentDate $appointmentTime") {
            http_response_code(400);
            echo json_encode(['error' => "Invalid date/time: $appointmentDate $appointmentTime"]);
            exit;
        }

        $checkStmt = $pdo->prepare("SELECT id FROM appointments WHERE id = ?");
        $checkStmt->execute([$appointmentId]);
        if ($checkStmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Appointment ID already exists']);
            exit;
        }

        $displayAppointmentId = 'APT' . str_pad($appointmentId, 4, '0', STR_PAD_LEFT);

        // Insert into appointments
        $stmt = $pdo->prepare("
            INSERT INTO appointments (id, patient_id, doctor_id, appointment_date, appointment_time, status, appointment_id, created_at, updated_at, modified_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'Admin')
        ");
        $stmt->execute([$appointmentId, $patientId, $doctorId, $appointmentDate, $appointmentTime, $status, $displayAppointmentId]);

        // Insert into doctor_schedule — end_time defaults to 30 min after start
        $endTime = (clone $dateTime)->modify('+30 minutes')->format('H:i:s');
        $scheduleStmt = $pdo->prepare("
            INSERT INTO doctor_schedule (doctor_id, schedule_date, start_time, end_time, status, appointment_id, notes, created_at, updated_at)
            VALUES (?, ?, ?, ?, 'Busy', ?, 'appointment with patient', NOW(), NOW())
        ");
        $scheduleStmt->execute([$doctorId, $appointmentDate, $appointmentTime, $endTime, $appointmentId]);

        // Fetch patient name + their users.id
        $patientStmt = $pdo->prepare("
            SELECT CONCAT(p.first_name,' ',p.last_name) AS name, u.id AS user_id
            FROM patients p JOIN users u ON u.patient_id = p.id
            WHERE p.id = ?");
        $patientStmt->execute([$patientId]);
        $patientRow = $patientStmt->fetch();
        $patientName   = $patientRow['name'] ?? 'Patient';
        $patientUserId = $patientRow['user_id'] ?? null;

        // Fetch doctor name + their users.id
        $doctorStmt = $pdo->prepare("
            SELECT CONCAT(d.first_name,' ',d.last_name) AS name, u.id AS user_id
            FROM doctors d JOIN users u ON u.staff_id = d.staff_id
            WHERE d.id = ?");
        $doctorStmt->execute([$doctorId]);
        $doctorRow = $doctorStmt->fetch();
        $doctorName   = $doctorRow['name'] ?? 'Doctor';
        $doctorUserId = $doctorRow['user_id'] ?? null;

        $dateFormatted = $dateTime->format('d M Y');
        $timeFormatted = $dateTime->format('H:i');
        $baseMessage       = "Your appointment $displayAppointmentId has been created on $dateFormatted at $timeFormatted as $status.";
        $doctorBaseMessage = "Appointment $displayAppointmentId for $patientName has been created on $dateFormatted at $timeFormatted as $status.";
        $patientMessage = $message ? "$baseMessage\n\nAdditional Note: $message" : $baseMessage;
        $doctorMessage  = $message ? "$doctorBaseMessage\n\nAdditional Note: $message" : $doctorBaseMessage;

        // Insert notifications only when a valid users.id exists for the recipient
        $notifyStmt = $pdo->prepare("
            INSERT INTO notifications (recipient_type, recipient_id, message, notification_type, is_read, created_at)
            VALUES (?, ?, ?, 'Alert', 0, NOW())
        ");
        if ($patientUserId) $notifyStmt->execute(['Patient', $patientUserId, $patientMessage]);
        if ($doctorUserId)  $notifyStmt->execute(['Doctor',  $doctorUserId,  $doctorMessage]);

        http_response_code(200);
        echo json_encode(['success' => true]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log("PDOException in add_appointment.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("General Exception in add_appointment.php: " . $e->getMessage());
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>