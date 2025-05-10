<?php
    session_start();
    include 'db.php';
    $error = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username=filter_input(INPUT_POST,"username",FILTER_SANITIZE_SPECIAL_CHARS);
        $password=$_POST['password'];

        $sql = "SELECT Password FROM admin WHERE UserName=?";
        $stmt=mysqli_prepare($conn,$sql);
        if(!$stmt){
            die("Prepare statement failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if(password_verify($password,$row['Password'])){
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit();
            }
            else{
                $error = true;
            }
        } else {
            $error = true;
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { 
            display: flex;
            background-color: #87CEFA;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            height: 100vh;
        }

        .login-container{
            background-color: white;
            width: 500px;
            border-radius: 5px;
            text-align: center;
            height: 350px;
        }

        .login-container h1 {
            text-align: center;
            margin: 25px auto;
        }

        .login-container input {
            width: 80%;
            height: 35px;
            border: 1px solid black;
            border-radius: 10px;
            margin: auto;
            margin: 10px auto 0 auto;
        }

        .login-container button {
            width: 80%;
            height: 35px;
            background-color: #00FA9A;
            border: none;
            border-radius: 10px;
            margin-top: 10px;
        }

        span{
            color: white;
            font-size: 15px;
        }

        .alt {
            width: 80%;
            margin: auto;
            background-color: transparent;
            color: red;
            text-align: left;
            padding-left: 10px;
        }
        
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="alt" style="<?= $error ? 'display:block;' : 'display:none;' ?>">Invalid username or password.</div>
            <button type="submit"><span>Login</span></button>
        </form>
    </div>
</body>
</html>