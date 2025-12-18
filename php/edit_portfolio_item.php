<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once '../php/db_connect.php';
session_start();

/* ===============================
   AUTH CHECK
================================ */
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

/* ===============================
   UNIFIED INPUT HANDLING
   (JSON + FormData)
================================ */
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents("php://input"), true) ?? [];
} else {
    $input = $_POST;
}


/* ===============================
   INPUT DATA
================================ */
$itemId        = $input['item_id'] ?? null;
$title         = $input['title'] ?? '';
$description   = $input['description'] ?? '';
$location      = $input['location'] ?? '';
$role          = $input['role'] ?? '';
$start_date    = $input['start_date'] ?? null;
$end_date      = $input['end_date'] ?? null;
$date_received = $input['date_received'] ?? null;

if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'Invalid item ID']);
    exit;
}

/* ===============================
   OWNERSHIP CHECK
================================ */
$checkStmt = $conn->prepare("
    SELECT pi.*, p.portfolio_id
    FROM Portfolio_Item pi
    JOIN Portfolio p ON pi.portfolio_id = p.portfolio_id
    WHERE pi.item_id = ? AND p.user_id = ?
");

if (!$checkStmt) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

$checkStmt->bind_param("ii", $itemId, $userId);
$checkStmt->execute();
$item = $checkStmt->get_result()->fetch_assoc();

if (!$item) {
    echo json_encode(['success' => false, 'message' => 'Item not found or access denied']);
    exit;
}

/* ===============================
   FILE UPLOAD (OPTIONAL)
================================ */
$attachment = $item['attachment'];

if (!empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $fileTmp  = $_FILES['attachment']['tmp_name'];
    $fileName = $_FILES['attachment']['name'];
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ['pdf','jpg','jpeg','png','doc','docx'];
    if (!in_array($fileExt, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit;
    }

    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFileName = uniqid() . "_" . basename($fileName);

    if (!move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
        exit;
    }

    $attachment = $newFileName;
}

/* ===============================
   UPDATE DATABASE
================================ */
$updateStmt = $conn->prepare("
    UPDATE Portfolio_Item
    SET title=?, description=?, location=?, role=?,
        start_date=?, end_date=?, date_received=?, attachment=?
    WHERE item_id=?
");

if (!$updateStmt) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

$updateStmt->bind_param(
    "ssssssssi",
    $title,
    $description,
    $location,
    $role,
    $start_date,
    $end_date,
    $date_received,
    $attachment,
    $itemId
);

if ($updateStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Portfolio item updated successfully',
        'portfolio_id' => $item['portfolio_id']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $updateStmt->error
    ]);
}
