<?php
    session_start();
    include 'db.php';

    $vehicleId = $_POST['vehicleId'] ?? $_SESSION['vehicleId'] ?? '';
    $fromDate = $_POST['fromDate'] ?? $_SESSION['fromDate'] ?? '';
    $fromTime = $_POST['fromTime'] ?? $_SESSION['fromTime'] ?? '';
    $toDate = $_POST['toDate'] ?? $_SESSION['toDate'] ?? '';
    $toTime = $_POST['toTime'] ?? $_SESSION['toTime'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT * FROM vehicles WHERE VehicleId = ?");
    mysqli_stmt_bind_param($stmt, "i", $vehicleId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $vehicle = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($conn, "SELECT * FROM brands WHERE Id = ?");
    mysqli_stmt_bind_param($stmt, "i", $vehicle['BrandId']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $brand = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    
    <script src="car-info.js" defer></script>
    
    <script src="https://unpkg.com/scrollreveal"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <title>GoFast - Car Information</title>
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
        <?php
            if (!isset($_SESSION['userLoggedin'])) {
                echo '
                    <button class="btn-2" onclick="showLoginForm()">
                        Login
                    </button>
                ';
            } else {
                $nameDisplay = $_SESSION['userName'];
                if (strlen($nameDisplay) > 5) {
                    $nameDisplay = substr($nameDisplay, 0, 5) . '...';
                }
                echo "
                    <button class=\"btn-2\" onclick=\"window.location.href = 'user-info.php'\">
                        $nameDisplay
                    </button>
                ";
            }
        ?>
        <div class="btn">
            <i class="fas fa-bars menu-btn"></i>
        </div>
    </nav>

    <div id="loginForm">
        <h2 style="text-align:center; margin-top: 30px;">Login</h2>
        <button type="button" class="x" onclick="closeLoginForm()"><span aria-hidden="true">&times;</span></button>
        <form id="loginFormSubmit" method="POST">
            <input type="hidden" name="vehicleId" value="<?= htmlspecialchars($vehicleId) ?>">
            <input type="hidden" name="fromDate" id="login-fromDate" value="<?= htmlspecialchars($fromDate) ?>">
            <input type="hidden" name="toDate" id="login-toDate" value="<?= htmlspecialchars($toDate) ?>">
            <input type="hidden" name="fromTime" id="login-fromTime" value="<?= htmlspecialchars($fromTime) ?>">
            <input type="hidden" name="toTime" id="login-toTime" value="<?= htmlspecialchars($toTime) ?>">
            <div id="login-error" style="color: red; display: none;"></div>
            <input type="text" name="fullname" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
            <h5>Don't have account? <a href="#" onclick="showRegisterForm()">Create account.</a></h5>
            <h5><a href="#" onclick="showForgotForm()">Forgot password</a></h5>
        </form>
    </div>

    <div id="forgotForm">
        <button type="button" class="x" onclick="closeForgotForm()"><span aria-hidden="true">&times;</span></button>
        <h2>Reset Password</h2>
        <form id="forgotPasswordForm" action="process-reset.php" method="POST">
            <div id="emailStep">
                <div id="email-error" style="color: red; display: none;"></div>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="button" onclick="sendResetEmail()">Send Reset Code</button>
                <h5>We'll send a reset code to your email</h5>
            </div>
            <div id="pinStep" style="display: none;">
                <div id="pin-error" style="color: red; display: none;"></div>
                <input type="text" name="pin" placeholder="Enter 6-digit PIN" maxlength="6" pattern="[0-9]{6}" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
                <h5>Enter the 6-digit code sent to your email</h5>
                <h4>Resend Code: <a href="#" onclick="sendResetEmail()">Resend Code</a></h4>
            </div>
        </form>
    </div>

    <div id="registerForm" style="display: none;">
        <h2 style="text-align:center; margin-top: 30px;">Register</h2>
        <button type="button" class="x" onclick="closeRegisterForm()"><span aria-hidden="true">&times;</span></button>
        <form id="registerFormSubmit" method="POST">
            <input type="hidden" name="vehicleId" value="<?= htmlspecialchars($vehicleId) ?>">
            <input type="hidden" name="fromDate" id="register-fromDate" value="<?= htmlspecialchars($fromDate) ?>">
            <input type="hidden" name="toDate" id="register-toDate" value="<?= htmlspecialchars($toDate) ?>">
            <input type="hidden" name="fromTime" id="register-fromTime" value="<?= htmlspecialchars($fromTime) ?>">
            <input type="hidden" name="toTime" id="register-toTime" value="<?= htmlspecialchars($toTime) ?>">
            <div id="register-step">
                <div id="register-error" class="error-message" style="color: red; display: none;"></div>
                <input type="text" name="fullname" placeholder="User Name" required><br>
                <div class="error-message" id="fullname-error" style="color: red; display: none;"></div>
                <input type="email" name="email" placeholder="Email" required><br>
                <div class="error-message" id="email-error" style="color: red; display: none;"></div>
                <input type="password" name="password" placeholder="Password" required><br>
                <div class="error-message" id="password-error" style="color: red; display: none;"></div>
                <input type="password" name="confirm-password" placeholder="Confirm Password" required><br>
                <div class="error-message" id="confirm-password-error" style="color: red; display: none;"></div>
                <input type="tel" name="phonenumber" placeholder="Phone Number" pattern="[0-9]+" required><br>
                <div class="error-message" id="phonenumber-error" style="color: red; display: none;"></div>
                <input type="date" name="dateofbirth" placeholder="Date of Birth" required><br>
                <div class="error-message" id="dateofbirth-error" style="color: red; display: none;"></div>
                <input type="text" name="address" placeholder="Address" required><br>
                <div class="error-message" id="address-error" style="color: red; display: none;"></div>
                <button type="button" id="register-button">Register</button>
                <h5>Already have account? <a href="#" onclick="showLoginForm()">Login now.</a></h5>
            </div>
            
            <div id="verify-step" style="display: none;">
                <div class="error-message" id="verify-error" style="color: red; display: none;"></div>
                <input type="text" name="verify-code" placeholder="Enter 6-digit code" maxlength="6" pattern="[0-9]{6}" required><br> 
                <button type="submit">Verify</button>
            </div>
        </form>
    </div>

    <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.5); z-index:999;" onclick="closeLoginForm()"></div>

    <section class="collection" id="collection-info">
        <div class="collection-category">
            <div class="collection-container" style="display: flex; gap: 20px; align-items: center; justify-content: center;">
                <div style="flex: 1;">
                    <img src="/Project/admin/<?= htmlspecialchars($vehicle['Vimage1']) ?>" alt="image" class="zoomable">
                </div>
                <div style="flex: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <img src="/Project/admin/<?= htmlspecialchars($vehicle['Vimage2']) ?>" alt="image" class="zoomable">
                    <img src="/Project/admin/<?= htmlspecialchars($vehicle['Vimage3']) ?>" alt="image" class="zoomable">
                    <img src="/Project/admin/<?= htmlspecialchars($vehicle['Vimage4']) ?>" alt="image" class="zoomable">
                    <img src="/Project/admin/<?= htmlspecialchars($vehicle['Vimage5']) ?>" alt="image" class="zoomable">
                </div>
            </div>
            <div class="container">
                <div class="car-information">
                    <h1><?= htmlspecialchars($vehicle['VehiclesTitle']) ?></h1>
                    <hr>
                    <h2>Overview</h2>
                    <div class="car-details">
                        <div class="detail-item">
                            <i class="fa fa-car-side"></i>
                            <span><?= htmlspecialchars($brand['brandName']) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-users"></i>
                            <span><?= htmlspecialchars($vehicle['SeatingCapacity']) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa-solid fa-gas-pump"></i>
                            <span><?= htmlspecialchars($vehicle['FuelType']) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-cogs"></i>
                            <span><?= htmlspecialchars($vehicle['ModelYear']) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fa fa-dollar-sign"></i>
                            <span id="temp"><?= htmlspecialchars($vehicle['PricePerDay']) ?>/Day</span>
                        </div>
                    </div>
                    <hr>    
                    <h2>Accessories</h2>
                    <div class="car-features">
                        <?php
                        $features = [
                            'AirConditioner' => ['icon' => 'fa-snowflake', 'label' => 'Air Conditioner'],
                            'PowerDoorLocks' => ['icon' => 'fa-lock', 'label' => 'Power Door Locks'],
                            'AntiLockBrakingSystem' => ['icon' => 'fa-car-crash', 'label' => 'Anti Lock Braking System'],
                            'BrakeAssist' => ['icon' => 'fa-balance-scale', 'label' => 'Brake Assist'],
                            'PowerSteering' => ['icon' => 'fa-car-crash', 'label' => 'Power Steering'],
                            'DriverAirbag' => ['icon' => 'fa-user-md', 'label' => 'Driver Airbag'],
                            'PassengerAirbag' => ['icon' => 'fa-user-injured', 'label' => 'Passenger Airbag'],
                            'PowerWindows' => ['icon' => 'fa-window-maximize', 'label' => 'Power Windows'],
                            'CDPlayer' => ['icon' => 'fa-compact-disc', 'label' => 'CD Player'],
                            'CentralLocking' => ['icon' => 'fa-lock', 'label' => 'Central Locking'],
                            'CrashSensor' => ['icon' => 'fa-bolt', 'label' => 'Crash Sensor'],
                            'LeatherSeats' => ['icon' => 'fa-couch', 'label' => 'Leather Seats'],
                        ];
                        ?>
                        <?php foreach ($features as $column => $info): ?>
                            <?php if (!empty($vehicle[$column])): ?>
                                <div class="feature">
                                    <i class="fa <?= htmlspecialchars($info['icon']) ?>"></i>
                                    <span><?= htmlspecialchars($info['label']) ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="line line-address-time"></div>
                <div class="pay-information">
                    <div class="row">
                        <h5>Pick-up Location</h5>
                        <h6>Garage</h6>
                    </div>
                    <div class="row">
                        <h5>Start Date:</h5>
                        <input type="text" id="from-date" name="fromdate" placeholder="Start Date" value="<?= htmlspecialchars($fromDate) ?>">
                        <select name="fromtime" id="from-time">
                        </select>
                    </div>

                    <div class="row">
                        <h5>End Date:</h5>
                        <input type="text" id="to-date" name="todate" placeholder="End Date" value="<?= htmlspecialchars($toDate) ?>">
                        <select name="totime" id="to-time">
                        </select>
                    </div>
                    <div class="row">
                        <h5>Message</h5>
                        <textarea id="message" placeholder="Message" oninput="autoResize(this)"></textarea>       
                    </div>
                    <div class="row">
                        <h5>Payment Method:</h5>
                        <h5>Pay on Pickup</h5>
                    </div>
                    <hr>
                    <div class="row">
                        <h5>Grand Total</h5>
                        <h5 id="total"></h5>
                    </div>
                    <div class="row">
                        <div id="renttime-error" style="color: red;"></div>
                    </div>
                    <div class="center-button">
                        <form action="check-booking.php" id="bookingForm" method="POST">
                            <input type="hidden" name="vehicleId" value="<?= htmlspecialchars($vehicleId) ?>">
                            <input type="hidden" name="start_date" id="start-date-hidden" value="<?= htmlspecialchars($fromDate) ?>">
                            <input type="hidden" name="start_time" id="start-time-hidden" value="<?= htmlspecialchars($fromTime) ?>">
                            <input type="hidden" name="end_date" id="end-date-hidden" value="<?= htmlspecialchars($toDate) ?>">
                            <input type="hidden" name="end_time" id="end-time-hidden" value="<?= htmlspecialchars($toTime) ?>">
                            <input type="hidden" name="message" id="message-hidden" value="">
                            <?php
                                if (!isset($_SESSION['userLoggedin'])) {
                                    echo '
                                        <button type="button" class="book-now" onclick="showLoginForm()"><span>Login to Book</span></button> 
                                    ';
                                } else {
                                    echo '
                                        <button type="submit" class="book-now"><span>Book Now</span></button>
                                    ';
                                }
                            ?>    
                        </form>
                    </div>
                </div>
            </div>            
    </section>  

    <div id="imagePopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center;">
        <img id="popupImg" src="" style="width:auto; height:90%; border-radius: 10px;">
    </div>

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
                <a href="index.php" class="footer-brand">Go<b class="accent">Fast</b></a>
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
</body>
</html>