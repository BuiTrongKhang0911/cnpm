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
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE FullName = ?");
        $stmt->bind_param("s", $username);
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

    // Handle registration with PIN verification
    $fullname = sanitize_input($_POST['fullname'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phonenumber = sanitize_input($_POST['phonenumber'] ?? '');
    $password = $_POST['password'] ?? '';
    $dateofbirth = $_POST['dateofbirth'] ?? '';
    $address = sanitize_input($_POST['address'] ?? '');
    $vehicleId = $_POST['vehicleId'] ?? '';
    $fromDate = $_POST['fromDate'] ?? '';
    $fromTime = $_POST['fromTime'] ?? '';
    $toDate = $_POST['toDate'] ?? '';
    $toTime = $_POST['toTime'] ?? '';
    $pin = $_POST['verify-code'] ?? '';

    if (!preg_match('/^\d{6}$/', $pin)) {
        echo json_encode(['success' => false, 'message' => 'Invalid PIN format']);
        exit;
    }

    // Validate PIN session
    if (!isset($_SESSION['reset_pin']) || !isset($_SESSION['reset_time']) || $_SESSION['reset_pin'] !== $pin || (time() - $_SESSION['reset_time']) > 120) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired PIN']);
        exit;
    }

    // Insert user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_sql = "INSERT INTO users (FullName, Email, Password, PhoneNumber, DateOfBirth, Address) VALUES (?, ?, ?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    if ($insert_stmt) {
        mysqli_stmt_bind_param($insert_stmt, "ssssss", $fullname, $email, $hashed_password, $phonenumber, $dateofbirth, $address);
        if (mysqli_stmt_execute($insert_stmt)) {
            $_SESSION['userLoggedin'] = true;
            $_SESSION['userId'] = mysqli_insert_id($conn);
            $_SESSION['userName'] = $fullname;
            $_SESSION['vehicleId'] = $vehicleId;
            $_SESSION['fromDate'] = $fromDate;
            $_SESSION['fromTime'] = $fromTime;
            $_SESSION['toDate'] = $toDate;
            $_SESSION['toTime'] = $toTime;
            $response['success'] = true;
            $response['message'] = 'Registration successful!';
        } else {
            $response['errors']['server'] = 'Error inserting user: ' . mysqli_stmt_error($insert_stmt);
        }
        mysqli_stmt_close($insert_stmt);
    } else {
        $response['errors']['server'] = 'Server error: ' . mysqli_error($conn);
    }
} else {
    $response['errors']['server'] = 'Invalid request.';
}
echo json_encode($response);
?>