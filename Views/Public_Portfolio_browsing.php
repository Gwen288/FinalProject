<?php
session_start();
require_once '../php/db_connect.php';

$userId = $_SESSION['user_id'] ?? null;
$portfolioId = $_GET['id'] ?? null;

if (!$portfolioId) {
    header("Location: Portfolio_browsing_page.php");
    exit;
}

/* Fetch portfolio */
$stmt = $conn->prepare("
    SELECT p.portfolio_id, p.title, p.description, p.profile_image, p.user_id
    FROM Portfolio p
    WHERE p.portfolio_id = ? AND p.visibility = 'public'
    LIMIT 1
");
$stmt->bind_param("i", $portfolioId);
$stmt->execute();
$portfolio = $stmt->get_result()->fetch_assoc();

if (!$portfolio) {
    echo "<h2 style='text-align:center;margin-top:50px;'>Portfolio not found or private.</h2>";
    exit;
}

/* Fetch items */
$itemStmt = $conn->prepare("
    SELECT *
    FROM Portfolio_Item
    WHERE portfolio_id = ?
    ORDER BY item_type, start_date DESC, date_received DESC
");
$itemStmt->bind_param("i", $portfolioId);
$itemStmt->execute();
$items = $itemStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($portfolio['title']) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family:Segoe UI,Tahoma; background:#f7fafc; color:#1a202c; margin:0; padding:0; }

.navbar { display:flex; justify-content:space-between; padding:20px 50px; background:#2c3e50; }
.navbar .logo { color:#fff; font-weight:bold; font-size:22px; }
.navbar ul { display:flex; list-style:none; gap:20px; }
.navbar a { color:#fff; text-decoration:none; padding:8px 14px; border-radius:6px; }
.navbar a:hover { background:#e67e22; }

.cv-container { max-width:900px; margin:40px auto 80px; background:#fff; padding:50px 60px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }

.cv-section { margin-bottom:45px; padding-bottom:25px; border-bottom:2px solid #000; }
.cv-section-title { font-size:16px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:6px; }
.cv-divider { border:none; border-top:2px solid #000; margin-bottom:18px; }

.cv-item-header { display:flex; justify-content:space-between; align-items:flex-start; }
.cv-item-title { font-size:18px; font-weight:600; }
.cv-item-date { font-size:13px; color:#444; white-space:nowrap; }
.cv-subtitle { font-size:14px; font-style:italic; margin-top:2px; color:#333; }
.cv-desc { margin-top:6px; padding-left:18px; }
.cv-desc ul { padding-left:18px; margin:0; }
.cv-desc li { font-size:14px; line-height:1.6; margin-bottom:4px; }

/* Attachment link */
.cv-attachment {
  display: inline-block;
  margin-top:6px;
  font-size:14px;
  color:#1a56db;
  text-decoration:none;
}
.cv-attachment:hover { text-decoration:underline; }
</style>
</head>
<body>

<nav class="navbar">
  <div class="logo">PortfolioHub</div>
  <ul>
    <li><a href="homepage.php">Home</a></li>
    <li><a href="Portfolio_browsing_page.php">Explore</a></li>
    <?php if ($userId) { ?>
        <?php
            $checkPortfolio = $conn->prepare("SELECT portfolio_id FROM Portfolio WHERE user_id=? LIMIT 1");
            $checkPortfolio->bind_param("i", $userId);
            $checkPortfolio->execute();
            $res = $checkPortfolio->get_result();
            $hasPortfolio = $res->num_rows > 0;
            if ($hasPortfolio) { 
                $row = $res->fetch_assoc();
                $userPortfolioId = $row['portfolio_id'];
        ?>
            <li><a href="User_Portfolio_page.php?id=<?= $userPortfolioId ?>">My Portfolio</a></li>
        <?php } else { ?>
            <li><a href="create_portfolio.php">Create Portfolio</a></li>
        <?php } ?>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="../php/logout.php">Logout</a></li>
    <?php } else { ?>
        <li><a href="login.php">Login</a></li>
    <?php } ?>
  </ul>
</nav>

<div class="cv-container">
  <h1><?= htmlspecialchars($portfolio['title']) ?></h1>
  <p><?= htmlspecialchars($portfolio['description']) ?></p>

<?php
$currentType = null;
if ($items && $items->num_rows > 0) {
    $items->data_seek(0);
    while ($item = $items->fetch_assoc()) {
        if ($currentType !== $item['item_type']) {
            if ($currentType !== null) echo "</div>";
            $currentType = $item['item_type'];
?>
<div class="cv-section">
    <div class="cv-section-title"><?= strtoupper(htmlspecialchars($currentType)) ?></div>
    <hr class="cv-divider">
<?php
        } // end if new type
?>
    <div class="cv-item-header">
        <div class="cv-item-title"><?= htmlspecialchars($item['title']) ?></div>
        <div class="cv-item-date">
            <?= ($item['start_date']!='0000-00-00') ? htmlspecialchars($item['start_date']) : '' ?>
            <?= ($item['end_date']!='0000-00-00') ? ' – '.htmlspecialchars($item['end_date']) : '' ?>
        </div>
    </div>

    <?php if ($item['role'] || $item['location']) { ?>
        <div class="cv-subtitle">
            <?= htmlspecialchars($item['role'] ?? '') ?>
            <?= $item['location'] ? ' — '.htmlspecialchars($item['location']) : '' ?>
        </div>
    <?php } ?>

    <div class="cv-desc">
        <ul>
        <?php foreach (preg_split("/\r\n|\n|\r/", $item['description']) as $line) {
            if (trim($line)) {
                echo "<li>" . htmlspecialchars($line) . "</li>";
            }
        } ?>
        </ul>

        <?php if (!empty($item['attachment'])) { ?>
          <a href="#" class="cv-attachment" data-attachment-id="<?= $item['item_id'] ?>">View attachment</a>
        <?php } ?>
    </div>
<?php
    } // end while
    if ($currentType !== null) echo "</div>";
} else {
    echo "<p style='padding:50px;'>No portfolio items found.</p>";
}
?>
</div>

<!-- Lightbox Overlay -->
<div id="lightboxOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:9999;">
  <span id="lightboxClose" style="position:absolute; top:20px; right:20px; font-size:30px; color:#fff; cursor:pointer;">&times;</span>
  <img id="lightboxImage" style="max-width:90%; max-height:90%; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.5);" />
</div>

<script src="../js/view_attachment_modal.js" defer></script>
<script  src="../js/logout.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
