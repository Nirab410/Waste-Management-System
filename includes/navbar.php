<!-- includes/navbar.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Home | EcoWaste'; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- Header -->
<header>
    <h1 style="color: #2fb463; font-size: 30px;">EcoWaste</h1>
    <nav>
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
        <a href="#footer">About Us</a>
        <a href="#footer">Contact</a>
    </nav>
    <nav class="login">
        <a href="login.php" class="login-btn">Login</a>
    </nav>
</header>