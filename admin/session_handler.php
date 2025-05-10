<?php
    $timeout = 600; 

    if (isset($_SESSION['last_activity'])) {
        if ((time() - $_SESSION['last_activity']) > $timeout) {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        }
    }

    $_SESSION['last_activity'] = time();
?>