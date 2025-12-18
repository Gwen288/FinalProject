<?php
session_start();
require_once '../php/db_connect.php';

$userId = $_SESSION['user_id'] ?? null;
$hasPortfolio = false;
$portfolioId = null;

if ($userId) {
    $stmt = $conn->prepare("SELECT portfolio_id FROM Portfolio WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result) {
        $hasPortfolio = true;
        $portfolioId = $result['portfolio_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portfolio System</title>
    <link rel="stylesheet" href="../css/homepage.css">
</head>
<body>

<!-- ====================== NAVBAR ====================== -->
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
            <li><a href="../php/logout.php" class="login-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- ====================== HERO SECTION ====================== -->
<section class="hero">
    <div class="hero-content">
        <h1>Showcase Your Skills & Creativity</h1>
        <p>A simple and elegant platform for Ashesi students to build and share professional portfolios.</p>

        <div class="hero-buttons">
            <a href="Portfolio_browsing_page.php" class="btn explore">Explore Portfolios</a>

            <?php if ($userId): ?>
                <?php if ($hasPortfolio): ?>
                    <a href="User_Portfolio_page.php?id=<?= $portfolioId ?>" class="btn create">My Portfolio</a>
                <?php else: ?>
                    <a href="create_portfolio.php" class="btn create">Create Your Portfolio</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="register.php" class="btn create">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ====================== FEATURES ====================== -->
<section class="features">
    <div class="feature-card">
        <h3>Create</h3>
        <p>Create your own digital portfolio and own your space.</p>
    </div>

    <div class="feature-card">
        <h3>Manage</h3>
        <p>Manage your profile, update achievements & skills.</p>
    </div>

    <div class="feature-card">
        <h3>Explore</h3>
        <p>Browse portfolios from students within the Ashesi community.</p>
    </div>
</section>

<!-- ====================== FOOTER ====================== -->
<footer class="footer">
    <div>
        <h4>Contact Us</h4>
        <p>Email: info@portfoliohub.com</p>
        <p>Phone: +233 24 000 0000</p>
    </div>

    <div>
        <h4>Quick Links</h4>
        <a href="Portfolio_browsing_page.php">Explore Portfolios</a>
        <?php if ($userId && $hasPortfolio): ?>
            <a href="view_portfolio.php?id=<?= $portfolioId ?>">My Portfolio</a>
        <?php else: ?>
            <a href="create_portfolio.php">Create Portfolio</a>
        <?php endif; ?>
        <a href="login.php">Login</a>
    </div>

    <div>
        <p>© <?= date('Y') ?> PortfolioHub — Student Portfolio System. All rights reserved.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/logout.js" defer></script>

</body>
</html>
