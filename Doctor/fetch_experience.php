<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['doctor_id']) || $_SESSION['role'] !== 'Doctor') { http_response_code(401); echo json_encode(['error' => 'Unauthorized']); exit(); }

include 'db_connect.php';

$response = ['success' => false, 'data' => []];

try {
    $staff_id = isset($_GET['staff_id']) ? $_GET['staff_id'] : null;
    if (!$staff_id) {
        throw new Exception('Staff ID not provided');
    }

    $stmt = $pdo->prepare("SELECT company_name, job_position, period_from, period_to 
                           FROM experience_informations 
                           WHERE staff_id = ? 
                           ORDER BY period_to DESC");
    $stmt->execute([$staff_id]);
    $experience = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = array_map(function($exp) {
        // Calculate duration
        $from = new DateTime($exp['period_from']);
        $to = $exp['period_to'] === 'Present' ? new DateTime() : new DateTime($exp['period_to']);
        $interval = $from->diff($to);
        $duration = '';
        if ($interval->y > 0) {
            $duration .= $interval->y . ' years ';
        }
        if ($interval->m > 0) {
            $duration .= $interval->m . ' months';
        }
        $duration = trim($duration);

        return [
            'company' => $exp['company_name'],
            'position' => $exp['job_position'],
            'time' => $exp['period_from'] . ' - ' . $exp['period_to'] . ' (' . $duration . ')'
        ];
    }, $experience);
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>