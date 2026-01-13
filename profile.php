<?php
session_start();
require "db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You are not logged in.'); window.location='login.php';</script>";
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Fetch user data
$sql = "SELECT user_id, username, email, about, dp_url, role, is_premium, subscribers, videos_uploaded, created_at
        FROM user
        WHERE user_id = $user_id";
$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . $conn->error);
}
if ($result->num_rows === 0) {
    die("User not found.");
}
$user = $result->fetch_assoc();

// Fetch user videos
$video_sql = "SELECT video_id, title, thumbnail_url, views, time
              FROM video
              WHERE username = '" . $conn->real_escape_string($user['username']) . "'
              ORDER BY time DESC";
$videos = $conn->query($video_sql);
require'nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($user['username']); ?> - Channel</title>
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    a{
      text-decoration:none;
      color:inherit;
    }
    body {
      background-color: #f9f9f9;
      font-family: Arial, sans-serif;
    }
    .banner {
      width: 100%;
      height: 220px;
      background: url('https://picsum.photos/1200/400') center/cover no-repeat;
      border-radius: 10px;
      margin-bottom: -60px;
    }
    .channel-info {
      display: flex;
      align-items: center;
      padding: 20px;
      background: white;
      border-bottom: 1px solid #ddd;
    }
    .channel-info img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid white;
      margin-right: 20px;
      margin-top: -40px;
      object-fit: cover;
    }
    .channel-info h2 {
      margin: 0;
      font-size: 24px;
    }
    .channel-info p {
      color: gray;
      margin: 2px 0;
    }
    .subscribe-btn {
      margin-left: auto;
      background: Y;
      color: white;
      font-weight: bold;
      border-radius: 20px;
      padding: 8px 20px;
      border: none;
    }
    .nav-tabs {
      border-bottom: 2px solid #ddd;
    }
    .nav-tabs .nav-link {
      color: #555;
      font-weight: 500;
    }
    .nav-tabs .nav-link.active {
      color: red;
      border-bottom: 2px solid red;
    }
    .video-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      padding: 20px;
    }
    .video-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: 0.3s;
    }
    .video-card:hover {
      transform: scale(1.03);
    }
    .video-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    .video-card .content {
      padding: 10px;
    }
    .video-card h5 {
      margin: 0;
      font-size: 16px;
      font-weight: bold;
    }
    .video-card p {
      font-size: 14px;
      color: gray;
      margin: 0;
    }
    /* ✅ Responsive Tweaks */
/* General Styles */
a { text-decoration: none; color: inherit; }
body {
    background-color: #f9f9f9;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

/* Banner */
.banner {
    width: 100%;
    height: 220px;
    background: url('https://picsum.photos/1200/400') center/cover no-repeat;
    border-radius: 10px;
    margin-bottom: -60px;
}

/* Channel Info */
.channel-info {
    display: flex;
    align-items: center;
    padding: 20px;
    background: white;
    border-bottom: 1px solid #ddd;
    flex-wrap: wrap;
}
.channel-info img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    margin-right: 20px;
    margin-top: -40px;
    object-fit: cover;
}
.channel-info h2 {
    margin: 0;
    font-size: 24px;
}
.channel-info p {
    color: gray;
    margin: 2px 0;
}

/* Tabs */
.nav-tabs {
    border-bottom: 2px solid #ddd;
}
.nav-tabs .nav-link {
    color: #555;
    font-weight: 500;
}
.nav-tabs .nav-link.active {
    color: red;
    border-bottom: 2px solid red;
}

/* Video Grid */
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
}
.video-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: 0.3s;
}
.video-card:hover {
    transform: scale(1.03);
}
.video-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}
.video-card .content {
    padding: 10px;
}
.video-card h5 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}
.video-card p {
    font-size: 14px;
    color: gray;
    margin: 0;
}

/* Responsive Tweaks */
@media (max-width: 1024px) {
    .banner { height: 180px; }
    .channel-info img { width: 100px; height: 100px; margin-top: -30px; }
    .channel-info h2 { font-size: 22px; }
    .channel-info p { font-size: 14px; }
    .subscribe-btn { padding: 6px 16px; font-size: 14px; }
    .video-card img { height: 140px; }
}

@media (max-width: 768px) {
    .channel-info { flex-direction: column; align-items: flex-start; gap: 15px; }
    .subscribe-btn { margin-left: 0; align-self: flex-end; }
    .video-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; padding: 15px; }
    .video-card img { height: 120px; }
}

@media (max-width: 480px) {
    .banner { height: 140px; }
    .channel-info img { width: 80px; height: 80px; margin-top: -20px; }
    .channel-info h2 { font-size: 18px; }
    .channel-info p { font-size: 12px; }
    .subscribe-btn { padding: 5px 12px; font-size: 12px; }
    .video-grid { grid-template-columns: 1fr; gap: 10px; padding: 10px; }
    .video-card img { height: 180px; }
    .video-card .content { padding: 8px; }
    .video-card h5 { font-size: 14px; }
    .video-card p { font-size: 12px; }
}

  </style>
</head>
<body>

  <!-- Banner -->
  <div class="banner"></div>

  <!-- Channel Info -->
  <div class="channel-info">
    <?php echo'<img src="'.$user['dp_url'].'" alt="Channel Logo">'?>
    <div>
      <h2><?php echo htmlspecialchars($user['username']); ?></h2>
      <p><?php echo number_format($user['subscribers']); ?> subscribers • <?php echo (int)$user['videos_uploaded']; ?> videos</p>
    </div>
    <button class="subscribe-btn"><a href="edit_profile.php" class="btn btn-warning fw-bold rounded-pill px-4">
      <i class="bi bi-pencil-square"></i> Edit Profile
    </a></button>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs justify-content-center">
    <li class="nav-item">
      <a class="nav-link active" href="#">Home</a>
    </li>
  </ul>

  <!-- Videos Grid -->
  <div class="video-grid">
    <?php if ($videos && $videos->num_rows > 0): ?>
      <?php while ($video = $videos->fetch_assoc()): ?>
        <div class="video-card">
          <a href="view.php?id=<?php echo (int)$video['video_id']; ?>" style="text-decoration:none; color:inherit;">
            <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" alt="video">
            <div class="content">
              <h5><?php echo htmlspecialchars($video['title']); ?></h5>
              <p>
                <?php echo number_format($video['views']); ?> views • 
                <?php 
                  $now = new DateTime();
                  $uploaded = new DateTime($video['time']);
                  $interval = $now->diff($uploaded);
                  if ($interval->days == 0) {
                      echo "Today";
                  } elseif ($interval->days == 1) {
                      echo "1 day ago";
                  } else {
                      echo $interval->days . " days ago";
                  }
                ?>
              </p>
            </div>
          </a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted text-center">No videos uploaded yet.</p>
    <?php endif; ?>
  </div>

</body>
</html>
