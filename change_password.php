<?php
    session_start();
    header('Content-Type: application/json'); 
    if (!isset($_SESSION['userLoggedin']) || !isset($_SESSION['userId'])) {
        echo json_encode([
            'success' => false,
            'error' => 'You are not logged in!'
        ]);
        exit;
    }
    include 'db.php';
    $userID = $_SESSION['userId'];
    $response = ['success' => false, 'error' => ''];

    function sanitize_input($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    $password = sanitize_input($_POST['oldpassword'] ?? '');
    $newpassword = sanitize_input($_POST['newpassword'] ?? '');
    $confirmpassword = sanitize_input($_POST['confirmpassword'] ?? '');

    if ($newpassword !== $confirmpassword) {
        $response['error'] = 'New password and confirm password do not match!';
        echo json_encode($response);
        exit;
    }
    
    if (strlen($newpassword) < 6) {
        $response['error'] = 'New password must be at least 6 characters long!';
        echo json_encode($response);
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT Password FROM users WHERE Id = ?");
    if (!$stmt) {
        $response['error'] = 'Database error: ' . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $currentHashedPassword = $row['Password'];
    } else {
        $response['error'] = 'User not found!';
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        echo json_encode($response);
        exit;
    }
    mysqli_stmt_close($stmt);

    if (!password_verify($password, $currentHashedPassword)) {
        $response['error'] = 'Password is incorrect!';
        mysqli_close($conn);
        echo json_encode($response);
        exit;
    }

    $newHashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "UPDATE users SET Password = ? WHERE Id = ?");
    if (!$stmt) {
        $response['error'] = 'Database error: ' . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "si", $newHashedPassword, $userID);
    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
        $response['message'] = 'Password updated successfully!';
    } else {
        $response['error'] = 'Failed to update password: ' . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    echo json_encode($response);
?>