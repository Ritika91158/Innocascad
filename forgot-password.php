<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug output
echo "<!-- Debug: Script is running -->";

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    echo "<!-- Debug: User is logged in, redirecting to index.php -->";
    header("location: index.php");
    exit;
}

// Include database configuration
require_once "config/database.php";

// Initialize variables
$email = "";
$email_err = "";
$success_msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";
    } elseif(!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        $email = trim($_POST["email"]);
        
        // Check if email exists in database
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    // Generate reset token
                    $token = bin2hex(random_bytes(32));
                    $hashed_token = password_hash($token, PASSWORD_DEFAULT);
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Store hashed token in database
                    $update_sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
                    
                    if($update_stmt = mysqli_prepare($conn, $update_sql)){
                        mysqli_stmt_bind_param($update_stmt, "sss", $hashed_token, $expiry, $email);
                        
                        if(mysqli_stmt_execute($update_stmt)){
                            // Send reset email using PHP mailer
                            require 'vendor/autoload.php';
                            
                            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                            
                            try {
                                // Server settings
                                $mail->isSMTP();
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'raviranjankashyap7@gmail.com'; // Your Gmail address
                                $mail->Password = ''; // Your Gmail App Password
                                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = 587;
                                
                                // Recipients
                                $mail->setFrom('raviranjankashyap7@gmail.com', 'InnoCascade');
                                $mail->addAddress($email);
                                
                                // Content
                                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/innocascade2/reset-password.php?token=" . $token . "&email=" . urlencode($email);
                                
                                $mail->isHTML(true);
                                $mail->Subject = 'Password Reset Request - InnoCascade';
                                $mail->Body = '
                                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                                        <h2 style="color: #4f46e5;">Password Reset Request</h2>
                                        <p>Hello,</p>
                                        <p>You have requested to reset your password. Click the button below to reset your password:</p>
                                        <p style="margin: 25px 0;">
                                            <a href="' . $reset_link . '" style="background: #4f46e5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
                                        </p>
                                        <p style="color: #666;">This link will expire in 1 hour.</p>
                                        <p style="color: #666;">If you did not request this, please ignore this email.</p>
                                        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                                        <p style="color: #666; font-size: 12px;">Best regards,<br>InnoCascade Team</p>
                                    </div>
                                ';
                                $mail->AltBody = "Hello,\n\nYou have requested to reset your password. Click the link below to reset your password:\n\n" . $reset_link . "\n\nThis link will expire in 1 hour.\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nInnoCascade Team";
                                
                                $mail->send();
                                $success_msg = "Password reset instructions have been sent to your email.";
                            } catch (Exception $e) {
                                $email_err = "Failed to send reset email. Error: " . $mail->ErrorInfo;
                            }
                        } else {
                            $email_err = "Something went wrong. Please try again later.";
                        }
                        mysqli_stmt_close($update_stmt);
                    }
                } else {
                    $email_err = "No account found with that email address.";
                }
            } else {
                $email_err = "Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - InnoCascade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
            --background-dark: #0a0a0f;
            --text-light: rgba(255, 255, 255, 0.9);
            --text-muted: rgba(255, 255, 255, 0.6);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-dark);
            color: var(--text-light);
            line-height: 1.6;
            min-height: 100vh;
        }

        .forgot-container {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .forgot-form {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .forgot-form h2 {
            text-align: center;
            margin-bottom: 1rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            color: var(--text-light);
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.08);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: rgba(239, 68, 68, 0.1);
        }

        .success-message {
            color: #10b981;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: rgba(16, 185, 129, 0.1);
        }

        .btn-reset {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--secondary-color);
        }

        .description {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="forgot-container">
        <div class="forgot-form">
            <h2>Forgot Password</h2>
            <p class="description">
                Enter your email address and we'll send you instructions to reset your password.
            </p>
            
            <?php 
            if(!empty($success_msg)){
                echo '<div class="success-message">' . $success_msg . '</div>';
            }
            if(!empty($email_err)){
                echo '<div class="error-message">' . $email_err . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-reset">Send Reset Link</button>
                </div>
                <div class="form-footer">
                    <p>Remember your password? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 