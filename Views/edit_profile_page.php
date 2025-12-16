<?php
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
    $stmtUser = $conn->prepare("SELECT email FROM Portfoliohub_Users WHERE user_id = ?");
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $userResult = $stmtUser->get_result()->fetch_assoc();
    $email = $userResult['email'] ?? '';

    $insert = $conn->prepare("
        INSERT INTO Student_Profile (user_id, email, bio, major, year, profile_picture)
        VALUES (?, ?, '', '', '', 'default.png')
    ");
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
<title>Edit Profile</title>
<link rel="stylesheet" href="../css/homepage.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .navbar .nav-links li a.login-btn {
    background: none;
    color: #fff;
}
.navbar .nav-links li a.login-btn:hover {
    background-color: #e67e22;
    color: #fff;
}

/* Profile card */
.profile-card {
    max-width: 600px;
    margin: 50px auto;
    background-color: #fff;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

/* Profile image wrapper with button beside it */
.profile-image-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.profile-image-wrapper img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.change-img-btn {
    background-color: #e67e22;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

.change-img-btn:hover {
    background-color: #cf6e1b;
}

/* Labels and inputs */
.profile-card label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}

.profile-card input,
.profile-card textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.profile-card button {
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 20px;
    font-weight: bold;
}

.edit-btn { background-color: #e67e22; color: #fff; }
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

<!-- Profile Card -->
<div class="profile-card">
    <h2>Edit Profile</h2>

    <!-- Profile Image + Change Button -->
    <div class="profile-image-wrapper">
        <img id="profile-img" src="../uploads/profiles/<?= htmlspecialchars($result['profile_picture'] ?? 'default1.png') ?>" alt="Profile Image">
        <label for="profile_picture" class="change-img-btn">Change Profile</label>
        <input type="file" id="profile_picture" accept="image/*" style="display: none;">
    </div>

    <label>Username</label>
    <input type="text" id="username" value="<?= htmlspecialchars($username) ?>" disabled>

    <label>Email</label>
    <input type="email" id="email" value="<?= htmlspecialchars($result['email']) ?>">

    <label>Bio</label>
    <textarea id="bio"><?= htmlspecialchars($result['bio']) ?></textarea>

    <label>Major</label>
    <input type="text" id="major" value="<?= htmlspecialchars($result['major']) ?>">

    <label>Year</label>
    <input type="text" id="year" value="<?= htmlspecialchars($result['year']) ?>">

    <button class="edit-btn" onclick="editProfile()">Save Changes</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/profile_actions.js"></script>

<script>
// Preview selected image
document.getElementById('profile_picture').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

<script  src="../js/logout.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>
