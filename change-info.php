<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$response = ['success' => false, 'errors' => []];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle username validation request
    if (isset($_POST['validate_username'])) {
        $username = sanitize_input($_POST['fullname']);
        $userId = $_SESSION['userId'];
        
        // Check if username exists in database (excluding current user)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE fullname = ? AND Id != ?");
        $stmt->bind_param("si", $username, $userId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $response['errors']['fullname'] = 'Username already exists.';
        }
        
        echo json_encode($response);
        exit;
    }

    // Handle form submission
    $userId = $_SESSION['userId'];
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $dob = sanitize_input($_POST['dob']);
    $address = sanitize_input($_POST['address'] ?? '');

    // Check if username exists (excluding current user)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE fullname = ? AND Id != ?");
    $stmt->bind_param("si", $fullname, $userId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $response['errors']['fullname'] = 'Username already exists.';
    }

    if (empty($response['errors'])) {
        $update_sql = "UPDATE users SET FullName = ?, Email = ?, PhoneNumber = ?, DateOfBirth = ?, Address = ? WHERE Id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);

        if ($update_stmt) {
            mysqli_stmt_bind_param($update_stmt, "sssssi", $fullname, $email, $phone, $dob, $address, $userId);
            
            if (mysqli_stmt_execute($update_stmt)) {
                $_SESSION['userName'] = $fullname;
                $response['success'] = true;
                $response['message'] = 'Information updated successfully!';
            } else {
                $response['errors']['server'] = 'Error updating user: ' . mysqli_stmt_error($update_stmt);
            }
            
            mysqli_stmt_close($update_stmt);
        } else {
            $response['errors']['server'] = 'Server error: ' . mysqli_error($conn);
        }
    }
} else {
    $response['errors']['server'] = 'Invalid request.';
}

echo json_encode($response);
?>