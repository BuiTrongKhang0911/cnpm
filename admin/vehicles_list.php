<?php
    session_start();
    if (!isset($_SESSION['loggedin'])) {
        header("Location: login.php");
        exit;
    }

    require_once "session_handler.php";
    include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $idToDelete = $_POST['delete_id'];

        $stmt = mysqli_prepare($conn, "DELETE FROM vehicles WHERE VehicleId = ?");
        mysqli_stmt_bind_param($stmt, "i", $idToDelete);

        if (mysqli_stmt_execute($stmt)) {
        } else {
            echo "Error deleting vehicle: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);

        header("Location: vehicles_list.php");
        exit;
    }

    $sql = "SELECT v.*, b.brandName FROM vehicles v JOIN brands b ON v.BrandId = b.id";
    $result = mysqli_query($conn,$sql);
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
    <link rel="stylesheet" href="vehicles_list.css">
    <title>Vehicles List</title>
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
                        <h2 class="page-title">Vehicles List</h2>
                        <div class="panel panel-default">
                            <div class="panel-heading">Listed Vehicles</div>
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
                                                        <th tabindex="0" rowspan="1" colspan="1">#</th>
                                                        <th tabindex="0" rowspan="1" colspan="1">Vehicles Title</th>
                                                        <th tabindex="0" rowspan="1" colspan="1">Vehicles Brand</th>
                                                        <th tabindex="0" rowspan="1" colspan="1">Price Per Day</th>
                                                        <th tabindex="0" rowspan="1" colspan="1">Fuel Type</th>
                                                        <th tabindex="0" rowspan="1" colspan="1">Model Year</th>
                                                        <th tabindex="0" rowspan="1" colspan="1">Seating Capacity</th>
                                                        <th tabindex="0" rowspan="1" colspan="1">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (mysqli_num_rows($result) > 0) {
                                                        $i = 1;
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo "<tr>
                                                                    <td>{$i}</td>
                                                                    <td>" . htmlspecialchars($row['VehiclesTitle']) . "</td>
                                                                    <td>" . htmlspecialchars($row['brandName']) . "</td>
                                                                    <td>" . htmlspecialchars($row['PricePerDay']) . "</td>
                                                                    <td>" . htmlspecialchars($row['FuelType']) . "</td>
                                                                    <td>" . htmlspecialchars($row['ModelYear']) . "</td>
                                                                    <td>" . htmlspecialchars($row['SeatingCapacity']) . "</td>
                                                                    <td>
                                                                        <form method='GET' action='edit_vehicle.php' style='display:inline-block; margin-right: 5px;'>
                                                                            <input type='hidden' name='update_id' value='" . htmlspecialchars($row['VehicleId']) . "'>
                                                                            <button type='submit' class='btn' style='background-color: transparent;'>
                                                                                <i class='fa fa-edit'></i>
                                                                            </button>
                                                                        </form>
                                                                        <form method='POST' action='' style='display:inline-block;'>
                                                                            <input type='hidden' name='delete_id' value='" . htmlspecialchars($row['VehicleId']) . "'>
                                                                            <button type='submit' class='btn' style='background-color: transparent;' onclick=\"return confirm('Are you sure you want to delete this vehicle?');\">
                                                                                <i class='fa fa-close'></i>
                                                                            </button>
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
                var VehiclesTitle = data[1].toLowerCase();
                var brandName = data[2].toLowerCase();
                var PricePerDay = data[3].toLowerCase();
                var FuelType = data[4].toLowerCase();
                var ModelYear = data[5].toLowerCase();
                var SeatingCapacity = data[6].toLowerCase();

                return VehiclesTitle.includes(searchTerm) || brandName.includes(searchTerm) || PricePerDay.includes(searchTerm) || FuelType.includes(searchTerm) || ModelYear.includes(searchTerm) || SeatingCapacity.includes(searchTerm);
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
    mysqli_close($conn);
?>