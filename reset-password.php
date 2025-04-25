<?php
session_start();

// Check if user is already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

require_once "config/database.php";

$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
$success_msg = $error_msg = "";

// Verify token and email
if (isset($_GET["token"]) && isset($_GET["email"])) {
    $token = $_GET["token"];
    $email = $_GET["email"];
    
    // Check if token exists and is valid
    $sql = "SELECT id, reset_token, reset_token_expiry FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Check if token has expired
        if (strtotime($user["reset_token_expiry"]) < time()) {
            header("location: forgot-password.php?error=expired");
            exit;
        }
        
        // Verify token
        if (!password_verify($token, $user["reset_token"])) {
            header("location: forgot-password.php?error=invalid");
            exit;
        }
    } else {
        header("location: forgot-password.php?error=invalid");
        exit;
    }
} else {
    header("location: forgot-password.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }
    
    // Check input errors before updating the database
    if (empty($new_password_err) && empty($confirm_password_err)) {
        // Update password
        $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?";
        $stmt = $conn->prepare($sql);
        
        $param_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param("ss", $param_password, $email);
        
        if ($stmt->execute()) {
            $success_msg = "Password has been reset successfully. You can now <a href='login.php'>login</a> with your new password.";
        } else {
            $error_msg = "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - InnoCascade</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        }

        .container {
            max-width: 400px;
            margin: 6rem auto 2rem;
            padding: 0 1rem;
        }

        .reset-password-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            color: var(--text-light);
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.08);
        }

        .submit-btn {
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

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .alert a {
            color: inherit;
            text-decoration: underline;
        }

        .error-text {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .password-requirements {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 0.5rem;
        }

        .password-requirements h3 {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
        }

        .password-requirements ul {
            list-style: none;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .password-requirements li {
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .password-requirements li i {
            font-size: 0.7rem;
        }

        .requirement-met {
            color: #10b981;
        }

        .requirement-not-met {
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="reset-password-container">
            <div class="header">
                <h1>Reset Password</h1>
                <p>Enter your new password below</p>
            </div>

            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?email=" . urlencode($email) . "&token=" . urlencode($token); ?>" method="post">
                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                    <?php if ($new_password_err): ?>
                        <div class="error-text"><?php echo $new_password_err; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    <?php if ($confirm_password_err): ?>
                        <div class="error-text"><?php echo $confirm_password_err; ?></div>
                    <?php endif; ?>
                </div>

                <div class="password-requirements">
                    <h3>Password Requirements:</h3>
                    <ul>
                        <li><i class="fas fa-circle"></i> At least 6 characters long</li>
                        <li><i class="fas fa-circle"></i> Contains at least one number</li>
                        <li><i class="fas fa-circle"></i> Contains at least one special character</li>
                    </ul>
                </div>

                <button type="submit" class="submit-btn">Reset Password</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Password requirements validation
        const newPassword = document.getElementById('new_password');
        const requirements = document.querySelectorAll('.password-requirements li i');

        newPassword.addEventListener('input', function() {
            const value = this.value;
            
            // Length requirement
            if (value.length >= 6) {
                requirements[0].className = 'fas fa-check requirement-met';
            } else {
                requirements[0].className = 'fas fa-circle requirement-not-met';
            }
            
            // Number requirement
            if (/\d/.test(value)) {
                requirements[1].className = 'fas fa-check requirement-met';
            } else {
                requirements[1].className = 'fas fa-circle requirement-not-met';
            }
            
            // Special character requirement
            if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
                requirements[2].className = 'fas fa-check requirement-met';
            } else {
                requirements[2].className = 'fas fa-circle requirement-not-met';
            }
        });
    </script>
</body>
</html> 