<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['error' => 'Unauthorized']); exit(); }

require_once 'db_connect.php';

$page   = isset($_GET['page'])   ? (int)$_GET['page']        : 1;
$limit  = 10;
$offset = ($page - 1) * $limit;
$search        = isset($_GET['search'])        ? trim($_GET['search'])        : '';
$employee_id   = isset($_GET['employee_id'])   ? trim($_GET['employee_id'])   : '';
$employee_name = isset($_GET['employee_name']) ? trim($_GET['employee_name']) : '';
$role          = isset($_GET['role'])          ? trim($_GET['role'])          : '';

// All role → table mappings
$allRoles = [
    'Doctor'       => 'doctors',
    'Nurse'        => 'nurses',
    'Receptionist' => 'receptionists',
    'Admin'        => 'admins',
];

// If a specific valid role is requested, query only that table
$rolesToQuery = (!empty($role) && isset($allRoles[$role]))
    ? [$role => $allRoles[$role]]
    : $allRoles;

// Build per-row conditions (columns that exist in every role table)
$conditions = [];
$params     = [];

if (!empty($search)) {
    $conditions[] = "(CONCAT(first_name, ' ', last_name) LIKE :search OR staff_id LIKE :search OR email LIKE :search OR contact_number LIKE :search)";
    $params[':search'] = "%$search%";
}
if (!empty($employee_id)) {
    $conditions[] = "staff_id LIKE :employee_id";
    $params[':employee_id'] = "%$employee_id%";
}
if (!empty($employee_name)) {
    $conditions[] = "CONCAT(first_name, ' ', last_name) LIKE :employee_name";
    $params[':employee_name'] = "%$employee_name%";
}

$whereClause = !empty($conditions) ? ' AND ' . implode(' AND ', $conditions) : '';

// Build UNION parts
$unionParts      = [];
$countUnionParts = [];
foreach ($rolesToQuery as $roleLabel => $table) {
    $unionParts[] = "
        SELECT id, staff_id,
               CONCAT(first_name, ' ', last_name) AS full_name,
               email, contact_number, created_at,
               '$roleLabel' AS role
        FROM $table
        WHERE 1=1 $whereClause";
    $countUnionParts[] = "SELECT id FROM $table WHERE 1=1 $whereClause";
}

$query      = '(' . implode(') UNION (', $unionParts)      . ') ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
$countQuery = 'SELECT COUNT(*) FROM (' . implode(' UNION ', $countUnionParts) . ') AS combined';

try {
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt = $pdo->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $countStmt->execute();
    $totalRecords = (int)$countStmt->fetchColumn();
    $totalPages   = $totalRecords > 0 ? (int)ceil($totalRecords / $limit) : 1;

    $response = [
        'employees'   => [],
        'totalPages'  => $totalPages,
        'currentPage' => $page,
    ];
    foreach ($employees as $emp) {
        $response['employees'][] = [
            'id'             => $emp['id'],
            'staff_id'       => $emp['staff_id'],
            'full_name'      => $emp['full_name'],
            'email'          => $emp['email'],
            'contact_number' => $emp['contact_number'],
            'created_at'     => date('d M Y', strtotime($emp['created_at'])),
            'role'           => $emp['role'],
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
exit;
