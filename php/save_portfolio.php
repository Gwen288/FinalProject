<?php
// save_portfolio.php
require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: login.php');
    exit;
}

// Check if student already has a portfolio
$checkStmt = $conn->prepare("SELECT portfolio_id FROM Portfolio WHERE user_id = ? LIMIT 1");
$checkStmt->bind_param("i", $userId);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    // Already has a portfolio, redirect to add items
    header("Location: ../Views/create_portfolio_item.php");
    exit;
}

// Handle form submission
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$category_id = $_POST['category_id'] ?? null;
$visibility = $_POST['visibility'] ?? 'public';
$status = $_POST['status'] ?? 'active';

// Validate required fields
if (empty($title) || empty($category_id)) {
    $_SESSION['error'] = "Title and Category are required.";
    header("Location: ../Views/create_portfolio.php");
    exit;
}

// Handle profile image upload
$profile_image = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        $newName = 'profile_' . $userId . '_' . time() . '.' . $ext;
        $uploadDir = '../uploads/portfolios/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadDir.$newName)) {
            $profile_image = $newName;
        }
    }
}

// Insert portfolio into database
$stmt = $conn->prepare("
    INSERT INTO Portfolio (user_id, category_id, profile_image, title, description, visibility, status, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");
$stmt->bind_param(
    "iisssss",
    $userId,
    $category_id,
    $profile_image,
    $title,
    $description,
    $visibility,
    $status
);

if ($stmt->execute()) {
    // Redirect to add portfolio items
    header("Location: ../Views/create_portfolio_item.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}
