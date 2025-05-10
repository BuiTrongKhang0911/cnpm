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

// Generate 6-digit PIN
$pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Store PIN in session
$_SESSION['reset_pin'] = $pin;
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
    $mail->Subject = 'Verify Your GoFast Account';
    $mail->Body = "
        <h2>GoFast Account Verification</h2>
        <p>Hello,</p>
        <p>Your verification code is: <strong style=\"font-size: 22px; color: #5d50fa;\">{$pin}</strong></p>
        <p>This code is valid for <b>2 minutes</b>. Please enter it on the website to complete your verification.</p>
        <p>If you did not request this code, please ignore this email.</p>
        <p style=\"margin-top: 30px; color: #888;\">Thank you,<br>GoFast Team</p>
    ";

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send email']);
}
?> 