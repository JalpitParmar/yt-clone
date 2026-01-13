<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buy Premium - YT Clone</title>
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(120deg, #ffeaea, #ffd6d6);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      overflow: hidden;
      animation: bgPulse 8s ease-in-out infinite;
    }
    .money {
      position: absolute;
      top: -50px;
      font-size: 28px;
      color: gold;
      animation: fall 10s linear infinite;
      opacity: 0.9;
    }
    @keyframes fall {
      0% { transform: translateY(0) rotate(0deg); opacity: 1; }
      100% { transform: translateY(110vh) rotate(360deg); opacity: 0; }
    }
    @keyframes bgPulse {
      0% { background: linear-gradient(120deg, #ffeaea, #ffd6d6); }
      50% { background: linear-gradient(120deg, #fff2f2, #ffe0e0); }
      100% { background: linear-gradient(120deg, #ffeaea, #ffd6d6); }
    }
    .container { display: flex; gap: 30px; position: relative; z-index: 1; }
    .plan {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      width: 250px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      transition: transform 0.3s ease;
    }
    .plan:hover { transform: scale(1.05); }
    .plan h2 { margin-bottom: 10px; color: #c40000; }
    .price { font-size: 28px; font-weight: bold; margin: 15px 0; }
    .plan ul { list-style: none; padding: 0; margin: 15px 0; }
    .plan ul li { margin: 8px 0; color: #333; }
    .btn {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      background: linear-gradient(90deg, #ff4d4d, #c40000);
      color: #fff;
      font-weight: bold;
      transition: background 0.3s ease, transform 0.2s ease;
      cursor: pointer;
    }
    .btn:hover {
      background: linear-gradient(90deg, #c40000, #a00000);
      transform: scale(1.05);
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

    /* ---------- Responsive ---------- */
@media (max-width: 992px) {
  .container {
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
  }
  .plan {
    width: 280px;
  }
}

@media (max-width: 768px) {
  body {
    padding: 20px;
    overflow-y: auto;
  }
  .container {
    flex-direction: column;
    align-items: center;
    gap: 25px;
  }
  .plan {
    width: 100%;
    max-width: 350px;
  }
}

@media (max-width: 480px) {
  .plan {
    padding: 20px;
    width: 100%;
    max-width: 320px;
  }
  .plan h2 {
    font-size: 20px;
  }
  .price {
    font-size: 22px;
  }
  .plan ul li {
    font-size: 14px;
  }
   .btn {
    display: block;
    width: 90%;
    max-width: 300px;
    margin: 10px auto;
    text-align: center;
    font-size: 16px;
    padding: 12px;
    border-radius: 8px;
  }
}
  </style>
</head>
<body>
  
<a href="index.php" class="btn-back">
  ← Back
</a>

  <!-- Floating Money Symbols -->
  <span class="money" style="left:10%; animation-delay:0s;">💰</span>
  <span class="money" style="left:25%; animation-delay:2s;">💵</span>
  <span class="money" style="left:40%; animation-delay:4s;">💲</span>
  <span class="money" style="left:60%; animation-delay:1s;">💸</span>
  <span class="money" style="left:80%; animation-delay:3s;">💰</span>
  <span class="money" style="left:90%; animation-delay:5s;">💵</span>

  <div class="container">
    <!-- Free Plan -->
    <div class="plan">
      <h2>Free</h2>
      <div class="price">₹0</div>
      <ul>
        <li>❌ Ad-free watching</li>
        <li>❌ Unlimited downloads</li>
        <li>✔ Banner Ads</li>
      </ul>
      <a href="#" class="btn">Choose Free</a>
    </div>

    <!-- Premium Plan -->
    <div class="plan">
      <h2>Premium</h2>
      <div class="price">₹49</div>
      <ul>
        <li>✔ Ad-free watching</li>
        <li>✔ Unlimited downloads</li>
        <li>❌ No Banner Ads</li>
      </ul>
      <button id="rzp-button1" class="btn">
      <?php
      if(isset($_SESSION['username'])){


        if($_SESSION['is_premium']){
          echo'Already Bought';
        }else{
          echo'Buy Now';
        }
      }else{
        echo'Buy Now';
      }
      ?>
      </button>
    </div>
  </div>

  <script>
    var options = {
        "key": "rzp_test_1DP5mmOlF5G5ag", // ✅ Public Razorpay Test Key
        "amount": 4900, // 49 INR = 4900 paise
        "currency": "INR",
        "name": "YT Clone",
        "description": "Premium Plan Purchase",
        "image": "https://yourwebsite.com/logo.png",
        "handler": function (response){
            alert("✅ Payment Successful!\nPayment ID: " + response.razorpay_payment_id);
            // You can call PHP file here to mark user as premium
            window.location.href = "payment_success.php?payment_id=" + response.razorpay_payment_id;
        },
        "prefill": {
            "name": "Jalpit Parmar",
            "email": "demo@example.com",
            "contact": "9999999999"
        },
        "theme": { "color": "#c40000" }
    };
    var rzp1 = new Razorpay(options);
    var a = document.getElementById('rzp-button1').innerText;
      document.getElementById('rzp-button1').onclick = function(e){
        if(a==="Already Bought"){
            window.location='index.php';
        }else{
        rzp1.open();
        e.preventDefault();
      }
    }
  </script>
</body>
</html>
