<?php
require "db.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You must be logged in to upload a video'); window.location='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $username = $_SESSION['username']; 
    $user_id = $_SESSION['user_id']; 

    // Directories
    $thumbnailDir = "assets/img/thumbnail/";
    $videoDir = "assets/video/yt/";

    // File names
    $thumbnailName =  $_FILES['thumbnail']['name'];
    $videoName =     $_FILES['video']['name'];

    $thumbnailName = '_' . $_FILES['thumbnail']['name'];
$videoName = '_' . $_FILES['video']['name'];
$thumbnailPath = $thumbnailDir . $thumbnailName;
$videoPath = $videoDir . $videoName;

if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnailPath)) {
    die("Failed to upload thumbnail. Check folder permissions.");
}

if (!move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
    die("Failed to upload video. Check folder permissions or file size.");
}


    $stmt = $conn->prepare("INSERT INTO video 
(user_id, title, description, category, thumbnail_url, video_url, views, likes, time, username) 
VALUES (?, ?, ?, ?, ?, ?, 0, 0, NOW(), ?)");

$stmt->bind_param("issssss", $user_id, $title, $description, $category, $thumbnailPath, $videoPath, $username);

if($stmt->execute()){
    echo "<script>alert('Video uploaded successfully!'); window.location='index.php';</script>";
} else {
    echo "Video insert failed: " . $stmt->error;
}


    $q2 = "UPDATE `user` SET `videos_uploaded`= `videos_uploaded`+1 where `user_id`='$user_id'";
    

    if (!$conn->query($q2)) {
        die("User update failed: " . $conn->error);
    }


    echo "<script>alert('Video uploaded successfully!'); window.location='index.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Upload Video – YT Clone</title>
<link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap');
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
    body{min-height:100vh;display:flex;justify-content:center;align-items:center;background:#fff;}
    .bokeh{position:absolute;width:100%;height:100%;overflow:hidden;z-index:0;}
    .bokeh span{position:absolute;display:block;border-radius:50%;background:rgba(255,0,0,0.2);animation:floatUp 6s linear infinite;opacity:0.7;}
    .bokeh span:nth-child(1){width:100px;height:100px;bottom:-120px;left:10%;animation-duration:7s;}
    .bokeh span:nth-child(2){width:150px;height:150px;bottom:-160px;left:30%;animation-duration:6s;}
    .bokeh span:nth-child(3){width:80px;height:80px;bottom:-100px;left:50%;animation-duration:5s;}
    .bokeh span:nth-child(4){width:120px;height:120px;bottom:-130px;left:70%;animation-duration:8s;}
    .bokeh span:nth-child(5){width:90px;height:90px;bottom:-100px;left:85%;animation-duration:6s;}
    .bokeh span:nth-child(6){width:60px;height:60px;bottom:-80px;left:20%;animation-duration:4s;}
    @keyframes floatUp{0%{transform:translateY(0) scale(1);}100%{transform:translateY(-120vh) scale(1.4);}}
    .upload-box{position:relative;z-index:1;background:rgba(255,255,255,0.95);backdrop-filter:blur(12px);border-radius:15px;padding:35px;width:480px;
      box-shadow:0 8px 20px rgba(0,0,0,0.1);animation:fadeIn 0.8s ease-in-out;}
    @keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
    .upload-box h2{text-align:center;margin-bottom:20px;color:#c40000;}
    .upload-box label{font-weight:500;color:#333;margin:10px 0 5px;display:block;}
    .upload-box input,.upload-box textarea,.upload-box select{width:100%;padding:10px;margin-bottom:12px;border:1px solid #c40000;border-radius:6px;font-size:14px;}
    .upload-box textarea{resize:none;height:80px;}
    .drop-zone{border:2px dashed #c40000;padding:25px;text-align:center;border-radius:8px;cursor:pointer;margin-bottom:12px;background:rgba(255,0,0,0.05);}
    .drop-zone.dragover{background:rgba(255,0,0,0.15);transform:scale(1.02);}
    .file-info{font-size:13px;color:#333;margin-top:6px;}
    .upload-box button{width:100%;padding:12px;background:linear-gradient(90deg,#ff4d4d,#c40000);border:none;border-radius:6px;color:white;font-size:16px;cursor:pointer;}
    .upload-box button:hover{background:linear-gradient(90deg,#c40000,#a00000);transform:scale(1.02);}
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
/* ✅ Responsive tweaks */
@media (max-width: 768px) {
  body {
    padding: 10px;
  }
  .upload-box {
    padding: 20px;
  }
  .upload-box h2 {
    font-size: 1.2rem;
  }
  .upload-box button {
    font-size: 15px;
    padding: 10px;
  }
}

@media (max-width: 480px) {
  .upload-box {
    width: 100%;    /* ✅ take full width on very small screens */
    margin: 10px;
    padding: 15px;
  }
  .upload-box h2 {
    font-size: 1rem;
  }
  .upload-box label {
    font-size: 13px;
  }
  .upload-box input,
  .upload-box textarea,
  .upload-box select {
    font-size: 13px;
    padding: 8px;
  }
  .upload-box button {
    font-size: 14px;
    padding: 10px;
  }
  a.btn-back {
    top: 10px;
    left: 10px;
    padding: 6px 12px;
    font-size: 14px;
  }
}

  </style>
</head>
<body>
  <a href="index.php" class="btn-back">
  ← Back
</a>
  
  <div class="bokeh"><span></span><span></span><span></span><span></span><span></span><span></span></div>
  <div class="upload-box">
    <h2>Upload New Video</h2>
    <form method="post" enctype="multipart/form-data">
      <label>Video Title</label>
      <input type="text" name="title" placeholder="Enter video title" required>

      <label>Description</label>
      <textarea name="description" placeholder="Enter video description" required></textarea>

      <label>Upload Video</label>
      <div class="drop-zone" id="dropZone">
        Drag & Drop your video here, or click to select
        <input type="file" name="video" accept="video/*" id="videoInput" hidden required>
        <div class="file-info" id="videoInfo"></div>
      </div>

      <label>Upload Thumbnail</label>
      <input type="file" name="thumbnail" accept="image/*" required>

      <label>Category</label>
      <select name="category" required>
        <option value="">-- Select Category --</option>
        <option>Music</option><option>Sports</option><option>Gaming</option><option>News</option>
        <option>Fashion</option><option>Education</option><option>Technology</option><option>Comedy</option>
        <option>Cooking</option><option>Fitness</option><option>Vlogs</option><option>Documentary</option>
        <option>Travel</option><option>Movie</option><option>Other</option>
      </select>

      <button type="submit">Upload Video</button>
    </form>
  </div>

  <script>
    const dropZone=document.getElementById('dropZone');
    const videoInput=document.getElementById('videoInput');
    const videoInfo=document.getElementById('videoInfo');
    dropZone.addEventListener('click',()=>videoInput.click());
    dropZone.addEventListener('dragover',e=>{e.preventDefault();dropZone.classList.add('dragover');});
    dropZone.addEventListener('dragleave',()=>dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop',e=>{e.preventDefault();dropZone.classList.remove('dragover');
      if(e.dataTransfer.files.length){videoInput.files=e.dataTransfer.files;videoInfo.textContent='Selected: '+e.dataTransfer.files[0].name;}});
    videoInput.addEventListener('change',()=>{if(videoInput.files.length){videoInfo.textContent='Selected: '+videoInput.files[0].name;}});
  </script>
</body>
</html>
 