<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
$portfolioId = $_POST['portfolio_id'] ?? null;

if (!$userId || !$portfolioId) {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// Delete portfolio items first
$stmt = $conn->prepare("
    DELETE pi FROM portfolio_item pi
    INNER JOIN portfolio p ON pi.portfolio_id = p.portfolio_id
    WHERE pi.portfolio_id=? AND p.user_id=?
");
$stmt->bind_param("ii", $portfolioId, $userId);
$stmt->execute();

// Delete portfolio
$stmt = $conn->prepare("DELETE FROM portfolio WHERE portfolio_id=? AND user_id=?");
$stmt->bind_param("ii", $portfolioId, $userId);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete portfolio']);
}
