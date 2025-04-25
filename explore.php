<?php
session_start();
require_once "config/database.php";

// Initialize filter variables
$sector_filter = isset($_GET['sector']) ? $_GET['sector'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Prepare the base SQL query
$sql = "SELECT i.*, u.username, COUNT(DISTINCT c.id) as comment_count, COUNT(DISTINCT col.id) as collab_count 
        FROM ideas i 
        LEFT JOIN users u ON i.user_id = u.id 
        LEFT JOIN comments c ON i.id = c.idea_id 
        LEFT JOIN collaborations col ON i.id = col.idea_id";

// Add filters
$where_conditions = array();
if(!empty($sector_filter)) {
    $where_conditions[] = "i.sector = '" . mysqli_real_escape_string($conn, $sector_filter) . "'";
}
if(!empty($search)) {
    $where_conditions[] = "(i.title LIKE '%" . mysqli_real_escape_string($conn, $search) . "%' OR i.description LIKE '%" . mysqli_real_escape_string($conn, $search) . "%')";
}

if(!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " GROUP BY i.id";

// Add sorting
switch($sort) {
    case 'most_commented':
        $sql .= " ORDER BY comment_count DESC";
        break;
    case 'most_collaborated':
        $sql .= " ORDER BY collab_count DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY i.created_at ASC";
        break;
    default: // newest
        $sql .= " ORDER BY i.created_at DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Ideas - InnovateHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(10px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 5%;
    max-width: 1400px;
    margin: 0 auto;
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
/* Keep the existing header styles as they are */

.explore-container {
    padding: 120px 2rem 4rem;
    background: linear-gradient(135deg, #000000, #1a1a2e);
    min-height: 100vh;
    color: #fff;
}

.explore-content {
    max-width: 1200px;
    margin: 0 auto;
}

.explore-header {
    background: rgba(10, 10, 20, 0.8);
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(255, 90, 31, 0.2);
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 90, 31, 0.3);
    backdrop-filter: blur(5px);
}

.explore-header h2 {
    color: #fff;
    margin-bottom: 1.5rem;
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    background: linear-gradient(to right, #ff5a1f, #8e24aa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 10px rgba(255, 90, 31, 0.5);
}

.filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    margin-bottom: 0.5rem;
    color: #ff5a1f;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.filter-group select,
.filter-group input {
    padding: 0.8rem;
    border: 1px solid #3a3a5a;
    border-radius: 8px;
    font-size: 1rem;
    background: rgba(20, 20, 40, 0.8);
    color: #fff;
    transition: all 0.3s ease;
}

.filter-group select:focus,
.filter-group input:focus {
    border-color: #ff5a1f;
    outline: none;
    box-shadow: 0 0 10px rgba(255, 90, 31, 0.3);
}

.ideas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.idea-card {
    background: rgba(10, 10, 20, 0.8);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(60, 60, 90, 0.3);
}

.idea-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 90, 31, 0.3);
}

.idea-image {
    width: 100%;
    height: 200px;
    background: #0a0a14;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}

.idea-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(0deg, rgba(10, 10, 20, 1) 0%, rgba(10, 10, 20, 0) 50%);
    pointer-events: none;
}

.idea-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.idea-content {
    padding: 1.5rem;
}

.idea-sector {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    background: linear-gradient(135deg, #ff5a1f, #8e24aa);
    color: #fff;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    box-shadow: 0 3px 10px rgba(255, 90, 31, 0.3);
}

.idea-title {
    color: #fff;
    margin-bottom: 1rem;
    font-size: 1.3rem;
    font-weight: 600;
}

.idea-description {
    color: #c0c0d0;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.5;
}

.idea-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid rgba(60, 60, 90, 0.3);
    color: #a0a0b0;
    font-size: 0.9rem;
}

.idea-stats {
    display: flex;
    gap: 1rem;
}

.idea-stat {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.idea-stat i {
    color: #ff5a1f;
}

.btn-view {
    display: inline-block;
    padding: 0.5rem 1.2rem;
    background: linear-gradient(135deg, #ff5a1f, #8e24aa);
    color: #fff;
    text-decoration: none;
    border-radius: 25px;
    transition: all 0.3s ease;
    font-weight: 500;
    letter-spacing: 0.5px;
    border: none;
    box-shadow: 0 3px 10px rgba(255, 90, 31, 0.3);
}

.btn-view:hover {
    background: linear-gradient(135deg, #ff5a1f, #8e24aa);
    box-shadow: 0 5px 15px rgba(255, 90, 31, 0.5);
    transform: translateY(-2px);
}

.no-ideas {
    text-align: center;
    padding: 3rem;
    background: rgba(10, 10, 20, 0.8);
    border-radius: 15px;
    color: #a0a0b0;
    border: 1px solid rgba(60, 60, 90, 0.3);
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);
}

.no-ideas i {
    font-size: 4rem;
    color: #ff5a1f;
    margin-bottom: 1.5rem;
    opacity: 0.8;
}

.no-ideas h3 {
    color: #fff;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

@media (max-width: 768px) {
    .filters {
        grid-template-columns: 1fr;
    }

    .ideas-grid {
        grid-template-columns: 1fr;
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

    <main class="explore-container">
        <div class="explore-content">
            <div class="explore-header">
                <h2>Explore Innovative Ideas</h2>
                <form action="" method="GET" class="filters">
                    <div class="filter-group">
                        <label>Search</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search ideas...">
                    </div>
                    <div class="filter-group">
                        <label>Sector</label>
                        <select name="sector">
                            <option value="">All Sectors</option>
                            <option value="Hospitality" <?php echo ($sector_filter == "Hospitality") ? "selected" : ""; ?>>Hospitality</option>
                            <option value="Financial Services" <?php echo ($sector_filter == "Financial Services") ? "selected" : ""; ?>>Financial Services</option>
                            <option value="Entertainment" <?php echo ($sector_filter == "Entertainment") ? "selected" : ""; ?>>Entertainment</option>
                            <option value="Retail" <?php echo ($sector_filter == "Retail") ? "selected" : ""; ?>>Retail</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Sort By</label>
                        <select name="sort">
                            <option value="newest" <?php echo ($sort == "newest") ? "selected" : ""; ?>>Newest First</option>
                            <option value="oldest" <?php echo ($sort == "oldest") ? "selected" : ""; ?>>Oldest First</option>
                            <option value="most_commented" <?php echo ($sort == "most_commented") ? "selected" : ""; ?>>Most Commented</option>
                            <option value="most_collaborated" <?php echo ($sort == "most_collaborated") ? "selected" : ""; ?>>Most Collaborated</option>
                        </select>
                    </div>
                    <div class="filter-group" style="justify-content: flex-end;">
                        <button type="submit" class="btn-view" style="margin-top: auto;">Apply Filters</button>
                    </div>
                </form>
            </div>

            <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="ideas-grid">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <div class="idea-card">
                            <div class="idea-image">
                                <?php if(!empty($row['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                <?php else: ?>
                                    <i class="fas fa-lightbulb" style="font-size: 3rem; color: #3498db;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="idea-content">
                                <span class="idea-sector"><?php echo htmlspecialchars($row['sector']); ?></span>
                                <h3 class="idea-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="idea-description"><?php echo htmlspecialchars($row['description']); ?></p>
                                <div class="idea-meta">
                                    <div class="idea-stats">
                                        <span class="idea-stat">
                                            <i class="fas fa-comment"></i>
                                            <?php echo $row['comment_count']; ?>
                                        </span>
                                        <span class="idea-stat">
                                            <i class="fas fa-users"></i>
                                            <?php echo $row['collab_count']; ?>
                                        </span>
                                    </div>
                                    <a href="view_idea.php?id=<?php echo $row['id']; ?>" class="btn-view">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-ideas">
                    <i class="fas fa-search" style="font-size: 3rem; color: #3498db; margin-bottom: 1rem;"></i>
                    <h3>No ideas found</h3>
                    <p>Try adjusting your filters or search criteria</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Add smooth scrolling to filter application
        document.querySelector('form.filters').addEventListener('submit', function(e) {
            e.preventDefault();
            this.submit();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html> 