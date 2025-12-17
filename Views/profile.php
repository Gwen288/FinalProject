<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: login.php');
    exit;
}

// Fetch profile info
$stmt = $conn->prepare("SELECT * FROM Student_Profile WHERE user_id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// Auto-create profile if it doesn't exist
if (!$result) {
    $stmtUser = $conn->prepare("SELECT email FROM PortfolioHub_Users WHERE user_id = ?");
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $userResult = $stmtUser->get_result()->fetch_assoc();
    $email = $userResult['email'] ?? '';

    $insert = $conn->prepare("INSERT INTO Student_Profile (user_id, email, bio, major, `year`, profile_picture) VALUES (?, ?, '', '', '', 'default.png')");
    $insert->bind_param("is", $userId, $email);
    $insert->execute();

    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
}

$username = $_SESSION['username'] ?? 'Unknown';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>
<link rel="stylesheet" href="../css/homepage.css">
<style>
/* ===== Navbar Fix ===== */
.navbar .nav-links li a.login-btn {
    background: none;
    color: #fff;
}
.navbar .nav-links li a.login-btn:hover {
    background-color: #e67e22;
    color: #fff;
}

/* ===== Profile Card ===== */
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
    border-radius: 50%;
    object-fit: cover;
}

.profile-header .user-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.profile-header .user-info h2 {
    margin: 0;
    font-size: 24px;
    color: #2c3e50;
}

.profile-header .user-info p {
    margin: 0;
    color: #777;
}

/* ===== Personal Info Section ===== */
.personal-info {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
}

.personal-info div {
    background: #f9f9f9;
    padding: 15px 20px;
    border-radius: 10px;
}

.personal-info div p {
    margin: 5px 0;
}

.personal-info .label {
    font-weight: bold;
    color: #2c3e50;
}

.edit-btn {
    background-color: #e67e22;
    color: #fff;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    margin-top: 20px;
    display: block;
    width: fit-content;
    margin-left: auto;
    transition: background 0.3s;
}

.edit-btn:hover {
    background-color: #cf6e1b;
}

@media(max-width: 768px) {
    .personal-info {
        grid-template-columns: 1fr;
    }
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    .edit-btn { width: 100%; float: none; }
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

<!-- Profile Container -->
<div class="profile-container">
    <div class="profile-header">
        <img src="../uploads/profiles/<?= htmlspecialchars($result['profile_picture'] ?? 'default1.png') ?>" alt="Profile Image">
        <div class="user-info">
            <h2><?= htmlspecialchars($username) ?></h2>
            <p><?= htmlspecialchars($result['major'] ?: 'Student') ?></p>
            <p><?= htmlspecialchars($result['year'] ?: '') ?></p>
        </div>
    </div>

    <div class="personal-info">
        <div>
            <p class="label">Email Address</p>
            <p><?= htmlspecialchars($result['email']) ?></p>
        </div>
        <div>
            <p class="label">Bio</p>
            <p><?= nl2br(htmlspecialchars($result['bio'] ?: 'N/A')) ?></p>
        </div>
        <div>
            <p class="label">Major</p>
            <p><?= htmlspecialchars($result['major'] ?: 'N/A') ?></p>
        </div>
        <div>
            <p class="label">Year</p>
            <p><?= htmlspecialchars($result['year'] ?: 'N/A') ?></p>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: right;">
        <button class="edit-btn" onclick="window.location.href='edit_profile_page.php'">Edit Profile</button>
    </div>
</div>

<script  src="../js/logout.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
