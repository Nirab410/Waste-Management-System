<?php 
$page_title = "EcoWaste | Sign Up";
include '../includes/navbar.php'; 
?>

<!-- Add signup.css after the navbar include -->
<link rel="stylesheet" href="styles/signup.css">

<div class="body">
    <div class="signup-card">
        <h1>Sign Up</h1>
        <p class="subtitle">Please fill your details for sign up</p>

        <form>
            <input type="text" class="input-field" placeholder="First name" required>
            <input type="text" class="input-field" placeholder="Last name" required>
            <input type="tel" class="input-field" placeholder="Phone number" required>
            <input type="email" class="input-field" placeholder="email" required>
            <input type="password" class="input-field" placeholder="Password" required>
            
            <button type="submit" class="signup-btn">Sign up</button>
        </form>
        
        <p class="footer-text">
            Already have an account? <a href="/Auth/login.php">Log In</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>