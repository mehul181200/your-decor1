<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    echo "<script>alert('Please login to continue.'); window.location.href='/Website/user/user_login.php';</script>";
    exit;
}

$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get grand total from form
$grand_total = $_POST['grand_total'] ?? 0;

// Fetch active cart items
$stmt = $conn->prepare("
    SELECT c.Cart_id, c.Product_id, c.Quantity, p.Name, p.Price, p.Product_image
    FROM cart c
    JOIN products p ON c.Product_id = p.Product_id
    WHERE c.Customer_id = ? AND c.Cart_status = 'active'
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - Your Decor</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, sans-serif; background:#f4f4f4; margin:0; }
    .checkout-container { max-width:900px; margin:40px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    h2 { text-align:center; color:#333; margin-bottom:20px; }
    .item { display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid #ddd; }
    .item img { height:70px; border-radius:6px; }
    .item-details { flex:1; margin-left:20px; }
    .item-details h3 { margin:0; font-size:16px; color:#222; }
    .item-details p { margin:4px 0; color:#555; font-size:14px; }
    .total { text-align:right; font-size:20px; font-weight:bold; margin-top:20px; color:#004d4d; }
    .confirm-btn { display:block; margin:30px auto 0; background:#28a745; color:#fff; border:none; padding:12px 25px; border-radius:6px; cursor:pointer; font-size:18px; transition:0.3s; }
    .confirm-btn:hover { background:#218838; }
    .back-cart { display:inline-block; margin-top:20px; padding:8px 15px; background:#000; color:#fff; border-radius:6px; text-decoration:none; }
    .back-cart:hover { background:#444; }
  </style>
</head>
<body>
  <div class="checkout-container">
    <h2>Checkout</h2>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="item">
          <img src="<?= htmlspecialchars($row['Product_image']) ?>" alt="<?= htmlspecialchars($row['Name']) ?>">
          <div class="item-details">
            <h3><?= htmlspecialchars($row['Name']) ?></h3>
            <p>Quantity: <?= $row['Quantity'] ?></p>
            <p>Price: ₹<?= number_format($row['Price'], 2) ?></p>
          </div>
          <div>
            <p><b>₹<?= number_format($row['Price'] * $row['Quantity'], 2) ?></b></p>
          </div>
        </div>
      <?php endwhile; ?>

      <div class="total">Grand Total: ₹<?= number_format($grand_total, 2) ?></div>

      <!-- Confirm Order -->
      <form method="post" action="purchase.php">
        <input type="hidden" name="grand_total" value="<?= $grand_total ?>">
        <button type="submit" class="confirm-btn">Confirm Order</button>
      </form>

    <?php else: ?>
      <p style="text-align:center; color:#777;">Your cart is empty. <a href="home.php">Back to Shop</a></p>
    <?php endif; ?>

    <a href="Cart.php" class="back-cart">← Back to Cart</a>
  </div>
</body>
</html>
<?php $conn->close(); ?>
