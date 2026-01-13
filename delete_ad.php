<?php
session_start();
require "db.php"; // include your DB connection



// ---- Check if ad_id is provided ----
if (!isset($_GET['ad_id']) || empty($_GET['ad_id'])) {
    die("Invalid request.");
}

$ad_id = (int) $_GET['ad_id'];

// ---- First fetch ad to delete file if needed ----
$result = $conn->query("SELECT `ad_video_url` FROM `ads` WHERE `ads_id` = $ad_id");
if ($result && $result->num_rows > 0) {
    $ad = $result->fetch_assoc();
    
    // Delete video file from folder (optional)
    if (!empty($ad['video_url']) && file_exists($ad['video_url'])) {
        unlink($ad['video_url']);
    }

    // ---- Delete ad record ----
    $delete = $conn->query("DELETE FROM ads WHERE ads_id = $ad_id");

    if ($delete) {
        // header("Location: addad.php");
        echo"ok";
        exit;
    } else {
        echo "Error deleting ad: " . $conn->error;
    }
} else {
    echo "Ad not found.";
}

$conn->close();
?>
