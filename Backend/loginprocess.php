<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: loginpage.php');
    exit();
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

function redirect_error(string $msg): void {
    header('Location: loginpage.php?error=' . urlencode($msg));
    exit();
}

if (empty($email) || empty($password)) {
    redirect_error('Please fill in all fields.');
}

$stmt = $pdo->prepare("SELECT id, patient_id, staff_id, password_hash, role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    redirect_error('Invalid email or password.');
}

if ($user['role'] === 'Patient' && is_null($user['patient_id'])) {
    redirect_error('Patient record not linked. Contact support.');
}

if (in_array($user['role'], ['Doctor', 'Nurse', 'Hospital Staff']) && is_null($user['staff_id'])) {
    redirect_error('Staff record not linked. Contact support.');
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['role']    = $user['role'];

if ($user['role'] === 'Patient') {
    $_SESSION['patient_id'] = $user['patient_id'];
}

if ($user['role'] === 'Doctor') {
    $stmt = $pdo->prepare("SELECT id FROM doctors WHERE staff_id = ?");
    $stmt->execute([$user['staff_id']]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$doctor) {
        redirect_error('Doctor record not found for this account. Contact support.');
    }
    $_SESSION['staff_id']  = $user['staff_id'];
    $_SESSION['doctor_id'] = $doctor['id'];
}

switch ($user['role']) {
    case 'Patient':       header('Location: ../Patient/index.html'); break;
    case 'Doctor':        header('Location: ../Doctor/doctordashboard.php'); break;
    case 'Nurse':         header('Location: ../Nurse/nursedashboard.php'); break;
    case 'Hospital Staff':header('Location: ../HospitalStaff/hospitalstaffdashboard.php'); break;
    case 'Admin':         header('Location: admindashboard.php'); break;
    default:              redirect_error('Unknown user role.');
}
exit();
?>
