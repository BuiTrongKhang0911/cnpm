<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

require_once "session_handler.php";
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['view_id'])) {

    $stmt = mysqli_prepare($conn, "SELECT * FROM booking WHERE BookingNumber=?");
    mysqli_stmt_bind_param($stmt, "i", $_GET['view_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $booking = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="booking.css">
    <title>Booking</title>
    <style>
        .panel-body {
            color: black;
        }
    </style>
</head>
<body>
    <div class="head">
        <h2>Car Rental Portal | Admin Panel</h2>
        <span class="menu-btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></span>
    </div>
    <div class="main-content">
        <div class="sidebar" id="sidebar">
            <div class="account" onclick="toggleAccount()">
                <div class="avatar"></div>
                <span>&#160 <?php echo $_SESSION['username']?></span>
            </div>
            <div class="account-options" id="accountOptions" style="display: none;">
                <a href="change_pass.php">Change Password</a>
                <a href="logout.php">Logout</a>
            </div>

            <ul class="slidebar-menu">
                <li class="ts-lable">Main</li>
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        &#160 Dashboard
                    </a>
                </li>
                <li class="menu-item" onclick="toggleSubmenu(this)">
                    <div class="more"><i class="fa fa-angle-down"></i></div>
                    <a href="#" class="parent"><i class="fa fa-tags"></i>&#160&#160 Brands</a>
                    <ul class="submenu">
                        <li><a href="add_brand.php">Create Brand</a></li>
                        <li><a href="manage_brands.php">Manage Brands</a></li>
                    </ul>
                </li>
                <li class="menu-item" onclick="toggleSubmenu(this)">
                    <div class="more"><i class="fa fa-angle-down"></i></div>
                    <a href="#" class="parent"><i class="fa fa-car"></i>&#160&#160 Vehicles</a>
                    <ul class="submenu">
                        <li><a href="add_vehicle.php">Add Vehicle</a></li>
                        <li><a href="vehicles_list.php">Vehicles List</a></li>
                    </ul>
                </li>
                <li class="menu-item" onclick="toggleSubmenu(this)">
                    <div class="more"><i class="fa fa-angle-down"></i></div>
                    <a href="#" class="parent"><i class="fa fa-book"></i>&#160&#160 Booking</a>
                    <ul class="submenu">
                        <li><a href="booking_status_new.php">New</a></li>
                        <li><a href="booking_status_confirm.php">Confirm</a></li>
                        <li><a href="booking_status_cancel.php">Cancel</a></li>
                    </ul>
                </li>
                <li>
                    <a href="registered_users.php">
                        <i class="fa fa-users"></i>
                        &#160Reg Users
                    </a>
                </li>
            </ul>
            <a href="logout.php">Logout</a>
        </div>
        <div class="content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title">Booking Details</h2>
                        <div class="panel panel-default">
                            <div class="panel-heading">Bookings Info</div>
                            <div class="panel-body">
                                <div id="print">
                                    <h3 style="text-align:center; color:red">Booking Details</h3>
                                    <table border="1" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <tbody>
                                            <?php
                                                $userSql = "SELECT * FROM users WHERE Id = " . $booking['userId'];
                                                $userResult = mysqli_query($conn, $userSql);
                                                $userRow = mysqli_fetch_assoc($userResult);

                                                $vehicleSql = "SELECT * FROM vehicles WHERE VehicleId = " . $booking['VehicleId'];
                                                $vehicleResult = mysqli_query($conn, $vehicleSql);
                                                $vehicleRow = mysqli_fetch_assoc($vehicleResult);
                                            ?>
                                            <tr>
                                                <th colspan="4" style="text-align:center;color:blue">User Details</th>
                                            </tr>
                                            <tr>
                                                <th>Booking Number</th>
                                                <td><?php echo htmlspecialchars($booking['BookingNumber'] ?? ''); ?></td>
                                                <th>Name</th>
                                                <td><?php echo htmlspecialchars($userRow['FullName'] ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td><?php echo htmlspecialchars($userRow['Email'] ?? ''); ?></td>
                                                <th>Phone Number</th>
                                                <td><?php echo htmlspecialchars($userRow['PhoneNumber'] ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td colspan="3"><?php echo htmlspecialchars($userRow['Address'] ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th colspan="4" style="text-align:center;color:blue">Booking Details</th>
                                            </tr>
                                            <tr>
                                                <th>Vehicle Name</th>
                                                <td><?php echo htmlspecialchars($vehicleRow['VehiclesTitle'] ?? ''); ?></td>
                                                <th>Booking Date</th>
                                                <td><?php echo htmlspecialchars($booking['PostingDate'] ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>From Date</th>
                                                <td><?php echo htmlspecialchars($booking['FromDate'] ?? ''); ?></td>
                                                <th>To Date</th>
                                                <td><?php echo htmlspecialchars($booking['ToDate'] ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Total Days</th>
                                                <td>
                                                    <?php
                                                        $fromDateTime = $booking['FromDate'] ?? '';
                                                        $toDateTime = $booking['ToDate'] ?? '';
                                                        
                                                        if ($fromDateTime && $toDateTime) {
                                                            $from = new DateTime($fromDateTime);
                                                            $to = new DateTime($toDateTime);
                                                            $interval = $from->diff($to);
                                                        
                                                            $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
                                                            $day=floor($hours/24);
                                                            $remain=$hours-$day*24;
                                                            if($remain>12){
                                                                $day+=1;
                                                            }
                                                            else if($remain>0){
                                                                $day+=0.5;
                                                            }
                                                            echo htmlspecialchars($day);
                                                        }
                                                    ?>
                                                </td>
                                                <th>Rent Per Days</th>
                                                <td><?php echo htmlspecialchars($vehicleRow['PricePerDay'] ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th colspan="3" style="text-align:center">Grand Total</th>
                                                <td>
                                                    <?php
                                                        $result=$vehicleRow['PricePerDay']*$day;
                                                        echo htmlspecialchars($result);
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Booking Status</th>
                                                <td colspan="3"><?php echo htmlspecialchars($booking['Status'] ?? ''); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <form method='POST' action='booking_status_confirm.php' style='display:inline'>
                                        <input type='hidden' name='confirmId' value="<?php echo htmlspecialchars($booking['BookingNumber'] ?? ''); ?>">
                                        <button type='submit'>Confirm</button>
                                    </form>
                                    <form method='POST' action='booking_status_cancel.php' style='display:inline'>
                                        <input type='hidden' name='cancelId' value="<?php echo htmlspecialchars($booking['BookingNumber'] ?? ''); ?>">
                                        <button type='submit'>Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="session_timeout.js"></script>
    <script src="js/dashboard.js"></script>
    <script>
        function toggleSubmenu(item) {
            const submenu = item.querySelector('.submenu');
            submenu.classList.toggle('show');
            item.classList.toggle('rotate');
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>