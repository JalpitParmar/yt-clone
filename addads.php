<?php
session_start();
// db.php - database connection
require 'db.php';

// ---- Check if Admin is logged in ----
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied. Admins only. <a href='login.php'>Login</a>");
}

// Handle Add New Ad
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addAd'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);

    // Upload video
    if (!empty($_FILES['video']['name'])) {
        $videoName = basename($_FILES['video']['name']);
        $videoTmp  = $_FILES['video']['tmp_name'];
        if (!is_dir($adsDir)) {
            mkdir($adsDir, 0777, true);
        }
// Directory for uploads (relative to project root)
$adsDir = "assets/video/ads/";

if (!is_dir(__DIR__ . "/" . $adsDir)) {
    mkdir(__DIR__ . "/" . $adsDir, 0777, true);
}

// Safe unique filename
$videoName = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES['video']['name']));
$videoPath = $adsDir . $videoName; // <-- relative path stored in DB
$fullPath  = __DIR__ . "/" . $videoPath; // full path for saving file

if (move_uploaded_file($_FILES['video']['tmp_name'], $fullPath)) {
    $sql = "INSERT INTO ads (ad_title, ad_video_url, ad_link) VALUES ('$title','$videoPath','$description')";
    $conn->query($sql);
}

    }

    header("Location: addads.php");
    exit;
}

// Handle Delete Ad
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $res = $conn->query("SELECT ad_video_url FROM ads WHERE ads_id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $filePath = __DIR__ . '/' . $row['ad_video_url']; // full path
        if (file_exists($filePath)) {
            unlink($filePath); // delete file
        }
    }

    $conn->query("DELETE FROM ads WHERE ads_id=$id");
    header("Location: addads.php");
    exit;
}


// Fetch ads
$result = $conn->query("SELECT * FROM ads ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ad Manager - YT Clone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #fff5f5, #ffeaea);
      min-height: 100vh;
    }
    .card { border-radius: 15px; box-shadow: 0 6px 20px rgba(255, 0, 0, 0.25); }
    .card-header { background: linear-gradient(90deg, #ff4d4d, #b22222); color: white; font-weight: bold; border-radius: 15px 15px 0 0; }
    video { width: 220px; border-radius: 10px; }
    .table td, .table th { vertical-align: middle; }
    /* General */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #fff5f5, #ffeaea);
    min-height: 100vh;
}
.card {
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(255, 0, 0, 0.25);
    margin-bottom: 20px;
}
.card-header {
    background: linear-gradient(90deg, #ff4d4d, #b22222);
    color: white;
    font-weight: bold;
    border-radius: 15px 15px 0 0;
}

/* Videos in Table */
video {
    width: 220px;
    border-radius: 10px;
}

/* Table adjustments */
.table td, .table th {
    vertical-align: middle;
}
.table {
    width: 100%;
    overflow-x: auto;
}

/* Responsive Tweaks */
@media (max-width: 1024px) {
    video {
        width: 180px;
        height: auto;
    }
    .card-body {
        padding: 15px;
    }
}

@media (max-width: 768px) {
    .card-body form .mb-3 input,
    .card-body form .mb-3 textarea,
    .card-body form button {
        width: 100%;
    }

    video {
        width: 150px;
    }

    table {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 10px;
    }

    video {
        width: 100%;
        height: auto;
        margin-bottom: 10px;
    }

    table {
        font-size: 12px;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
    }

    .table tbody tr td {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border: none;
    }

    .table tbody tr td::before {
        content: attr(data-label);
        font-weight: bold;
        flex: 1;
        text-align: left;
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
.back-nav{
  width: 100%;
  height: 10px;
}
  </style>
</head>
<body>
<div class="container py-5">
<div class="back-nav">
  <a href="javascript:history.back()" class="btn-back">
    ← Back
  </a>
</div>

  <!-- Add New Ad -->
  <div class="card mb-4">
    <div class="card-header">Add New Ad</div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Ad Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Ad Description</label>
          <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Upload Ad Video</label>
          <input type="file" name="video" class="form-control" accept="video/*" required>
        </div>
        <button type="submit" name="addAd" class="btn btn-danger">Add Ad</button>
      </form>
    </div>
  </div>

  <!-- Manage Ads -->
  <div class="card">
    <div class="card-header">Manage Ads</div>
    <div class="card-body">
      <table class="table table-bordered text-center">
        <thead class="table-danger">
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Description</th>
            <th>Ad Video</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $result=$conn->query("SELECT * FROM `ads`")?>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['ads_id'] ?></td>
                <td><?= htmlspecialchars($row['ad_title']) ?></td>
                <td><?= htmlspecialchars($row['ad_link']) ?></td>
                <td>
                  <video controls>
                    <?php echo "hi".$row['ad_video_url'];?>
                  <?php echo '<source src="'.$row['ad_video_url'].'" type="video/mp4">';?> 
                    Your browser does not support video.
                  </video>
                </td>
                <td>
                  <a href="addads.php?delete=<?= $row['ads_id'] ?>" 
   class="btn btn-sm btn-danger" 
   onclick="return confirm('Delete this ad?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">No ads available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
