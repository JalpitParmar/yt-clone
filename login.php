<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM user WHERE email = '$email' LIMIT 1");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['is_premium'] = $row["is_premium"];
            $_SESSION['role'] = $row['role'];
            $_SESSION['dp_url'] = $row['dp_url'];

            echo "<script>alert('Login successful!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Invalid Email or password.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
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

    .wave {
  position: fixed; /* fixed instead of absolute */
  bottom: -50px;   /* slightly below the viewport for smoother effect */
  left: 0;
  width: 200%;
  height: 200px;
  background: rgba(255, 0, 0, 0.2);
  border-radius: 100%;
  animation: wave 8s linear infinite;
  z-index: 0; /* keep behind login box */
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

    /* Login Box */
    .login-box {
      position: relative;
      z-index: 10;
      background: rgba(255, 0, 0, 0.05);
      backdrop-filter: blur(12px);
      border-radius: 15px;
      padding: 40px;
      width: 350px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      animation: fadeIn 1.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 28px;
      color: #c40000;
    }

    .login-box input {
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

    .login-box button {
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

    .login-box button:hover {
      transform: scale(1.05);
      background: linear-gradient(90deg, #c40000, #a00000);
    }

    .login-box p {
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
    }

    .login-box a {
      color: #c40000;
      text-decoration: none;
      font-weight: 500;
    }

    .login-box a:hover {
      text-decoration: underline;
    }
    @keyframes wave {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* Ensure login box stays above waves */
.login-box {
  position: relative;
  z-index: 10;
}

/* Responsive adjustments for fixed waves */
@media (max-width: 768px) {
  .wave {
    height: 150px;
  }
  .wave:nth-child(2) {
    height: 150px;
  }
}

@media (max-width: 480px) {
  .wave {
    height: 120px;
  }
  .wave:nth-child(2) {
    height: 120px;
  }
}
  </style>
</head>
<body>
  <!-- Wave animation background -->
  <div class="wave"></div>
  <div class="wave"></div>

  <!-- Login Box -->
  <div class="login-box">
    <h2>Login</h2>
    <form method="post">
      <input type="text" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don’t have an account? <a href="signup.php">Sign Up</a></p>
  </div>
</body>
</html>
