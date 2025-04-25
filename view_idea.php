<?php
session_start();
require_once "config/database.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: explore.php");
    exit();
}

$idea_id = intval($_GET['id']);

// Fetch idea details with user info
$sql = "SELECT i.*, u.username, u.profile_image 
        FROM ideas i 
        LEFT JOIN users u ON i.user_id = u.id 
        WHERE i.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $idea_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0) {
    header("Location: explore.php");
    exit();
}

$idea = mysqli_fetch_assoc($result);

// Fetch comments
$sql = "SELECT c.*, u.username, u.profile_image 
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        WHERE c.idea_id = ? 
        ORDER BY c.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $idea_id);
mysqli_stmt_execute($stmt);
$comments_result = mysqli_stmt_get_result($stmt);

// Handle comment submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $comment = trim($_POST['comment']);
    if(!empty($comment)) {
        $sql = "INSERT INTO comments (idea_id, user_id, comment) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $idea_id, $_SESSION['id'], $comment);
        if(mysqli_stmt_execute($stmt)) {
            header("Location: view_idea.php?id=" . $idea_id . "#comments");
            exit();
        }
    }
}

// Check if user is collaborating
$is_collaborating = false;
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $sql = "SELECT id FROM collaborations WHERE idea_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idea_id, $_SESSION['id']);
    mysqli_stmt_execute($stmt);
    $collab_result = mysqli_stmt_get_result($stmt);
    $is_collaborating = mysqli_num_rows($collab_result) > 0;
}

// Handle collaboration toggle
if(isset($_POST['toggle_collaboration']) && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if($is_collaborating) {
        $sql = "DELETE FROM collaborations WHERE idea_id = ? AND user_id = ?";
    } else {
        $sql = "INSERT INTO collaborations (idea_id, user_id) VALUES (?, ?)";
    }
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idea_id, $_SESSION['id']);
    if(mysqli_stmt_execute($stmt)) {
        header("Location: view_idea.php?id=" . $idea_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($idea['title']); ?> - InnoCascade</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
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

        .idea-container {
            padding: 120px 2rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .idea-content {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            animation: fadeIn 0.8s ease-out;
        }

        .idea-header {
            position: relative;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .idea-image {
            width: 100%;
            height: 400px;
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .idea-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .idea-image:hover img {
            transform: scale(1.05);
        }

        .idea-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--primary-color);
            box-shadow: 0 2px 10px rgba(255, 90, 31, 0.2);
        }

        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-details {
            display: flex;
            flex-direction: column;
        }

        .author-name {
            font-weight: 600;
            color: var(--text-color);
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
        }

        .idea-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .idea-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(to right, #fff, var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .idea-sector {
            display: inline-block;
            padding: 0.5rem 1.2rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: #fff;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(255, 90, 31, 0.2);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .idea-description {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.8;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            padding: 0 2.5rem;
        }

        .idea-actions {
            display: flex;
            gap: 1rem;
            padding: 0 2.5rem 2.5rem;
        }

        .btn-collaborate {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(255, 90, 31, 0.3);
        }

        .btn-collaborate.active {
            background: linear-gradient(to right, #2ecc71, #27ae60);
        }

        .btn-collaborate:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 90, 31, 0.4);
        }

        .btn-collaborate.active:hover {
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.4);
        }

        .comments-section {
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.02);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .comments-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
        }

        .comment-form {
            margin-bottom: 2.5rem;
        }

        .comment-form textarea {
            width: 100%;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            resize: vertical;
            min-height: 120px;
            margin-bottom: 1rem;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(255, 90, 31, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }

        .comment-form button {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: #fff;
            border: none;
            padding: 1rem 2rem;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(255, 90, 31, 0.3);
        }

        .comment-form button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 90, 31, 0.4);
        }

        .comments-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .comment {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--primary-color);
        }

        .comment-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .comment-meta {
            flex: 1;
        }

        .comment-author {
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.2rem;
        }

        .comment-date {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .comment-content {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .idea-container {
                padding: 100px 1rem 2rem;
            }

            .idea-header {
                padding: 1.5rem;
            }

            .idea-image {
                height: 300px;
            }

            .idea-title {
                font-size: 2rem;
            }

            .idea-description {
                padding: 0 1.5rem;
                font-size: 1rem;
            }

            .idea-actions {
                padding: 0 1.5rem 1.5rem;
                flex-direction: column;
            }

            .btn-collaborate {
                width: 100%;
                justify-content: center;
            }

            .comments-section {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="idea-container">
        <div class="idea-content" data-aos="fade-up">
            <div class="idea-header">
                <?php if(!empty($idea['image_path'])): ?>
                    <div class="idea-image">
                        <img src="<?php echo htmlspecialchars($idea['image_path']); ?>" alt="Idea Image">
                    </div>
                <?php endif; ?>

                <div class="idea-meta">
                    <div class="author-info">
                        <div class="author-avatar">
                            <img src="<?php echo !empty($idea['profile_image']) ? htmlspecialchars($idea['profile_image']) : 'assets/images/default-avatar.png'; ?>" alt="Author Avatar">
                        </div>
                        <div class="author-details">
                            <span class="author-name"><?php echo htmlspecialchars($idea['username']); ?></span>
                            <span class="idea-date"><?php echo date('F j, Y', strtotime($idea['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <h1 class="idea-title"><?php echo htmlspecialchars($idea['title']); ?></h1>
                <span class="idea-sector"><?php echo htmlspecialchars($idea['sector']); ?></span>
            </div>

            <div class="idea-description">
                <?php echo nl2br(htmlspecialchars($idea['description'])); ?>
            </div>

            <div class="idea-actions">
                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <form method="POST">
                        <button type="submit" name="toggle_collaboration" class="btn-collaborate <?php echo $is_collaborating ? 'active' : ''; ?>">
                            <i class="fas <?php echo $is_collaborating ? 'fa-handshake' : 'fa-handshake-angle'; ?>"></i>
                            <?php echo $is_collaborating ? 'Stop Collaborating' : 'Collaborate'; ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="comments-section" id="comments">
                <h2 class="comments-header">Comments</h2>

                <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <form class="comment-form" method="POST" data-aos="fade-up">
                        <textarea name="comment" placeholder="Share your thoughts..." required></textarea>
                        <button type="submit">
                            <i class="fas fa-paper-plane"></i> Post Comment
                        </button>
                    </form>
                <?php endif; ?>

                <div class="comments-list">
                    <?php while($comment = mysqli_fetch_assoc($comments_result)): ?>
                        <div class="comment" data-aos="fade-up">
                            <div class="comment-header">
                                <div class="comment-avatar">
                                    <img src="<?php echo !empty($comment['profile_image']) ? htmlspecialchars($comment['profile_image']) : 'assets/images/default-avatar.png'; ?>" alt="Commenter Avatar">
                                </div>
                                <div class="comment-meta">
                                    <div class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></div>
                                    <div class="comment-date"><?php echo date('F j, Y g:i a', strtotime($comment['created_at'])); ?></div>
                                </div>
                            </div>
                            <div class="comment-content">
                                <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true
        });
    </script>
</body>
</html> 