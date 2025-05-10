<?php
session_start();
require 'db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Check if email exists in database
$stmt = $conn->prepare("SELECT Id FROM users WHERE Email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Email not found']);
    exit;
}

// Generate 6-digit PIN
$pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Store PIN in session
$_SESSION['reset_pin'] = $pin;
$_SESSION['reset_email'] = $email;
$_SESSION['reset_time'] = time();

// Send email with PIN
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'angiatoru09@gmail.com';
    $mail->Password = 'adhu fsei wbqj bvol';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('angiatoru09@gmail.com', 'Car Info');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Code';
    $mail->Body = "
        <h2>Password Reset Request</h2>
        <p>Your password reset code is: <strong>{$pin}</strong></p>
        <p>This code will expire in 2 minutes.</p>
        <p>If you didn't request this, please ignore this email.</p>
    ";

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send email']);
}
?> 