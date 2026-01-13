<?php
session_start();
include 'db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user details
$sql = "SELECT * FROM user WHERE user_id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $about = $conn->real_escape_string($_POST['about']);
    $dp_url = $user['dp_url']; // keep old dp if not updated

    // Handle profile picture upload
    if (!empty($_FILES['dp_url']['name'])) {
        $targetDir = "assets/img/dp/";  
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["dp_url"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["dp_url"]["tmp_name"], $targetFile)) {
            $dp_url = $targetFile;
        }
    }

    // Update query
    $updateSql = "UPDATE user 
                  SET username='$username', email='$email', about='$about', dp_url='$dp_url'
                  WHERE user_id=$user_id";

    if ($conn->query($updateSql)) {
        $conn->query("UPDATE `video` SET `username`='$username' WHERE `user_id`='$user_id'");
        $conn->query("UPDATE `comment` SET `form_username`='$username' WHERE `form_user_id`='$user_id'");
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap');
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      background: #fff;
    }

    .wave {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 200%;
      height: 200px;
      background: rgba(255, 0, 0, 0.2);
      border-radius: 100%;
      animation: wave 8s linear infinite;
    }
    .wave:nth-child(2){
      animation-delay:-4s;
      opacity:.5;
      background:rgba(255,0,0,0.3);
    }
    @keyframes wave{
      0%{transform:translateX(0);}
      100%{transform:translateX(-50%);}
    }

    .edit-box {
      position: relative;
      z-index: 10;
      background: rgba(255,0,0,0.05);
      backdrop-filter: blur(12px);
      border-radius: 15px;
      padding: 40px;
      width: 400px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      animation: fadeIn 1.5s ease;
    }
    @keyframes fadeIn {
      from{opacity:0;transform:translateY(-30px);}
      to{opacity:1;transform:translateY(0);}
    }
    .edit-box h2 {
      text-align:center;
      margin-bottom:25px;
      font-size:28px;
      color:#c40000;
    }

    .profile-pic {
      position:relative;
      width:110px;
      height:110px;
      margin:0 auto 20px;
    }
    .profile-pic img {
      width:100%;
      height:100%;
      border-radius:50%;
      object-fit:cover;
      border:3px solid #c40000;
      box-shadow:0 5px 15px rgba(0,0,0,0.2);
    }
    .profile-pic label {
      position:absolute;
      bottom:0;right:0;
      background:#c40000;
      color:#fff;
      width:32px;
      height:32px;
      display:flex;
      justify-content:center;
      align-items:center;
      border-radius:50%;
      cursor:pointer;
      font-size:16px;
      border:2px solid #fff;
      transition:transform .3s ease;
    }
    .profile-pic label:hover{transform:scale(1.1);background:#a00000;}
    .profile-pic input{display:none;}

    .edit-box input,
    .edit-box textarea {
      width:100%;
      padding:12px;
      margin:12px 0;
      border:1px solid #c40000;
      border-radius:8px;
      font-size:15px;
      background:rgba(255,255,255,0.9);
      outline:none;
    }
    .edit-box textarea{resize:none;height:80px;}

    .edit-box button {
      width:100%;
      padding:12px;
      background:linear-gradient(90deg,#ff4d4d,#c40000);
      border:none;
      border-radius:8px;
      color:#fff;
      font-size:16px;
      font-weight:600;
      cursor:pointer;
      transition:transform .3s ease, background .3s;
    }
    .edit-box button:hover {
      transform:scale(1.05);
      background:linear-gradient(90deg,#c40000,#a00000);
    }

    .alert {
      padding:10px;
      border-radius:8px;
      margin-bottom:10px;
      text-align:center;
      font-size:14px;
    }
    .success { background:#d4edda; color:#155724;}
    .error { background:#f8d7da; color:#721c24;}
    a.btn-back {
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

  </style>
</head>
<body>
  <a href="index.php" class="btn-back">
    ← Back
  </a>
  <div class="wave"></div>
  <div class="wave"></div>

  <div class="edit-box">
    <h2>Edit Profile</h2>

    <?php if(isset($_SESSION['success'])): ?>
      <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
      <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
     <div class="profile-pic">
  <img src="<?= !empty($user['dp_url']) ? $user['dp_url'] : 'assets/img/dp/default.png'; ?>" id="preview">
  <label for="fileUpload">✎</label>
  <input type="file" name="dp_url" id="fileUpload" accept="image/*" onchange="loadFile(event)">
</div>


      <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
      <textarea name="about" placeholder="About Me"><?= htmlspecialchars($user['about']); ?></textarea>
      <button type="submit">Update Profile</button>
    </form>
  </div>

  <script>
    function loadFile(event){
      document.getElementById('preview').src = URL.createObjectURL(event.target.files[0]);
    }
  </script>
</body>
</html>
