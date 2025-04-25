<?php
session_start();
require_once "config/database.php";
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$name = $email = $subject = $message = "";
$name_err = $email_err = $subject_err = $message_err = "";
$success_msg = $error_msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter your name.";
    } else{
        $name = trim($_POST["name"]);
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Validate subject
    if(empty(trim($_POST["subject"]))){
        $subject_err = "Please enter a subject.";
    } else{
        $subject = trim($_POST["subject"]);
    }
    
    // Validate message
    if(empty(trim($_POST["message"]))){
        $message_err = "Please enter your message.";
    } else{
        $message = trim($_POST["message"]);
    }
    
    // Check input errors before sending email
    if(empty($name_err) && empty($email_err) && empty($subject_err) && empty($message_err)){
        try {
            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'raviranjankashyap7@gmail.com';
            $mail->Password = 'lupz rwrq enfz imvq';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            
            // Additional settings for security and reliability
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Set UTF-8 encoding
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // Recipients
            $mail->setFrom('raviranjankashyap7@gmail.com', 'InnoCascade Website');
            $mail->addAddress('raviranjankashyap7@gmail.com', 'Ravi Ranjan');
            $mail->addReplyTo($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "New Contact Form Message: " . $subject;
            
            // Create HTML message body with a professional template
            $messageBody = "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #ff6b6b; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background: #f9f9f9; }
                    .footer { text-align: center; padding: 20px; font-size: 0.8em; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>New Contact Form Message</h2>
                    </div>
                    <div class='content'>
                        <p><strong>From:</strong> " . htmlspecialchars($name) . "</p>
                        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                        <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                        <div style='margin-top: 20px;'>
                            <strong>Message:</strong><br>
                            " . nl2br(htmlspecialchars($message)) . "
                        </div>
                    </div>
                    <div class='footer'>
                        <p>This message was sent from the InnoCascade contact form.</p>
                    </div>
                </div>
            </body>
            </html>";
            
            $mail->Body = $messageBody;
            $mail->AltBody = strip_tags($message);

            // Send the email
            if($mail->send()) {
                // Store in database
                $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
                
                if($stmt = mysqli_prepare($conn, $sql)){
                    mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_email, $param_subject, $param_message);
                    
                    $param_name = $name;
                    $param_email = $email;
                    $param_subject = $subject;
                    $param_message = $message;
                    
                    if(mysqli_stmt_execute($stmt)){
                        $success_msg = "Thank you! Your message has been sent successfully. We'll get back to you soon.";
                        // Clear form data
                        $name = $email = $subject = $message = "";
                    } else{
                        $error_msg = "Database Error: Message sent but could not be stored.";
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        } catch (Exception $e) {
            $error_msg = "Message could not be sent. Please try again later or contact us directly at raviranjankashyap7@gmail.com";
        }
    }
    
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - InnoCascade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-color: #ff6b6b;
            --secondary-color: #9c27b0;
            --dark-color: #121212;
            --light-color: #ffffff;
            --gradient-primary: linear-gradient(90deg, #ff6b6b, #9c27b0);
            --gradient-dark: linear-gradient(135deg, rgba(18, 18, 18, 0.95), rgba(32, 32, 32, 0.95));
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--dark-color);
            color: var(--light-color);
        }

        /* Header Styles to match the image */
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

        /* Contact Section Styles */
        .contact-container {
            padding: 120px 2rem 4rem;
            background-image: url('/api/placeholder/1200/800');
            background-size: cover;
            background-position: center;
            background-blend-mode: overlay;
            background-color: rgba(18, 18, 18, 0.9);
            min-height: 100vh;
            position: relative;
        }

        .contact-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-dark);
            z-index: -1;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .contact-header p {
            color: #ccc;
            max-width: 600px;
            margin: 0 auto;
        }

        .contact-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .contact-info {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.5s ease;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-info:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .contact-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.5s ease 0.2s;
            animation-fill-mode: both;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-form:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .contact-info h2,
        .contact-form h2 {
            color: var(--light-color);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .contact-info h2::after,
        .contact-form h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 3px;
        }

        .contact-info p {
            color: #ccc;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .contact-methods {
            list-style: none;
            margin-top: 2rem;
            padding: 0;
        }

        .contact-method {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .contact-method:hover {
            transform: translateX(5px);
        }

        .contact-method i {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            color: var(--light-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3);
        }

        .contact-method div {
            flex: 1;
        }

        .contact-method h3 {
            color: var(--light-color);
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .contact-method p {
            color: #ccc;
            margin: 0;
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light-color);
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .social-link:hover {
            background: var(--gradient-primary);
            color: var(--light-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        .success-message {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid rgba(16, 185, 129, 0.3);
            animation: fadeIn 0.5s ease;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid rgba(239, 68, 68, 0.3);
            animation: fadeIn 0.5s ease;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--light-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--light-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: rgba(255, 107, 107, 0.5);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: var(--light-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(255, 107, 107, 0.3);
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 107, 107, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .error {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block;
        }

        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.2), rgba(156, 39, 176, 0.2));
            z-index: -1;
            animation: float 8s ease-in-out infinite;
        }

        .decoration-circle-1 {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .decoration-circle-2 {
            width: 200px;
            height: 200px;
            bottom: 10%;
            right: 5%;
            animation-delay: 2s;
        }

        .decoration-circle-3 {
            width: 150px;
            height: 150px;
            top: 30%;
            right: 15%;
            animation-delay: 4s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
            100% {
                transform: translateY(0) rotate(0deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
            }
            
            .contact-container {
                padding: 100px 1rem 2rem;
            }
            
            .contact-header h1 {
                font-size: 2rem;
            }
            
            .decoration-circle {
                opacity: 0.3;
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
    
    <div class="contact-container">
        <div class="decoration-circle decoration-circle-1"></div>
        <div class="decoration-circle decoration-circle-2"></div>
        <div class="decoration-circle decoration-circle-3"></div>
        
        <div class="contact-header">
            <h1>Get In Touch With Us</h1>
            <p>Have questions or ideas? We're here to help. Reach out to our team and we'll respond as soon as possible.</p>
        </div>
        
        <div class="contact-content">
            <div class="contact-info">
                <h2>Contact Information</h2>
                <p>We're excited to hear from you! Choose your preferred method of communication and let's start a conversation.</p>
                
                <ul class="contact-methods">
                    <li class="contact-method">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>support@innocascade.com</p>
                        </div>
                    </li>
                    <li class="contact-method">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Phone</h3>
                            <p>+1 (555) 123-4567</p>
                        </div>
                    </li>
                    <li class="contact-method">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Address</h3>
                            <p>123 Innovation Street<br>Tech City, TC 12345</p>
                        </div>
                    </li>
                </ul>
                
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Send us a Message</h2>
                
                <?php if (!empty($success_msg)): ?>
                    <div class="success-message"><?php echo $success_msg; ?></div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div class="error-message"><?php echo $error_msg; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" class="form-control" 
                               value="<?php echo htmlspecialchars($name); ?>" placeholder="Your name" required>
                        <?php if (!empty($name_err)): ?>
                            <span class="error"><?php echo $name_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($email); ?>" placeholder="Your email address" required>
                        <?php if (!empty($email_err)): ?>
                            <span class="error"><?php echo $email_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" 
                               value="<?php echo htmlspecialchars($subject); ?>" placeholder="Message subject" required>
                        <?php if (!empty($subject_err)): ?>
                            <span class="error"><?php echo $subject_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="5" 
                                  placeholder="Type your message here..." required><?php echo htmlspecialchars($message); ?></textarea>
                        <?php if (!empty($message_err)): ?>
                            <span class="error"><?php echo $message_err; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>