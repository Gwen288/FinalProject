<?php
session_start();

// Destroy session
$_SESSION = [];
session_unset();
session_destroy();

// Return JSON response
echo json_encode(['success' => true]);
