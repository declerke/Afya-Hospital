<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }
include 'db_connect.php';

$response = ['success' => false, 'data' => []];

try {
    $staff_id = isset($_GET['staff_id']) ? $_GET['staff_id'] : null;
    if (!$staff_id) {
        throw new Exception('Staff ID not provided');
    }

    $stmt = $pdo->prepare("SELECT institution, starting_date, complete_date, degree, subject 
                           FROM education_informations 
                           WHERE staff_id = ? 
                           ORDER BY complete_date DESC");
    $stmt->execute([$staff_id]);
    $education = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = array_map(function($edu) {
        return [
            'institution' => $edu['institution'],
            'degree' => $edu['degree'],
            'subject' => $edu['subject'],
            'time' => $edu['starting_date'] . ' - ' . $edu['complete_date']
        ];
    }, $education);
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>