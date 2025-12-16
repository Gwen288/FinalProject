<?php
require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: login.php');
    exit;
}

// Fetch portfolio
$stmt = $conn->prepare("SELECT * FROM portfolio WHERE user_id=? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$portfolio = $stmt->get_result()->fetch_assoc();

if (!$portfolio) {
    header('Location: create_portfolio.php');
    exit;
}

// Fetch categories for dropdown
$catQuery = $conn->query("SELECT category_id, category_name FROM category ORDER BY category_name");
$categories = [];
while ($row = $catQuery->fetch_assoc()) {
    $categories[] = $row;
}

$username = $_SESSION['username'] ?? 'Unknown';
$portfolioId = $portfolio['portfolio_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Portfolio</title>
<link rel="stylesheet" href="../css/homepage.css">
<style>
.navbar .nav-links li a.login-btn {
    background: none;
    color: #fff;
}
.navbar .nav-links li a.login-btn:hover {
    background-color: #e67e22;
    color: #fff;
}
.profile-container {
    max-width: 900px; margin: 50px auto; background-color: #fff; border-radius: 12px;
    padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.profile-header {
    display:flex; align-items:center; gap:20px; border-bottom:1px solid #eee;
    padding-bottom:20px; margin-bottom:20px;
}
.profile-header img {
    width:120px; height:120px; border-radius:12px; object-fit:cover;
}
.profile-header .user-info h2 { margin:0; font-size:24px; color:#2c3e50; }
.profile-header .user-info p { margin:3px 0; color:#777; }

.form-group { margin-bottom:20px; }
.form-group label { display:block; font-weight:600; margin-bottom:6px; }
.form-group input, .form-group textarea, .form-group select { width:100%; padding:12px 14px; border-radius:8px; border:1px solid #cbd5e0; font-size:14px; }
.form-group textarea { min-height:120px; resize:vertical; }

/* ===== Buttons ===== */
.btn {
    background-color: #22c55e; /* green */
    color: #fff;
    border: none;
    padding: 14px 32px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
    text-decoration: none;
}
.btn:hover { background-color: #16a34a; }

.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

@media(max-width:768px){
    .profile-header{flex-direction:column;text-align:center;}
    .button-container{flex-direction:column; gap:10px;}
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo">PortfolioHub</div>
    <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="Portfolio_browsing_page.php">Explore</a></li>
        <li><a href="profile.php" class="login-btn">Profile</a></li>
        <li><a href="../php/logout.php" class="login-btn">Logout</a></li>
    </ul>
</nav>

<div class="profile-container">
    <div class="profile-header">
        <img src="../uploads/<?= htmlspecialchars($portfolio['profile_image'] ?? 'default.png') ?>" alt="Portfolio Image">
        <div class="user-info">
            <h2><?= htmlspecialchars($portfolio['title']) ?></h2>
            <p>Visibility: <?= htmlspecialchars($portfolio['visibility']) ?></p>
        </div>
    </div>

    <!-- Edit Portfolio Form -->
    <form id="editPortfolioForm" enctype="multipart/form-data">
        <input type="hidden" name="portfolio_id" value="<?= $portfolio['portfolio_id'] ?>">

        <div class="form-group">
            <label for="title">Portfolio Title</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($portfolio['title']) ?>">
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" ><?= htmlspecialchars($portfolio['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select name="visibility" id="visibility" >
                <option value="private" <?= $portfolio['visibility']=='private'?'selected':'' ?>>Private</option>
                <option value="public" <?= $portfolio['visibility']=='public'?'selected':'' ?>>Public</option>
            </select>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select name="category_id" id="category" >
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= $cat['category_id']==$portfolio['category_id']?'selected':'' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image">
        </div>

        <div class="button-container">
            <button type="submit" class="btn">Update Portfolio</button>
            <a href="User_Portfolio_page.php" class="btn">View Portfolio</a> </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/portfolio_actions.js"></script>
<script  src="../js/logout.js" defer></script>
</body>
</html>