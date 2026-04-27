<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

require_once 'db_connect.php'; // Your PDO connection file

try {
    // Fetch all users
    $query = "
        SELECT 
            CONCAT(first_name, ' ', IFNULL(last_name, '')) AS name,
            role,
            IFNULL(contact_number, 'N/A') AS contact,
            email
        FROM users
        ORDER BY name ASC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users_' . date('Y-m-d_His') . '.csv"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Write CSV headers
    fputcsv($output, ['Name', 'Role', 'Contact', 'Email']);

    // Write user data
    foreach ($users as $user) {
        fputcsv($output, [
            $user['name'],
            $user['role'],
            $user['contact'],
            $user['email']
        ]);
    }

    // Close the stream
    fclose($output);
    exit;
} catch (PDOException $e) {
    // Handle errors (for debugging, you can log this instead of displaying)
    die('Error generating CSV: ' . $e->getMessage());
} catch (Exception $e) {
    die('Unexpected error: ' . $e->getMessage());
}