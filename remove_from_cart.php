<?php
session_start();
$conn = new mysqli("localhost", "root", "", "your_decor");

$cart_id = intval($_POST['cart_id'] ?? 0);
if ($cart_id > 0) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE Cart_id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
}
$conn->close();
header("Location: view_cart.php");
exit;
?>_