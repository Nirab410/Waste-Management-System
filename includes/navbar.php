<!-- includes/navbar.php -->
<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Home | EcoWaste'; ?></title>
    <link rel="stylesheet" href="/includes/styles.css">
</head>
<body>

<!-- Header -->
<header>
    <h1 style="color: #2fb463; font-size: 30px;">EcoWaste</h1>
    <nav>
        <a href="/pages/HomePage/home.php">Home</a>
        <a href="#footer">About Us</a>
        <a href="#footer">Contact</a>
    </nav>
    <?php if(isset($_SESSION['user_id'])) : ?>
        <a href="/Auth/logout.php" id="logout-btn">Logout</a>
    <?php else : ?>    
        <nav class="login">
            <a href="/Auth/login.php" class="login-btn">Login</a>
        </nav>
     <?php endif; ?>   
</header>