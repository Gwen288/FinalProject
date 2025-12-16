<?php
require_once '../php/db_connect.php';
session_start();

$itemId = $_GET['id'] ?? null;

if (!$itemId || !is_numeric($itemId)) {
    die("Invalid request.");
}

$stmt = $conn->prepare("
    SELECT attachment
    FROM Portfolio_Item
    WHERE item_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $itemId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result || empty($result['attachment'])) {
    die("Attachment not found.");
}

$filePath = "../uploads/" . $result['attachment'];

if (!file_exists($filePath)) {
    die("File does not exist.");
}

/* Detect file type */
$mime = mime_content_type($filePath);
header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"" . basename($filePath) . "\"");
header("Content-Length: " . filesize($filePath));

readfile($filePath);
exit;
