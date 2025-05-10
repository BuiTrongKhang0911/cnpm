<?php
session_start();

require_once "session_handler.php";
include 'db.php';

$id = '';
$vehicle = null;

if (isset($_GET['update_id'])) {
    $id = $_GET['update_id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM vehicles WHERE VehicleId = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $vehicle = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

$brands = [];
$brand_result = mysqli_query($conn, "SELECT id, brandName FROM brands");
while ($row = mysqli_fetch_assoc($brand_result)) {
    $brands[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $brand = $_POST['brand'];
    $vehicleoverview=$_POST['vehicleoverview'];
    $price = $_POST['price'];
    $fuelType = $_POST['fueltype'];
    $modelYear = $_POST['modelYear'];
    $seatingCapacity = $_POST['seatingCapacity'];
    $airConditioner = isset($_POST['airConditioner']) ? 1 : 0;
    $powerDoorLocks = isset($_POST['powerDoorLocks']) ? 1 : 0;
    $antiLockBrakingSystem = isset($_POST['antiLockBrakingSystem']) ? 1 : 0;
    $brakeAssist = isset($_POST['brakeAssist']) ? 1 : 0;
    $powerSteering = isset($_POST['powerSteering']) ? 1 : 0;
    $driverAirbag = isset($_POST['driverAirbag']) ? 1 : 0;
    $passengerAirbag = isset($_POST['passengerAirbag']) ? 1 : 0;
    $powerWindows = isset($_POST['powerWindows']) ? 1 : 0;
    $cdPlayer = isset($_POST['cdPlayer']) ? 1 : 0;
    $centralLocking = isset($_POST['centralLocking']) ? 1 : 0;
    $crashSensor = isset($_POST['crashSensor']) ? 1 : 0;
    $leatherSeats = isset($_POST['leatherSeats']) ? 1 : 0;

    $imagePaths = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024;

    for ($i = 1; $i <= 5; $i++) {
        if (isset($_FILES["image$i"]) && $_FILES["image$i"]["error"] == UPLOAD_ERR_OK) {
            $fileType = $_FILES["image$i"]["type"];
            $fileSize = $_FILES["image$i"]["size"];
            if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
                $targetDir = "img/";
                $targetFile = $targetDir . uniqid() . '_' . basename($_FILES["image$i"]["name"]);
                if (move_uploaded_file($_FILES["image$i"]["tmp_name"], $targetFile)) {
                    if (!empty($vehicle["Vimage$i"]) && file_exists($vehicle["Vimage$i"])) {
                        unlink($vehicle["Vimage$i"]);
                    }
                    $imagePaths[] = $targetFile;
                } else {
                    $imagePaths[] = $vehicle["Vimage$i"];
                }
            } else {
                $imagePaths[] = $vehicle["Vimage$i"];
                $message = "Error: Invalid file type or size for image $i.";
                $messageType = "danger";
            }
        } else {
            $imagePaths[] = $vehicle["Vimage$i"];
        }
    }   

    $sql = "UPDATE vehicles SET 
        VehiclesTitle=?, BrandId=?, VehicleOverview=?, PricePerDay=?, FuelType=?, ModelYear=?, 
        SeatingCapacity=?, AirConditioner=?, PowerDoorLocks=?, AntiLockBrakingSystem=?, 
        BrakeAssist=?, PowerSteering=?, DriverAirbag=?, PassengerAirbag=?, PowerWindows=?, 
        CDPlayer=?, CentralLocking=?, CrashSensor=?, LeatherSeats=?, 
        Vimage1=?, Vimage2=?, Vimage3=?, Vimage4=?, Vimage5=?
        WHERE VehicleId=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "sisisiiiiiiiiiiiiiisssssi",
        $title, $brand, $vehicleoverview, $price, $fuelType, $modelYear, $seatingCapacity,
        $airConditioner, $powerDoorLocks, $antiLockBrakingSystem,
        $brakeAssist, $powerSteering, $driverAirbag, $passengerAirbag, $powerWindows,
        $cdPlayer, $centralLocking, $crashSensor, $leatherSeats,
        $imagePaths[0], $imagePaths[1], $imagePaths[2], $imagePaths[3], $imagePaths[4],
        $id
    );


    if (mysqli_stmt_execute($stmt)) {
        header("Location: vehicles_list.php");
        exit;
    } else {
        echo "Error updating vehicle: " . mysqli_stmt_error($stmt);
    }
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
    <title>Add Vehicle</title>
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
                        <?php if (!empty($message)) { ?>
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <?php echo $message; ?>
                            </div>
                        <?php } ?>
						<h2 class="page-title">Edit Vehicle</h2>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">   
									<div class="panel-heading">Car information</div>
									<div class="panel-body">
                                        <form method="post" class="form-horizontal" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Vehicle Title</label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($vehicle['VehiclesTitle'] ?? ''); ?>" required="">
                                                </div>
                                                <label class="col-sm-2 control-label">Select Brand</label>
                                                <div class="col-sm-4">
                                                    <div class="btn-group bootstrap-select">
                                                        <select class="selectpicker" name="brand" required="">
                                                        <?php foreach ($brands as $b): ?>
                                                            <option value="<?php echo $b['id']; ?>" <?php if ($vehicle['BrandId'] == $b['id']) echo 'selected'; ?>>
                                                                <?php echo htmlspecialchars($b['brandName']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hr-dashed"></div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Vehical Overview</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" name="vehicleoverview" rows="3" required=""><?php echo htmlspecialchars($vehicle['VehicleOverview'] ?? ''); ?></textarea>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Price Per Day(in USD)</label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="price" class="form-control" value="<?php echo htmlspecialchars($vehicle['PricePerDay'] ?? ''); ?>" required="">
                                                </div>
                                                <label class="col-sm-2 control-label">Select Fuel Type</label>
                                                <?php 
                                                    $fuelTypes = ["Petrol", "Diesel", "CNG"];
                                                ?>
                                                <div class="col-sm-4">
                                                    <div class="btn-group bootstrap-select">
                                                        <select class="selectpicker" name="fueltype" required="" tabindex="-98">
                                                        <?php foreach ($fuelTypes as $type): ?>
                                                            <option value="<?php echo $type; ?>" <?php if ($vehicle['FuelType'] == $type) echo 'selected'; ?>>
                                                                <?php echo $type; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Model Year</label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="modelYear" class="form-control" value="<?php echo htmlspecialchars($vehicle['ModelYear'] ?? ''); ?>" required="">
                                                </div>
                                                <label class="col-sm-2 control-label">Seating Capacity</label>
                                                <div class="col-sm-4">
                                                    <input type="text" name="seatingCapacity" class="form-control" value="<?php echo htmlspecialchars($vehicle['SeatingCapacity'] ?? ''); ?>" required="">
                                                </div>
                                            </div>
                                            <div class="hr-dashed"></div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <h4><b>Upload Images</b></h4>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    Image 1<input type="file" name="image1" id="image1" accept="image/*">
                                                    <img id="img1" style="display: block; max-width: 100px; margin-top: 10px;" src="<?php echo htmlspecialchars($vehicle['Vimage1'] ?? ''); ?>">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 2<input type="file" name="image2" id="image2" accept="image/*">
                                                    <img id="img2" style="display: block; max-width: 100px; margin-top: 10px;" src="<?php echo htmlspecialchars($vehicle['Vimage2'] ?? ''); ?>">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 3<input type="file" name="image3" id="image3" accept="image/*">
                                                    <img id="img3" style="display: block; max-width: 100px; margin-top: 10px;" src="<?php echo htmlspecialchars($vehicle['Vimage3'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    Image 4<input type="file" name="image4" id="image4" accept="image/*">
                                                    <img id="img4" style="display: block; max-width: 100px; margin-top: 10px;"  src="<?php echo htmlspecialchars($vehicle['Vimage4'] ?? ''); ?>">
                                                </div>
                                                <div class="col-sm-4">
                                                    Image 5<input type="file" name="image5" id="image5" accept="image/*">
                                                    <img id="img5" style="display: block; max-width: 100px; margin-top: 10px;"  src="<?php echo htmlspecialchars($vehicle['Vimage5'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="hr-dashed"></div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <h4><b>Accessories</b></h4>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="airconditioner" name="airConditioner" value="1" <?php if ($vehicle['AirConditioner']) echo 'checked'; ?>>
                                                        <label for="airconditioner"> Air Conditioner </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="powerdoorlocks" name="powerDoorLocks" value="1" <?php if ($vehicle['PowerDoorLocks']) echo 'checked'; ?>>
                                                        <label for="powerdoorlocks"> Power Door Locks </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="antilockbrakingsys" name="antiLockBrakingSystem" value="1" <?php if ($vehicle['AntiLockBrakingSystem']) echo 'checked'; ?>>
                                                        <label for="antilockbrakingsys"> AntiLock Braking System </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="brakeassist" name="brakeAssist" value="1" <?php if ($vehicle['BrakeAssist']) echo 'checked'; ?>>
                                                        <label for="brakeassist"> Brake Assist </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="powersteering" name="powerSteering" value="1" <?php if ($vehicle['PowerSteering']) echo 'checked'; ?>>
                                                        <label for="powersteering"> Power Steering </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="driverairbag" name="driverAirbag" value="1" <?php if ($vehicle['DriverAirbag']) echo 'checked'; ?>>
                                                        <label for="driverairbag">Driver Airbag</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="passengerairbag" name="passengerAirbag" value="1" <?php if ($vehicle['PassengerAirbag']) echo 'checked'; ?>>
                                                        <label for="passengerairbag"> Passenger Airbag </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="powerwindow" name="powerWindows" value="1" <?php if ($vehicle['PowerWindows']) echo 'checked'; ?>>
                                                        <label for="powerwindow"> Power Windows </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="cdplayer" name="cdPlayer" value="1" <?php if ($vehicle['CDPlayer']) echo 'checked'; ?>>
                                                        <label for="cdplayer"> CD Player </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="centrallocking" name="centralLocking" value="1" <?php if ($vehicle['CentralLocking']) echo 'checked'; ?>>
                                                        <label for="centrallocking">Central Locking</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="crashcensor" name="crashSensor" value="1" <?php if ($vehicle['CrashSensor']) echo 'checked'; ?>>
                                                        <label for="crashcensor"> Crash Sensor </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="checkbox checkbox-inline">
                                                        <input type="checkbox" id="leatherseats" name="leatherSeats" value="1" <?php if ($vehicle['LeatherSeats']) echo 'checked'; ?>>
                                                        <label for="leatherseats"> Leather Seats </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-8 col-sm-offset-2">
                                                    <button class="btn btn-primary" name="submit" type="submit">Save changes</button>
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

    <script src="session_timeout.js"></script>
    <script src="js/dashboard.js"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });

        for (let i = 1; i <= 5; i++) {
            document.getElementById("image" + i).addEventListener("change", function(event) {
                const file = event.target.files[0];
                const img = document.getElementById("img" + i);
                if (file) {
                    const url = URL.createObjectURL(file);
                    img.src = url;
                    img.style.display = "block";
                } else {
                    img.src = "";
                    img.style.display = "none";
                }
            });
        }
        function toggleSubmenu(item) {
            const submenu = item.querySelector('.submenu');
            submenu.classList.toggle('show');
            item.classList.toggle('rotate');
        }
    </script>
</body>
</html>