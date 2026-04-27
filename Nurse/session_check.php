<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Nurse') {
    header('Location: ../Backend/loginpage.php');
    exit();
}
