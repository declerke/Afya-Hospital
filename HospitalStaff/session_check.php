<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Hospital Staff') {
    header('Location: ../Backend/loginpage.php');
    exit();
}
