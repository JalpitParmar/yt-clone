<?php
session_start();
require "db.php";

// Check login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You must be logged in to subscribe'); window.location='login.php';</script>";
    exit();
}

// Validate params
if (!isset($_GET['user_id']) || !isset($_GET['c_user_id']) || !isset($_GET['video_id'])) {
    die("Invalid subscription request.");
}
$user_id   = $_SESSION['user_id'];   // person who is subscribing
$c_user_id = $_GET['c_user_id']; // channel owner
$video_id  = $_GET['video_id'];



// Check if already subscribed
$sql = "SELECT * FROM `subscribe` WHERE `subscriber_user_id`='$user_id' AND `subscribe_user_id`='$c_user_id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $q="DELETE FROM `subscribe` WHERE `subscriber_user_id`='$user_id' AND `subscribe_user_id`='$c_user_id'";
    $q2= "UPDATE `user` SET `subscribers`=`subscribers`-1 WHERE `user_id`='$c_user_id'";
    $result = $conn->query($q);
    $conn->query($q2);
    echo "<script>window.history.back();</script>";

    exit();
} else {
    $q = "INSERT INTO `subscribe`(`subscriber_user_id`, `subscribe_user_id`) VALUES ('$user_id','$c_user_id')";
    $q2= "UPDATE `user` SET `subscribers`=`subscribers`+1 WHERE `user_id`='$c_user_id'";
    if ($conn->query($q)) {
        $conn->query($q2);
        echo "<script>window.history.back();</script>";
        exit();
    } else {
        echo "<script>alert('Error subscribing.');window.history.back();</script>";
        exit();
    }
}
?>
