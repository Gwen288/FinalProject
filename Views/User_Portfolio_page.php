<?php
require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: login.php');
    exit;
}

// Fetch portfolio
$stmt = $conn->prepare("
    SELECT p.*, c.category_name
    FROM portfolio p
    LEFT JOIN category c ON p.category_id = c.category_id
    WHERE p.user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$portfolio = $stmt->get_result()->fetch_assoc();

if (!$portfolio) {
    header('Location: create_portfolio.php');
    exit;
}

$username = $_SESSION['username'] ?? 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Portfolio</title>
<link rel="stylesheet" href="../css/homepage.css">
<style>
/* ===== Profile Container ===== */
.profile-container {
    max-width: 900px;
    margin: 50px auto;
    background-color: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.profile-header img {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
}

.profile-header .user-info h2 {
    margin: 0;
    font-size: 24px;
    color: #2c3e50;
}

.portfolio-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.portfolio-info div {
    background: #f9f9f9;
    padding: 15px 20px;
    border-radius: 10px;
}

.portfolio-info div p {
    margin: 5px 0;
}

.label {
    font-weight: bold;
    color: #2c3e50;
}

/* ===== Buttons ===== */
.edit-btn, .delete-btn, .add-item-btn {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 20px;
    margin-right: 10px;
    background-color: #22c55e; /* green */
    color: #fff;
    transition: background 0.3s;
}

.edit-btn:hover, .add-item-btn:hover, .delete-btn:hover {
    background-color: #16a34a; /* darker green on hover */
}

@media(max-width: 768px) {
    .portfolio-info { grid-template-columns: 1fr; }
    .profile-header { flex-direction: column; text-align: center; }
    .edit-btn, .delete-btn, .add-item-btn { width: 100%; margin-right: 0; }
}
</style>
</head>
<body>

<!-- Navbar (UNCHANGED) -->
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
        </div>
    </div>

    <div class="portfolio-info">
        <div>
            <p class="label">Title</p>
            <p><?= htmlspecialchars($portfolio['title']) ?></p>
        </div>

        <div>
            <p class="label">Description</p>
            <p><?= nl2br(htmlspecialchars($portfolio['description'] ?: 'N/A')) ?></p>
        </div>

        <div>
            <p class="label">Category</p>
            <p><?= htmlspecialchars($portfolio['category_name'] ?? 'N/A') ?></p>
        </div>

        <div>
            <p class="label">Visibility</p>
            <p><?= htmlspecialchars(ucfirst($portfolio['visibility'])) ?></p>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: right;">
        <button class="edit-btn" onclick="window.location.href='edit_user_portfolio_page.php'">Edit Portfolio</button>
        <button class="add-item-btn" onclick="window.location.href='create_portfolio_item.php'">Add Item</button>
        <button class="delete-btn" id="deletePortfolioBtn">Delete Portfolio</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/portfolio_actions.js"></script>
<script  src="../js/logout.js" defer></script>
</body>
</html>
