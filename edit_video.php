<?php
session_start();
require "db.php";

// Check login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You must be logged in'); window.location='login.php';</script>";
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$video_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch video details
$q = "SELECT * FROM video WHERE video_id='$video_id' AND user_id='$user_id'";
$result = $conn->query($q);

if (!$result || $result->num_rows == 0) {
    echo "<script>alert('Video not found or you are not authorized'); window.location='manage_videos.php';</script>";
    exit();
}
$video = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Thumbnail update (optional)
    $thumbnail_name = $video['thumbnail_url'];
    if (!empty($_FILES['thumbnail']['name'])) {
        $target_dir = "thumbnails/";
        $thumbnail_name = time() . "_" . basename($_FILES['thumbnail']['name']);
        $target_file = $target_dir . $thumbnail_name;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_file);
    }

    $update = "UPDATE video 
               SET title='$title', description='$description', thumbnail_url='$thumbnail_name' 
               WHERE video_id='$video_id' AND user_id='$user_id'";
    if ($conn->query($update)) {
        echo "<script>alert('Video updated successfully'); window.location='manage_videos.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating video');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Video</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    @import url('https://fonts.googleapis.com/css?family=Exo:400,700');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Exo', sans-serif;
      height: 100vh;
      overflow: hidden;
    }

    /* Background area */
    .area {
      background: #de233c;
      background: linear-gradient(135deg, #ff5f7e, #de233c);
      width: 100%;
      height: 100vh;
      position: absolute;
      top: 0;
      left: 0;
      z-index: -1;
    }

    .circles {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    .circles li {
      position: absolute;
      display: block;
      list-style: none;
      width: 20px;
      height: 20px;
      background: rgba(255, 255, 255, 0.25);
      animation: animate 25s linear infinite;
      bottom: -150px;
    }

    .circles li:nth-child(1){ left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
    .circles li:nth-child(2){ left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
    .circles li:nth-child(3){ left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
    .circles li:nth-child(4){ left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
    .circles li:nth-child(5){ left: 65%; width: 20px; height: 20px; animation-delay: 0s; }
    .circles li:nth-child(6){ left: 75%; width: 110px; height: 110px; animation-delay: 3s; }
    .circles li:nth-child(7){ left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
    .circles li:nth-child(8){ left: 50%; width: 25px; height: 25px; animation-delay: 15s; animation-duration: 45s; }
    .circles li:nth-child(9){ left: 20%; width: 15px; height: 15px; animation-delay: 2s; animation-duration: 35s; }
    .circles li:nth-child(10){ left: 85%; width: 150px; height: 150px; animation-delay: 0s; animation-duration: 11s; }

    @keyframes animate {
      0% { transform: translateY(0) rotate(0deg); opacity: 1; border-radius: 0; }
      100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; border-radius: 50%; }
    }

    /* Form container */
    .container {
  max-width: 600px;       /* default width for desktop */
  width: 90%;
  padding: 30px;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
  margin: 60px auto 40px auto;
  transition: all 0.3s ease-in-out;
}

    h2 {
      color: #de233c;
      font-weight: bold;
      text-align: center;
      margin-bottom: 25px;
    }

    .btn-save {
      background: #de233c;
      color: #fff;
      font-weight: bold;
    }
    .btn-save:hover {
      background: #b91b2f;
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
/* Mobile Responsiveness */
@media (max-width: 768px) {
  .container {
    width: 95%;
    padding: 20px;
    margin-top: 40px;
  }

  .container img {
    width: 100%;
    height: auto;
  }

  .btn-save, .btn-secondary {
    width: 100%;
    margin-bottom: 10px;
  }

  .mb-3 input,
  .mb-3 textarea,
  .mb-3 label {
    font-size: 14px;
  }

  h2 {
    font-size: 20px;
    margin-bottom: 20px;
  }
}

/* Smaller phones */
@media (max-width: 480px) {
  body {
    padding: 10px 0;
  }
  .container {
    width: 95%;
    padding: 15px;
    margin-top: 30px;
  }
  .container {
    padding: 15px;
    margin-top: 30px;
  }

  .mb-3 input,
  .mb-3 textarea,
  .mb-3 label {
    font-size: 13px;
  }

  h2 {
    font-size: 18px;
    margin-bottom: 15px;
  }

  .btn-back {
    padding: 6px 12px;
    font-size: 14px;
  }
}

/* Ensure background animation scales */
.area, .circles li {
  max-width: 100%;
}

/* Make buttons and form elements fit smaller screens */
form {
  display: flex;
  flex-direction: column;
}

  </style>
</head>
<body>
  <div class="back-nav">
  <a href="index.php" class="btn-back">
    ← Back
  </a>
</div>
  <!-- Background Animation -->
  <div class="area">
    <ul class="circles">
      <li></li><li></li><li></li><li></li><li></li>
      <li></li><li></li><li></li><li></li><li></li>
    </ul>
  </div>

  <!-- Edit Form -->
  <div class="container">
    <h2>Edit Video</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($video['title']); ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($video['description']); ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Current Thumbnail</label><br>
        <img src="<?php echo htmlspecialchars($video['thumbnail_url']); ?>" width="200" class="rounded mb-2">
      </div>

      <div class="mb-3">
        <label class="form-label">Change Thumbnail (optional)</label>
        <input type="file" name="thumbnail" class="form-control">
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-save">Save Changes</button>
        <a href="manage_videos.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
