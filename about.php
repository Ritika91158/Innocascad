<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Innocascade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-color: #ff5500;
            --secondary-color: #3a0ca3;
            --background-dark: #0d0d0d;
            --text-light: #ffffff;
            --text-dark: #0d0d0d;
            --card-bg: rgba(255, 255, 255, 0.9);
            --gradient-bg: linear-gradient(135deg, #ff5500, #3a0ca3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--background-dark);
            color: var(--text-light);
            overflow-x: hidden;
        }

        /* Header Styling */
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

        .logo h1 {
            font-size: 2rem;
            color: #fff;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: linear-gradient(to right, #ff5a1f, #8e24aa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
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

        /* Main Content Styling */
        .about-container {
            position: relative;
            padding: 120px 2rem 4rem;
            background: var(--background-dark);
            overflow: hidden;
        }

        /* Background Animation */
        .animated-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 85, 0, 0.3), rgba(58, 12, 163, 0.3));
            z-index: -1;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: var(--gradient-bg);
            filter: blur(60px);
            opacity: 0.3;
            animation: float 15s infinite ease-in-out;
        }

        .circle:nth-child(1) {
            width: 500px;
            height: 500px;
            top: -200px;
            right: -100px;
            animation-delay: 0s;
        }

        .circle:nth-child(2) {
            width: 400px;
            height: 400px;
            bottom: -150px;
            left: -100px;
            background: linear-gradient(135deg, rgba(58, 12, 163, 0.5), rgba(255, 85, 0, 0.5));
            animation-delay: -5s;
        }

        .circle:nth-child(3) {
            width: 300px;
            height: 300px;
            top: 40%;
            left: 30%;
            background: linear-gradient(135deg, rgba(255, 85, 0, 0.5), rgba(58, 12, 163, 0.5));
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1);
            }
            25% {
                transform: translate(50px, -50px) scale(1.1);
            }
            50% {
                transform: translate(0, 50px) scale(0.9);
            }
            75% {
                transform: translate(-50px, -25px) scale(1.05);
            }
        }

        /* About Header Styling */
        .about-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
            z-index: 1;
        }

        .about-header h1 {
            color: var(--text-light);
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            background: var(--gradient-bg);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: glow 3s infinite alternate;
        }

        @keyframes glow {
            0% {
                text-shadow: 0 0 10px rgba(255, 85, 0, 0.5);
            }
            100% {
                text-shadow: 0 0 20px rgba(58, 12, 163, 0.7);
            }
        }

        .about-header p {
            color: var(--text-light);
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.8;
        }

        /* Team Section Styling */
        .team-section {
            padding: 4rem 2rem;
            position: relative;
            z-index: 1;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--text-light);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: var(--gradient-bg);
            margin: 15px auto;
            border-radius: 2px;
        }

        .section-description {
            text-align: center;
            color: var(--text-light);
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .team-member {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.5s ease, box-shadow 0.5s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .team-member:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 40px rgba(255, 85, 0, 0.2);
        }

        .member-image-wrapper {
            position: relative;
            padding-top: 100%;
            overflow: hidden;
        }

        .member-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .member-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 85, 0, 0.9), rgba(58, 12, 163, 0.9));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .member-image:hover .member-overlay {
            opacity: 1;
        }

        .member-image:hover img {
            transform: scale(1.1);
        }

        .member-bio {
            color: var(--text-light);
            text-align: center;
            padding: 1rem;
            font-style: italic;
            font-size: 1.1rem;
        }

        .member-info {
            padding: 1.5rem;
            text-align: center;
        }

        .member-info h3 {
            color: var(--text-light);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .member-role {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .member-skills {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .member-skills span {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.9rem;
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .member-skills span:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            text-decoration: none;
            transition: transform 0.3s ease, background-color 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-link:hover {
            transform: translateY(-5px) rotate(360deg);
        }

        .social-link.linkedin:hover {
            background: #0077b5;
        }

        .social-link.github:hover {
            background: #333;
        }

        .social-link.twitter:hover {
            background: #1da1f2;
        }

        /* Mission Section Styling */
        .mission-section {
            max-width: 800px;
            margin: 4rem auto;
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .mission-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg at 50% 50%, transparent 0%, rgba(255, 85, 0, 0.1) 25%, transparent 50%);
            animation: rotate 20s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            100% {
                transform: rotate(360deg);
            }
        }

        .mission-section h2 {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            font-size: 2rem;
            position: relative;
            display: inline-block;
        }

        .mission-section h2::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 100%;
            height: 3px;
            background: var(--gradient-bg);
        }

        .mission-section p {
            color: var(--text-light);
            line-height: 1.8;
            font-size: 1.1rem;
        }

        /* Footer Styling */
        footer {
            background: rgba(13, 13, 13, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 4rem 5%;
        }

        .footer-section h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 50px;
            height: 2px;
            background: var(--gradient-bg);
        }

        .footer-section p {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
        }

        .footer-section ul li a {
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--primary-color);
            padding-left: 8px;
        }

        .footer-section .social-links {
            display: flex;
            gap: 1rem;
        }

        .footer-section .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-section .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-5px);
        }

        .footer-bottom {
            text-align: center;
            padding: 1.5rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* World of Ideas Section (as shown in the image) */
        .world-ideas {
            text-align: center;
            margin: 2rem 0 4rem;
            position: relative;
        }

        .world-ideas h2 {
            font-size: 2.5rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 1.5rem;
        }

        .world-ideas::after {
            content: '';
            display: block;
            width: 150px;
            height: 4px;
            background: var(--primary-color);
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }

            .section-title {
                font-size: 2rem;
            }

            .about-header h1 {
                font-size: 2.5rem;
            }

            .mission-section {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
<header class="header">
        <nav class="navbar">
            <div class="logo">
                <h1>INNOCASCADE</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="explore.php">Explore Ideas</a></li>
                
                <li><a href="share_idea.php">Share Idea</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                    <li><a href="register.php" class="btn-signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="about-container">
        <!-- Animated Background -->
        <div class="animated-bg">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
        </div>

        <div class="about-header">
            <h1>About Innocascade</h1>
            <p>We are a team of passionate innovators dedicated to fostering creativity and collaboration in the technology sector. Our platform connects students and professionals to share, explore, and implement innovative ideas in various industries.</p>
        </div>

        <div class="world-ideas">
            <h2>World of Ideas...</h2>
        </div>

        <div class="team-section">
            <h2 class="section-title">Meet Our Team</h2>
            <p class="section-description">The talented individuals behind InnoCascade</p>
            
            <div class="team-grid">
                <div class="team-member" data-aos="fade-up" data-aos-delay="100">
                    <div class="member-image-wrapper">
                        <div class="member-image">
                            <img src="assets/images/Ravi.jpg" alt="Ravi Ranjan">
                            <div class="member-overlay">
                                <div class="member-bio">
                                    "Passionate about creating innovative solutions that make a difference in student's lives."
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Ravi Ranjan</h3>
                        <p class="member-role">Project Lead & Developer</p>
                        <div class="member-skills">
                            <span>PHP</span>
                            <span>MySQL</span>
                            <span>Laravel</span>
                        </div>
                        <div class="social-links">
                            <a href="https://www.linkedin.com/in/ravi-web-developer/" class="social-link linkedin"><i class="fab fa-linkedin"></i></a>
                            <a href="https://github.com/Raviranjan010" class="social-link github"><i class="fab fa-github"></i></a>
                            <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member" data-aos="fade-up" data-aos-delay="200">
                    <div class="member-image-wrapper">
                        <div class="member-image">
                            <img src="assets/images/rohan.jpg" alt="Rohan Yadav">
                            <div class="member-overlay">
                                <div class="member-bio">
                                    "Specializing in building robust and scalable backend systems."
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Rohan Yadav</h3>
                        <p class="member-role">Backend Developer</p>
                        <div class="member-skills">
                            <span>Node.js</span>
                            <span>MongoDB</span>
                            <span>AWS</span>
                        </div>
                        <div class="social-links">
                            <a href="https://www.linkedin.com/in/rohan2004/" class="social-link linkedin"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="social-link github"><i class="fab fa-github"></i></a>
                            <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member" data-aos="fade-up" data-aos-delay="300">
                    <div class="member-image-wrapper">
                        <div class="member-image">
                            <img src="assets/images/ritika.jpg" alt="Ritika">
                            <div class="member-overlay">
                                <div class="member-bio">
                                    "Creating beautiful and intuitive user interfaces with modern web technologies."
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Ritika</h3>
                        <p class="member-role">Frontend Developer</p>
                        <div class="member-skills">
                            <span>React</span>
                            <span>Vue.js</span>
                            <span>CSS3</span>
                        </div>
                        <div class="social-links">
                            <a href="https://www.linkedin.com/in/ritika-10b438297/?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" class="social-link linkedin"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="social-link github"><i class="fab fa-github"></i></a>
                            <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member" data-aos="fade-up" data-aos-delay="400">
                    <div class="member-image-wrapper">
                        <div class="member-image">
                            <img src="assets/images/Sahil.jpg" alt="Sahil Bhardwaj">
                            <div class="member-overlay">
                                <div class="member-bio">
                                    "Focused on creating delightful user experiences through innovative design solutions."
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Sahil Bhardwaj</h3>
                        <p class="member-role">UI/UX Designer</p>
                        <div class="member-skills">
                            <span>Figma</span>
                            <span>Adobe XD</span>
                            <span>Sketch</span>
                        </div>
                        <div class="social-links">
                            <a href="https://www.linkedin.com/in/sahil-bhardwaj-4a1627294/?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" class="social-link linkedin"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="social-link github"><i class="fab fa-github"></i></a>
                            <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mission-section">
            <h2>Our Mission</h2>
            <p>At Innocascade, we believe in the power of collaboration and innovation. Our mission is to create a platform where students and professionals can share their technology ideas, collaborate on projects, and bring their innovations to life. We focus on fostering creativity in tertiary sectors like Hospitality, Financial Services, Entertainment, and Retail, helping to shape the future of these industries through technology.</p>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Innocascade</h3>
                <p>Empowering student innovation in tertiary sectors</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Connect With Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Innocascade. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>