<?php
require "db.php";
session_start();

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

if($search != '' && $category != '' && $category != 'All'){
    $q = "SELECT * FROM video WHERE (title LIKE '%$search%' OR username LIKE '%$search%') AND category='$category' ORDER BY time DESC LIMIT $limit OFFSET $offset";
} elseif($search != ''){
    $q = "SELECT * FROM video WHERE title LIKE '%$search%' OR username LIKE '%$search%' ORDER BY time DESC LIMIT $limit OFFSET $offset";
} elseif($category != '' && $category != 'All'){
    $q = "SELECT * FROM video WHERE category='$category' ORDER BY time DESC LIMIT $limit OFFSET $offset";
} else {
    $q = "SELECT * FROM video ORDER BY RAND() LIMIT $limit OFFSET $offset";
}

$result = $conn->query($q);

if($result->num_rows > 0){
    while($video = $result->fetch_assoc()){
        ?>
        <a href="view.php?id=<?php echo $video['video_id']; ?>">
          <div class="video-card">
            <div class="video-thumb">
              <img src="<?php echo $video['thumbnail_url']; ?>" alt="thumb">
            </div>
            <div class="video-info">
              <h4><?php echo htmlspecialchars($video['title']); ?></h4>
              <p>
                <?php echo $video['username'];?> • 
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
          </div>
        </a>
        <?php
    }
} else {
    echo ''; // no more videos
}
?>
