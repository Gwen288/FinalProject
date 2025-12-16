<?php
session_start();
require_once '../php/db_connect.php';

/* ======================
   FETCH CATEGORIES
===================== */
$categories = [];
$catQuery = $conn->query("SELECT category_id, category_name FROM Category ORDER BY category_name");
while ($row = $catQuery->fetch_assoc()) {
    $categories[] = $row;
}

/* ======================
   FILTER INPUTS
===================== */
$search = $_GET['search'] ?? '';
$categoryId = $_GET['category'] ?? '';

/* ======================
   PORTFOLIO QUERY
===================== */
$sql = "SELECT 
          p.portfolio_id,
          p.title,
          p.description,
          p.profile_image,
          p.user_id,
          c.category_name
        FROM Portfolio p
        JOIN Category c 
          ON p.category_id = c.category_id
        WHERE p.visibility = 'public'";

$params = [];
$types  = "";

/* SEARCH */
if (!empty($search)) {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $searchLike = "%$search%";
    $params[] = $searchLike;
    $params[] = $searchLike;
    $types .= "ss";
}

/* CATEGORY FILTER */
if (!empty($categoryId)) {
    $sql .= " AND c.category_id = ?";
    $params[] = $categoryId;
    $types .= "i";
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

/* ======================
   CHECK IF LOGGED-IN USER HAS PORTFOLIO
===================== */
$userId = $_SESSION['user_id'] ?? null;
$hasPortfolio = false;
$portfolioId = null;

if ($userId) {
    $stmtUser = $conn->prepare("SELECT portfolio_id FROM Portfolio WHERE user_id=? LIMIT 1");
    $stmtUser->bind_param("i", $userId);
    $stmtUser->execute();
    $res = $stmtUser->get_result();
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $hasPortfolio = true;
        $portfolioId = $row['portfolio_id'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Explore Student Portfolios</title>

  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; }
    body { background-color:#f5f5f5; color:#333; }

    .navbar { display:flex; justify-content:space-between; align-items:center; padding:20px 50px; background:#2c3e50; }
    .logo { font-size:24px; font-weight:bold; color:#fff; }
    .navbar ul { list-style:none; display:flex; gap:20px; }
    .navbar a { color:#fff; text-decoration:none; padding:8px 15px; border-radius:5px; }
    .navbar a:hover { background:#e67e22; }

    .page-header { padding:60px 50px 20px; }
    .page-header h1 { font-size:36px; color:#2c3e50; margin-bottom:10px; }
    .page-header p { color:#666; }

    .controls { display:flex; gap:20px; padding:20px 50px 40px; flex-wrap:wrap; }
    .controls input, .controls select {
      padding:12px 15px;
      border-radius:8px;
      border:1px solid #ccc;
      min-width:220px;
    }

    .portfolio-grid {
      display:grid;
      grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
      gap:30px;
      padding:0 50px 80px;
    }

    .portfolio-card {
      background:#fff;
      border-radius:15px;
      overflow:hidden;
      box-shadow:0 10px 20px rgba(0,0,0,0.1);
      transition:0.3s;
      cursor:pointer;
      display:flex;
      flex-direction:column;
    }

    .portfolio-card:hover {
      transform:translateY(-8px);
      box-shadow:0 20px 30px rgba(0,0,0,0.15);
    }

    .portfolio-image {
      height:180px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:20px;
      font-weight:bold;
      text-align:center;
      padding:15px;
      background:linear-gradient(135deg,#3498db,#2ecc71);
      color:#fff;
    }

    .portfolio-image img {
      width:100%;
      height:100%;
      object-fit:cover;
      border-radius:8px;
    }

    .portfolio-content { padding:20px; }
    .portfolio-content h3 { color:#2c3e50; margin-bottom:6px; }
    .portfolio-content p { font-size:14px; color:#666; margin-bottom:10px; }

    .tag {
      display:inline-block;
      background:#e67e22;
      color:#fff;
      font-size:12px;
      padding:5px 10px;
      border-radius:20px;
    }

    .footer { background:#2c3e50; color:#fff; padding:40px; text-align:center; }
  </style>
</head>

<body>
<nav class="navbar">
    <div class="logo">PortfolioHub</div>
    <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="Portfolio_browsing_page.php">Explore</a></li>

        <?php if ($userId): ?>
            <?php if ($hasPortfolio): ?>
                <li><a href="User_Portfolio_page.php?id=<?= $portfolioId ?>">My Portfolio</a></li>
            <?php else: ?>
                <li><a href="create_portfolio.php">Create Portfolio</a></li>
            <?php endif; ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="../php/logout.php" >Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" >Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="page-header">
  <h1>Explore Student Portfolios</h1>
  <p>Browse academic and personal achievements by students</p>
</section>

<form method="GET" class="controls">
  <input type="text" name="search" placeholder="Search by keyword"
         value="<?= htmlspecialchars($search) ?>" />
  <select name="category">
    <option value="">All Categories</option>
    <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['category_id'] ?>"
        <?= ($categoryId == $cat['category_id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['category_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <button style="padding:12px 20px;background:#3498db;color:#fff;border:none;border-radius:8px;">
    Filter
  </button>
</form>

<section class="portfolio-grid">
<?php if ($result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="portfolio-card"
         onclick="location.href='Public_Portfolio_browsing.php?id=<?= $row['portfolio_id'] ?>'">

    <div class="portfolio-image">
      <?php
        $imagePath = "../uploads/profiles/" . $row['profile_image'];

     if (!empty($row['profile_image']) && file_exists($imagePath)):
      ?>
        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($row['title']) ?>">
      <?php else: ?>
        <img src="../uploads/profiles/default1.png" alt="Default profile image">
      <?php endif; ?>
    </div>


      <div class="portfolio-content">
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars(substr($row['description'], 0, 80)) ?>...</p>
        <span class="tag"><?= htmlspecialchars($row['category_name']) ?></span>
      </div>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="padding:50px;">No portfolios found.</p>
<?php endif; ?>
</section>

<footer class="footer">
  <p>&copy; <?= date('Y') ?> Student Portfolio System</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script  src="../js/logout.js" defer></script>

</body>
</html>
