<?php
// create_portfolio_item.php
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

// Fetch the user's single portfolio
$stmt = $conn->prepare(
    "SELECT portfolio_id FROM Portfolio WHERE user_id = ? LIMIT 1"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$portfolio = $stmt->get_result()->fetch_assoc();

if (!$portfolio) {
    // User must create portfolio first
    header("Location: create_portfolio.php");
    exit;
}

$portfolioId = $portfolio['portfolio_id'];

// Fetch enum values for item_type from database
$enumQuery = $conn->query("SHOW COLUMNS FROM Portfolio_Item LIKE 'item_type'");
$row = $enumQuery->fetch_assoc();
$enumValues = [];
if ($row) {
    preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches);
    if (isset($matches[1])) {
        $enumValues = explode("','", $matches[1]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add to Your Portfolio</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    :root {
      --primary-green: #2f855a;
      --primary-green-dark: #276749;
      --soft-bg: #f0fff4;
      --card-bg: #ffffff;
      --text-dark: #2d3748;
      --text-muted: #718096;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, var(--soft-bg), #edf2f7);
      color: var(--text-dark);
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 50px;
      background: #2c3e50;
    }

    .logo {
      font-size: 24px;
      font-weight: bold;
      color: #fff;
    }

    .navbar ul {
      list-style: none;
      display: flex;
      gap: 20px;
    }

    .navbar a {
      color: #fff;
      text-decoration: none;
      padding: 8px 15px;
      border-radius: 6px;
      transition: 0.3s;
    }

    .navbar a:hover {
      background-color: #e67e22;
    }

    .container {
      max-width: 900px;
      margin: 50px auto;
      background: var(--card-bg);
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      border-top: 6px solid var(--primary-green);
    }

    h1 {
      margin-bottom: 8px;
      color: var(--primary-green);
    }

    .subtitle {
      color: var(--text-muted);
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 14px;
      border-radius: 8px;
      border: 1px solid #cbd5e0;
      font-size: 14px;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    .helper-text {
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .btn {
      background: var(--primary-green);
      color: #fff;
      border: none;
      padding: 14px 32px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .btn:hover {
      background: var(--primary-green-dark);
    }

    a.btn {
    text-decoration: none; /* removes underline */
    }
    
    a.btn:hover {
    text-decoration: none; /* ensures it stays removed on hover */
    }


    .footer {
      text-align: center;
      margin: 40px 0;
      color: var(--text-muted);
    }

    @media (max-width: 768px) {
      .grid {
        grid-template-columns: 1fr;
      }
      .container {
        margin: 20px;
        padding: 30px;
      }
    }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="logo">PortfolioHub</div>
  <ul>
    <li><a href="homepage.php">Home</a></li>
    <li><a href="Portfolio_browsing_page.php">Explore More</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</nav>

<div class="container">
  <h1>Add to Your Portfolio</h1>
  <p class="subtitle">Showcase a project, experience, leadership role, or academic achievement</p>

  <form action="../php/save_portfolio_item.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="portfolio_id" value="<?= $portfolioId ?>">

    <div class="form-group">
      <label>Item Type</label>
      <select name="item_type">
        <option value="">Select Type</option>
        <?php foreach ($enumValues as $value): ?>
            <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($value) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="helper-text">Choose the category that best represents this experience</div>
    </div>

    <div class="form-group">
      <label>Title</label>
      <input type="text" name="title" placeholder="e.g. Machine Learning Research Project">
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea name="description" placeholder="Describe your contribution, skills used, and outcomes"></textarea>
    </div>

    <div class="grid">
      <div class="form-group">
        <label>Location / Institution</label>
        <input type="text" name="location" placeholder="Company, School, Conference">
      </div>

      <div class="form-group">
        <label>Role</label>
        <input type="text" name="role" placeholder="Your role or position">
      </div>
    </div>

    <div class="grid">
      <div class="form-group">
        <label>Start Date</label>
        <input type="date" name="start_date">
      </div>

      <div class="form-group">
        <label>End Date</label>
        <input type="date" name="end_date">
      </div>
    </div>

    <div class="form-group">
      <label>Date Received (Awards / Certifications)</label>
      <input type="date" name="date_received">
    </div>

    <div class="form-group">
      <label>Attachment (PDF, Image, etc.)</label>
      <input type="file" name="attachment">
    </div>

    <input type="hidden" name="status" value="active">

    <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
      <button type="submit" class="btn">Add to Portfolio</button>
      <a href="view_portfolio.php?id=<?= $portfolioId ?>" class="btn">View Portfolio</a>
    </div>
  </form>
</div>

<div class="footer">&copy; <?= date('Y') ?> Student Portfolio System</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/logout.js" defer></script>

</body>
</html>
