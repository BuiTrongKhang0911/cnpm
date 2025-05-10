<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and validate input
$pin = trim($_POST['pin'] ?? '');
$newPassword = trim($_POST['new_password'] ?? '');
$email = $_SESSION['reset_email'] ?? '';

// Validate PIN format
if (!preg_match('/^\d{6}$/', $pin)) {
    echo json_encode(['success' => false, 'message' => 'Invalid PIN format']);
    exit;
}

// Validate password length
if (strlen($newPassword) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit;
}

try {
    // Check if PIN matches and is not expired (60 seconds)
    if (!isset($_SESSION['reset_pin']) || 
        !isset($_SESSION['reset_time']) || 
        $_SESSION['reset_pin'] !== $pin || 
        (time() - $_SESSION['reset_time']) > 120) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired PIN']);
        exit;
    }

    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in database
    $stmt = $conn->prepare("UPDATE users SET Password = ? WHERE Email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    
    if ($stmt->execute()) {
        // Clear reset session data
        unset($_SESSION['reset_pin']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_time']);
        
        echo json_encode(['success' => true, 'message' => 'Password has been reset successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
    }
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while resetting your password']);
} 