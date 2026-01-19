<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home | EcoWaste </title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Rethink sans', Tahoma, sans-serif;
        }

        body {
            background-color: #f5f7f6;
            color: #333;
            line-height: 1.6;
        }
        /* HERO SECTION */
.hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 60px;
    gap: 40px;
}

.hero-image img {
    max-width: 420px;
    width: 100%;
    height: auto;
}

        /* Navbar */
        header {
            background: #FEFEFE;
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: rgb(0, 0, 0);
            box-sizing: border-box;
        }

        header h1 {
            font-size: 26px;
        }

        nav a {
            color: rgb(0, 0, 0);
            text-decoration: none;
            margin-left: 20px;
            font-size: 16px;
        }
        .login-btn {
            background-color: #2fb463; /* green */
            color: #000;
            padding: 12px 40px;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: rgb(210, 245, 205);
        }

      .navber{
            max-width: 900px;
            padding: 40px;
      }
      .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #dff3ea;
        color: #1f7f57;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 25px;
    }

    .badge span {
        font-size: 16px;
    }

    h1 {
        font-size: 56px;
        font-weight: 800;
        line-height: 1.1;
        color: #000;
        margin-bottom: 20px;
    }

    h1 .green {
        color: #27ae60;
    }

    p {
        max-width: 600px;
        font-size: 18px;
        color: #333;
        line-height: 1.6;
    }

        /* Awareness Section */
        .awareness {
            padding: 60px;
            background: white;
        }

        .awareness h2 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 20px;
        }

        .awareness p {
            text-align: center;
            max-width: 800px;
            margin: auto;
            margin-bottom: 40px;
        }

        .cards {
            display: flex;
            gap: 30px;
            justify-content: center;
        }

        .card {
            background: #f1f8f4;
            padding: 30px;
            width: 300px;
            border-radius: 8px;
        }

        .card h3 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        /* Access Portals */
.portals h2 {
    text-align: center;
}
.portals{
      padding: 40px;
}

.portal-grid {
      margin: 20px;
    display: grid;
    grid-template-columns: repeat(4, 2fr);
    gap: 80px;
}

/* CARD */
.portal {
    color: #fff;
    padding: 28px;
    min-height: 320px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.portal1{
      background-color: #24A058;
      border-radius: 10px;
      margin-bottom: 45px;
}
.portal2{
      background-color:#3170EC;
      border-radius: 10px;
      margin-top: 45px;
      
}
.portal3{
      background-color: #9842E7;
      border-radius: 10px;
      margin-bottom: 45px;
}
.portal4{
      background-color: #E46012;
      border-radius: 10px;
      margin-top: 45px;
}
/* --- THE STAGGER EFFECT --- */
/* This pushes the 2nd and 4th cards down */

/* ICON */
.portal .icon {
    width: 48px;
    height: 48px;
    margin-bottom: 10px;
}

.portal h4 {
    font-size: 26px;
    margin: 10px 0;
}

.portal p {
    font-size: 15px;
    line-height: 1.6;
    opacity: 0.95;
}

/* BUTTON */
.portal .btn {
    margin-top: 20px;
    border: 2px solid #000;
    background: transparent;
    color: #fff;
    padding: 12px 20px;
    border-radius: 30px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    width: fit-content;
}

.portal .btn span {
    font-size: 18px;
}
        /* Footer */
        footer {
            background: #000000;
            color: white;
            padding: 50px 60px;
            opacity: 1;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 30px;
            color: white;
        }
        .footer-grid p{
            color:White;
        }

        footer h3 {
            margin-bottom: 15px;
            
        }

        footer a {
            display: block;
            color: white;
            text-decoration: none;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .copyright {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            opacity: 0.8;
        }
    </style>
</head>
<body>

<!-- Header -->
<header>
    <h1 style="color: #2fb463; font-size: 30px;">EcoWaste</h1>
    <nav>
        <a href="home.html" style="color: #2fb463;">Home</a>
        <a href="#footer">About Us</a>
        <a href="#footer">Contact</a>
    </nav>
    <nav class="login">
              <a href="login.html" class="login-btn">Login</a>
      </nav>
</header>

<!-- Hero Section -->
<div class="hero">
    <div class="navber">
        <div class="badge">
            <span>♻️</span>
            Sustainable Future
        </div>

        <h1>
            Smart Waste<br>
            Collection for<br>
            <span class="green">a Cleaner Tomorrow</span>
        </h1>

        <p>
            Manage waste efficiently with modern technology
            and collaborate with your community for a
            sustainable environment.
        </p>
    </div>

    <div class="hero-image">
        <img src="image.png" alt="Reuse Reduce Recycle">
    </div>
</div>


<!-- Awareness Section -->
<section class="awareness">
    <h2>Waste Awareness</h2>
    <p>
        Understanding proper waste management is crucial for our environment.
        Every action counts towards building a sustainable future for generations to come.
    </p>

    <div class="cards">
        <div class="card">
            <img src="reduce.png" alt="img">
            <h3>Reduce</h3>
            <p>
                Minimize waste generation by choosing products with less packaging
                and avoiding single-use items.
            </p>
        </div>

        <div class="card">
            <img src="reuse.png" alt="img">
            <h3>Reuse</h3>
            <p>
                Give items a second life before discarding them. Donate unused goods
                and choose reusable alternatives.
            </p>
        </div>

        <div class="card">
            <img src="recycle.png" alt="img">
            <h3>Recycle</h3>
            <p>
                Transform waste into valuable resources by properly sorting materials
                like paper, plastic, glass, and metal.
            </p>
        </div>
    </div>
</section>

<!-- Access Portals -->
<section class="portals" id="login">
    <h2>Access Portals</h2>

    <div class="portal-grid">

        <div class="portal1">
            <div class="portal">
            <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" class="icon">
            <h4>Admin Portal</h4>
            <p>Manage system settings, users, and monitor overall operations.</p>
            <a href="ad_demo.html" class="btn" style="text-decoration: none;">
    Access Portal <span>→</span>
  </a>
        </div>
        </div>

        <div class="portal2">
            <div class="portal">
            <img src="https://cdn-icons-png.flaticon.com/512/3596/3596091.png" class="icon">
            <h4>Collector Portal</h4>
            <p>View assigned routes, update collection status, and manage tasks.</p>
           <a href="cd_demo.html" class="btn" style="text-decoration: none;">
    Access Portal <span>→</span>
  </a>
        </div>
        </div>

        <div class="portal3">
            <div class="portal">
            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077063.png" class="icon">
            <h4>Resident Portal</h4>
            <p>Schedule pickups, report issues, and track collection history.</p>
            <a href="ResidentPortal.html" class="btn" style="text-decoration: none;">
    Access Portal <span>→</span>
  </a>
        </div>
        </div>

        <div class="portal4">
            <div class="portal">
            <img src="https://cdn-icons-png.flaticon.com/512/3050/3050525.png" class="icon">
            <h4>Recycling Center</h4>
            <p>Manage inventory, track processing, and coordinate pickups.</p>
            <a href="RecyclingCenter.html" class="btn" style="text-decoration: none;">
    Access Portal <span>→</span>
  </a>
        </div>
        </div>

    </div>
</section>


<!-- Footer -->
<footer id="footer">
    <div class="footer-grid">
        <div>
            <h3>EcoWaste</h3>
            <p>
                Building a sustainable future through smart waste management
                and community collaboration.
            </p>
        </div>

        <div>
            <h3>Quick Links</h3>
            <a href="#">Home</a>
            <a href="#">About Us</a>
            <a href="#">Contact</a>
        </div>

        <div>
            <h3>Help Center</h3>
            <a href="#">FAQs</a>
            <a href="#">Support</a>
        </div>

        <div>
            <h3>Contact Info</h3>
            <p>Email: info@ecowaste.com</p>
            <p>Phone: +1 (555) 123-4567</p>
            <p>Address: 123 Green Street, Eco City</p>
        </div>
    </div>

    <div class="copyright">
        © 2026 EcoWaste Management System. All rights reserved.
    </div>
</footer>
<script>
/* =================================================
   GLOBAL HELPERS
================================================= */
const $ = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);


/* =================================================
   1. SMOOTH SCROLL
================================================= */
$$('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
        const target = $(link.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});


/* =================================================
   2. ACTIVE NAVBAR LINK
================================================= */
const sections = $$('section');
const navLinks = $$('nav a');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(sec => {
        if (scrollY >= sec.offsetTop - 120) {
            current = sec.id;
        }
    });

    navLinks.forEach(a => {
        a.style.fontWeight = 'normal';
        if (a.getAttribute('href') === `#${current}`) {
            a.style.fontWeight = '700';
        }
    });
});


/* =================================================
   3. SCROLL REVEAL
================================================= */
const revealEls = $$('.card, .portal1, .portal2, .portal3, .portal4');

revealEls.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(50px)';
    el.style.transition = '0.7s ease';
});

function reveal() {
    revealEls.forEach(el => {
        if (el.getBoundingClientRect().top < innerHeight - 100) {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }
    });
}
window.addEventListener('scroll', reveal);
reveal();


/* =================================================
   4. PORTAL HOVER EFFECT
================================================= */
$$('.portal').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'scale(1.05)';
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'scale(1)';
    });
});



/* =================================================
   6. COUNTER ANIMATION
================================================= */
function counter(el, target) {
    let count = 0;
    const inc = target / 100;
    const interval = setInterval(() => {
        count += inc;
        el.innerText = Math.floor(count);
        if (count >= target) {
            el.innerText = target;
            clearInterval(interval);
        }
    }, 20);
}

const stats = document.createElement('div');
stats.innerHTML = `
<div style="display:flex;gap:40px;justify-content:center;padding:40px">
  <div><h2 id="c1">0</h2><p>Tons Collected</p></div>
  <div><h2 id="c2">0</h2><p>Active Users</p></div>
  <div><h2 id="c3">0</h2><p>Recycling Centers</p></div>
</div>`;
document.querySelector('.awareness').appendChild(stats);

/* =================================================
   DELAYED SCROLL-BASED COUNTER
================================================= */
let counterStarted = false;

function startCounters() {
    if (counterStarted) return;

    const section = document.querySelector('.awareness');
    const sectionTop = section.getBoundingClientRect().top;

    if (sectionTop < window.innerHeight - 150) {
        counterStarted = true;

        setTimeout(() => {
            counter(document.getElementById('c1'), 1200);
            counter(document.getElementById('c2'), 450);
            counter(document.getElementById('c3'), 32);
        }, 0); 
    }
}

window.addEventListener('scroll', startCounters);


/* =================================================
   7. ROLE-BASED PORTAL DETECTION + TOAST
================================================= */
$$('.portal .btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const role = btn.closest('.portal').querySelector('h4').innerText;
        toast(`Opening ${role}`);
    });
});

function toast(msg) {
    const t = document.createElement('div');
    t.innerText = msg;
    t.style = `
    position:fixed; bottom:90px; right:30px;
    background:#2fb463; color:#000;
    padding:12px 20px; border-radius:8px;
    `;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}
</script>



</body>
</html>
