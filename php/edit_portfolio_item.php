<?php
require_once '../php/db_connect.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Fetch input data
$itemId = $_POST['item_id'] ?? null;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$location = $_POST['location'] ?? '';
$role = $_POST['role'] ?? '';
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$date_received = $_POST['date_received'] ?? null;

if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

// Fetch item to ensure it belongs to the user
$stmt = $conn->prepare("
    SELECT pi.* 
    FROM portfolio_item pi
    JOIN portfolio p ON pi.portfolio_id = p.portfolio_id
    WHERE pi.item_id = ? AND p.user_id = ?
");
$stmt->bind_param("ii", $itemId, $userId);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    echo json_encode(['success' => false, 'message' => 'Item not found or access denied']);
    exit;
}

// Handle file upload if a new file is provided
$attachment = $item['attachment']; // default = old attachment
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $fileTmp  = $_FILES['attachment']['tmp_name'];
    $fileName = $_FILES['attachment']['name'];
    $fileExt  = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed = ['pdf','jpg','jpeg','png','doc','docx'];
    if (!in_array(strtolower($fileExt), $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit;
    }

    $newFileName = uniqid().'_'.basename($fileName);
    $uploadDir = "../uploads/";

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
        $attachment = $newFileName; // new file
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        exit;
    }
}

// Update database
$updateStmt = $conn->prepare("
    UPDATE portfolio_item 
    SET title=?, description=?, location=?, role=?, start_date=?, end_date=?, date_received=?, attachment=? 
    WHERE item_id=?
");
$updateStmt->bind_param(
    "ssssssssi",
    $title, $description, $location, $role, $start_date, $end_date, $date_received, $attachment, $itemId
);

if ($updateStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Portfolio item updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => $updateStmt->error]);
}
