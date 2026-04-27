<?php
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AFYA Hospital</title>
    <link rel="stylesheet" href="loginstyling.css">
    <style>
        .error-msg { color: #c0392b; background: #fdecea; border: 1px solid #e74c3c;
                     padding: 8px 12px; border-radius: 4px; margin-bottom: 12px;
                     font-size: 0.9rem; text-align: center; }
    </style>
</head>
<body background="Backend\image7.png">
    <div class="auth-container">
        <h2>AFYA HOSPITAL</h2>
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="loginprocess.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign In</button>
        </form>
        <p>Don't have an account? <a href="signuppage.php">Sign Up</a></p>
    </div>
</body>
</html>
