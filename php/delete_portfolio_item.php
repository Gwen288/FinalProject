<?php
require_once 'db_connect.php';
session_start();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$itemId = $input['item_id'] ?? null;

if (!$itemId) {
    echo json_encode(['success' => false, 'message' => 'No item ID provided.']);
    exit;
}

// Optional: verify ownership
$userId = $_SESSION['user_id'] ?? null;
$stmt = $conn->prepare("DELETE pi FROM portfolio_item pi
                        JOIN portfolio p ON pi.portfolio_id = p.portfolio_id
                        WHERE pi.item_id = ? AND p.user_id = ?");
$stmt->bind_param("ii", $itemId, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete item.']);
}
