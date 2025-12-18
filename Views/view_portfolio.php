<?php
require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: login.php");
    exit;
}

$viewMode = $_GET['view'] ?? 'cards';

/* ======================
   FETCH PORTFOLIO
====================== */
$stmt = $conn->prepare("
    SELECT portfolio_id, title, `description`
    FROM Portfolio
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$portfolio = $stmt->get_result()->fetch_assoc();

if (!$portfolio) {
    header("Location: create_portfolio.php");
    exit;
}

$portfolioId = $portfolio['portfolio_id'];

/* ======================
   FETCH ITEMS
====================== */
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
<title>My Portfolio</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
  --green: #2f855a;
  --dark: #1a202c;
  --muted: #4a5568;
  --bg: #f7fafc;
}

/* RESET */
* { margin:0; padding:0; box-sizing:border-box; font-family:"Segoe UI", Tahoma, sans-serif; }
body { background: var(--bg); color: var(--dark); }

/* NAVBAR */
.navbar { display:flex; justify-content: space-between; padding:20px 50px; background:#2c3e50; }
.navbar .logo { color:#fff; font-weight:bold; font-size:22px; }
.navbar ul { display:flex; list-style:none; gap:20px; }
.navbar a { color:#fff; text-decoration:none; padding:8px 14px; border-radius:6px; }
.navbar a:hover { background-color:#e67e22; }

/* HEADER */
.header { max-width:900px; margin:40px auto 20px; padding:0 20px; }
.header h1 { font-size:28px; color: var(--green); }
.header p { color: var(--muted); margin-top:6px; }

/* BUTTONS */
.actions { max-width:900px; margin:20px auto; padding:0 20px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.btn { background: linear-gradient(135deg,#22c55e,#38bdf8); color:#020617; font-weight:600; text-decoration:none; padding:12px 20px; border-radius:999px; font-size:14px; }
.btn:hover { opacity:.9; }

/* ======================
   CARD VIEW
====================== */
.grid { max-width:1200px; margin:50px auto 100px; padding:0 20px; display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:35px; }

.card { position:relative; background:linear-gradient(180deg,rgba(255,255,255,.12),rgba(255,255,255,.02)); border-radius:22px; padding:18px; box-shadow:0 30px 60px rgba(0,0,0,.25); transition:.45s cubic-bezier(.16,1,.3,1); }

.card:hover { transform:translateY(-12px) scale(1.02); }

.card-media {
    height:170px;
    border-radius:16px;
    overflow:hidden;
    background:linear-gradient(135deg,#38a169,#2f855a);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    font-weight:700;
    color:#fff;
    text-align:center;
    padding:15px;
    margin-bottom:12px; /* space between image and card-body */
}

.card-media img { width:100%; height:100%; object-fit:cover; }

.card-body { padding:0 6px 40px; } /* padding-bottom for actions */

.card-body h3 { font-size:18px; margin-bottom:4px; color:var(--dark); }
.card-meta { font-size:13px; color:var(--muted); margin-bottom:8px; }
.card-desc { font-size:14px; line-height:1.5; }

.badge { position:absolute; top:16px; right:16px; background:rgba(0,0,0,.6); padding:6px 12px; border-radius:999px; font-size:11px; font-weight:600; color:#fff; }

/* Card actions at bottom-right */
.card-actions { position:absolute; bottom:16px; right:16px; display:flex; gap:8px; }
.icon-btn { background: rgba(0,0,0,0.6); color: #fff; border:none; padding:6px 10px; border-radius:8px; cursor:pointer; font-size:14px; transition:0.2s; }
.icon-btn:hover { background: rgba(0,0,0,0.8); }
.icon-btn::after {
    content: attr(title); /* uses the button's title attribute */
    position: absolute;
    bottom: 120%; /* position above the button */
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.8);
    color: #fff;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: 0.2s;
    z-index: 1000;
}

/* Show tooltip on hover */
.icon-btn:hover::after {
    opacity: 1;
}
/* ======================
   CV / RESUME VIEW
====================== */
.cv-container { max-width:900px; margin:40px auto 80px; background:#fff; padding:50px 60px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
.cv-section { margin-bottom:45px; padding-bottom:25px; border-bottom:2px solid #000; }
.cv-section:last-child { border-bottom:none; }
.cv-section-title { font-size:14px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:6px; }
.cv-divider { border:none; border-top:2px solid #000; margin-bottom:18px; }
.cv-item { margin-bottom:20px; }
.cv-item-header { display:flex; justify-content:space-between; align-items:flex-start; }
.cv-item-title { font-size:16px; font-weight:600; }
.cv-item-date { font-size:13px; color:#444; white-space:nowrap; }
.cv-subtitle { font-size:14px; font-style:italic; margin-top:2px; color:#333; }
.cv-desc { margin-top:6px; padding-left:18px; }
.cv-desc ul { padding-left:18px; margin:0; }
.cv-desc li { font-size:14px; line-height:1.6; margin-bottom:4px; }

/* CV attachment link */
.cv-attachment {
  display: inline-block;
  margin-top: 6px;
  font-size: 14px;
  color: #1a56db;
  text-decoration: none;
}

.cv-attachment:hover {
  text-decoration: underline;
}

#lightboxOverlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.85);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
  cursor: pointer;
}

#lightboxOverlay img {
  max-width: 95%;
  max-height: 95%;
  object-fit: contain;
  box-shadow: 0 0 20px rgba(0,0,0,0.5);
}

#lightboxClose {
  position: absolute;
  top: 20px;
  right: 20px;
  font-size: 36px;
  color: #fff;
  cursor: pointer;
  z-index: 10001;
}

</style>
</head>

<body>

<nav class="navbar">
  <div class="logo">PortfolioHub</div>
  <ul>
    <li><a href="homepage.php">Home</a></li>
    <li><a href="Portfolio_browsing_page.php">Explore</a></li>
    <li><a href="../php/logout.php">Logout</a></li>
  </ul>
</nav>

<div class="header">
  <h1><?= htmlspecialchars($portfolio['title']) ?></h1>
  <p><?= htmlspecialchars($portfolio['description']) ?></p>
</div>

<div class="actions">
  <a href="create_portfolio_item.php" class="btn">Add Item</a>
  <div>
    <a href="?view=cards" class="btn">Cards View</a>
    <a href="?view=cv" class="btn">Full CV View</a>
  </div>
</div>

<?php if ($viewMode === 'cards'): ?>
<div class="grid">
<?php while ($item = $items->fetch_assoc()): ?>
  <div class="card" data-id="<?= $item['item_id'] ?>" >
    <span class="badge"><?= htmlspecialchars($item['item_type']) ?></span>

    <!-- Card Media -->
    <div class="card-media">
        <?php
        $imgPath = "../uploads/portfolio_items" . $item['attachment'];
        if (!empty($item['attachment']) && file_exists($imgPath)):
        ?>
            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
        <?php else: ?>
            <?= htmlspecialchars($item['title']) ?>
        <?php endif; ?>
    </div>

    <!-- Card Body -->
    <div class="card-body">
      <h3><?= htmlspecialchars($item['title']) ?></h3>
      <div class="card-meta">
        <?= htmlspecialchars($item['location'] ?? '') ?>
        <?= $item['start_date'] ? ' • '.$item['start_date'] : '' ?>
      </div>
      <div class="card-desc">
        <?= htmlspecialchars(substr($item['description'],0,110)) ?>...
      </div>

      <div class="card-actions">
        <button class="icon-btn edit" onclick="window.location.href='edit_portfolio_item_page.php?id=<?= $item['item_id'] ?>'" title="Edit">✏️</button>
        <button class="icon-btn delete" title="Delete">🗑️</button>
      </div>
    </div>
  </div>
<?php endwhile; ?>
</div>

<?php else: ?>
<div class="cv-container">
<?php
$current = null;
$items->data_seek(0);

while ($item = $items->fetch_assoc()):
  if ($current !== $item['item_type']):
    if ($current !== null) { echo "</div>"; }
    $current = $item['item_type'];
?>
  <div class="cv-section">
    <div class="cv-section-title"><?= strtoupper(htmlspecialchars($current)) ?></div>
    <hr class="cv-divider">
<?php endif; ?>

    <div class="cv-item">
      <div class="cv-item-header">
        <div class="cv-item-title"><?= htmlspecialchars($item['title']) ?></div>
        <div class="cv-item-date">
          <?= htmlspecialchars($item['start_date'] ?? '') ?>
          <?= $item['end_date'] ? ' – '.$item['end_date'] : '' ?>
        </div>
      </div>
      <?php if ($item['role'] || $item['location']): ?>
        <div class="cv-subtitle">
          <?= htmlspecialchars($item['role'] ?? '') ?>
          <?= $item['location'] ? ' — '.$item['location'] : '' ?>
        </div>
      <?php endif; ?>
      <div class="cv-desc">
        <ul>
          <?php foreach (preg_split("/\r\n|\n|\r/", $item['description']) as $line): ?>
            <?php if (trim($line)): ?>
              <li><?= htmlspecialchars($line) ?></li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>

        <?php if (!empty($item['attachment'])): ?>
          <a
          href="#"
          class="cv-attachment"
           data-attachment-id="<?= $item['item_id'] ?>"
            >
             View attachment
            </a>

        <?php endif; ?>
      </div>
    </div>
<?php endwhile; ?>
<?php if ($current !== null) echo "</div>"; ?>
</div>
<?php endif; ?>

<div class="footer">&copy; <?= date('Y') ?> Student Portfolio System</div>

<!-- Lightbox overlay -->
<div id="lightboxOverlay" style="display:none;">
  <span id="lightboxClose">&times;</span>
  <img id="lightboxImage">
</div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/logout.js" defer></script>
<script src="../js/portfolio_items_actions.js"></script>
<script src="../js/view_attachment_modal.js"></script>


</body>
</html>
