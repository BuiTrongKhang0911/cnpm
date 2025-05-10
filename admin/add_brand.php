<?php   
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit;
    }
    include 'db.php';

    $success=true;
    $message="";
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $brandname = trim($_POST['brandname']);

        $sql = "SELECT brandName FROM brands";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (strcasecmp($row['brandName'], $brandname) == 0) {
                    $message = "Brand already exists. Cannot add duplicate.";
                    $success = false;
                    break;
                }
            }
        }

        if ($success) {
            $sql = "INSERT INTO brands (brandName) VALUES (?)";
            $stmt = mysqli_prepare($conn, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $brandname);
    
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Brand added successfully.";
                } else {
                    $message = "Failed to add brand.";
                    $success = false;
                }
    
                mysqli_stmt_close($stmt);
            } else {
                $message = "Failed to prepare statement.";
                $success = false;
            }
        }
    
        mysqli_close($conn);
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
    <title>Car Rental Portal | Admin Create Brand</title>
    <style>
        .form-control {
            box-sizing: border-box;
            margin: 0;
            display: block;
            width: 100%;
            height: 46px;
            padding: 12px 16px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #3e3f3a;
            background-color: #ffffff;
            background-image: none;
            border: 1px solid #dfd7ca;
            border-radius: 4px;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            box-shadow: none;
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
                    <div class="col-ml-12">
                        <h2 class="page-title">Create Brand</h2>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Create Brand</div>
                                    <div class="panel-body">
                                        <form method="POST" action="" class="form-horizontal" enctype="multipart/form-data">
                                            <?php
                                                if ($message) {
                                                    $class = $success ? "alert alert-success" : "alert alert-danger";
                                                    echo "<div class='$class'>$message</div>";
                                                }
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Brand Name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="brandname" required>
                                                </div>
                                            </div>
                                            <div class="hr-dashed"></div>
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-4">
                                                    <button class="btn btn-primary" type="submit">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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