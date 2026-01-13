<?php
include 'db.php'; // your DB connection

// Fetch reports with JOIN to get reporter, video, and uploader
$sql = "SELECT r.Reprot_id, r.Reason_for_Reporting, r.Additional_Details,
               ru.username AS reporter_name, ru.user_id AS reporter_id,
               v.title AS video_title, v.video_url, v.video_id,
               vu.username AS uploader_name, vu.user_id AS uploader_id
        FROM report r
        JOIN user ru ON r.user_id = ru.user_id     -- reporter
        JOIN video v ON r.video_id = v.video_id    -- video
        JOIN user vu ON v.user_id = vu.user_id     -- uploader
        ORDER BY r.Reprot_id DESC";

$result = $conn->query($sql);

// Handle Delete report
if (isset($_GET['delete_report_id'])) {
    $id = intval($_GET['delete_report_id']);
    $conn->query("DELETE FROM report WHERE Reprot_id=$id");
    header("Location: managereports.php");
    exit;
}

// Handle Delete video
if (isset($_GET['delete_video_id'])) {
    $id = intval($_GET['delete_video_id']);

    $res = $conn->query("SELECT video_url FROM video WHERE video_id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $filePath = __DIR__ . '/' . $row['video_url'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $conn->query("DELETE FROM video WHERE video_id=$id");
    $conn->query("DELETE FROM report WHERE video_id=$id");
    $conn->query("DELETE FROM likes WHERE video_id=$id");
    $conn->query("DELETE FROM comment WHERE video_id=$id");
    header("Location: managereports.php");
    exit;
}

// Handle Delete user
if (isset($_GET['delete_user_id'])) {
    $id = intval($_GET['delete_user_id']);

    // Delete all user’s videos + files
    $res = $conn->query("SELECT video_url FROM video WHERE user_id=$id");
    while ($res && $row = $res->fetch_assoc()) {
        $filePath = __DIR__ . '/' . $row['video_url'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $conn->query("DELETE FROM user WHERE user_id=$id");
    $conn->query("DELETE FROM video WHERE user_id=$id");
    $conn->query("DELETE FROM report WHERE user_id=$id");
    $conn->query("DELETE FROM likes WHERE to_user_id=$id OR from_user_id=$id");
    $conn->query("DELETE FROM comment WHERE to_user_id=$id OR from_user_id=$id");

    header("Location: managereports.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Reports - YT Clone</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    body { margin:0; font-family:'Segoe UI',sans-serif; min-height:100vh;
           display:flex; justify-content:center; align-items:flex-start;
           padding:50px 0; overflow-x:hidden; }
    body::before { content:""; position:fixed; top:0; left:0; width:200%; height:200%;
           background:linear-gradient(-45deg,#ffe0e0,#ffcccc,#ff9999,#ff6666);
           background-size:400% 400%; animation:gradientFlow 12s ease infinite; z-index:0; }
    @keyframes gradientFlow { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
    .report-container { position:relative; z-index:1; background:#fff; padding:30px;
           border-radius:15px; box-shadow:0 8px 25px rgba(255,0,0,0.2); width:90%; max-width:1000px; }
    .table { background:#fff; border-radius:12px; overflow:hidden; }
    .table thead { background:linear-gradient(90deg,#ff4d4d,#b22222); color:#fff; }
    .btn-action { margin:2px; font-size:.85rem; border-radius:8px; transition:.2s; }
    .btn-action:hover { transform:scale(1.05); box-shadow:0 4px 10px rgba(0,0,0,0.2); }
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
    /* General */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 50px 0;
    overflow-x: hidden;
}

/* Container */
.report-container {
    position: relative;
    z-index: 1;
    background: #fff;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(255,0,0,0.2);
    width: 90%;
    max-width: 1000px;
}

/* Table */
.table {
    width: 100%;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
}
.table thead {
    background: linear-gradient(90deg,#ff4d4d,#b22222);
    color: #fff;
}
.table td, .table th {
    vertical-align: middle;
}
.btn-action {
    margin: 2px;
    font-size: .85rem;
    border-radius: 8px;
    transition: .2s;
}
.btn-action:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

/* Back button */
a.btn-back {
    position: fixed;
    top: 15px;
    left: 15px;
    background-color: #6c757d;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    transition: background-color 0.2s ease-in-out, transform 0.1s ease-in-out;
    z-index: 9999;
}
a.btn-back:hover { background-color: #5a6268; transform: scale(1.05); }
a.btn-back:active { transform: scale(0.95); }

/* Responsive Tweaks */

/* Large tablets */
@media (max-width: 1024px) {
    .report-container { padding: 15px; }
    .btn-action { font-size: .8rem; padding: 4px 8px; }
}

/* Tablets */
@media (max-width: 768px) {
    .btn-action { font-size: .75rem; padding: 3px 6px; }
    table { font-size: 13px; }
}

/* Mobile phones */
@media (max-width: 480px) {
    .report-container { padding: 10px; }
    table { font-size: 12px; }
    
    /* Convert table to block layout for mobiles */
    .table thead { display: none; }
    .table tbody tr {
        display: block;
        margin-bottom: 15px;
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
    .btn-action { width: 100%; margin: 5px 0; }
}
  </style>
</head>
<body>
<div class="back-nav">
  <a href="index.php" class="btn-back">
    ← Back
  </a>
</div>
<div class="report-container">
  <h2 class="mb-4 text-danger fw-bold text-center">Manage Reports</h2>

  <table class="table table-hover text-center align-middle shadow-sm">
    <thead>
      <tr>
        <th>Report ID</th>
        <th>Reporter</th>
        <th>Video</th>
        <th>Uploader</th>
        <th>Reason</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td>#<?= $row['Reprot_id'] ?></td>
            <td><?= htmlspecialchars($row['reporter_name']) ?></td>
            <td><?= htmlspecialchars($row['video_title']) ?></td>
            <td><?= htmlspecialchars($row['uploader_name']) ?></td>
            <td><?= htmlspecialchars($row['Reason_for_Reporting']) ?></td>
            <td>
              <a href="view.php?id=<?= $row['video_id'] ?>" target="_blank" class="btn btn-sm btn-primary btn-action">View Video</a>
              <a href="managereports.php?delete_report_id=<?= $row['Reprot_id'] ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Delete this report?')">Delete Report</a>
              <a href="managereports.php?delete_video_id=<?= $row['video_id'] ?>" class="btn btn-sm btn-warning btn-action" onclick="return confirm('Delete this video?')">Delete Video</a>
              <a href="managereports.php?delete_user_id=<?= $row['uploader_id'] ?>" class="btn btn-sm btn-dark btn-action" onclick="return confirm('Delete this user?')">Delete User</a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-muted">No reports found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
