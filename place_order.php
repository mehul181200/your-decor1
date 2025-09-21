<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    echo "<script>alert('Please login to continue.'); window.location.href='/Website/user/user_login.php';</script>";
    exit;
}

$step = $_GET['step'] ?? 1;

// Fetch customer info
$customer_stmt = $conn->prepare("SELECT * FROM customer_information WHERE Customer_id = ?");
$customer_stmt->bind_param("i", $customer_id);
$customer_stmt->execute();
$customer = $customer_stmt->get_result()->fetch_assoc();

// Fetch cart items
$cart_stmt = $conn->prepare("
    SELECT c.Product_id, c.Quantity, p.Name, p.Price, p.Product_image
    FROM cart c
    JOIN products p ON c.Product_id = p.Product_id
    WHERE c.Customer_id = ? AND c.Cart_status = 'active'
");
$cart_stmt->bind_param("i", $customer_id);
$cart_stmt->execute();
$cart_items = $cart_stmt->get_result();

$grand_total = 0;
$products = [];
while ($item = $cart_items->fetch_assoc()) {
    $item_total = $item['Price'] * $item['Quantity'];
    $grand_total += $item_total;
    $products[] = $item;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Purchase - Step <?= $step ?></title>
    <style>
        body { font-family: Arial; background:#f9f9f9; padding:20px; }
        .container { max-width:800px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; }
        .btn { padding:10px 20px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        .btn:hover { background:#218838; }
        .form-group { margin-bottom:15px; }
        label { display:block; margin-bottom:5px; }
        input, textarea, select { width:100%; padding:8px; border:1px solid #ccc; border-radius:4px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Step <?= $step ?> of 3</h2>

    <?php if ($step == 1): ?>
        <h3>Customer & Product Details</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($customer['Name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($customer['Email']) ?></p>
        <hr>
        <?php foreach ($products as $p): ?>
            <div>
                <p><strong><?= htmlspecialchars($p['Name']) ?></strong> (Qty: <?= $p['Quantity'] ?>) - ₹<?= number_format($p['Price'], 2) ?></p>
            </div>
        <?php endforeach; ?>
        <p><strong>Grand Total:</strong> ₹<?= number_format($grand_total, 2) ?></p>
        <form method="get">
            <input type="hidden" name="step" value="2">
            <button class="btn">Next: Address</button>
        </form>

    <?php elseif ($step == 2): ?>
        <h3>Delivery Address</h3>
        <form method="post" action="purchase.php?step=3">
            <div class="form-group">
                <label>Delivery Address</label>
                <textarea name="delivery_address" required></textarea>
            </div>
            <input type="hidden" name="grand_total" value="<?= $grand_total ?>">
            <button class="btn">Next: Payment</button>
        </form>

    <?php elseif ($step == 3 && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <?php $_SESSION['delivery_address'] = $_POST['delivery_address']; ?>
        <h3>Payment Method</h3>
        <form method="post" action="finalize_order.php">
            <div class="form-group">
                <label>Choose Payment Method</label>
                <select name="payment_method" required>
                    <option value="COD">Cash on Delivery</option>
                    <option value="Online">Online Payment</option>
                </select>
            </div>
            <input type="hidden" name="grand_total" value="<?= $_POST['grand_total'] ?>">
            <button class="btn">Place Order</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>