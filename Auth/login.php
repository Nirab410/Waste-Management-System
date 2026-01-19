<?php 
$page_title = "EcoWaste | Login";
include '../includes/navbar.php'; 
?>

<!-- Add login.css after the navbar include -->
<link rel="stylesheet" href="styles/login.css">

<div class="body">
    <div class="login-card">
        <h1>Log In</h1>
        <p class="subtitle">Please fill your details for log in</p>

        <form>
            <input type="text" class="input-field" placeholder="Username or email">
            <input type="password" class="input-field" placeholder="Password">
            
            <a href="#" class="forgot-password">Forgotten Password?</a>
            
            <button type="submit" class="login-btn">Log In</button>
        </form>
        <p class="footer-text">
            Don't have an account? <a style="text-decoration: none;" href="/Auth/signup.php">Sign Up</a>
        </p>
    </div>
</div>

<script>
// Helper function to select
const $ = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);

/* INPUT FOCUS EFFECT */
$$('.input-field').forEach(input => {
    input.addEventListener('focus', () => {
        input.style.backgroundColor = '#e0f0d9';
        input.style.boxShadow = '0 0 5px #96c078';
    });
    input.addEventListener('blur', () => {
        input.style.backgroundColor = '#dfe6d9';
        input.style.boxShadow = 'none';
    });
});

/* SHOW/HIDE PASSWORD TOGGLE */
const passwordInput = document.querySelector('input[type="password"]');

const toggleBtn = document.createElement('span');
toggleBtn.innerText = '👁️';
toggleBtn.style = `
position: absolute; right: 25px; top: 50%;
transform: translateY(-50%);
cursor: pointer; font-size: 16px;
`;

const loginCard = document.querySelector('.login-card');
loginCard.style.position = 'relative';
passwordInput.parentNode.appendChild(toggleBtn);

toggleBtn.addEventListener('click', () => {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.innerText = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleBtn.innerText = '👁️';
    }
});

/* FORGOT PASSWORD ALERT */
$('.forgot-password').addEventListener('click', e => {
    e.preventDefault();
    alert("🔑 Please contact support to reset your password!");
});

/* FORM SUBMIT VALIDATION */
const form = document.querySelector('form');
form.addEventListener('submit', e => {
    e.preventDefault();
    const username = form.querySelector('input[type="text"]').value.trim();
    const password = passwordInput.value.trim();

    if (!username || !password) {
        alert("⚠️ Please fill in both fields!");
        return;
    }

    // Demo success alert
    alert(`✅ Logged in as ${username}`);
    form.reset();
});

/* BUTTON ANIMATION ON CLICK */
$('.login-btn').addEventListener('mousedown', () => {
    $('.login-btn').style.transform = 'scale(0.95)';
});
$('.login-btn').addEventListener('mouseup', () => {
    $('.login-btn').style.transform = 'scale(1)';
});
</script>

<?php include '../includes/footer.php'; ?>