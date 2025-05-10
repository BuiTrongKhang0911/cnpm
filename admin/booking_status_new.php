<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

require_once "session_handler.php";
include 'db.php';

$sql = "SELECT * FROM booking where Status like 'New'";
$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="dashboard.css">
    <title>Booking</title>
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
                        <h2 class="page-title">New Bookings</h2>
                        <div class="panel panel-default">
                            <div class="panel-heading">Bookings Info</div>
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
                                                    <tr role="row">
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 34.2px;">#</th>
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 140.2px;">User Name</th>
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 140.2px;">Vehicle Name</th>
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 184.2px;">From Date</th>
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 184.2px;">To Date</th>
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 140.2px;">Status</th>
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 184.2px;">Posting Date</th>
                                                        <th tabindex="0" rowspan="1" colspan="1" style="width: 100px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (mysqli_num_rows($result) > 0) {
                                                        $i = 1;
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            $vehicleName = "Unknown";
                                                            $vehicleSql = "SELECT VehiclesTitle FROM vehicles WHERE VehicleId = " . $row['VehicleId'];
                                                            $vehicleResult = mysqli_query($conn, $vehicleSql);
                                                            if ($vehicleResult && $vehicleRow = mysqli_fetch_assoc($vehicleResult)) {
                                                                $vehicleName = $vehicleRow['VehiclesTitle'];
                                                            }

                                                            $userName = "Unknown";
                                                            $userSql = "SELECT FullName FROM users WHERE Id = " . $row['userId'];
                                                            $userResult = mysqli_query($conn, $userSql);
                                                            if ($userResult && $userRow = mysqli_fetch_assoc($userResult)) {
                                                                $userName = $userRow['FullName'];
                                                            }
                                                            echo "<tr>
                                                                    <td>{$i}</td>
                                                                    <td>{$userName}</td>
                                                                    <td>{$vehicleName}</td>
                                                                    <td>{$row['FromDate']}</td>
                                                                    <td>{$row['ToDate']}</td>
                                                                    <td>{$row['Status']}</td>
                                                                    <td>{$row['PostingDate']}</td>
                                                                    <td>
                                                                        <form method='GET' action='bookingDetailForNew.php' style='display:inline'>
                                                                            <input type='hidden' name='view_id' value='{$row['BookingNumber']}'>
                                                                            <button type='submit'>View</button>
                                                                        </form>
                                                                    </td>
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
                                        <div class="col-sm-7">
                                            <div class="dataTables_paginate paging_simple_numbers" id="zctb_paginate"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="session_timeout.js"></script>
    <script src="js/dashboard.js"></script>
    <script>
        function toggleSubmenu(item) {
            const submenu = item.querySelector('.submenu');
            submenu.classList.toggle('show');
            item.classList.toggle('rotate');
        }

        $(document).ready(function () {
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var searchTerm = $('#zctb_filter input').val().toLowerCase();
                var userName = data[1].toLowerCase();
                var vehicleName = data[2].toLowerCase();
                var fromDate = data[3].toLowerCase();
                var toDate = data[4].toLowerCase();
                var status = data[5].toLowerCase();
                var postingDate = data[6].toLowerCase();

                return userName.includes(searchTerm) || vehicleName.includes(searchTerm) || fromDate.includes(searchTerm) || toDate.includes(searchTerm) || status.includes(searchTerm) || postingDate.includes(searchTerm);
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