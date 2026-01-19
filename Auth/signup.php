<?php 
$page_title = "EcoWaste | Sign Up";
include '../includes/navbar.php'; 
?>

<!-- Add signup.css after the navbar include -->
<link rel="stylesheet" href="styles/signup.css">

<?php
// Include database connection
include '../dbConnection.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $address = trim($_POST['address']);
    
    // Validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($role)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        try {
            // Check if email already exists
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkEmail->execute([$email]);
            
            if ($checkEmail->rowCount() > 0) {
                $error = "Email already registered!";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $sql = "INSERT INTO users (full_name, email, password, role, phone, address, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$full_name, $email, $hashedPassword, $role, $phone, $address]);
                
                $success = "Registration successful! You can now login.";
                
                // Redirect to login after 2 seconds
                header("refresh:2;url=login.php");
            }
        } catch (PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<div class="body">
    <div class="signup-card">
        <h1>Sign Up</h1>
        <p class="subtitle">Please fill your details for sign up</p>

        <?php if ($error): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" class="input-field" name="full_name" placeholder="Full name" required>
            
            <input type="email" class="input-field" name="email" placeholder="Email" required>
            
            <input type="tel" class="input-field" name="phone" placeholder="Phone number" required>
            
            <select class="input-field" name="role" required>
                <option value="">Select Role</option>
                <option value="RESIDENT">Resident</option>
                <option value="COLLECTOR">Collector</option>
                <option value="CENTER_CONTROLLER">Center Controller</option>
                <option value="ADMIN">Admin</option>
            </select>
            
            <textarea class="input-field" name="address" placeholder="Address" rows="3" required></textarea>
            
            <input type="password" class="input-field" name="password" placeholder="Password (min 6 characters)" required>
            
            <button type="submit" class="signup-btn">Sign up</button>
        </form>
        
        <p class="footer-text">
            Already have an account? <a href="login.php">Log In</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>