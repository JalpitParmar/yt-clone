<?php
session_start();
require "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You are not logged in. <a href='login.php'>Login here</a>");
}

$user_id = (int)$_SESSION['user_id'];

// Validate video id
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$video_id = (int)$_GET['id'];

// Fetch video for this user
$sql = "SELECT * FROM video WHERE video_id = $video_id AND user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Video not found or you don’t have permission to delete it.");
}

$row = $result->fetch_assoc();

// Delete thumbnail file if exists
if (!empty($row['thumbnail_url']) && file_exists($row['thumbnail_url'])) {
    unlink($row['thumbnail_url']);
}

// Delete actual video file if exists
if (!empty($row['video_url']) && file_exists($row['video_url'])) {
    unlink($row['video_url']);
}

//delete comment 
$conn->query("DELETE FROM comment WHERE video_id = $video_id");

// ✅ Delete all likes related to this video
$del_likes = "DELETE FROM likes WHERE video_id = $video_id";
$conn->query($del_likes);

// ✅ Delete video record from database
$del_sql = "DELETE FROM video WHERE video_id = $video_id AND user_id = $user_id";
if ($conn->query($del_sql)) {

    // ✅ Decrease uploaded_videos count by 1 in user table
    $update_user = "UPDATE user SET videos_uploaded = videos_uploaded - 1 WHERE user_id = $user_id";
    $conn->query($update_user);

    header("Location: manage_videos.php?msg=Video+deleted+successfully");
    exit();
} else {
    echo "Error deleting video: " . $conn->error;
}
?>
