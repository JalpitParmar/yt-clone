<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in '); window.location='login.php';</script>";
    exit();
}
$user_id = $_SESSION['user_id']; // assuming login system

if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];

    // mark as premium
    $sql = "UPDATE user SET is_premium = 1 WHERE user_id = $user_id";
    $_SESSION['is_premium']=1;
    if ($conn->query($sql) === TRUE) {
        // SUCCESS → show success video & redirect home
        $video = "assets/video/pay/success.mp4"; 
        $redirect = "index.php";
        $message = "Payment Successful! You are now a Premium Member 🎉";
    } else {
        // FAILED → show failed video & redirect back to buy
        $video = "assets/video/pay/failure.mp4"; 
        $redirect = "buy_premium.php";
        $message = "Payment Failed ❌ Please try again!";
    }
} else {
    // no payment id = fail
    $video = "assets/failure.mp4"; 
    $redirect = "buy.php";
    $message = "Payment Failed ❌ Please try again!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Status</title>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
</head>
<body style="text-align:center; margin-top:50px;">
    <h2><?php echo $message; ?></h2>
    <video id="statusVideo" width="600" autoplay>
        <source src="<?php echo $video; ?>" type="video/mp4">
    </video>

    <script>
        let vid = document.getElementById("statusVideo");
        vid.onended = function() {
            window.location.href = "<?php echo $redirect; ?>";
        };
    </script>
</body>
</html>
