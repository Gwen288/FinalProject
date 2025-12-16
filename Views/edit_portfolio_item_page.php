<?php
require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) { 
    header('Location: login.php'); 
    exit; 
}

$itemId = $_GET['id'] ?? null;
if (!$itemId) { 
    echo "Invalid item ID."; 
    exit; 
}

// Fetch portfolio item only if it belongs to the logged-in user
$stmt = $conn->prepare("
    SELECT pi.*, p.portfolio_id
    FROM portfolio_item pi
    JOIN portfolio p ON pi.portfolio_id = p.portfolio_id
    WHERE pi.item_id = ? AND p.user_id = ?
");
$stmt->bind_param("ii", $itemId, $userId);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) { 
    echo "Item not found or access denied."; 
    exit; 
}

// Page title = item type
$pageTitle = !empty($item['item_type']) ? $item['item_type'] : 'Edit Item';
$portfolioId = $item['portfolio_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($pageTitle) ?></title>
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

    * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background: linear-gradient(135deg, var(--soft-bg), #edf2f7); color: var(--text-dark); }

    .navbar { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; background:#2c3e50; }
    .logo { font-size:24px; font-weight:bold; color:#fff; }
    .navbar ul { list-style:none; display:flex; gap:20px; }
    .navbar a { color:#fff; text-decoration:none; padding:8px 15px; border-radius:6px; transition:0.3s; }
    .navbar a:hover { background-color: #e67e22; }

    .container { max-width: 900px; margin: 50px auto; background: var(--card-bg); padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border-top: 6px solid var(--primary-green); }

    h1 { margin-bottom: 8px; color: var(--primary-green); }
    .subtitle { color: var(--text-muted); margin-bottom: 30px; }

    .form-group { margin-bottom: 20px; }
    .form-group label { display:block; margin-bottom:6px; font-weight:600; }
    .form-group input, .form-group textarea { width:100%; padding:12px 14px; border-radius:8px; border:1px solid #cbd5e0; font-size:14px; }
    .form-group textarea { resize: vertical; min-height:120px; }

    .grid { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; }

    .btn { background: var(--primary-green); color:#fff; border:none; padding:14px 32px; border-radius:8px; font-size:16px; cursor:pointer; transition:0.3s; }
    .btn:hover { background: var(--primary-green-dark); }
    a.btn {
    text-decoration: none; /* removes underline */
    }
    a.btn:hover {
    text-decoration: none; /* ensures it stays removed on hover */
    }


    .footer { text-align:center; margin:40px 0; color:var(--text-muted); }

    @media (max-width:768px) {
      .grid { grid-template-columns:1fr; }
      .container { margin:20px; padding:30px; }
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
  <!-- Display item type as the header -->
  <h1><?= htmlspecialchars($item['item_type']) ?></h1>
  <p class="subtitle">Update details of this project, experience, or achievement</p>

  <form id="editForm" action="../php/edit_portfolio_item.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
    <input type="hidden" name="portfolio_id" value="<?= $portfolioId ?>">

    <div class="form-group">
      <label>Title</label>
      <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" >
    </div>

    <div class="form-group">
      <label>Description</label>
      <textarea name="description" ><?= htmlspecialchars($item['description']) ?></textarea>
    </div>

    <div class="grid">
      <div class="form-group">
        <label>Location / Institution</label>
        <input type="text" name="location" value="<?= htmlspecialchars($item['location']) ?>">
      </div>

      <div class="form-group">
        <label>Role</label>
        <input type="text" name="role" value="<?= htmlspecialchars($item['role']) ?>">
      </div>
    </div>

    <div class="grid">
      <div class="form-group">
        <label>Start Date</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($item['start_date']) ?>">
      </div>

      <div class="form-group">
        <label>End Date</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($item['end_date']) ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Date Received (Awards / Certifications)</label>
      <input type="date" name="date_received" value="<?= htmlspecialchars($item['date_received']) ?>">
    </div>

    <div class="form-group">
      <label>Attachment (PDF, Image, etc.)</label>
      <input type="file" name="attachment">
      <?php if (!empty($item['attachment'])): ?>
        <p>Current: <?= htmlspecialchars($item['attachment']) ?></p>
      <?php endif; ?>
    </div>

    <div style="margin-top: 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
      <button type="submit" class="btn">Update Portfolio Item</button>
      <a href="view_portfolio.php?id=<?= $portfolioId ?>" class="btn">View Portfolio</a>
    </div>
  </form>
</div>

<div class="footer">&copy; <?= date('Y') ?> Student Portfolio System</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/logout.js" defer></script>
<script src="../js/portfolio_items_actions.js"></script>

</body>
</html>
