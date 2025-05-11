<?php
    session_start();
    include 'db.php';

    function sanitize_input($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    $sql = "SELECT * FROM vehicles";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $now = new \DateTime();
    $currentMinute = $now->format('i');
    $currentHour = $now->format('H');
    
    if ($currentMinute <= 29) {
        $startTime = sprintf("%02d:30", $currentHour);
    } else {
        $startTime = sprintf("%02d:00", ($currentHour + 1) % 24);
    }
    
    $startDate = $now->format('Y-m-d');
    $endDate = (clone $now)->modify('+1 day')->format('Y-m-d');
    $endTime = $startTime;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="index.js" defer></script>
    <script src="https://unpkg.com/scrollreveal"></script>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
        />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <title>GoFast</title>
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
            <div id="login-error" style="color: red; display: none;"></div>
            <input type="text" name="fullname" placeholder="User Name" required><br>
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

    <div class="hero-page">
        <div class="hero-headlines">
            <h1>
                Quick and Simple Car <b class="accent">Rental</b> with GoFast.
            </h1>
            <p>
                We provide an extensive fleet of vehicles for every journey. Whether
                you're planning a family vacation or a corporate meeting
            </p>
            <?php
                if (!isset($_SESSION['userLoggedin'])) {
                    echo '
                        <button class="btn-2 btn-hero" onclick="showLoginForm()">
                            Get Started
                        </button>
                    ';
                } else {
                    echo '
                        <button class="btn-2 btn-hero" onclick="window.location.href = \'collection.php\'">
                            Get Started
                        </button>
                    ';
                }
            ?>
        </div>
        <img src="assets/image/hero_img.png" class="hero-page-img" alt="img" />
    </div>

    <section class="about" id="about">
        <div class="about-container">
            <h1>Your Trusted Partner for Seamless Car Rentals</h1>
            <p class="about-subline">
                Experience the ultimate convenience with GoFast, where renting a
                car rental becomes a breeze with just a few taps.
            </p>

            <div class="about-info">
                <div class="about-info-item">
                    <hr class="about-hr" />
                    <img src="assets/image/about-info-item-1.png" alt="img" />
                    <h5>Efficiency</h5>
                    <p>
                        GoFast stands out for its streamlined rental process, ensuring
                        customers can instantly reserve their chosen vehicles with ease.
                    </p>
                </div>

                <div class="about-info-item">
                    <hr class="about-hr" />
                    <img src="assets/image/about-info-item-2.png" alt="img" />
                    <h5>Diverse Fleet</h5>
                    <p>
                        GoFast boasts a diverse fleet of well-maintained vehicles,
                        designed to meet every customer's unique requirements and style.
                    </p>
                </div>

                <div class="about-info-item">
                    <hr class="about-hr" />
                    <img src="assets/image/about-info-item-3.png" alt="img" />
                    <h5>Exceptional Service</h5>
                    <p>
                        Beyond just providing vehicles, GoFast is committed to
                        offering outstanding support throughout your entire journey.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="collection" id="collection">
        <h1>Our car Collection</h1>

        <div class="collection-container" id="vehicle-list">
            <?php $i=1; while ($i<=6 && $row = $result->fetch_assoc()): $i++;?>
                <div class="collection-car-item">
                    <img src="/Project/admin/<?= htmlspecialchars($row['Vimage1']) ?>" alt="img" id="collection-car-item-img" />
                    <div class="car-info-container">
                        <h2><?= htmlspecialchars($row['VehiclesTitle']) ?></h2>
                        <div class="car-info">
                            <div class="car-price">
                                <h5>$<?= htmlspecialchars($row['PricePerDay']) ?></h5>
                                <h6>/Day</h6>
                            </div>
                            <div class="car-flue">
                                <i class="fa-solid fa-gas-pump"></i>
                                <h6><?= htmlspecialchars($row['FuelType']) ?></h6>
                            </div>
                        </div>
                        <div class="car-info">
                            <div class="car-capacity">
                                <i class="fa-solid fa-person-seat"></i>
                                <h6><?= htmlspecialchars($row['SeatingCapacity']) ?> seats</h6>
                            </div>
                        </div>
                        <form action="car-info.php" method="POST">
                            <input type="hidden" name="vehicleId" value="<?= htmlspecialchars($row['VehicleId']) ?>">
                            <input type="hidden" name="fromDate" value="<?= $startDate ?>">
                            <input type="hidden" name="fromTime" value="<?= $startTime ?>">
                            <input type="hidden" name="toDate" value="<?= $endDate ?>">
                            <input type="hidden" name="toTime" value="<?= $endTime ?>">
                            <button type="submit" class="btn-2 btn-car">
                                <p>Book Now</p>
                                <i class="fa-solid fa-phone"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?> 
        </div>

        <button class="btn-car btn-herocar" onclick="window.location.href = 'collection.php'">
            <p>See All Cars</p>
            <i class="fa-solid fa-arrow-right-long"></i>
        </button>
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
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
</body>
</html>