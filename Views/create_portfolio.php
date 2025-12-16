<?php
// create_portfolio.php
require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: login.php');
    exit;
}

// Prevent duplicate portfolios
$checkStmt = $conn->prepare("SELECT portfolio_id FROM portfolio WHERE user_id = ? LIMIT 1");
$checkStmt->bind_param("i", $userId);
$checkStmt->execute();
$existing = $checkStmt->get_result()->fetch_assoc();
if ($existing) {
    header("Location: create_portfolio_item.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Your Portfolio</title> 
<style>
:root { --primary-green: #2f855a; --primary-green-dark: #276749; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; }
body { background: linear-gradient(135deg, #f0fff4, #edf2f7); color:#333; }

/* NAVBAR */
.navbar { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; background-color:#2c3e50; }
.logo { font-size:24px; font-weight:bold; color:#fff; }
.navbar ul { list-style:none; display:flex; gap:20px; }
.navbar a { color:#fff; text-decoration:none; padding:8px 15px; border-radius:5px; }
.navbar a:hover { background:#e67e22; }

/* CONTAINER */
.container { position:relative; border-radius:16px; background:#fff; padding:40px; box-shadow:0 12px 30px rgba(0,0,0,0.1); overflow:hidden; }
.container::before { content:""; position:absolute; top:0; left:0; right:0; height:6px; background:linear-gradient(135deg, #3498db, #2ecc71); border-top-left-radius:16px; border-top-right-radius:16px; }

.container h1 { margin-bottom:10px; }
.subtitle { color:#666; margin-bottom:30px; }

/* FORM */
.form-group { margin-bottom:22px; }
.form-group label { display:block; margin-bottom:6px; font-weight:600; }
.form-group input, .form-group textarea, .form-group select { width:100%; padding:12px 14px; border-radius:8px; border:1px solid #ccc; }
textarea { resize:vertical; min-height:120px; }

/* BUTTON */
.btn { background:var(--primary-green); color:#fff; border:none; padding:14px 30px; border-radius:8px; font-size:16px; cursor:pointer; transition:0.3s; }
.btn:hover { background:var(--primary-green-dark); }

/* FOOTER */
.footer { text-align:center; margin-top:40px; color:#777; }
</style>
</head>
<body>

<!-- NAV -->
<nav class="navbar">
  <div class="logo">PortfolioHub</div>
  <ul>
    <li><a href="homepage.php">Home</a></li>
    <li><a href="Portfolio_browsing_page.php">Explore More</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</nav>

<!-- MAIN -->
<div class="container">
  <h1>Create Your Portfolio</h1>
  <p class="subtitle">This will be your academic profile showcasing achievements and experiences.</p>

  <form action="../php/save_portfolio.php" method="POST" enctype="multipart/form-data">

    <div class="form-group">
      <label>Portfolio Title</label>
      <input type="text" name="title" required placeholder="e.g. Wendy Dwumfuor – Academic Portfolio">
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea name="description" placeholder="Brief overview of your academic journey and interests"></textarea>
    </div>

    <div class="form-group">
      <label>Category</label>
      <select name="category_id" required>
        <option value="">Select Category</option>
        <option value="1">General</option>
        <option value="1">Business & Finance</option>
        <option value="2">Design & Arts</option>
        <option value="3">Engineering</option>
        <option value="4">Research & Academic</option>
        <option value="4">Technology</option>
      </select>
    </div>

    <div class="form-group">
      <label>Profile Image (optional)</label>
      <input type="file" name="profile_image">
    </div>

    <div class="form-group">
      <label>Portfolio Visibility</label>
      <select name="visibility">
        <option value="public">Public</option>
        <option value="private">Private</option>
      </select>
    </div>

    <button class="btn">Create Portfolio</button>
  </form>
</div>

<div class="footer">&copy; <?= date('Y') ?> Student Portfolio System</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script  src="../js/logout.js" defer></script>
</body>
</html>
