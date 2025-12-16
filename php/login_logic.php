<?php
session_start(); 
header("Content-Type: application/json");

// receive json from login.js
$data = json_decode(file_get_contents("php://input"), true);

// check if data exists
if (!$data) {
    echo json_encode(["success" => false, "message" => "No data received"]);
    exit;
}

$email=trim($data["email"]??'');
$input_password=trim($data["password"]??'');
$remember = $data["remember"] ?? false;


//check if input fills are empty
if (!$email || !$input_password) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

//connecting to the database
require("db_connect.php");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB Connection failed: " . $conn->connect_error]);
    exit;
}

//selecting userid,firstname,lastname and password from database
$stmt = $conn->prepare("SELECT user_id,first_name, last_name, `password`,`role` FROM portfolioHub_users WHERE TRIM(email)=?");
$stmt->bind_param("s", $email);
$stmt->execute();

$stmt->store_result();


//checks if any user row was found
if ($stmt->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "User does not exist. Try again."]);
    exit;
}

$stmt->bind_result($user_id, $first_name, $last_name,$hash,$user_type);
$stmt->fetch();


if (password_verify($input_password, $hash)) {
    // store user id and username session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $first_name . " " . $last_name;
    $_SESSION['usertype']=$user_type;

    $redirect=($user_type==='admin')?"../Views/homepage.php":"../Views/homepage.php";

    if ($remember) {
        // set cookie for 30 days
        setcookie("user_email", $email, time() + (86400 * 30), "/");
    } else {
        // delete cookie if it exists by setting the expiry date one hour back
        setcookie("user_email", "", time() - 3600, "/");
    }

    echo json_encode([
        "success" => true,
        "username" => $_SESSION['username'],
        "user_id" => $_SESSION['user_id'],
        "redirect" => $redirect
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Incorrect password. Try again."]);
}

$stmt->close();
$conn->close();
?>