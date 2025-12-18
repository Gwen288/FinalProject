<?php
// upload_default.php
$uploadDir = __DIR__ . "/../uploads/portfolios/";

// Make sure the folder exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Path to the file you want to upload from your local machine
$localFile = __DIR__ . "/default.jpg"; // place default.avif in the same folder as this PHP script temporarily

// Destination on the server
$destFile = $uploadDir . "default.jpg";

if (file_exists($localFile)) {
    if (copy($localFile, $destFile)) {
        echo "Default image uploaded successfully!";
    } else {
        echo "Failed to upload default image.";
    }
} else {
    echo "Local file default.jpg not found in the same folder as this script.";
}
?>
