<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customer_id = $_SESSION['customer_id'];
$delivery_address = $_SESSION['delivery_address'];
$payment_method = $_POST['payment_method'];
$payment_status = ($payment_method === 'COD') ? 'Pending' : 'Paid';
$grand_total = $_POST['grand_total'];

// Fetch cart items
$sql = "
    SELECT c.Product_id, c.Quantity, p.price
    FROM cart c
    JOIN products p ON c.Product_id = p.Product_id
    WHERE c.Customer_id = ? AND c.Cart_status = 'active'
";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error); // <-- debug help
}

$stmt->bind_param("i", $customer_id);
$stmt->execute();
$items = $stmt->get_result();

while ($row = $items->fetch_assoc()) {
    $total_price = $row['price'] * $row['Quantity'];

    $insert = $conn->prepare("
        INSERT INTO order_information 
        (Customer_id, Product_id, Quantity, price_per_unit, Total_price, Delivery_address, Payment_method, Payment_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$insert) {
        die("Insert prepare failed: " . $conn->error);
    }

    $insert->bind_param(
        "iiidisss", 
        $customer_id, 
        $row['Product_id'], 
        $row['Quantity'], 
        $row['price'], 
        $total_price, 
        $delivery_address, 
        $payment_method, 
        $payment_status
    );
    $insert->execute();
}

// Mark cart items as purchased
$conn->query("UPDATE cart SET Cart_status = 'purchased' WHERE Customer_id = $customer_id");

echo "<script>alert('Order placed successfully!'); window.location.href='home.php';</script>";
?>
