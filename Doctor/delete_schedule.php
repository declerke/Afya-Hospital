<?php
include 'db_connect.php';
session_start();

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$schedule_id = isset($_POST['id']) ? $_POST['id'] : null;
$user_id = $_SESSION['user_id']; // The doctor's user_id from the session

if (!$schedule_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid schedule ID']);
    exit();
}

try {
    // Start a transaction to ensure data consistency
    $pdo->beginTransaction();

    // Step 1: Fetch the schedule to get the appointment_id (if any)
    $stmt = $pdo->prepare("SELECT appointment_id FROM doctor_schedule WHERE id = ?");
    $stmt->execute([$schedule_id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Schedule not found']);
        exit();
    }

    $appointment_id = $schedule['appointment_id'];

    // Step 2: If there is an appointment_id, delete the appointment
    if ($appointment_id) {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->execute([$appointment_id]);

        if ($stmt->rowCount() > 0) {
            // Log the deletion of the appointment
            $stmt = $pdo->prepare("
                INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp, details)
                VALUES (?, ?, ?, ?, NOW(), ?)
            ");
            $stmt->execute([
                $user_id,
                'Delete',
                'appointments',
                $appointment_id,
                "Deleted appointment with ID $appointment_id tied to schedule ID $schedule_id"
            ]);
        }
    }

    // Step 3: Delete the schedule
    $stmt = $pdo->prepare("DELETE FROM doctor_schedule WHERE id = ?");
    $stmt->execute([$schedule_id]);

    if ($stmt->rowCount() > 0) {
        // Log the deletion of the schedule
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, action, table_name, record_id, timestamp, details)
            VALUES (?, ?, ?, ?, NOW(), ?)
        ");
        $stmt->execute([
            $user_id,
            'Delete',
            'doctor_schedule',
            $schedule_id,
            "Deleted schedule with ID $schedule_id"
        ]);

        // Commit the transaction
        $pdo->commit();
        echo json_encode(['success' => true]);
    } else {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Schedule not found']);
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error deleting schedule: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>