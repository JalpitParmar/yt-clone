<?php
require "db.php";
session_start();

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';
$limit = 20; // videos per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
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
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>YouTube Clone - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    /* ---------- Global ---------- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }
    body {
      background: #f9f9f9;
      color: #333;
    }
    a { text-decoration: none; color: inherit; }
    

    /* ---------- Video Grid ---------- */
    .video-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .video-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      cursor: pointer;
      transform: scale(1);
      transition: transform 0.3s, box-shadow 0.3s;
      animation: fadeInUp 0.6s ease forwards;
    }
    .video-card:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    }
    .video-thumb {
      width: 100%;
      height: 160px;
      background: #ddd;
      position: relative;
    }
    .video-thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .video-info {
      padding: 12px;
    }
    .video-info h4 {
      font-size: 16px;
      margin-bottom: 6px;
      font-weight: bold;
      color: #222;
    }
    .video-info p {
      font-size: 14px;
      color: #777;
    }
/* ---------- Category Bar ---------- */
.category-bar {
  display: flex;
  overflow-x: auto;
  white-space: nowrap;
  background: #fff;
  padding: 10px 15px;
  border-bottom: 1px solid #ddd;
  position: sticky;
  top: 60px; /* below navbar */
  z-index: 999;
  scrollbar-width: thin; /* Firefox */
  scrollbar-color: #ccc transparent; /* Firefox */
}

/* Hide scrollbar for Chrome, Safari, Edge */
.category-bar::-webkit-scrollbar {
  height: 6px;
}
.category-bar::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 10px;
}
.category-bar::-webkit-scrollbar-track {
  background: transparent;
}

.category-bar a {
  display: inline-block;
  padding: 6px 16px;
  margin-right: 10px;
  border-radius: 20px;
  background: #f0f0f0;
  color: #333;
  font-size: 14px;
  text-decoration: none;
  transition: all 0.3s;
}

.category-bar a:hover {
  background: #ddd;
  color: #000;
}

.category-bar a.active {
  background: #000;
  color: #fff;
  font-weight: 500;
}

    /* ---------- Animation ---------- */
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
     /* ---------- Responsive ---------- */
    @media (max-width: 768px) {
      .video-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        
      }
      .category-bar {
    padding: 8px 10px;
  }
  .category-bar a {
    font-size: 13px;
    padding: 5px 12px;
  }
      .video-thumb {
        height: 140px;
      }
      .video-info h4 {
        font-size: 14px;
      }
      .video-info p {
        font-size: 12px;
      }
    }

    @media (max-width: 480px) {
      .video-grid {
        grid-template-columns: 1fr;
        gap: 12px;
        padding: 10px;
      }
      .video-thumb {
        height: 180px;
      }
      .video-info h4 {
        font-size: 15px;
      }
      .video-info p {
        font-size: 13px;
      }
      .category-bar a {
    font-size: 12px;
    padding: 4px 10px;
  }
    }
  </style>
</head>
<body>

  <?php require "nav.php"; ?>
  <!-- Category Bar -->
<div class="category-bar">
  <?php
    $categories = ["All", "Music", "Sports", "Gaming", "News", "Fashion", "Education", "Technology", "Comedy", "Cooking", "Fitness", "Vlogs", "Documentary", "Travel","Movie", "Other"];
    $selected = isset($_GET['category']) ? $_GET['category'] : "All";

    foreach($categories as $cat){
        $active = ($selected == $cat) ? "active" : "";
        $link = ($cat == "All") ? "index.php" : "index.php?category=".urlencode($cat);
        echo '<a href="'.$link.'" class="'.$active.'">'.$cat.'</a>';
    }
  ?>
</div>

  <!-- Content -->
  <div class="content" id="content">
    
    <div class="video-grid">
<?php if($result->num_rows > 0): ?>
  <?php while ($video = $result->fetch_assoc()): ?>
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
  <?php endwhile; ?>
<?php else: ?>
  <p>No videos found for "<?php echo htmlspecialchars($search); ?>"</p>
<?php endif; ?>
</div>

  </div>

  <!-- JS -->
  <script>
    const menuBtn = document.getElementById("menu-btn");
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");

    menuBtn.addEventListener("click", () => {
      sidebar.classList.toggle("active");
      content.classList.toggle("active");
    });
  </script>
<script>
let page = 2; // next page
let loading = false;

window.addEventListener('scroll', () => {
    if((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100 && !loading){
        loading = true;
        const urlParams = new URLSearchParams(window.location.search);
        const search = urlParams.get('search') || '';
        const category = urlParams.get('category') || '';

        fetch(`load_videos.php?page=${page}&search=${search}&category=${category}`)
        .then(res => res.text())
        .then(data => {
            if(data.trim() != ''){
                document.querySelector('.video-grid').insertAdjacentHTML('beforeend', data);
                page++;
                loading = false;
            }
        });
    }
});
</script>

</body>
</html>
