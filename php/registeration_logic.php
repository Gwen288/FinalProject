<?php
session_start();


// Set JSON header
header("Content-Type: application/json");

// Get the raw POST data from JS
$data = json_decode(file_get_contents("php://input"), true);

// Check if data exists
if (!$data) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit;
}

// Extract and sanitize values
$firstname = trim($data["f_name"] ?? '');
$lastname  = trim($data["l_name"] ?? '');
$email     = trim($data["email"] ?? '');
$password  = trim($data["password"] ?? '');

// Validate required fields
if (!$firstname || !$lastname || !$email || !$password) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Hash the password
$h_password = password_hash($password, PASSWORD_DEFAULT);

// Connect to the database
require("db_connect.php");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection failed: " . $conn->connect_error]);
    exit;
}

// Prepare and execute insert query
$stmt = $conn->prepare("INSERT INTO PortfolioHub_Users (first_name, last_name,email, `password`) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstname, $lastname, $email, $h_password);

if ($stmt->execute()) {

    $user_id=$conn->insert_id;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $firstname . " " . $lastname;
    $_SESSION['user_type']="student";

    echo json_encode(["status" => "success", "message" => "Your account has been created!"]);


} else {
    echo json_encode(["status" => "error", "message" => "Failed to create account. Email might exist"]);
}

$stmt->close();
$conn->close();
?>



