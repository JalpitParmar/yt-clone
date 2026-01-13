<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to like a video'); window.location='login.php';</script>";
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid video ID.");
}


$video_id = (int)$_GET['id'];

// Fetch video data
$sql = "SELECT video_id, user_id, username, title, description, category, thumbnail_url, video_url, views, likes, time 
        FROM video 
        WHERE video_id = $video_id";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Video not found.");
}

$video = $result->fetch_assoc();

// Fetch channel data
$sql2 = "SELECT user_id, username, email, password, about, dp_url, role, is_premium, subscribers, videos_uploaded, created_at 
         FROM user WHERE user_id=" . $video['user_id'];
$result2 = $conn->query($sql2);
$user = $result2->fetch_assoc();

// Fetch comments
$sql3 = "SELECT * FROM comment WHERE video_id=" . $video_id;
$result3 = $conn->query($sql3);
$comment_count = $result3->num_rows;

// Increase view count
$conn->query("UPDATE video SET views = views + 1 WHERE video_id = $video_id");

// Watch history
if (!isset($_SESSION['watch_history'])) $_SESSION['watch_history'] = [];
if (($key = array_search($video['video_id'], $_SESSION['watch_history'])) !== false) {
    unset($_SESSION['watch_history'][$key]);
}
array_unshift($_SESSION['watch_history'], $video['video_id']);
$_SESSION['watch_history'] = array_slice($_SESSION['watch_history'], 0, 20);

// Watched count
if (!isset($_SESSION['watched_count'])) $_SESSION['watched_count'] = 0;
$_SESSION['watched_count']++;

// Determine ad
$showAd = false; 
$ad = null;
// Make sure the user is logged in first
if(isset($_SESSION['is_premium'])) {
    $isPremium = $_SESSION['is_premium'];
} else {
    $isPremium = 0; // default value if not set
}

if (!$isPremium) {  // only non-premium see ads
    if ($_SESSION['watched_count'] % 5 === 0) {
        $resAd = $conn->query("SELECT * FROM ads ORDER BY RAND() LIMIT 1");
        if ($resAd && $resAd->num_rows > 0) {
            $ad = $resAd->fetch_assoc();
            $showAd = true;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <title>Watch - YT Clone</title>
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito:wght@600;700&display=swap" rel="stylesheet">

    <style>
    :root{
      --bg:#f7f8fc;
      --card:#ffffff;
      --muted:#6c757d;
      --ring:#e7e9f3;
      --brand:#ff0033;             /* Subscribe color */
      --brand-2:#0d6efd;           /* Accent */
      --radius:18px;
      --shadow:0 10px 30px rgba(15,23,42,.08);
      --shadow-soft:0 6px 18px rgba(15,23,42,.06);
    }
    *{box-sizing:border-box}
    html,body{background:var(--bg); color:#1f2937; font-family:Inter,system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";}
    a { text-decoration: none; color: inherit; }
    /* Top Nav */
    .app-nav{
      position: sticky; top:0; z-index:1000;
      backdrop-filter: saturate(1.2) blur(8px);
      background:rgba(255,255,255,.75);
      border-bottom:1px solid var(--ring);
    }
    .search-wrap{
      background:var(--card);
      border:1px solid var(--ring);
      border-radius:999px;
      box-shadow:var(--shadow-soft);
      transition:box-shadow .2s ease, transform .2s ease;
    }
    .search-wrap:focus-within{ box-shadow:0 12px 30px rgba(13,110,253,.12); transform:translateY(-1px); }
    .search-wrap input{ border:none; outline:none; background:transparent; }
    .icon-btn{
      display:inline-flex; align-items:center; justify-content:center;
      width:42px;height:42px;border-radius:999px;border:1px solid var(--ring);
      background:var(--card); box-shadow:var(--shadow-soft);
      transition:transform .2s ease, box-shadow .2s ease;
    }
    .icon-btn:hover{ transform:translateY(-1px); box-shadow:var(--shadow); }

    /* Layout */
    .video-shell{
      background:var(--card); border:1px solid var(--ring);
      border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);
    }
    .video-player{
      width:100%; aspect-ratio:16/9; border:0;
      display:block;
    }
    .video-title{ font-size:1.35rem; font-weight:700; letter-spacing:.2px; }
    .pill{
      display:inline-flex; align-items:center; gap:.45rem;
      padding:.5rem .9rem; border-radius:999px; border:1px solid var(--ring);
      background:var(--card); box-shadow:var(--shadow-soft);
      transition:transform .15s ease, box-shadow .15s ease;
      user-select:none; cursor:pointer;
    }
    .pill:hover{ transform:translateY(-1px); box-shadow:var(--shadow); }
    .actions .pill i{ font-size:1rem; }
    


    /* Channel Card */
    .channel-card{
      display:flex; gap:14px; align-items:center; padding:14px;
      background:var(--card); border:1px solid var(--ring); border-radius:calc(var(--radius) - 6px);
      box-shadow:var(--shadow-soft);
    }
    .avatar{ width:52px; height:52px; border-radius:999px; object-fit:cover; border:2px solid #fff; box-shadow:var(--shadow-soft); }
    .subscribe-btn{
      background:var(--brand); color:#fff; border:none; padding:.6rem 1.2rem; border-radius:999px;
      font-weight:700; letter-spacing:.2px; box-shadow:0 10px 24px rgba(255,0,51,.18);
      transition:transform .15s ease, box-shadow .15s ease, filter .2s ease;
    }
    .subscribe-btn:hover{ transform:translateY(-1px); box-shadow:0 14px 30px rgba(255,0,51,.22); filter:saturate(1.1); }
    .subscribed-btn {
  background: #e5e5e5;  /* light gray */
  color: #111;          /* dark text */
  border: none;
  padding: .6rem 1.2rem;
  border-radius: 999px;
  font-weight: 700;
  letter-spacing: .2px;
  box-shadow: 0 10px 24px rgba(0, 0, 0, .1);
  transition: transform .15s ease, box-shadow .15s ease, filter .2s ease;
}

.subscribed-btn:hover {
  background: #d6d6d6;  /* slightly darker gray */
  transform: translateY(-1px);
  box-shadow: 0 14px 30px rgba(0, 0, 0, .15);
  filter: saturate(1.05);
}


    /* Description */
    .desc-card{
      background:var(--card); border:1px solid var(--ring); border-radius:calc(var(--radius) - 6px);
      box-shadow:var(--shadow-soft); padding:16px;
    }
    .desc-gradient{
      position:relative; max-height:120px; overflow:hidden;
    }
    .desc-gradient::after{
      content:""; position:absolute; inset:auto 0 0 0; height:48px;
      background:linear-gradient(180deg, rgba(255,255,255,0), #fff);
    }
    .show-more{ cursor:pointer; color:var(--brand-2); font-weight:600; }

    /* Tags */
    .tag{
      border:1px solid var(--ring); background:#fbfcff; color:#334155;
      padding:.35rem .7rem; border-radius:999px; font-size:.85rem; box-shadow:var(--shadow-soft);
    }

    /* Comments */
    .comment-input{
      background:#fbfbfe; border:1px solid var(--ring); border-radius:12px; padding:10px 12px;
    }
    .comment-card{
      background:var(--card); border:1px solid var(--ring); border-radius:12px; padding:14px;
      box-shadow:var(--shadow-soft); transition:transform .15s ease, box-shadow .15s ease;
    }
    .comment-card:hover{ transform:translateY(-2px); box-shadow:var(--shadow); }

    /* Sidebar */
    .upnext-card{
      background:var(--card); border:1px solid var(--ring); border-radius:14px; padding:10px;
      box-shadow:var(--shadow-soft);
    }
    .up-item{
      display:flex; gap:12px; padding:10px; border-radius:12px; transition:transform .15s ease, box-shadow .15s ease, background .2s ease;
      cursor:pointer;
    }
    .up-item:hover{ background:#f2f6ff; transform:translateX(4px); box-shadow:var(--shadow-soft); }
    .thumb{ width:168px; height:94px; border-radius:10px; object-fit:cover; }
    .thumb-wrap{ position:relative; }
    .time-badge{
      position:absolute; right:8px; bottom:8px; font-size:.75rem; padding:.2rem .45rem;
      background:rgba(0,0,0,.75); color:#fff; border-radius:6px;
    }

    /* Micro animation */
    .float-in{ animation:floatIn .6s ease-out both; }
    @keyframes floatIn{
      from{ opacity:0; transform:translateY(10px) scale(.98); }
      to{ opacity:1; transform:translateY(0) scale(1); }
    }
    .sidebar {
  position: fixed;
  top: 0;
  left: -260px;
  width: 260px;
  height: 100vh;
  background: var(--card);
  border-right: 1px solid var(--ring);
  box-shadow: var(--shadow);
  z-index: 1050;
  overflow-y: auto;
  transition: left 0.3s ease;
}
.sidebar ul li {
  padding: 10px 0;
  cursor: pointer;
  border-bottom: 1px dashed var(--ring);
}
.sidebar ul li:hover {
  color: var(--brand-2);
}

.sidebar.show {
  left: 0;
}

.overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.35);
  z-index: 1049;
  display: none;
}
.overlay.show {
  display: block;
}
/* 🔹 Main & Ad Video same responsive style */

#adVideo {
  width: 100%;
  max-width: 720px;   /* keeps it from being too big on desktop */
  aspect-ratio: 16/9; /* keeps proper shape */
  border-radius: 10px;
  display: block;
  margin: auto;

}
#skipAdBtn {
  position: absolute;
  visibility: hidden;
  top: 10px;
  right: 10px;
  padding: 6px 12px;
  font-size: 14px;
  background: #ff0000;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.ad-container {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.85);
  z-index: 9999;
}

.back-nav{
  width: 100%;
  height: 40px;
}a.btn-back {
  position: fixed;
  top: 10px;        /* distance from top */
  left: 12px;       /* distance from left */
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

    /* Utility */
    .muted{ color:var(--muted); }
    .divider{ border-top:1px dashed var(--ring); }
  </style>

</head>
<body>
  <div class="back-nav">
    <a href="index.php" class="btn-back">
      ← Back
    </a>
  </div>

<div id="overlay" class="overlay"></div>
<main class="container-fluid px-3 px-md-4 py-3 py-md-4">
    <div class="row g-4">
      <!-- Left: Video + Meta -->
      <div class="col-lg-8">
        <section class="video-shell float-in" data-aos="zoom-in">
          <video id="mainVideo" class="video-player" controls>
            <source src="<?php echo htmlspecialchars($video['video_url']); ?>" type="video/mp4">
          </video>
        </section>

        <!-- Title + Actions -->
        <section class="mt-3" data-aos="fade-up" data-aos-delay="50">
          <h1 class="video-title mb-2"><?php echo htmlspecialchars($video['title']); ?></h1>
          <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3">
            <span class="muted">
              <?php echo htmlspecialchars($video['views']); ?> views •  
              <?php 
                  $now = new DateTime();
                  $uploaded = new DateTime($video['time']);
                  $interval = $now->diff($uploaded);
                  echo ($interval->days == 0) ? "Today" : (($interval->days == 1) ? "1 day ago" : $interval->days . " days ago");
              ?> • 
            </span>
            <div class="ms-auto d-flex flex-wrap gap-2 actions">
              <a href="like.php?like_user_id=<?php echo $_SESSION['user_id'];?>&like_c_user_id=<?php echo $video['user_id'];?>&like_video_id=<?php echo $video['video_id'];?>">
              <?php
                  $s="SELECT * FROM likes WHERE form_user_id=".$_SESSION['user_id']." AND to_user_id=".$video['user_id']." AND video_id=".$video['video_id'];
                  $s1 = $conn->query($s);
                  echo ($s1 && $s1->num_rows>0) ? 
                    '<div class="pill"><i class="bi bi-hand-thumbs-up"></i><span>'.$video['likes'].'</span></div>' :
                    '<div class="pill"><i class="bi bi-hand-thumbs-up"></i><span>'.$video['likes'].'</span></div>';
              ?>
              </a>
              <div class="pill"><i class="bi bi-hand-thumbs-down"></i></div>
              <div class="pill" onclick="shareVideo()"><i class="bi bi-share"></i><span>Share</span></div>
              <?php
if ($isPremium) {
    // Premium: unlimited downloads
    echo '<a href="'.htmlspecialchars($video['video_url']).'" download>
            <div class="pill"><i class="bi bi-download"></i><span>Save</span></div>
          </a>';
} else {
    // Non-premium: max 3 per day
    $today = date("Y-m-d");

    if (!isset($_SESSION['download_count_date']) || $_SESSION['download_count_date'] != $today) {
        $_SESSION['download_count_date'] = $today;
        $_SESSION['download_count'] = 0;
    }

    if ($_SESSION['download_count'] < 3) {
        echo '<a href="download.php?video='.urlencode($video['video_url']).'">
                <div class="pill"><i class="bi bi-download"></i><span>Save</span></div>
              </a>';
    } else {
        echo '<a href="buy_premium.php">
        <div class="pill text-muted"><i class="bi bi-download"></i><span>Limit reached</span></div>
        </a>';
    }
}
?>

              <a href="report.php?video_id=<?php echo $video['video_id'];?>">
                <div class="pill text-danger"><i class="bi bi-flag-fill"></i><span>Report</span></div>
              </a>  
            </div>
          </div>
        </section>

        <!-- Channel -->
        <section class="mt-3 channel-card" data-aos="fade-up" data-aos-delay="100">
          <img class="avatar" src="<?php echo $user['dp_url'];?>" alt="channel">
          <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2">
              <div>
                <div class="fw-bold"><?php echo $user['username'];?></div>
                <div class="muted" style="font-size:.9rem;"><?php echo $user['subscribers'];?> subscribers</div>
              </div>
              <div class="ms-auto d-flex align-items-center gap-2">
                <?php
                  $sql = "SELECT * FROM subscribe WHERE subscriber_user_id=".$_SESSION['user_id']." AND subscribe_user_id=".$video['user_id'];
                  $result = $conn->query($sql);
                  if ($result && $result->num_rows > 0) {
                      echo '<button class="subscribed-btn">Subscribe</button>';
                  } else {
                      echo '<a href="subscribe.php?user_id='.$_SESSION['user_id'].'&c_user_id='.$video['user_id'].'&video_id='.$video['video_id'].'"><button class="subscribe-btn">Subscribe</button></a>';
                  }
                ?>
              </div>
            </div>
          </div>
        </section>

        <!-- Description -->
        <section class="mt-3 desc-card" data-aos="fade-up" data-aos-delay="150">
          <div id="descWrap" class="desc-gradient">
            <p class="mb-2"><?php echo $video['description'];?></p>
          </div>
          <div class="mt-2">
            <span id="toggleDesc" class="show-more">Show more</span>
          </div>
        </section>

        <!-- Comments -->
        <section class="mt-4" data-aos="fade-up" data-aos-delay="200">
          <h5 class="fw-bold mb-3">Comments • <?php echo $comment_count;?></h5>

          <div class="d-flex align-items-start gap-2 mb-3">
            <img class="avatar" src="<?php echo $_SESSION['dp_url'];?>" alt="me" style="width:44px;height:44px;">
            <div class="flex-grow-1">
              <form action="comment.php" method="post">
                <input name="message" class="form-control comment-input" placeholder="Add a comment..."/>
                <div class="d-flex gap-2 mt-2">
                  <input type="hidden" name="video_id" value="<?php echo $video['video_id']; ?>">
                  <button type="reset"  class="btn btn-sm btn-light rounded-pill px-3">Cancel</button>
                  <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Comment</button>
                </div>
              </form>
            </div>
          </div>

          <?php
            if ($result3 && $result3->num_rows > 0) {
                while ($comment = $result3->fetch_assoc()) {
                    $q = "SELECT dp_url FROM user WHERE user_id=".$comment['form_user_id'];
                    $r = $conn->query($q);
                    $dp = $r->fetch_assoc();
                    echo '<div class="comment-card mb-2" data-aos="fade-up" data-aos-delay="100">';
                    echo '<div class="d-flex gap-2 align-items-start">';
                    echo '<img class="avatar" src="'.$dp['dp_url'].'" style="width:42px;height:42px;">';
                    echo '<div><div class="fw-semibold">'.$comment['form_username'].'</div>';
                    echo '<div class="mt-1">'.$comment['massage'].'</div></div></div></div>';
                }
            } else {
                echo '<p class="text-muted">No comments yet. Be the first to comment!</p>';
            }
          ?>
        </section>
      </div>

      <!-- Right: Up Next -->
      <aside class="col-lg-4">
        <div class="upnext-card" data-aos="fade-left">
          <h6 class="fw-bold mb-2">Up next</h6>
          <div class="divider mb-2"></div>
          <?php 
            $q = "SELECT * FROM `video` 
      WHERE video_id != $video_id 
      ORDER BY RAND() 
      LIMIT 5";
$r = $conn->query($q);

if ($r && $r->num_rows > 0) {
    while($next = $r->fetch_assoc()){
        echo '
        <a style="text-decoration:none; color:inherit;" href="view.php?id='.$next['video_id'].'">
        <div class="up-item">
            <div class="thumb-wrap">
                <img class="thumb" src="'.$next['thumbnail_url'].'" alt="">
            </div>
            <div class="flex-grow-1">
                <div class="fw-semibold">'.$next['title'].'</div>
                <div class="muted" style="font-size:.9rem;">'.$next['username'].' • '.$next['views'].' views</div>
            </div>
        </div>
        </a>';
    }
} else {
    echo '<p class="text-muted">No more videos available.</p>';
}

          ?>
        </div>
      </aside>
    </div>

    <?php if ($showAd && isset($ad['ad_video_url'])): ?>
<div class="ad-container" id="adOverlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.75); display:flex; justify-content:center; align-items:center; z-index:9999; flex-direction:column;">
    <video id="adVideo" class="ad-video"muted >
        <source src="<?php echo $ad['ad_video_url']; ?>" type="video/mp4">
      </video>
      <button id="skipAdBtn">Skip Ad</button>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const adVideo = document.getElementById('adVideo');
    const mainVideo = document.getElementById('mainVideo');
    const adOverlay = document.getElementById('adOverlay');
    const skipBtn = document.getElementById('skipAdBtn');

    // Pause main video
    mainVideo.pause();

    // Try to autoplay ad
    adVideo.play().catch(() => {
        console.log("Autoplay blocked, user interaction needed.");
    });

    // Enable skip button after 5 seconds
    let counter = 5;
    const interval = setInterval(() => {
        counter--;
        if(counter <= 0){
            clearInterval(interval);
            skipBtn.textContent = "Skip Ad";
            skipBtn.disabled = false;
        }
    }, 5000);
    
    // Skip ad
    skipBtn.addEventListener('click', () => {
        adOverlay.remove();
        mainVideo.play();
    });

    // Auto-close when ad ends
    adVideo.onended = function() {
        adOverlay.remove();
        mainVideo.play();
    };
});
</script>
<?php endif; ?>


</main>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({ duration: 700, once: true });

// Description toggle
const wrap = document.getElementById('descWrap');
const toggle = document.getElementById('toggleDesc');
let expanded = false;
toggle?.addEventListener('click', () => {
    expanded = !expanded;
    if (expanded){
        wrap.classList.remove('desc-gradient');
        toggle.textContent = 'Show less';
    } else {
        wrap.classList.add('desc-gradient');
        toggle.textContent = 'Show more';
        wrap.scrollIntoView({behavior:'smooth', block:'nearest'});
    }
});

// Share video
function shareVideo() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({title: document.title, text:"Check out this video!", url:url}).catch(err=>console.log(err));
    } else {
        navigator.clipboard.writeText(url).then(()=>alert("Video link copied to clipboard!"));
    }
}

// Ad logic
<?php if ($showAd && isset($ad['ad_video_url'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    const mainVideo = document.getElementById('mainVideo');
    const adOverlay = document.getElementById('adOverlay');
    const adVideo = document.getElementById('adVideo');
    const skipBtn = document.getElementById('skipAdBtn');
    

    mainVideo.pause();
    let counter = 5;
    const interval = setInterval(() => {
      counter--;
      skipBtn.textContent = "Skip Ad (" + counter + ")";
      if (counter <= 0) {
          document.getElementById('skipAdBtn').style.visibility="visible";
            clearInterval(interval);
            skipBtn.textContent = "Skip Ad";
            skipBtn.disabled = false;
        }
    }, 1000);

    skipBtn.addEventListener('click', function() {
        adOverlay.remove();
        mainVideo.play();
    });

    adVideo.onended = function() {
        adOverlay.remove();
        mainVideo.play();
    };
});
<?php endif; ?>
</script>
</body>
</html>
