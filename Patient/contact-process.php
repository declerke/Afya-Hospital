<?php
// Process contact form
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        $to           = "info@afyahospital.com";
        $email_subject = "Contact Form: " . strip_tags($subject);
        $email_body   = "New message from the contact form.\n\n" .
                        "Name: $name\n" .
                        "Email: $email\n" .
                        "Phone: $phone\n" .
                        "Message:\n$message";
        $headers = "From: noreply@afyahospital.com\r\nReply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL);

        if (mail($to, $email_subject, $email_body, $headers)) {
            $success_message = "Thank you for your message. We will get back to you soon.";
        } else {
            $error_message = "Sorry, there was an error sending your message. Please try again later.";
        }
    }
}

// Redirect back to contact page with status
if (!empty($success_message)) {
    header('Location: contact.html?status=success&message=' . urlencode($success_message));
    exit;
} elseif (!empty($error_message)) {
    header('Location: contact.html?status=error&message=' . urlencode($error_message));
    exit;
} else {
    header('Location: contact.html');
    exit;
}
?>