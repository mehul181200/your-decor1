<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Optional: clear session cart or order data if needed
// unset($_SESSION['cart']); // if you're storing cart in session
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You - Your Decore</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 700px;
      margin: 80px auto;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      text-align: center;
    }
    h1 {
      color: #28a745;
      font-size: 32px;
      margin-bottom: 20px;
    }
    p {
      font-size: 18px;
      color: #555;
      margin-bottom: 30px;
    }
    .btn {
      display: inline-block;
      padding: 12px 25px;
      background: #004d4d;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-size: 16px;
      transition: background 0.3s;
    }
    .btn:hover {
      background: #007272;
    }
    .icon {
      font-size: 60px;
      color: #28a745;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="icon">✅</div>
    <h1>Thank You for Your Order!</h1>
    <p>Your purchase has been successfully placed. We’re preparing your items and will notify you once they’re on the way.</p>
    <a href="home.php" class="btn">Continue Shopping</a>
  </div>
</body>
</html>