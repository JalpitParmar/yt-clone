<?php
require "db.php";
session_start();

// Get user_id
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to download.");
}
$user_id = $_SESSION['user_id'];

// Fetch premium status
$sql = "SELECT is_premium FROM user WHERE user_id = $user_id LIMIT 1";
$res = $conn->query($sql);
$user = $res->fetch_assoc();
$isPremium = ($user['is_premium'] == 1);

// Validate video URL
if (!isset($_GET['video'])) {
    die("Invalid request.");
}
$video = $_GET['video'];

if (!$isPremium) {
    // Non-premium: enforce 3 per day

    $today = date("Y-m-d");

    if (!isset($_SESSION['download_date']) || $_SESSION['download_date'] != $today) {
        $_SESSION['download_date'] = $today;
        $_SESSION['download_count'] = 0;
    }

    if ($_SESSION['download_count'] >= 3) {
        die("<script>alert('Download limit reached (3 per day). Upgrade to premium!'); window.location.href = 'buy_premium.php'</script>");
    }

    $_SESSION['download_count']++;
}

// Force download
header("Content-Disposition: attachment; filename=" . basename($video));
header("Content-Type: application/octet-stream");
readfile($video);
exit;
