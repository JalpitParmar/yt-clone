<?php
session_start();
require "db.php";

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to comment. <a href='login.php'>Login here</a>");
}

$from_user_id = (int)$_SESSION['user_id'];
$from_username = $conn->real_escape_string($_SESSION['username']);

// Validate request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST['message']) || empty($_POST['video_id'])) {
        die("Invalid request.");
    }

    $message = $conn->real_escape_string($_POST['message']);
    $video_id = (int)$_POST['video_id'];

    // Get video uploader (to_user_id)
    $sql = "SELECT user_id FROM video WHERE video_id = $video_id LIMIT 1";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        die("Video not found.");
    }

    $row = $result->fetch_assoc();
    $to_user_id = (int)$row['user_id'];

    // Insert comment (using correct column names)
    $insert = "INSERT INTO comment ( `form_username`, `form_user_id`, `to_use_id`, `video_id`, `massage`) 
               VALUES ('$from_username', $from_user_id, $to_user_id, $video_id, '$message')";

    if ($conn->query($insert)) {
        header("Location: view.php?id=$video_id&msg=Comment+posted+successfully");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    die("Invalid access.");
}
?>
