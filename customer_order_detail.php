<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: /Website/user/user_login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}
$order_id = intval($_GET['order_id']);
$customer_id = intval($_SESSION['customer_id']);

$conn = new mysqli('localhost', 'root', '', 'your_decor');
if ($conn->connect_error) die('Connection error: ' . $conn->connect_error);

// Check if this order belongs to the logged-in customer
$check = $conn->prepare("SELECT 1 FROM order_information WHERE order_id = ? AND Customer_id = ? LIMIT 1");
$check->bind_param('ii', $order_id, $customer_id);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) {
    die('You are not authorized to view this order.');
}
$check->close();

// Fetch order details
$sql = "SELECT oi.*, p.Name AS product_name, p.product_image
        FROM order_information oi
        LEFT JOIN products p ON p.Product_id = oi.Product_id
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="/Website/style/style.css?v=1.4">
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { color: #007bff; margin-top: 0; }
        .product-card { border-bottom: 1px solid #ddd; padding: 15px 0; }
        .product-card:last-child { border-bottom: none; }
        .product-meta { font-size: 14px; color: #555; margin-bottom: 10px; }
        .product-thumb { width: 80px; height: auto; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Details</h2>
        <?php if ($orders->num_rows === 0): ?>
            <p style="color:#666;">No products found for this order.</p>
        <?php else: ?>
            <?php while ($row = $orders->fetch_assoc()):
                $img = $row['product_image'] ?: '/images/default.png';
                $qty = $row['Quantity'] ?? 1;
                $unit = $row['price_per_unit'] ?? 0;
                $line = $row['Total_price'] ?? ($unit * $qty);
            ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($img); ?>" class="product-thumb" alt="Product Image">
                <div class="product-meta">
                    <strong>Product:</strong> <?php echo htmlspecialchars($row['product_name']); ?><br>
                    <strong>Qty:</strong> <?php echo $qty; ?> |
                    <strong>Unit Price:</strong> ₹<?php echo number_format($unit, 2); ?> |
                    <strong>Total:</strong> ₹<?php echo number_format($line, 2); ?><br>
                    <strong>Payment:</strong> <?php echo htmlspecialchars($row['Payment_method']); ?> |
                    <strong>Status:</strong> <?php echo htmlspecialchars($row['Payment_status']); ?><br>
                    <strong>Delivery Address:</strong> <?php echo htmlspecialchars($row['Delivery_address']); ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <div style="margin-top:20px;"><a href="order_history.php">← Back to Order History</a></div>
    </div>
</body>
</html>
