<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to view watch history'); window.location='login.php';</script>";
    exit();
}

$history = $_SESSION['watch_history'] ?? [];

$videos = [];
if (!empty($history)) {
    $ids = implode(",", array_map('intval', $history)); // safe integer list
    $sql = "SELECT video_id, title, thumbnail_url FROM video WHERE video_id IN ($ids) ORDER BY FIELD(video_id, $ids)";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $videos[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Watch History - YT Clone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    body { background: #fff; font-family: Arial, sans-serif; }
    .container { margin-top: 40px; max-width: 900px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h2 { color: #c40000; font-weight: bold; }
    .history-item {
      display: flex; align-items: center;
      background: #f8f8f8; border-radius: 10px;
      padding: 12px; margin-bottom: 15px;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .history-item:hover { transform: scale(1.02); box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
    .thumbnail { flex: 0 0 160px; }
    .thumbnail img { width: 100%; border-radius: 8px; }
    .details { flex: 1; margin-left: 15px; }
    .details h5 { margin: 0; font-weight: bold; color: #333; }
    .clear-btn {
      display: block; margin: 20px auto 0;
      background: #c40000; color: #fff; border: none;
      padding: 10px 20px; border-radius: 6px;
      transition: 0.3s;
    }
    .clear-btn:hover { background: #a50000; }
    /* Responsive Adjustments */
@media (max-width: 768px) {
  .history-item {
    flex-direction: column;
    align-items: flex-start;
    padding: 10px;
  }
  .thumbnail {
    width: 100%;
    margin-bottom: 10px;
  }
  .details {
    margin-left: 0;
  }
  .details h5 {
    font-size: 16px;
  }
  .clear-btn {
    width: 90%;
    font-size: 15px;
    padding: 10px;
  }
}

@media (max-width: 480px) {
  .header h2 {
    font-size: 22px;
  }
  .header p {
    font-size: 14px;
  }
  .details h5 {
    font-size: 14px;
  }
  .clear-btn {
    font-size: 14px;
    padding: 8px;
  }

}
a.btn-back {
  position: fixed;
  top: 15px;        /* distance from top */
  left: 15px;       /* distance from left */
  background-color: #6c757d; /* Bootstrap secondary color */
  color: #fff;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  text-decoration: none;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  transition: background-color 0.2s ease-in-out, transform 0.1s ease-in-out;
  z-index: 9999; /* keep it above everything */
}

a.btn-back:hover {
  background-color: #5a6268;
  transform: scale(1.05);
}

a.btn-back:active {
  transform: scale(0.95);
}

  </style>
</head>
<body>
  
<a href="index.php" class="btn-back">
  ← Back
</a>

  <div class="container">
    <div class="header">
      <h2>Watch History</h2>
      <p class="text-muted">Here are the videos you’ve watched recently</p>
    </div>

    <?php if (!empty($videos)): ?>
      <?php foreach ($videos as $video): ?>
        <a href="view.php?id=<?php echo $video['video_id']; ?>" style="text-decoration:none; color:inherit;">
          <div class="history-item">
            <div class="thumbnail">
              <img src="<?php echo $video['thumbnail_url']; ?>" alt="Video Thumbnail">
            </div>
            <div class="details">
              <h5><?php echo htmlspecialchars($video['title']); ?></h5>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted text-center">No watch history yet.</p>
    <?php endif; ?>

    <!-- Clear History Button -->
    <form method="post">
      <button class="clear-btn" name="clear_history">Clear All History</button>
    </form>

    <?php
      if (isset($_POST['clear_history'])) {
          unset($_SESSION['watch_history']);
          echo "<script>window.location='watch_history.php';</script>";
      }
    ?>
  </div>
</body>
</html>
