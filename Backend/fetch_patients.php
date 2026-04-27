<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

include 'db_connect.php'; 

try {
    // Fetch all patients
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, date_of_birth, address, contact_number, email, medical_history, created_at
        FROM patients
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format patients data
    $formattedPatients = [];
    foreach ($patients as $patient) {
        $formattedPatients[] = [
            'id' => $patient['id'],
            'full_name' => $patient['first_name'] . ' ' . $patient['last_name'],
            'date_of_birth' => $patient['date_of_birth'],
            'address' => $patient['address'] ?: 'N/A',
            'contact_number' => $patient['contact_number'],
            'email' => $patient['email'],
            'medical_history' => $patient['medical_history'] ?: 'N/A',
            'created_at' => $patient['created_at']
        ];
    }

    $response = [
        'patients' => $formattedPatients,
        'error' => null
    ];
} catch (PDOException $e) {
    $response = ['error' => "Database error: " . $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
?>