<?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit;
    }

    require_once "session_handler.php";
    include 'db.php';

    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    $userCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM vehicles";
    $result = mysqli_query($conn, $sql);
    $vehicleCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM booking WHERE Status LIKE 'New'";
    $result = mysqli_query($conn, $sql);
    $bookingCount = mysqli_num_rows($result);

    $sql = "SELECT * FROM brands";
    $result = mysqli_query($conn, $sql);
    $brandCount = mysqli_num_rows($result);
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
    <title>Car Rental Portal | Admin Dashboard</title>
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
                    <div class="col-ml-12">
                        <h2 class="page-title">Dashboard</h2>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-body bk-primary text-light" style="background-color: #385c8c;">  
                                        <div class="stat-panel text-center">
                                            <div class="stat-panel-number h1"><?php echo $userCount; ?></div>
                                            <div class="stat-panel-title text-uppercase">Reg Users</div>
                                        </div>
                                    </div>
                                    <a href="registered_users.php" class="block-anchor panel-footer">
                                        Full Detail &#160;
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-body bk-primary text-light" style="background-color: #98c44c;">  
                                        <div class="stat-panel text-center">
                                            <div class="stat-panel-number h1"><?php echo $vehicleCount; ?></div>
                                            <div class="stat-panel-title text-uppercase">Listed Vehicles</div>
                                        </div>
                                    </div>
                                    <a href="vehicles_list.php" class="block-anchor panel-footer">
                                        Full Detail &#160;
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-body bk-primary text-light"  style="background-color: #30ace4;">  
                                        <div class="stat-panel text-center">
                                            <div class="stat-panel-number h1"><?php echo $bookingCount; ?></div>
                                            <div class="stat-panel-title text-uppercase">Total Bookings</div>
                                        </div>
                                    </div>
                                    <a href="registered_users.php" class="block-anchor panel-footer">
                                        Full Detail &#160;
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-body bk-primary text-light"  style="background-color: #f87c3c;">  
                                        <div class="stat-panel text-center">
                                            <div class="stat-panel-number h1"><?php echo $brandCount; ?></div>
                                            <div class="stat-panel-title text-uppercase">Listed Brands</div>
                                        </div>
                                    </div>
                                    <a href="registered_users.php" class="block-anchor panel-footer">
                                        Full Detail &#160;
                                        <i class="fa fa-arrow-right"></i>
                                    </a>
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