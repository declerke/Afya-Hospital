<?php
require_once 'config/db.php';

// Process contact form
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Send email
        $to = "info@afyahospital.com"; // Replace with your email
        $email_subject = "Contact Form: $subject";
        $email_body = "You have received a new message from the contact form.\n\n" .
                      "Name: $name\n" .
                      "Email: $email\n" .
                      "Phone: $phone\n" .
                      "Message:\n$message";
        $headers = "From: $email";
        
        // Attempt to send email
        if (mail($to, $email_subject, $email_body, $headers)) {
            $success_message = "Thank you for your message. We will get back to you soon.";
            
            // Clear form data
            $_POST = array();
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