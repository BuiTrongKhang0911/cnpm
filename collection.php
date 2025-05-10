<?php
    session_start();
    include 'db.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $startDate=$_POST['start_date'];
        $startTime=$_POST['start_time'];
        $endDate=$_POST['end_date'];
        $endTime=$_POST['end_time'];

        $fromDateTime = "$startDate $startTime:00";
        $toDateTime = "$endDate $endTime:00";

        $sql = "SELECT * FROM vehicles WHERE VehicleId NOT IN (
            SELECT VehicleId FROM booking WHERE 
            (Status LIKE 'New' OR Status LIKE 'Confirm') AND 
            NOT (ToDate <= ? OR FromDate >= ?)
        )";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param("ss", $fromDateTime, $toDateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        $vehicles = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $vehicles[] = [
                    'id' => $row['VehicleId'],
                    'image' => $row['Vimage1'],
                    'name' => $row['VehiclesTitle'],
                    'price' => $row['PricePerDay'],
                    'fuel' => $row['FuelType'],
                    'seat' => $row['SeatingCapacity']
                ];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($vehicles);

        $stmt->close();
        $conn->close();
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">   
    <script src="https://unpkg.com/scrollreveal"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <title>GoFast - Car Collection</title>
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
                    <button class="btn-2" onclick="showLoginForm() style="display: none;">
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
    
    <div class="search">
        <div class="search-form">
            <div id="time-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Select Rental Duration</h3>
                        <button id="close-time-modal" style="color: black;">x</button>
                    </div>
                    <label>Rental Time:
                        <input type="text" id="rental-time-input" placeholder="Chọn ngày" style="display: none;"/>
                        <div id="rental-time"></div>
                    </label>
                    <br>
                    <div class="time-select">
                        <label for="start-hour">Pickup Time:</label>
                        <select id="start-hour"></select>
                        <label for="end-hour">Return Time:</label>
                        <select id="end-hour"></select>
                    </div>
                    <div class="summary">
                        <p>Selected Time: <span id="summary-time"></span></p>
                        <p>Duration: <span id="duration"></span></p>
                    </div>
                    <form action="" method="POST" id="submitIndex">
                        <input type="hidden" name="start_date" id="start-date-hidden" value="">
                        <input type="hidden" name="start_time" id="start-time-hidden" value="">
                        <input type="hidden" name="end_date" id="end-date-hidden" value="">
                        <input type="hidden" name="end_time" id="end-time-hidden" value="">
                        <button class="continue-btn">Continue</button>
                    </form>
                </div>
            </div>
            <div class="search-form__item address flex-1">
                <div class="title">
                    <div class="wrap-svg"></div>
                    <i class="fas fa-map-marker-alt"></i>  Location
                </div>
                <div class="address">
                    <p>Ho Chi Minh City</p>
                </div>
            </div>
            <div class="line line-address-time"></div>
            <div class="search-form__item flex-2">
                <div class="title">
                    <div class="wrap-svg"></div>
                    <i class="fas fa-clock"></i>  Rental Period
                </div>
                <div class="choose">
                    <button id="open-time-modal" class="choose-item has-arrow">
                        <label>
                            <span id="time-range" class="value"></span>
                        </label>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="collection" id="collection-page">
        <div class="collection-category">
            <hr />
            <div class="collection-container" id="vehicle-list">
            </div>
        </div>
    </section>

    <footer class="collection-footer">
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
    
    <script src="collection.js"></script>
</body>
</html>