<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

include 'db_connect.php'; 

try {
    // Department mapping
    $departmentMap = [
        'Cardiologist' => 'CAD',
        'Neurologist' => 'NER',
        'Dermatologist' => 'DER',
        'Oncologist' => 'ONC',
        'Pediatrician' => 'PED',
        'Internist' => 'INT',
        'Orthopedist' => 'ORT',
        'Gynecologist' => 'GYN',
        'Urologist' => 'URO',
        'Pulmonologist' => 'PUL',
        'Endocrinologist' => 'END',
        'Gastroenterologist' => 'GAS',
        'Radiologist' => 'RAD',
        'Anesthesiologist' => 'ANE',
        'Surgeon' => 'SUR'
    ];

    // Get list of doctors with their latest schedule status
    $stmt = $pdo->prepare("
        SELECT d.id, d.first_name, d.last_name, d.department,
               ds.status
        FROM doctors d
        LEFT JOIN (
            SELECT doctor_id, status
            FROM doctor_schedule
            ORDER BY created_at DESC
        ) ds ON d.id = ds.doctor_id
        GROUP BY d.id, d.first_name, d.last_name, d.department
        LIMIT 6
    ");
    $stmt->execute();
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format doctors data with department initials and status
    $formattedDoctors = [];
    foreach ($doctors as $doctor) {
        $deptInitial = $departmentMap[$doctor['department']] ?? 'UNK'; // Default to 'UNK' if not found
        $statusClass = '';
        switch ($doctor['status']) {
            case 'Available':
                $statusClass = 'online';
                break;
            case 'Busy':
                $statusClass = 'offline';
                break;
            case 'On-Call':
                $statusClass = 'away';
                break;
            case 'Blocked':
                $statusClass = 'offline'; // Treat Blocked as offline
                break;
            default:
                $statusClass = 'online'; // Default to online if no status
        }
        $formattedDoctors[] = [
            'id' => $doctor['id'],
            'full_name' => $doctor['first_name'] . ' ' . $doctor['last_name'],
            'dept_initial' => $deptInitial,
            'status_class' => $statusClass
        ];
    }

    
    $response = [
        'doctors' => $formattedDoctors,
        'error' => null
    ];
} catch (PDOException $e) {
    $response = ['error' => "Database error: " . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
?>