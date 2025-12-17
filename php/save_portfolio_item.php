<?php
// save_portfolio_item.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: ../Views/login.php');
    exit;
}

// Get POST data
$portfolio_id = $_POST['portfolio_id'] ?? null;
$item_type = $_POST['item_type'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$location = $_POST['location'] ?? '';
$role = $_POST['role'] ?? '';
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$date_received = $_POST['date_received'] ?? null;

$attachment = null;

// Handle file upload
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/portfolio_items/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $filename = time() . '_' . basename($_FILES['attachment']['name']);
    $targetFile = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
        $attachment = $filename;
    }
}

// Insert portfolio item into database
$stmt = $conn->prepare("INSERT INTO Portfolio_Item 
    (portfolio_id, item_type, title, `description`, `location`, `role`, `start_date`, end_date, date_received, attachment)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "isssssssss",
    $portfolio_id, $item_type, $title, $description, $location, $role, $start_date, $end_date, $date_received, $attachment
);

if ($stmt->execute()) {
    header("Location: ../Views/create_portfolio_item.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}
?>
