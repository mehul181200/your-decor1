<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
  header("Location: /Website/user/user_login.php");
  exit();
}

$customer_id = intval($_SESSION['customer_id']);
if (!$customer_id) {
  http_response_code(400);
  echo "<h2>Invalid session. Please <a href='/Website/user/user_login.php'>login again</a>.</h2>";
  exit;
}

$conn = new mysqli('localhost', 'root', '', 'your_decor');
if ($conn->connect_error) {
  die('Connection error: ' . $conn->connect_error);
}

// ग्राहक द्वारा ऑर्डर किए गए उत्पादों को ड्रॉपडाउन के लिए प्राप्त करें
$ordered_products = [];
$sql_products = "SELECT DISTINCT p.Product_id, p.Name 
                 FROM products p
                 JOIN order_information oi ON p.Product_id = oi.Product_id
                 WHERE oi.Customer_id = ?";
$stmt_products = $conn->prepare($sql_products);
if ($stmt_products) {
    $stmt_products->bind_param('i', $customer_id);
    $stmt_products->execute();
    $result = $stmt_products->get_result();
    while ($row = $result->fetch_assoc()) {
        $ordered_products[] = $row;
    }
    $stmt_products->close();
}


$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product_id = intval($_POST['product_id']);
  $rating = intval($_POST['rating']);
  $review_text = trim($_POST['review_text']);
  $review_image = '';

  // इनपुट की जाँच करें
  if (empty($product_id) || empty($rating) || empty($review_text)) {
    $error = 'Please fill in all required fields (Product, Rating, and Review).';
  } else {
    // फ़ाइल अपलोड को हैंडल करें
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
      $target_dir = "uploads/";

      // ## यही महत्वपूर्ण हिस्सा है ##
      // यह जाँचता है कि 'uploads' फ़ोल्डर मौजूद है या नहीं।
      // यदि नहीं, तो यह उसे बना देता है।
      if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
      }
      
      // ओवरराइटिंग से बचने के लिए एक यूनिक फ़ाइल नाम बनाएँ
      $image_extension = pathinfo($_FILES["review_image"]["name"], PATHINFO_EXTENSION);
      $review_image = uniqid('review_', true) . '.' . $image_extension;
      $target_file = $target_dir . $review_image;
      
      // जाँचें कि फ़ाइल वास्तव में एक इमेज है
      $check = getimagesize($_FILES["review_image"]["tmp_name"]);
      if ($check !== false) {
        // फ़ाइल को 'uploads' फोल्डर में ले जाएँ
        if (!move_uploaded_file($_FILES["review_image"]["tmp_name"], $target_file)) {
          $error = 'Sorry, there was an error uploading your file.';
          $review_image = ''; 
        }
      } else {
        $error = 'File is not an image.';
        $review_image = '';
      }
    }

    if(empty($error)) {
        // डेटाबेस में समीक्षा डालें
        $sql = "INSERT INTO review (Customer_id, Product_id, Rating, Review_text, Review_image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
          $stmt->bind_param('iiiss', $customer_id, $product_id, $rating, $review_text, $review_image);
          if ($stmt->execute()) {
            $message = 'Thank you for your review!';
          } else {
            $error = 'Error submitting review: ' . $stmt->error;
          }
          $stmt->close();
        } else {
          $error = 'Database prepare error: ' . $conn->error;
        }
    }
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Your Review</title>
  <link rel="stylesheet" href="/Website/style/style.css?v=1.1">
  <style>
    body {
      background: #f7f8fa;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .main {
      max-width: 700px;
      margin: 40px auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 16px #0001;
      padding: 36px 32px;
    }
    h2 {
      font-size: 2.2rem;
      margin-bottom: 1em;
      color: #222;
      font-weight: 700;
      text-align: center;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      font-size: 1.1em;
      color: #333;
      margin-bottom: 8px;
      font-weight: 600;
    }
    .form-group input[type="text"],
    .form-group input[type="file"],
    .form-group textarea,
    .form-group select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1em;
      box-sizing: border-box;
    }
    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }
    .rating-group {
        display: flex;
        gap: 15px;
        align-items: center;
    }
     .rating-group label {
        margin-bottom: 0;
        font-weight: normal;
        display: flex;
        align-items: center;
        gap: 5px;
     }
    .btn {
      display: inline-block;
      padding: 12px 28px;
      border-radius: 6px;
      background: #2563eb;
      color: #fff !important;
      text-decoration: none;
      font-size: 1.1em;
      font-weight: 500;
      transition: background 0.18s;
      border: none;
      cursor: pointer;
      width: 100%;
    }
    .btn:hover {
      background: #1741a6;
    }
    .message, .error {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 6px;
      text-align: center;
      font-size: 1.1em;
    }
    .message {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    a {
      color: #2563eb;
      text-decoration: underline;
      transition: color 0.15s;
    }
    a:hover {
      color: #1741a6;
    }
  </style>
</head>
<body>
  <div class="main">
    <h2>Add Your Review</h2>

    <?php if ($message): ?>
      <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="customer_review.php" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="product_id">Select Product</label>
        <select id="product_id" name="product_id" required>
          <option value="">-- Choose a product --</option>
          <?php foreach ($ordered_products as $product): ?>
            <option value="<?php echo htmlspecialchars($product['Product_id']); ?>">
              <?php echo htmlspecialchars($product['Name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Rating</label>
        <div class="rating-group">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <label for="rating<?php echo $i; ?>">
              <input type="radio" id="rating<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required> <?php echo $i; ?> ★
            </label>
          <?php endfor; ?>
        </div>
      </div>

      <div class="form-group">
        <label for="review_text">Your Review</label>
        <textarea id="review_text" name="review_text" rows="5" required></textarea>
      </div>

      <div class="form-group">
        <label for="review_image">Upload Image (Optional)</label>
        <input type="file" id="review_image" name="review_image" accept="image/*">
      </div>

      <div class="form-group">
        <button type="submit" class="btn">Submit Review</button>
      </div>
    </form>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="order_history.php">← Back to Order History</a>
    </div>
  </div>
</body>
</html>