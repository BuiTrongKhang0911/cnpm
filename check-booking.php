<?php
session_start();
header('Content-Type: application/json'); 
include 'db.php';

if (!isset($_SESSION['userId'])) {
    $response['error'] = 'User not logged in.';
    echo json_encode($response);
    exit;
}

$response = ['success' => false, 'error' => ''];
$userId = $_SESSION['userId'];
$vehicleId=$_POST['vehicleId'];
$fromDate=$_POST['start_date'];
$fromTime=$_POST['start_time'];
$toDate=$_POST['end_date'];
$toTime=$_POST['end_time'];
$message = $_POST['message'];
$fromDateTime = "$fromDate $fromTime:00";
$toDateTime = "$toDate $toTime:00";

$sql="SELECT * FROM booking WHERE VehicleId=? AND NOT (ToDate <= ? OR FromDate >= ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $vehicleId, $fromDateTime, $toDateTime);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $response['error'] = 'This car is already booked for the selected time period.';
    echo json_encode($response);
    exit;
}
else{
    $sql = "INSERT INTO booking (userId, VehicleId, FromDate, ToDate, message, Status) VALUES (?, ?, ?, ?, ?, 'New')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $userId, $vehicleId, $fromDateTime, $toDateTime, $message);  
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>