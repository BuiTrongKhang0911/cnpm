<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

require_once "session_handler.php";
include 'db.php';

$sql = "SELECT * FROM users";
$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>s
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="dashboard.css">
    <title>Car Rental Portal |Admin Manage Testimonials</title>
    <style>
        .dataTables_filter,
        .dataTables_paginate {
            text-align: right;
        }
        .pagination{
            margin: 0;
        }
        .form-control {
            font-weight: 100;
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
                        <h2 class="page-title">Registered Users</h2>
                        <div class="panel panel-default">
                            <div class="panel-heading">Reg Users</div>
                            <div class="panel-body">
                                <div id="zctb_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="row">
                                        <div class="col-sm-6"></div>
                                        <div class="col-sm-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="zctb" class="display table table-striped table-bordered table-hover dataTable" cellspacing="0" width="100%" role="grid" aria-describedby="zctb_info" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Full Name</th>
                                                        <th>Email </th>
                                                        <th>Phone Number</th>
                                                        <th>Date of Birth</th>
                                                        <th>Address</th>
                                                        <th>Registration Date</th>
										            </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($result -> num_rows > 0) {
                                                        $i = 1;
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo "<tr>
                                                                    <td>{$i}</td>
                                                                    <td>" . htmlspecialchars($row['FullName']) . "</td>
                                                                    <td>" . htmlspecialchars($row['Email']) . "</td>
                                                                    <td>" . htmlspecialchars($row['PhoneNumber']) . "</td>
                                                                    <td>" . htmlspecialchars($row['DateOfBirth']) . "</td>
                                                                    <td>" . htmlspecialchars($row['Address']) . "</td>
                                                                    <td>" . htmlspecialchars($row['RegDate']) . "</td>
                                                                </tr>";
                                                            $i++;
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5"></div>
                                        <div class="col-sm-7"></div>
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

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function toggleSubmenu(item) {
            const submenu = item.querySelector('.submenu');
            submenu.classList.toggle('show');
            item.classList.toggle('rotate');
        }

        $(document).ready(function () {
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var searchTerm = $('#zctb_filter input').val().toLowerCase();
                var FullName = data[1].toLowerCase();
                var Email = data[2].toLowerCase();
                var PhoneNumber = data[3].toLowerCase();
                var DateOfBirth = data[4].toLowerCase();
                var Address = data[5].toLowerCase();
                var RegDate = data[6].toLowerCase();

                return FullName.includes(searchTerm) || Email.includes(searchTerm) || PhoneNumber.includes(searchTerm) || DateOfBirth.includes(searchTerm) || Address.includes(searchTerm) || RegDate.includes(searchTerm);
            });

            var table = $('#zctb').DataTable({
                "searching": true,
                "search": {
                    "caseInsensitive": true
                },
                "dom": 'lfrtip'
            });

            $('#zctb_filter input').on('keyup', function () {
                table.draw();
            });
            $('#zctb_length').appendTo('.row .col-sm-6:first');
            $('#zctb_filter').appendTo('.row .col-sm-6:last');
            $('#zctb_info').appendTo('.row .col-sm-5');
            $('#zctb_paginate').appendTo('.row .col-sm-7');
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>