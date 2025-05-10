<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = sanitize_input($_POST['fullname'] ?? '');
    $password = sanitize_input($_POST['password'] ?? '');
    $vehicleId=$_POST['vehicleId'] ?? '';
    $fromDate=$_POST['fromDate'] ?? '';
    $fromTime=$_POST['fromTime'] ?? '';
    $toDate=$_POST['toDate'] ?? '';
    $toTime=$_POST['toTime'] ?? '';

    $sql = "SELECT * FROM users WHERE FullName = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $fullname);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['Password'])) {
                $_SESSION['userLoggedin'] = true;
                $_SESSION['userId'] = $user['Id'];
                $_SESSION['userName'] = $user['FullName'];
                $_SESSION['vehicleId'] = $vehicleId;
                $_SESSION['fromDate'] = $fromDate;
                $_SESSION['fromTime'] = $fromTime;
                $_SESSION['toDate'] = $toDate;
                $_SESSION['toTime'] = $toTime;
                $response['success'] = true;
            } else {
                $response['message'] = 'Invalid username or password.';
            }
        } else {
            $response['message'] = 'Invalid username or password.';
        }

        mysqli_stmt_close($stmt);
    } else {
        $response['message'] = 'Error: ' . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>