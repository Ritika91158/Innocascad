<?php
session_start();
?>
<header class="header">
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <h1>INNOCASCADE</h1>
                <span class="tagline">STUDENT INNOVATION</span>
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="explore.php">Explore</a></li>
            <li><a href="share_idea.php">Share Idea</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Login</a></li>
                <li><a href="register.php" class="btn-signup">Sign Up</a></li>
            <?php endif; ?>
        </ul>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>
</header>

<style>
.header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background: rgba(10, 10, 15, 0.9);
    backdrop-filter: blur(10px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 5%;
    max-width: 1400px;
    margin: 0 auto;
}

.logo a {
    text-decoration: none;
}

.logo h1 {
    font-size: 2rem;
    color: #fff;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 0;
}

.logo .tagline {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 2px;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 2rem;
    margin: 0;
    padding: 0;
}

.nav-links a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.95rem;
}

.nav-links a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--primary-color);
    transition: width 0.3s ease;
}

.nav-links a:hover::after {
    width: 100%;
}

.btn-login, .btn-signup, .btn-logout {
    padding: 0.5rem 1.5rem;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-login {
    background: transparent;
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-signup, .btn-logout {
    background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
    color: #fff;
    border: none;
}

.btn-login:hover, .btn-signup:hover, .btn-logout:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.menu-toggle {
    display: none;
    font-size: 1.5rem;
    color: #fff;
    cursor: pointer;
}

@media (max-width: 992px) {
    .nav-links {
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .menu-toggle {
        display: block;
    }

    .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: rgba(10, 10, 15, 0.95);
        flex-direction: column;
        padding: 1rem;
        gap: 1rem;
        text-align: center;
    }

    .nav-links.active {
        display: flex;
    }

    .btn-login, .btn-signup, .btn-logout {
        display: inline-block;
        width: 200px;
        text-align: center;
        margin: 0.5rem 0;
    }
}
</style>

<script>
document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.nav-links').classList.toggle('active');
});
</script> 