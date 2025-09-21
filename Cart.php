<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get customer ID from session
$customer_id = $_SESSION['customer_id'] ?? null;

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$customer_id) {
        echo "<script>alert('Please login to add items to cart.'); window.location.href='/Website/user/user_login.php';</script>";
        exit;
    }

    // Get product data from POST
    $product_id = intval($_POST['Product_id'] ?? 0);
 $quantity = intval($_POST['quantity'] ?? 1);
    $cart_status = 'active';

    // Defensive check
    if ($product_id <= 0 || $quantity <= 0) {
        echo "<script>alert('Invalid product or quantity.'); window.history.back();</script>";
        exit;
    }

    // ✅ Check stock AFTER quantity is defined
    $stockCheck = $conn->prepare("SELECT Stock_quantity FROM products WHERE Product_id = ?");
    $stockCheck->bind_param("i", $product_id);
    $stockCheck->execute();
    $stockResult = $stockCheck->get_result();
    $stockRow = $stockResult->fetch_assoc();

    if (!$stockRow || $quantity > $stockRow['Stock_quantity']) {
        echo "<script>alert('Requested quantity exceeds available stock.'); window.history.back();</script>";
        exit;
    }

    // Insert into cart table
    $stmt = $conn->prepare("INSERT INTO cart (Customer_id, Product_id, Quantity, Cart_status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $customer_id, $product_id, $quantity, $cart_status);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Product added to cart successfully!'); window.location.href='view_cart.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to add product to cart.'); window.history.back();</script>";
    }

    $stmt->close();
}

$conn->close();
?>