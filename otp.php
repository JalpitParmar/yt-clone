<?php
session_start();
require "db.php"; // Your DB connection

if (!isset($_SESSION['otp']) || !isset($_SESSION['reg_data'])) {
    header('Location: signup.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = trim($_POST['otp'] ?? '');

    if ($enteredOtp === $_SESSION['otp']) {
        // Insert user data into DB
        $data = $_SESSION['reg_data'];
        $sql = "INSERT INTO user (username, email, password, About, dp_url, role, is_Premium, Subscribers, Videos_Uploaded)
                VALUES (?, ?, ?, ?, ?, 'user', 0, 0, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $data['username'], $data['email'], $data['password'], $data['about'], $data['dp_url']);
        if ($stmt->execute()) {
            // Clear OTP and reg_data from session
            unset($_SESSION['otp'], $_SESSION['reg_data']);

            // Redirect to home page
            header("Location: login.php");
            exit;
        } else {
            $message = "Database error: Could not register user.";
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>Invalid OTP. Please try again.</div>";
    }
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OTP Verification - YT Clone</title>
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #fff5f5, #ffeaea);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      overflow: hidden;
    }

    /* Bubble background */
    .bubble {
      position: absolute;
      bottom: -100px;
      background: radial-gradient(circle, #ff4d4d 40%, rgba(139, 0, 0, 0.6));
      border-radius: 50%;
      opacity: 0.7;
      animation: rise 12s infinite ease-in;
    }

    @keyframes rise {
      0% {
        transform: translateY(0) scale(0.8);
        opacity: 0.9;
      }
      100% {
        transform: translateY(-120vh) scale(1.3);
        opacity: 0;
      }
    }

    .otp-container {
      background: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(255, 0, 0, 0.25);
      text-align: center;
      z-index: 1;
      max-width: 400px;
      width: 100%;
    }

    .otp-container h2 {
      margin-bottom: 15px;
      color: #b22222;
    }

    .otp-input {
      width: 80%;
      max-width: 250px;
      height: 50px;
      font-size: 20px;
      text-align: center;
      border: 2px solid #ddd;
      border-radius: 8px;
      outline: none;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      margin: 20px 0;
    }

    .otp-input:focus {
      border-color: #ff4d4d;
      box-shadow: 0 0 8px rgba(255, 77, 77, 0.5);
    }

    .btn {
      display: inline-block;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      background: linear-gradient(90deg, #ff4d4d, #b22222);
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .btn:hover {
      transform: scale(1.05);
      background: linear-gradient(90deg, #b22222, #800000);
    }
  </style>
</head>
<body>
  <!-- Floating red bubbles -->
  <div class="bubble" style="width:60px; height:60px; left:10%; animation-delay:0s;"></div>
  <div class="bubble" style="width:80px; height:80px; left:25%; animation-delay:2s;"></div>
  <div class="bubble" style="width:50px; height:50px; left:50%; animation-delay:4s;"></div>
  <div class="bubble" style="width:100px; height:100px; left:70%; animation-delay:1s;"></div>
  <div class="bubble" style="width:70px; height:70px; left:85%; animation-delay:3s;"></div>

  <!-- OTP Box -->
   
    <form method="POST">
  <div class="otp-container">
    <h2>OTP Verification</h2>
    <p>Enter the 6-digit code sent to your email</p>
    <input name="otp" type="text" maxlength="6" placeholder="Enter OTP" class="otp-input">
    <br>
    <button type="submit" class="btn">Verify OTP</button>
  </form>
  </div>
</body>
</html>
