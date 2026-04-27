<?php
session_start();
$_SESSION = [];
session_destroy();
header('Location: ../Backend/loginpage.php');
exit();
