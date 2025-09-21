<?php
session_start();

$product_id = $_GET['product_id'] ?? null;

if (!isset($_SESSION['customer_id'])) {
    $_SESSION['redirect_to_product'] = $product_id;
    header("Location: user_login.php");
    exit();
}

header("Location: product.php?id=$product_id");
exit();