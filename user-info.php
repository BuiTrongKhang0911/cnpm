<?php
session_start();
if (!isset($_SESSION['userLoggedin'])) {
    header("Location: index.php");
    exit;
}
include 'db.php';

$userID = $_SESSION['userId'];

$sql = "SELECT * FROM users WHERE Id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Error prepare (users): " . $conn->error;
    exit;
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found!";
    exit;
}
$stmt->close();

$sql = "SELECT * FROM booking WHERE userId = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Error prepare (booking): " . $conn->error;
    exit;
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$bookings = $stmt->get_result(); 
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="user-info.js" defer></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <title>GoFast - User Information</title>
</head>
<body>
    <nav>
        <a href="index.php" class="brand">
            <h1>Go<b class="accent">Fast</b></h1>
        </a>
        <div class="menu">
            <div class="btn">
                <i class="fas fa-times close-btn"></i>
            </div>
            <?php
                if (!isset($_SESSION['userLoggedin'])) {
                    echo '
                        <a href="#" class="btn-2-login" id="btn-2-login" onclick="showLoginForm()">LOGIN</a>
                    ';
                } else {
                    $nameDisplay = $_SESSION['userName'];
                    if (strlen($nameDisplay) > 5) {
                        $nameDisplay = substr($nameDisplay, 0, 5) . '...';
                    }
                    echo "
                        <a href=\"user-info.php\" class=\"btn-2-login\" id=\"btn-2-login\">$nameDisplay</a>
                    ";
                }
            ?>
            <a href="index.php">HOME</a>
            <a href="collection.php">CARS</a>
            <a href="index.php#about">ABOUT</a>
        </div>
        <button style="width: 160px; height: 50px; background-color: transparent; border: none">
        </button>
        <div class="btn">
            <i class="fas fa-bars menu-btn"></i>
        </div>
    </nav>

    <section class="collection" id="collection-page">
        <div class="collection-category">
            <hr />  
            <div class="collection-container" id="vehicle-list" style="display: flex;">
                <div id="sidebar-menu" style="flex: 1; background: white; padding: 20px; text-align: right;">
                    <h2>My Account</h2>
                    <ul style="list-style: none; padding: 0;">
                        <li><button onclick="showSection('user-info')">User Details</button></li>
                        <li><button onclick="showSection('change-password')">Change Password</button></li>
                        <li><button onclick="showSection('booking-history')">Booking History</button></li>
                        <li><button onclick="logout()">Logout</button></li>
                    </ul>
                </div>
                <div class="line line-address-time"></div>
                <div id="content-form" style="flex: 3; background: #f8f9fa; padding: 20px;">
                    <div id="user-info" class="form-section" style="display: none;">
                        <h3>User information</h3>
                        <div style="display: flex; flex-direction: column; gap: 15px; padding-left: 15px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <h5 style="min-width: 150px;">Reg Date - </h5>
                                <h6><?= htmlspecialchars($user['RegDate'])?></h6>
                            </div>
                
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <h5 style="min-width: 150px;">Last Update at - </h5>
                                <h6><?= htmlspecialchars($user['UpdateDate'])?></h6>
                            </div>

                            <form action="change-info.php" method="POST" id="changeUserInfo">
                                <div id="update-error" class="error-message" style="color: red;"></div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">User Name</h5>
                                    <input type="text" name="fullname" placeholder="Full name" style="flex: 1;" value="<?= htmlspecialchars($user['FullName'])?>" readonly required>
                                </div>
                                <div class="error-message" id="fullname-error" style="color: red; display: none;"></div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">Email Address</h5>
                                    <input type="email" name="email" placeholder="email@gmail.com" style="flex: 1;" value="<?= htmlspecialchars($user['Email'])?>" readonly required>
                                </div>
                                <div class="error-message" id="email-error" style="color: red; display: none;"></div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">Phone Number</h5>
                                    <input type="text" name="phone" placeholder="0909111111" style="flex: 1;" value="<?= htmlspecialchars($user['PhoneNumber'])?>" required>
                                </div>
                                <div class="error-message" id="phone-error" style="color: red; display: none;"></div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">Date of Birth</h5>
                                    <input type="date" name="dob" id="date-input" placeholder="dd/mm/yyyy" maxlength="10" style="flex: 1;" value="<?= htmlspecialchars($user['DateOfBirth'])?>" required>
                                </div>
                                <div class="error-message" id="dob-error" style="color: red; display: none;"></div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">Your Address</h5>
                                    <textarea id="textarea-address" style="flex: 1; min-height: 60px; resize: none; overflow: hidden;" oninput="autoResize(this)"><?= htmlspecialchars($user['Address'])?></textarea>
                                    <input type="hidden" name="address" value="<?= htmlspecialchars($user['Address'])?>" required>
                                </div>
                                <div class="error-message" id="address-error" style="color: red; display: none;"></div>
                                <button type="submit" id="change-info-submit" style="margin-top: 20px; padding: 10px 20px;">Save change</button>
                            </form>
                        </div>
                    </div>

                    <div id="change-password" class="form-section" style="display: none;">
                        <h3>Change Password</h3>
                        <div style="display: flex; flex-direction: column; gap: 15px; padding-left: 15px;">
                            <form action="change_password.php" method="POST" id="changePassword">
                                <div style="color: red;" id="error"></div>
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">Current Password</h5>
                                    <input type="password" name="oldpassword" style="flex: 1;" required>
                                </div>
                    
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">New Password</h5>
                                    <input type="password" name="newpassword" style="flex: 1;" required>
                                </div>  
                    
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <h5 style="min-width: 150px;">Confirm New Password</h5>
                                    <input type="password" name="confirmpassword" style="flex: 1;" required>
                                </div>
                                <button type="submit" style="margin-top: 20px; padding: 10px 20px;">Save change</button>
                            </form>
                        </div>
                    </div>

                    <div id="booking-history" class="form-section" style="display: none;">
                        <?php 
                        if (mysqli_num_rows($bookings) > 0):
                            while ($row = mysqli_fetch_assoc($bookings)): 
                                $vehicleId = $row['VehicleId'];
                                $vsql = "SELECT * FROM vehicles WHERE VehicleId = ?";
                                $vstmt = mysqli_prepare($conn, $vsql);
                                if (!$vstmt) {
                                    echo "Error prepare (vehicles): " . $conn->error;
                                    continue;
                                }
                                mysqli_stmt_bind_param($vstmt, "i", $vehicleId);
                                mysqli_stmt_execute($vstmt);
                                $vehicleResult = mysqli_stmt_get_result($vstmt);
                                $vehicle = mysqli_fetch_assoc($vehicleResult);
                                mysqli_stmt_close($vstmt);

                                if (!$vehicle) {
                                    continue;
                                }

                                $fromDateTime = $row['FromDate'];
                                $toDateTime = $row['ToDate'];
                                $from = new DateTime($fromDateTime);
                                $to = new DateTime($toDateTime);
                                $interval = $from->diff($to);
                                $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
                                $day = floor($hours / 24);
                                $remain = $hours - $day * 24;
                                if ($remain > 12) {
                                    $day += 1;
                                } elseif ($remain > 0) {
                                    $day += 0.5;
                                }
                                $total = $vehicle['PricePerDay'] * $day;
                        ?>
                                <h2 style="color: red; font-weight: bold;">Booking No <?= htmlspecialchars($row['BookingNumber']) ?></h2>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px;">
                                    <div style="display: flex; align-items: center; gap: 20px;">
                                        <img src="/Project/admin/<?= htmlspecialchars($vehicle['Vimage1']) ?>" alt="img" style="width: 150px; height: auto; border-radius: 8px; object-fit: cover;">
                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                            <h4 style="margin: 0;"><?= htmlspecialchars($vehicle['VehiclesTitle']) ?></h4>
                                            <p style="margin: 0; color: gray;">From <?= htmlspecialchars($row['FromDate']) ?> To <?= htmlspecialchars($row['ToDate']) ?></p>
                                            <p style="margin: 0; color: gray;">Message: <span style="color: black;"><?= htmlspecialchars($row['message']) ?></span></p>
                                            <p style="margin: 0; color: gray;">Total Days: <span style="color: black;"><?= htmlspecialchars($day) ?></span></p>
                                            <p style="margin: 0; color: gray;">Total: <span style="color: black;">$<?= htmlspecialchars($total) ?></span></p>
                                        </div>
                                    </div>
                                    <button style="padding: 10px 20px; background: none; border: 1px solid red; color: red; border-radius: 5px; font-weight: bold;">
                                        <?php 
                                            if($row['Status']=='New'){
                                                echo "Not Confirm yet";
                                            }
                                            else if($row['Status']=='Confirm'){
                                                echo "Confirmed";
                                            }
                                            else if($row['Status']=='Cancel'){
                                                echo "Canceled";
                                            }
                                        ?>
                                    </button>
                                </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <div style="text-align: center; padding: 20px;">
                                <h3 style="color: #666; margin-bottom: 10px;">You haven't booked any vehicles yet</h3>
                                <p style="color: #888;">Explore and book your vehicle now!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="index-footer">
        <div class="callout">
            <h2>Let's drive with GoFast Today!</h2>
            <p class="callout-description">
                Need assistance or ready to reserve your wheels? Contact us now for
                prompt service and effortless booking!
            </p>
        </div>

        <div class="footer-bottom">
            <div>
                <a href="/" class="footer-brand">Go<b class="accent">Fast</b></a>
            <div>
                <h5 style="color: white;">Hotline: 1900 1111</h5>
                <h5 style="color: white;">Email: contact@gofast.vn</h5>
                <h5 style="color: white;">Address: 123 Nguyễn Văn Cừ Street, Ward 2, District 5, Ho Chi Minh City</h5>
            </div>
            </div>
            <div class="socials">
                <a href="/" class="social-item">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="/" class="social-item">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="/" class="social-item">
                    <i class="fa-brands fa-x-twitter"></i>
                </a>
                <a href="/" class="social-item">
                    <i class="fa-brands fa-telegram"></i>
                </a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
    <?php
    $conn->close();
    ?>
</body>
</html>