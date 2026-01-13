<?php 
session_start();
require "db.php"; 

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You must be logged in to manage videos'); window.location='login.php';</script>";
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// Fetch videos uploaded by this user
$q = "SELECT * FROM `video` WHERE `user_id` = '$user_id' ORDER BY time DESC";
$result = $conn->query($q);


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Videos - YT Clone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    body {
      background: linear-gradient(120deg, #ffffff, #ffe5e5);
      font-family: Arial, sans-serif;
    }

    .container {
      margin-top: 50px;
    }

    .table {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    .table th {
      background: #c40000;
      color: #fff;
      text-align: center;
    }

    .table td {
      vertical-align: middle;
      text-align: center;
    }

    .btn-action {
      padding: 6px 12px;
      border-radius: 6px;
      color: #fff;
      font-size: 14px;
      text-decoration: none;
      transition: transform 0.2s ease;
    }

    .btn-action:hover {
      transform: scale(1.1);
    }

    .btn-view {
      background: #007bff;
    }

    .btn-edit {
      background: #ffc107;
      color: #000;
    }

    .btn-delete {
      background: #dc3545;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .header h2 {
      color: #c40000;
      font-weight: bold;
    }
    /* ✅ Make table responsive */
.table-responsive {
  border-radius: 12px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

/* ✅ Ensure images resize properly */
.table img {
  max-width: 100px;
  height: auto;
  border-radius: 8px;
}

/* ✅ Responsive text adjustments */
@media (max-width: 768px) {
  .header h2 {
    font-size: 1.5rem;
  }
  .header p {
    font-size: 0.9rem;
  }
  .table th,
  .table td {
    font-size: 14px;
    padding: 8px;
  }
  .btn-action {
    font-size: 13px;
    padding: 5px 10px;
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

/* ✅ Make table responsive */
.table-responsive {
  border-radius: 12px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

/* ✅ Ensure images resize properly */
.table img {
  max-width: 100px;
  height: auto;
  border-radius: 8px;
}

/* ✅ Mobile-first design */
@media (max-width: 480px) {
  .header h2 {
    font-size: 1.2rem;
  }
  .header p {
    font-size: 0.8rem;
  }
  .table th,
  .table td {
    font-size: 12px;
    padding: 6px;
    white-space: nowrap;
  }
  .table img {
    max-width: 70px;
  }
  /* ✅ Stack buttons on mobile */
  .btn-action {
    display: block;
    margin: 3px auto;
    width: 90%;
    font-size: 12px;
  }
  
}
  </style>
</head>
<body>
  <a href="index.php" class="btn-back">
  ← Back
</a>

  <div class="container">
    <div class="header">
      <h2>Manage Your Videos</h2>
      <p class="text-muted">Edit, delete, or view your uploaded videos</p>
    </div>
<div class="table-responsive">
  

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Thumbnail</th>
          <th>Title</th>
          <th>description</th>
          <th>likes</th>
          <th>Views</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td style="width: 150px;">
                <img src="<?php echo htmlspecialchars($row['thumbnail_url']); ?>" 
                     alt="Video Thumbnail" 
                     class="img-fluid rounded">
              </td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['description']); ?></td>
              <td>
                <?php echo (int)$row['likes']; ?>
              </td>
              <td><?php echo (int)$row['views']; ?></td>
              <td>
                <a href="view.php?id=<?php echo $row['video_id']; ?>" class="btn-action btn-view">View</a>
                <a href="edit_video.php?id=<?php echo $row['video_id']; ?>" class="btn-action btn-edit">Edit</a>
                <a href="delete_video.php?id=<?php echo $row['video_id']; ?>" class="btn-action btn-delete" 
                   onclick="return confirm('Are you sure you want to delete this video?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted">No videos uploaded yet.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  </div>
</body>
</html>
