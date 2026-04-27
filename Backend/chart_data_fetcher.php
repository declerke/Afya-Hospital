<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }


include 'db_connect.php';

try {
    // Patient Totals (current and last year, for the line graph)
    // Current year's patients (last 12 months)
    $stmt = $pdo->prepare("
        SELECT DATE_FORMAT(created_at, '%b %Y') AS month, COUNT(*) AS total
        FROM patients
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY YEAR(created_at), MONTH(created_at)
        ORDER BY created_at ASC
    ");
    $stmt->execute();
    $patientTotalsCurrent = $stmt->fetchAll();

    // Last year's patients (same period, offset by 12 months)
    $stmt = $pdo->prepare("
        SELECT DATE_FORMAT(DATE_SUB(created_at, INTERVAL 12 MONTH), '%b %Y') AS month, COUNT(*) AS total
        FROM patients
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 MONTH)
          AND created_at < DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY YEAR(DATE_SUB(created_at, INTERVAL 12 MONTH)), MONTH(DATE_SUB(created_at, INTERVAL 12 MONTH))
        ORDER BY created_at ASC
    ");
    $stmt->execute();
    $patientTotalsLastYear = $stmt->fetchAll();

    // Patient In Data (ICU and OPD, last 6 months for bar graph, all statuses)
    $stmt = $pdo->prepare("
        SELECT 
            DATE_FORMAT(admission_date, '%b') AS month,
            dept_type,
            COUNT(*) AS count
        FROM patient_in
        WHERE admission_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY YEAR(admission_date), MONTH(admission_date), dept_type
        ORDER BY admission_date ASC
    ");
    $stmt->execute();
    $patientInData = $stmt->fetchAll();

    // Organize patient in data by month and dept_type
    $months = [];
    $icuData = [];
    $opdData = [];
    $uniqueMonths = [];

    foreach ($patientInData as $row) {
        $month = $row['month'];
        if (!in_array($month, $uniqueMonths)) {
            $uniqueMonths[] = $month;
            $months[] = $month;
            $icuData[] = 0;
            $opdData[] = 0;
        }
        $index = array_search($month, $uniqueMonths);
        if ($row['dept_type'] === 'ICU') {
            $icuData[$index] = (int)$row['count'];
        } elseif ($row['dept_type'] === 'OPD') {
            $opdData[$index] = (int)$row['count'];
        }
    }

    // Upcoming Appointments Data (all Scheduled appointments, no date range limit)
    $stmt = $pdo->prepare("
        SELECT 
            a.id AS appointment_id,
            a.patient_id,
            a.doctor_id,
            a.appointment_date,
            a.appointment_time,
            p.first_name AS patient_first_name,
            p.last_name AS patient_last_name,
            p.address AS patient_location,
            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name
        FROM appointments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        WHERE a.status = 'Scheduled'
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
    ");
    $stmt->execute();
    $appointments = $stmt->fetchAll();

    // Combine all data into a single response
    $response = [
        'patientTotals' => [
            'current' => $patientTotalsCurrent,
            'lastYear' => $patientTotalsLastYear
        ],
        'patientIn' => [
            'months' => $months,
            'icu' => $icuData,
            'opd' => $opdData
        ],
        'appointments' => $appointments
    ];

    
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>