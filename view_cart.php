<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    echo "<script>alert('Please login to view your cart.'); window.location.href='/Website/user/user_login.php';</script>";
    exit;
}

$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch cart items with product details
$stmt = $conn->prepare("
    SELECT c.Cart_id, c.Product_id, c.Quantity, c.added_at, p.Name, p.Price, p.Product_image
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
  <title>Your Cart - Your Decore</title>
  <link rel="stylesheet" href="style/footer.css">
  <style>
    body { font-family: 'Segoe UI', Tahoma, sans-serif; background:#f4f4f4; margin:0; }
    .cart-container { max-width:1000px; margin:40px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
    h2 { text-align:center; color:#333; margin-bottom:20px; }
    .cart-item { display:flex; align-items:center; justify-content:space-between; padding:15px 0; border-bottom:1px solid #ddd; }
    .cart-item img { height:80px; width:auto; border-radius:6px; }
    .item-details { flex:1; margin-left:20px; }
    .item-details h3 { margin:0; font-size:18px; color:#222; }
    .item-details p { margin:4px 0; color:#555; }
    .item-actions { text-align:right; }
    .remove-btn { background:#dc3545; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
    .remove-btn:hover { background:#c82333; }
    .total-box { text-align:right; font-size:18px; font-weight:bold; margin-top:20px; color:#004d4d; }
    .back-home { display:inline-block; margin-top:30px; padding:10px 20px; background:#000; color:#fff; border-radius:6px; text-decoration:none; transition:0.3s; }
    .back-home:hover { background:#444; }
    .buy-btn {
  background: #28a745;
  color: #fff;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: 0.3s;
}
.buy-btn:hover {
  background: #218838;
}

  </style>
</head>
<body>
  <div class="cart-container">
    <h2>Your Shopping Cart</h2>
    <?php
    $grand_total = 0;
    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
        $total_price = $row['Price'] * $row['Quantity'];
        $grand_total += $total_price;
    ?>
    <div class="cart-item">
      <img src="<?= htmlspecialchars($row['Product_image']) ?>" alt="<?= htmlspecialchars($row['Name']) ?>">
      <div class="item-details">
        <h3><?= htmlspecialchars($row['Name']) ?></h3>
        <p>Quantity: <?= $row['Quantity'] ?></p>
        <p>Price: ₹<?= number_format($row['Price'], 2) ?></p>
        <p>Total: ₹<?= number_format($total_price, 2) ?></p>
        <p>Added: <?= date("d M Y, h:i A", strtotime($row['added_at'])) ?></p>
      </div>
      <div class="item-actions">
        <form method="post" action="remove_from_cart.php">
          <input type="hidden" name="cart_id" value="<?= $row['Cart_id'] ?>">
          <button type="submit" class="remove-btn">Remove</button>
        </form>
      </div>
    </div>

    <?php endwhile; ?>
        <div class="total-box">Grand Total: ₹<?= number_format($grand_total, 2) ?></div>

    <!-- Buy Now Button -->
    <?php if ($grand_total > 0): ?>
      <form method="post" action="checkout.php" style="text-align:right; margin-top:20px;">
        <input type="hidden" name="grand_total" value="<?= $grand_total ?>">
        <button type="submit" class="buy-btn">Buy Now</button>
      </form>
    <?php endif; ?>

    
    <?php else: ?>
      <p style="text-align:center; color:#777;">🛒 Your cart is empty.</p>
    <?php endif; ?>
    <a href="home.php" class="back-home">← Back to Home</a>
  </div>
<?php $conn->close(); ?>
<?php include 'includes/footer.php'; ?>
</body>
</html>