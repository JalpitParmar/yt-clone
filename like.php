<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to like a video'); window.location='login.php';</script>";
    exit();
}

if ( !$_GET['like_user_id'] || !$_GET['like_c_user_id'] || !$_GET['like_video_id']) {
    die("Invalid video user.");
}

$user_id   = (int)$_GET['like_video_id'];    // the logged-in user
$c_user_id = (int)$_GET['like_c_user_id'];  // uploader
$video_id  = (int)$_GET['like_video_id'];

// Check if already liked
$sql = "SELECT 1 FROM `likes` 
        WHERE `form_user_id` = '$user_id' 
          AND `to_user_id`   = '$c_user_id' 
          AND `video_id`     = '$video_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
   $d="DELETE  FROM `likes` WHERE `form_user_id` = '$user_id' 
          AND `to_user_id`   = '$c_user_id' 
          AND `video_id`     = '$video_id'";
    $d2 = "UPDATE `video` SET `likes` = `likes` - 1 WHERE `video_id` = '$video_id'";
          echo "<script>window.history.back();</script>";
          $conn->query($d);
          $conn->query($d2);
    exit();
}

// Insert like
$q  = "INSERT INTO `likes` (`form_user_id`, `to_user_id`, `video_id`) 
       VALUES ('$user_id','$c_user_id','$video_id')";
$q2 = "UPDATE `video` SET `likes` = `likes` + 1 WHERE `video_id` = '$video_id'";

$conn->query($q);
$conn->query($q2);

header("Location: view.php?id=" . $video_id);


exit();
?>
