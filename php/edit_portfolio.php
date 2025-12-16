<?php
session_start();
require_once 'db_connect.php';

$userId = $_SESSION['user_id'] ?? null;
$portfolioId = $_POST['portfolio_id'] ?? null;

if (!$userId || !$portfolioId) {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// Collect fields from form
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$visibility = $_POST['visibility'] ?? 'private';
$categoryId = $_POST['category_id'] ?? null;

// Handle profile image if uploaded
$profileImage = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    $tmpName = $_FILES['profile_image']['tmp_name'];
    $filename = uniqid() . '_' . basename($_FILES['profile_image']['name']);
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($tmpName, $destination)) {
        $profileImage = $filename;
    }
}

// Build update query
$fields = "title=?, description=?, visibility=?, category_id=?";
$params = [$title, $description, $visibility, $categoryId];
$types = "sssi";

if ($profileImage) {
    $fields .= ", profile_image=?";
    $params[] = $profileImage;
    $types .= "s";
}

$params[] = $portfolioId;
$params[] = $userId;
$types .= "ii";

$sql = "UPDATE portfolio SET $fields WHERE portfolio_id=? AND user_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update portfolio.']);
}
