<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config/database.php";

$title = $description = $sector = "";
$title_err = $description_err = $sector_err = $image_err = "";
$success_msg = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate title
    if(empty(trim($_POST["title"]))){
        $title_err = "Please enter a title for your idea.";
    } else{
        $title = trim($_POST["title"]);
    }
    
    // Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please describe your idea.";
    } else{
        $description = trim($_POST["description"]);
    }
    
    // Validate sector
    if(empty(trim($_POST["sector"]))){
        $sector_err = "Please select a sector.";
    } else{
        $sector = trim($_POST["sector"]);
    }
    
    // Handle image upload
    $target_dir = "uploads/ideas/";
    $image_path = "";
    
    if(isset($_FILES["idea_image"]) && $_FILES["idea_image"]["error"] == 0){
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["idea_image"]["name"];
        $filetype = $_FILES["idea_image"]["type"];
        $filesize = $_FILES["idea_image"]["size"];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)){
            $image_err = "Please select a valid image format (JPG, JPEG, PNG, GIF)";
        }
        
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize){
            $image_err = "Image size is larger than the allowed limit (5MB)";
        }
        
        // Verify MYME type of the file
        if(in_array($filetype, $allowed)){
            // Create directory if it doesn't exist
            if(!file_exists($target_dir)){
                mkdir($target_dir, 0777, true);
            }
            
            // Generate unique filename
            $new_filename = uniqid() . "." . $ext;
            $target_path = $target_dir . $new_filename;
            
            if(move_uploaded_file($_FILES["idea_image"]["tmp_name"], $target_path)){
                $image_path = $target_path;
            } else{
                $image_err = "Error uploading the image.";
            }
        } else{
            $image_err = "Error: There was a problem uploading your file. Please try again."; 
        }
    }
    
    // Check input errors before inserting in database
    if(empty($title_err) && empty($description_err) && empty($sector_err) && empty($image_err)){
        $sql = "INSERT INTO ideas (user_id, title, description, sector, image_path) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "issss", $param_user_id, $param_title, $param_description, $param_sector, $param_image_path);
            
            $param_user_id = $_SESSION["id"];
            $param_title = $title;
            $param_description = $description;
            $param_sector = $sector;
            $param_image_path = $image_path;
            
            if(mysqli_stmt_execute($stmt)){
                $success_msg = "Your idea has been shared successfully!";
                // Clear form data
                $title = $description = $sector = "";
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
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
    <title>Share Your Idea - InnoCascade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
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
        :root {
            --primary-color: #ff5a1f;
            --secondary-color: #8e24aa;
            --accent-color: #ff3d00;
            --text-color: #ffffff;
            --dark-bg: #101010;
            --card-bg: rgba(30, 30, 40, 0.85);
            --gradient-start: rgba(255, 90, 31, 0.8);
            --gradient-end: rgba(142, 36, 170, 0.8);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--dark-bg);
            overflow-x: hidden;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(10, 10, 15, 0.8), rgba(10, 10, 15, 0.9)),
                url('https://images.unsplash.com/photo-1541560052-5e137f5827b5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') center/cover fixed;
        }

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

        .share-container {
            padding: 120px 2rem 4rem;
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            background: linear-gradient(to right, #fff, var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInUp 1s ease;
        }

        .share-form-container {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.8s ease-out;
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(255, 90, 31, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }

        textarea.form-control {
            min-height: 200px;
            resize: vertical;
        }

        .image-upload {
            position: relative;
            padding: 3rem 2rem;
            border: 2px dashed rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.02);
        }

        .image-upload:hover {
            border-color: var(--primary-color);
            background: rgba(255, 90, 31, 0.05);
        }

        .image-upload i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .image-upload p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
        }

        .image-upload small {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
        }

        .image-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .btn-submit {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: #fff;
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1.5rem;
            box-shadow: 0 5px 15px rgba(255, 90, 31, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 90, 31, 0.4);
        }

        .success-message {
            background: rgba(46, 213, 115, 0.1);
            border: 1px solid rgba(46, 213, 115, 0.2);
            color: #2ed573;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .error-message {
            color: #ff4757;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            animation: fadeIn 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .share-container {
                padding: 100px 1rem 2rem;
            }

            .page-title {
                font-size: 2.2rem;
            }

            .share-form-container {
                padding: 1.5rem;
            }

            .form-group label {
                font-size: 1rem;
            }

            .btn-submit {
                padding: 1rem 1.5rem;
                font-size: 1rem;
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

    <div class="share-container">
        <h1 class="page-title">Share Your Innovation</h1>

        <div class="share-form-container">
            <?php if(!empty($success_msg)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" data-aos="fade-up" data-aos-delay="100">
                <div class="form-group">
                    <label for="title">Title of Your Idea</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter a captivating title">
                    <?php if(!empty($title_err)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $title_err; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" placeholder="Describe your innovative idea in detail..."><?php echo htmlspecialchars($description); ?></textarea>
                    <?php if(!empty($description_err)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $description_err; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="sector">Sector</label>
                    <select name="sector" id="sector" class="form-control">
                        <option value="">Select a sector</option>
                        <option value="Hospitality" <?php echo ($sector == "Hospitality") ? "selected" : ""; ?>>Hospitality</option>
                        <option value="Financial Services" <?php echo ($sector == "Financial Services") ? "selected" : ""; ?>>Financial Services</option>
                        <option value="Entertainment" <?php echo ($sector == "Entertainment") ? "selected" : ""; ?>>Entertainment</option>
                        <option value="Retail" <?php echo ($sector == "Retail") ? "selected" : ""; ?>>Retail</option>
                    </select>
                    <?php if(!empty($sector_err)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $sector_err; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Image (Optional)</label>
                    <div class="image-upload">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag and drop an image or click to browse</p>
                        <small>Max file size: 5MB (JPG, JPEG, PNG, GIF)</small>
                        <input type="file" name="idea_image" accept="image/*">
                    </div>
                    <?php if(!empty($image_err)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $image_err; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-lightbulb"></i> Share Your Idea
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true
        });

        // Add animation when form is submitted
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('.share-form-container').style.opacity = '0.7';
        });

        // Preview image before upload
        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.image-upload').style.backgroundImage = `url(${e.target.result})`;
                    document.querySelector('.image-upload').style.backgroundSize = 'cover';
                    document.querySelector('.image-upload').style.backgroundPosition = 'center';
                    document.querySelector('.image-upload i').style.display = 'none';
                    document.querySelector('.image-upload p').style.display = 'none';
                    document.querySelector('.image-upload small').style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html> 