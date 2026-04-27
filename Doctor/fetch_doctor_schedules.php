<?php
include 'db_connect.php';

header('Content-Type: application/json');

$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : null;

if (!$doctor_id || $doctor_id === 'N/A') {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor ID']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT ds.id, ds.schedule_date, ds.start_time, ds.end_time, ds.status, ds.notes, ds.appointment_id,
               CONCAT(p.first_name, ' ', p.last_name) AS patient_name
        FROM doctor_schedule ds
        LEFT JOIN appointments a ON ds.appointment_id = a.id
        LEFT JOIN patients p ON a.patient_id = p.id
        WHERE ds.doctor_id = ?
        ORDER BY ds.schedule_date DESC
    ");
    $stmt->execute([$doctor_id]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data for the frontend
    $formattedSchedules = [];
    foreach ($schedules as $schedule) {
        // Format the time
        $startTime = new DateTime($schedule['start_time']);
        $formattedStartTime = $startTime->format('h:i A'); // e.g., 09:00 AM

        if ($schedule['end_time']) {
            $endTime = new DateTime($schedule['end_time']);
            $formattedEndTime = $endTime->format('h:i A'); // e.g., 12:00 PM
            $availableTime = $formattedStartTime . ' - ' . $formattedEndTime;
        } else {
            $availableTime = $formattedStartTime; // e.g., 09:00 AM
        }

        $formattedSchedules[] = [
            'id' => $schedule['id'],
            'date' => $schedule['schedule_date'],
            'status' => $schedule['status'],
            'available_time' => $availableTime,
            'patient_name' => $schedule['patient_name'] ?? 'N/A', // Patient name or N/A if no appointment
            'notes' => $schedule['notes'] ?? 'N/A'
        ];
    }

    echo json_encode(['success' => true, 'schedules' => $formattedSchedules]);
} catch (PDOException $e) {
    error_log("Error fetching schedules: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>