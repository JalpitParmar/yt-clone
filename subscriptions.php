<?php
require "db.php";
session_start();

// Check login
if(!isset($_SESSION['user_id'])){
    echo "<script>alert('Please login first'); window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get subscribed channels
$channels_sql = "SELECT s.subscribe_user_id, u.username, u.dp_url
                 FROM subscribe s
                 JOIN user u ON s.subscribe_user_id = u.user_id
                 WHERE s.subscriber_user_id = $user_id";
$channels_result = $conn->query($channels_sql);

// Get videos from all subscribed channels
$videos_sql = "SELECT v.*, u.username, u.dp_url 
               FROM subscribe s 
               JOIN video v ON s.subscribe_user_id = v.user_id
               JOIN user u ON v.user_id = u.user_id
               WHERE s.subscriber_user_id=$user_id
               ORDER BY v.time DESC";
$videos_result = $conn->query($videos_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subscriptions - MyTube</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link rel="icon" type="image/png" href="assets/img/logo.png">
<style>
body { margin:0; font-family: Arial, sans-serif; background:#f9f9f9; }
.top-bar { display:flex; overflow-x:auto; padding:10px; background:#fff; border-bottom:1px solid #ddd; }
.channel { display:flex; flex-direction:column; align-items:center; justify-content:center; margin-right:15px; text-align:center; cursor:pointer; text-decoration:none; color:#333; }
.channel img { width:50px; height:50px; border-radius:50%; margin-bottom:5px; border:2px solid #c40000; }
.channel span { font-size:12px; max-width:60px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.main { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:20px; padding:20px; }
.video-card { background:#fff; border-radius:12px; overflow:hidden; cursor:pointer; transition:0.3s; }
.video-card:hover { box-shadow:0 6px 15px rgba(0,0,0,0.2); }
.video-thumb img { width:100%; height:160px; object-fit:cover; }
.video-info { padding:10px; }
.video-info h4 { font-size:16px; margin-bottom:5px; }
.video-info p { font-size:14px; color:#555; }
@media(max-width:768px){
    .main { grid-template-columns: 1fr; padding:10px; }
}
</style>
</head>
<body>

<?php require "nav.php"; ?>

<!-- Subscribed Channels Horizontal Bar -->
<div class="top-bar">
    <?php if($channels_result->num_rows > 0): ?>
        <?php while($channel = $channels_result->fetch_assoc()): ?>
            <a href="subscriptions.php?channel=<?php echo $channel['subscribe_user_id']; ?>" class="channel">
                <img src="<?php echo $channel['dp_url']; ?>" alt="dp">
                <span><?php echo $channel['username']; ?></span>
            </a>
        <?php endwhile; ?>
    <?php else: ?>
        <span>No subscriptions yet.</span>
    <?php endif; ?>
</div>

<!-- Video Feed -->
<div class="main">
    <?php if($videos_result->num_rows > 0): ?>
        <?php while($video = $videos_result->fetch_assoc()): ?>
            <a href="view.php?id=<?php echo $video['video_id']; ?>" style="text-decoration:none;">
                <div class="video-card">
                    <div class="video-thumb">
                        <img src="<?php echo $video['thumbnail_url']; ?>" alt="thumb">
                    </div>
                    <div class="video-info">
                        <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                        <p><?php echo $video['username']; ?> • <?php echo number_format($video['views']); ?> views</p>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="padding:20px;">No videos found.</p>
    <?php endif; ?>
</div>

</body>
</html>
