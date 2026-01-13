<?php
session_start();
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data safely
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $about = $_POST['about'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Handle DP upload (or default)
        if (isset($_FILES['dp_img']) && $_FILES['dp_img']['error'] == 0) {
            $ext = pathinfo($_FILES['dp_img']['name'], PATHINFO_EXTENSION);
            $dp_name = uniqid() . "dp." . $ext;
            if (!move_uploaded_file($_FILES['dp_img']['tmp_name'], $upload_path)) {
              $dp_name = "default.png";
            }
          } else {
            $dp_name = "default.png";
          }
          $upload_path = "assets/img/dp/" . $dp_name;

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store all user data and otp in session
        $_SESSION['reg_data'] = [
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
            'about' => $about,
            'dp_url' => $upload_path,
        ];
        $_SESSION['otp'] = strval($otp);

        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jalpitparmar1234@gmail.com';           // your Gmail
            $mail->Password   = 'ynua jqff zblm dbbk';        // your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('yourgmail@gmail.com', 'YouTube Clone');
            $mail->addAddress($email, $username);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
           $mail->isHTML(true);
$mail->Subject = 'Your One-Time Password (OTP)';

// HTML body
$mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OTP Verification</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin:0; padding:0; }
    .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 10px; padding: 30px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    h2 { color: #c40000; }
    p { font-size: 16px; color: #333; }
    .otp { display: inline-block; background: #c40000; color: #fff; font-size: 24px; font-weight: bold; padding: 10px 20px; border-radius: 5px; margin: 20px 0; letter-spacing: 5px; }
    .footer { font-size: 12px; color: #777; margin-top: 20px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>OTP Verification</h2>
    <p>Use the following One-Time Password (OTP) to complete your action:</p>
    <div class="otp">' . $otp . '</div>
    <p>This OTP is valid for 10 minutes. Do not share it with anyone.</p>
    <div class="footer">If you did not request this, please ignore this email.</div>
  </div>
</body>
</html>
';
            $mail->send();

            // Redirect to OTP verification page
            header("Location: otp.php");
            exit;
        } catch (Exception $e) {
            $error = "OTP email could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup</title>
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      background: #ffffff;
      color: #333;
    }

    /* Wave Background */
   .wave {
  position: fixed;      /* fixed instead of absolute */
  bottom: -50px;        /* slightly below the viewport */
  left: 0;
  width: 200%;
  height: 200px;
  background: rgba(255, 0, 0, 0.2);
  border-radius: 100%;
  animation: wave 8s linear infinite;
  z-index: 0;           /* behind the signup box */
}

.wave:nth-child(2) {
  animation-delay: -4s;
  opacity: 0.5;
  background: rgba(255, 0, 0, 0.3);
}

@keyframes wave {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* Ensure signup box stays above waves */
.signup-box {
  position: relative;
  z-index: 10;
}

    /* Signup Box */
    .signup-box {
      position: relative;
      z-index: 10;
      background: rgba(255, 0, 0, 0.05);
      backdrop-filter: blur(12px);
      border-radius: 15px;
      padding: 40px;
      width: 380px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      animation: fadeIn 1.5s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .signup-box h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 28px;
      color: #c40000;
    }

    /* Profile Picture Upload */
    .profile-pic {
      position: relative;
      width: 110px;
      height: 110px;
      margin: 0 auto 20px;
    }
    .profile-pic img {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #c40000;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .profile-pic label {
      position: absolute;
      bottom: 0;
      right: 0;
      background: #c40000;
      color: #fff;
      width: 32px;
      height: 32px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      cursor: pointer;
      font-size: 16px;
      border: 2px solid #fff;
      transition: transform 0.3s ease;
    }
    .profile-pic label:hover {
      transform: scale(1.1);
      background: #a00000;
    }
    .profile-pic input {
      display: none;
    }

    .signup-box input, 
    .signup-box textarea {
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      border: 1px solid #c40000;
      border-radius: 8px;
      outline: none;
      font-size: 15px;
      background: rgba(255, 255, 255, 0.9);
      color: #333;
    }
    .signup-box textarea {
      resize: none;
      height: 80px;
    }

    .signup-box button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #ff4d4d, #c40000);
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.3s ease, background 0.3s;
    }
    .signup-box button:hover {
      transform: scale(1.05);
      background: linear-gradient(90deg, #c40000, #a00000);
    }

    .signup-box p {
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
    }
    .signup-box a {
      color: #c40000;
      text-decoration: none;
      font-weight: 500;
    }
    .signup-box a:hover {
      text-decoration: underline;
    }
    /* Responsive adjustments for fixed waves */
@media (max-width: 1024px) {
  .wave {
    height: 180px;
  }
  .wave:nth-child(2) {
    height: 180px;
  }
}

@media (max-width: 768px) {
  .wave {
    height: 150px;
  }
  .wave:nth-child(2) {
    height: 150px;
  }
  .signup-box {
    width: 320px;
    padding: 30px;
  }
}

@media (max-width: 480px) {
  .wave {
    height: 120px;
  }
  .wave:nth-child(2) {
    height: 120px;
  }
  .signup-box {
    width: 90%;
    padding: 20px;
  }
}
  </style>
</head>
<body>
  <!-- Wave animation background -->
  <div class="wave"></div>
  <div class="wave"></div>

  <!-- Signup Box -->
  <div class="signup-box">
    <h2>Create Account</h2>
    <form method="post">
      <!-- Profile Picture Upload -->
      <div class="profile-pic">
        <img src="https://via.placeholder.com/110" id="preview">
        <label for="fileUpload">+</label>
        <input type="file" name="dp_img" id="fileUpload" accept="image/*" onchange="loadFile(event)">
      </div>

      
      <input type="email" name="email" placeholder="Email" required>
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
     
      <textarea name="about" placeholder="About Me"></textarea>
      <button type="submit">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>

  <script>
    // Preview uploaded profile picture
    function loadFile(event) {
      const img = document.getElementById("preview");
      img.src = URL.createObjectURL(event.target.files[0]);
    }
  </script>
</body>
</html>
