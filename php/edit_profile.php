<?php
require_once '../php/db_connect.php';
session_start();
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get POST input
$email = trim($_POST['email'] ?? '');
$bio   = trim($_POST['bio'] ?? '');
$major = trim($_POST['major'] ?? '');
$year  = trim($_POST['year'] ?? '');

// Validate email
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Handle profile picture upload
$profilePictureName = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    $fileName = $_FILES['profile_picture']['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Rename file to avoid conflicts
    $profilePictureName = "profile_{$userId}." . $fileExt;
    $uploadDir = "../uploads/profiles/";
    $destPath = $uploadDir . $profilePictureName;

    if (!move_uploaded_file($fileTmpPath, $destPath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload profile image']);
        exit;
    }
}

// Update database
if ($profilePictureName) {
    $stmt = $conn->prepare("UPDATE Student_Profile SET email=?, bio=?, major=?, year=?, profile_picture=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $email, $bio, $major, $year, $profilePictureName, $userId);
} else {
    $stmt = $conn->prepare("UPDATE Student_Profile SET email=?, bio=?, major=?, year=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $email, $bio, $major, $year, $userId);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}
?>
