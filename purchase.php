<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login
$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    echo "<script>alert('Please login to continue.'); window.location.href='/Website/user/user_login.php';</script>";
    exit;
}

// Fetch customer info
$customerQuery = $conn->prepare("SELECT Full_Name, Email FROM customer_information WHERE customer_id=?");
$customerQuery->bind_param("i", $customer_id);
$customerQuery->execute();
$customer = $customerQuery->get_result()->fetch_assoc();

// Fetch cart items
$cart_items_result = $conn->query("
    SELECT p.product_id, p.name AS Name, c.quantity AS Quantity, p.price AS Price 
    FROM cart c 
    JOIN products p ON c.product_id=p.product_id 
    WHERE c.customer_id=$customer_id AND c.Cart_status='active'
");

// Calculate total price
$total_mrp = 0;
$cart_items = [];
if ($cart_items_result) {
    while ($item = $cart_items_result->fetch_assoc()) {
        $item['total_price'] = $item['Quantity'] * $item['Price'];
        $total_mrp += $item['total_price'];
        $cart_items[] = $item;
    }
}
$total_amount = $total_mrp;

// Handle form submission
if (isset($_POST['confirm_order'])) {
    $product_ids = $_POST['product_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $delivery_address = $_POST['building'] . ", " . $_POST['village'] . ", " .
                        $_POST['city'] . ", " . $_POST['state'] . " - " . $_POST['pincode'];
    $payment_method = $_POST['payment_method'] ?? 'COD';

    // Set payment status based on method
    if (in_array($payment_method, ['COD', 'EMI'])) {
        $payment_status = 'pending';
    } else {
        $payment_status = 'paid';
    }

    if (count($product_ids) !== count($quantities)) {
        die("Mismatch in product and quantity data.");
    }

    for ($i = 0; $i < count($product_ids); $i++) {
        $product_id = $product_ids[$i];
        $quantity = $quantities[$i];

        // Fetch price
        $productQuery = $conn->prepare("SELECT price FROM products WHERE product_id=?");
        $productQuery->bind_param("i", $product_id);
        $productQuery->execute();
        $productResult = $productQuery->get_result();
        if ($productResult->num_rows == 0) continue;
        $productData = $productResult->fetch_assoc();
        $price_per_unit = $productData['price'];
        $total_price = $price_per_unit * $quantity;

        // Insert into order_information
        $orderQuery = $conn->prepare("
            INSERT INTO order_information 
            (Customer_id, Product_id, quantity, price_per_unit, Total_price, Delivery_address, Payment_method, Payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $orderQuery->bind_param(
            "iiidisss",
            $customer_id,
            $product_id,
            $quantity,
            $price_per_unit,
            $total_price,
            $delivery_address,
            $payment_method,
            $payment_status
        );
        $orderQuery->execute();
        $order_id = $orderQuery->insert_id;

        // Insert into payment_information
        $transaction_id = uniqid("tx_");
        $paid_at = date("Y-m-d H:i:s");

        $paymentQuery = $conn->prepare("
            INSERT INTO payment_information 
            (order_id, Customer_id, Payment_method, Payment_status, Transaction_id, Amount, Paid_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $paymentQuery->bind_param(
            "iisssds",
            $order_id,
            $customer_id,
            $payment_method,
            $payment_status,
            $transaction_id,
            $total_price,
            $paid_at
        );
        $paymentQuery->execute();
    }

    // Mark cart items as inactive
    $cleanup = $conn->prepare("UPDATE cart SET Cart_status='inactive' WHERE Customer_id=?");
    $cleanup->bind_param("i", $customer_id);
    $cleanup->execute();

    echo "<script>alert('Order placed successfully!'); window.location.href='/Website/thank_you.php';</script>";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Purchase - Your Decor</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f9f9f9; margin:0; padding:0; }
    .container { max-width:1100px; margin:40px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
    .stepper { display:flex; justify-content:space-between; margin-bottom:30px; }
    .step { flex:1; text-align:center; padding:10px; border-bottom:3px solid #ccc; font-weight:bold; color:#777; cursor:pointer; }
    .step.active { border-color:#28a745; color:#28a745; }
    .section { display:none; }
    .section.active { display:block; }
    input, select, textarea { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px; }
    .nav-buttons { display: flex; justify-content: space-between; margin-top: 20px; }
    .nav-buttons button { width: 48%; }
    button { padding:10px 20px; background:#28a745; color:#fff; border:none; border-radius:5px; cursor:pointer; }
    button:hover { background:#218838; }
    .container-payment { display: flex; max-width: 1100px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px #ddd; }
    .sidebar { width: 220px; border-right: 1px solid #eee; padding: 30px 0 30px 30px; }
    .sidebar h4 { margin-bottom: 18px; font-size: 16px; color: #333; }
    .sidebar ul { list-style: none; padding: 0; margin: 0; }
    .sidebar li { margin-bottom: 12px; }
    .sidebar button.paymode-btn { width: 100%; background: none; border: none; text-align: left; padding: 10px 12px; font-size: 15px; color: #444; border-radius: 4px; cursor: pointer; transition: background 0.2s; }
    .sidebar button.active, .sidebar button:hover { background: #ffe6ef; color: #070707ff; font-weight: bold; }
    .main-payment { flex: 1; padding: 40px 40px 40px 40px; }
    .main-payment h3 { margin-top: 0; color: #000000ff; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; margin-bottom: 6px; color: #555; }
    .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 15px; }
    .submit-btn { background: #000000ff; color: #fff; border: none; padding: 12px 0; width: 100%; border-radius: 4px; font-size: 18px; cursor: pointer; margin-top: 10px; }
    .submit-btn:hover { background: #000000ff; }
    .price-details { width: 270px; background: #fafafa; border-left: 1px solid #eee; padding: 30px 24px; }
    .price-details h4 { margin-top: 0; color: #333; font-size: 16px; }
    .price-row { display: flex; justify-content: space-between; margin-bottom: 10px; color: #444; }
    .price-row.total { font-weight: bold; font-size: 18px; color: #222; }
    .coupon { color: #010101ff; cursor: pointer; font-size: 13px; }
    .bank-offer { background: #f5faff; color: #1976d2; padding: 10px 16px; border-radius: 4px; margin-bottom: 18px; font-size: 14px; }
    @media (max-width: 900px) {
        .container-payment { flex-direction: column; }
        .sidebar, .price-details { width: 100%; border: none; }
        .main-payment { padding: 24px; }
    }
  </style>
  <script src="https://pay.google.com/gp/v1/js/pay.js"></script>
</head>
<body>
  <div class="container">
    <div class="stepper">
      <div class="step active" id="step1-tab" onclick="goToStep(1)">1. Customer & Product</div>
      <div class="step" id="step2-tab" onclick="goToStep(2)">2. Address</div>
      <div class="step" id="step3-tab" onclick="goToStep(3)">3. Payment</div>
    </div>

    <form method="post" id="payment-form">
      <div class="section active" id="step1">
        <h3>Customer Information</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($customer['Full_Name'] ?? '') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($customer['Email'] ?? '') ?></p>

        <h3>Cart Items</h3>
        <?php foreach ($cart_items as $item): ?>
          <p><?= $item['Name'] ?> - Qty: <?= $item['Quantity'] ?> - ₹<?= number_format($item['Price'], 2) ?></p>
          <input type="hidden" name="product_id[]" value="<?= $item['product_id'] ?>">
          <input type="hidden" name="quantity[]" value="<?= $item['Quantity'] ?>">
        <?php endforeach; ?>
        <div class="nav-buttons">
            <button type="button" onclick="goToStep(2)">Next</button>
        </div>
      </div>

      <div class="section" id="step2">
        <h3>Delivery Address</h3>
        <input type="text" name="building" placeholder="Building" required>
        <input type="text" name="village" placeholder="Village" required>
        <input type="text" name="city" placeholder="City" required>
        <input type="text" name="state" placeholder="State" required>
        <input type="text" name="pincode" placeholder="Pincode" required>
        <div class="nav-buttons">
            <button type="button" onclick="goToStep(1)">Previous</button>
            <button type="button" onclick="goToStep(3)">Next</button>
        </div>
      </div>

      <div class="section" id="step3">
        <div class="container-payment">
          <div class="sidebar">
            <h4>Choose Payment Mode</h4>
            <ul>
              <li><button type="button" class="paymode-btn active" data-mode="cod">Cash On Delivery</button></li>
              <li><button type="button" class="paymode-btn" data-mode="card">Credit/Debit Card</button></li>
              <li><button type="button" class="paymode-btn" data-mode="upi">GPay/PhonePay/Upi</button></li>
              <li><button type="button" class="paymode-btn" data-mode="wallet">Paytm/Wallets</button></li>
              <li><button type="button" class="paymode-btn" data-mode="netbanking">Net Banking</button></li>
              <li><button type="button" class="paymode-btn" data-mode="emi">EMI/Pay Later</button></li>
            </ul>
          </div>
          <div class="main-payment">
            <div class="bank-offer">10% Instant Discount on IDFC FIRST Bank Cards on min spend of Rs 2,500. TCA</div>
            <div id="form-content">
              <h3>CASH ON DELIVERY</h3>
              <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" maxlength="10" placeholder="Enter your mobile" required>
              </div>
              <input type="hidden" name="payment_method" id="hidden_payment_method" value="COD">
              <button class="submit-btn" type="submit" name="confirm_order">Place Order</button>
            </div>
          </div>
          <div class="price-details">
            <h4>PRICE DETAILS (Items)</h4>
            <div class="price-row total"><span>Total Amount</span><span>₹<?= number_format($total_amount, 2) ?></span></div>
          </div>
        </div>
        <div class="nav-buttons">
            <button type="button" onclick="goToStep(2)">Previous</button>
        </div>
      </div>
    </form>
  </div>

  <script>
    function goToStep(step) {
      document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
      document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
      document.getElementById('step' + step).classList.add('active');
      document.getElementById('step' + step + '-tab').classList.add('active');
    }

    const modes = {
      card: {
        html: `<h3>CREDIT/DEBIT CARD</h3>
          <div class='form-group'><label>Card Number</label><input type='text' maxlength='16' placeholder='2557 7666 7889 54' required></div>
          <div class='form-group'><label>Name on Card</label><input type='text' placeholder='Umer Ahmad' required></div>
          <div style='display: flex; gap: 10px;'><div class='form-group' style='flex:1;'><label>Expiry</label><input type='text' maxlength='5' placeholder='12/23' required></div><div class='form-group' style='flex:1;'><label>CVV</label><input type='text' maxlength='4' placeholder='131' required></div></div>
          <input type="hidden" name="payment_method" id="hidden_payment_method" value="Credit Card">
          <button class='submit-btn' type='submit' name='confirm_order'>Submit</button>`,
        method: 'Credit Card'
      },
      cod: {
        html: `<h3>CASH ON DELIVERY</h3>
          <div class='form-group'><label>Mobile Number</label><input type='text' maxlength='10' placeholder='Enter your mobile' required></div>
          <input type="hidden" name="payment_method" id="hidden_payment_method" value="COD">
          <button class='submit-btn' type='submit' name='confirm_order'>Place Order</button>`,
        method: 'COD'
      },
      upi: {
        html: `<h3>GPay/PhonePay/UPI</h3>
          <div class='form-group'><label>Enter UPI ID</label><input type='text' placeholder='example@upi' required></div>
          <input type="hidden" name="payment_method" id="hidden_payment_method" value="GPay">
          <button class='submit-btn' type='submit' name='confirm_order'>Pay with UPI</button>`,
        method: 'GPay'
      },
      wallet: {
        html: `<h3>Paytm/Wallets</h3>
          <div class='form-group'><label>Mobile Number</label><input type='text' maxlength='10' placeholder='Enter your mobile' required></div>
          <input type="hidden" name="payment_method" id="hidden_payment_method" value="Wallet">
          <button class='submit-btn' type='submit' name='confirm_order'>Pay with Wallet</button>`,
        method: 'Wallet'
      },
      netbanking: {
        html: `<h3>Net Banking</h3>
          <div class='form-group'><label>Select Bank</label><input type='text' placeholder='Bank Name' required></div>
          <input type="hidden" name="payment_method" id="hidden_payment_method" value="Net Banking">
          <button class='submit-btn' type='submit' name='confirm_order'>Pay with Net Banking</button>`,
        method: 'Net Banking'
      },
      emi: {
        html: `<h3>EMI/Pay Later</h3>
          <div class='form-group'><label>Card Number</label><input type='text' maxlength='16' placeholder='Card Number' required></div>
          <input type="hidden" name="payment_method" id="hidden_payment_method" value="EMI">
          <button class='submit-btn' type='submit' name='confirm_order'>Pay with EMI</button>`,
        method: 'EMI'
      }
    };

    document.querySelectorAll('.paymode-btn').forEach(btn => {
      btn.onclick = function() {
        document.querySelectorAll('.paymode-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const mode = this.getAttribute('data-mode');
        document.getElementById('form-content').innerHTML = modes[mode].html;
      }
    });
  </script>
</body>
</html>