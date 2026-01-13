<?php
session_start();
include 'db.php'; // your db connection

// Ensure user logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to report a video.");
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $video_id = intval($_POST['video_id']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);

    $sql = "INSERT INTO report (video_id, user_id, Reason_for_Reporting, Additional_Details) 
            VALUES ('$video_id', '$user_id', '$reason', '$details')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>window.location='view.php?id=$video_id';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit;
}

// Get video_id
$video_id = isset($_GET['video_id']) ? intval($_GET['video_id']) : (isset($_COOKIE['video_id']) ? intval($_COOKIE['video_id']) : 0);
if ($video_id === 0) {
    die("No video selected to report.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report Video</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="assets/img/logo.png">
   <style>
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
<body class="bg-light">
    <div class="back-nav">
    <a href="index.php" class="btn-back">
      ← Back
    </a>
  </div>
<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-3 text-danger">Report Video</h3>
        <form method="POST">
            <input type="hidden" name="video_id" value="<?php echo $video_id; ?>">

            <!-- Reason (dropdown for common reasons) -->
            <div class="mb-3">
                <label for="reason" class="form-label">Reason for reporting:</label>
                <select name="reason" id="reason" class="form-select" required>
                    <option value="">-- Select a reason --</option>
                    <option value="Spam">Spam</option>
                    <option value="Harmful">Harmful or Dangerous</option>
                    <option value="Inappropriate">Sexual/Inappropriate</option>
                    <option value="Violence">Violence</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <!-- Additional Details -->
            <div class="mb-3">
                <label for="details" class="form-label">Additional details (optional):</label>
                <textarea name="details" id="details" class="form-control" rows="4"></textarea>
            </div>
            
            <button type="submit" class="btn btn-danger">Submit Report</button>
            <a href="view.php?video_id=<?php echo $video_id; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>
