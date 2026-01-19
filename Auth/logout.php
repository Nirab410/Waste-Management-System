<?php
    session_start();
    session_unset();
    session_destroy();
    header("Location: /Auth/login.php"); // Redirect to login
    exit();
?>