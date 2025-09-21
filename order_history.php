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

$conn = new mysqli('localhost','root','','your_decor');
if ($conn->connect_error) die('Connection error: '.$conn->connect_error);

// Fetch customer
$stmt = $conn->prepare("SELECT * FROM customer_information WHERE Customer_id = ? LIMIT 1");
if (!$stmt) {
  die('Prepare failed (customer): ' . $conn->error);
}
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$customer) die('Customer not found');

// Fetch all products ordered by customer (with product name)
$sql = "SELECT oi.order_id, p.Name AS Product_name, oi.Quantity, oi.Total_price, pi.Payment_status
        FROM order_information oi
        LEFT JOIN payment_information pi ON pi.order_id = oi.order_id
        LEFT JOIN products p ON p.Product_id = oi.Product_id
        WHERE oi.Customer_id = ?
        ORDER BY oi.order_id DESC";
$pstmt = $conn->prepare($sql);
if (!$pstmt) {
  die('Prepare failed (orders): ' . $conn->error);
}
$pstmt->bind_param('i', $customer_id);
$pstmt->execute();
$products = $pstmt->get_result();
$pstmt->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Order History</title>
  <link rel="stylesheet" href="/Website/style/style.css?v=1.1">
  <style>
    body {
      background: #f7f8fa;
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .main {
      max-width: 950px;
      margin: 40px auto;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 16px #0001;
      padding: 36px 32px 32px 32px;
    }
    h2 {
      font-size: 2.2rem;
      margin-bottom: 0.5em;
      color: #222;
      font-weight: 700;
      letter-spacing: 0.5px;
    }
    h3 {
      font-size: 1.3rem;
      margin-bottom: 1em;
      color: #444;
      font-weight: 600;
    }
    .card {
      background: #f9fafb;
      border-radius: 10px;
      box-shadow: 0 1px 4px #0001;
      padding: 24px 18px 18px 18px;
      margin-bottom: 24px;
    }
    .table-responsive {
      overflow-x: auto;
    }
    table.customers {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 0;
      background: #fff;
    }
    table.customers th, table.customers td {
      padding: 12px 10px;
      text-align: left;
      border-bottom: 1px solid #e3e6ea;
    }
    table.customers th {
      background: #f1f3f6;
      font-weight: 600;
      color: #333;
      font-size: 1rem;
      letter-spacing: 0.2px;
    }
    table.customers tr:last-child td {
      border-bottom: none;
    }
    table.customers td {
      font-size: 1rem;
      color: #222;
    }
    .badge {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 12px;
      font-size: 0.95em;
      font-weight: 500;
      color: #fff;
    }
    .badge-paid {
      background: #27ae60;
    }
    .badge-pending {
      background: #f39c12;
    }
    .badge-failed {
      background: #e74c3c;
    }
    .btn {
      display: inline-block;
      padding: 5px 16px;
      border-radius: 6px;
      background: #2563eb;
      color: #fff !important;
      text-decoration: none;
      font-size: 0.98em;
      font-weight: 500;
      transition: background 0.18s;
      border: none;
      cursor: pointer;
    }
    .btn.small {
      padding: 3px 10px;
      font-size: 0.93em;
    }
    .btn:hover {
      background: #1741a6;
    }
    a {
      color: #2563eb;
      text-decoration: underline;
      transition: color 0.15s;
    }
    a:hover {
      color: #1741a6;
    }
    @media (max-width: 700px) {
      .main {
        padding: 12px 2vw;
      }
      .card {
        padding: 10px 4px 8px 4px;
      }
      table.customers th, table.customers td {
        padding: 7px 4px;
        font-size: 0.97em;
      }
    }
  </style>
</head>
<body>
  <div class="main" style="max-width:900px;margin:40px auto;">
    <h2>My Order History</h2>
    <div class="card">
      <h3>Ordered Products</h3>
      <div class="table-responsive">
        <table class="customers">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Product Name</th>
              <th>Quantity</th>
              <th>Total Price</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($products->num_rows === 0) { ?>
              <tr><td colspan="5">No orders found.</td></tr>
            <?php } else { while ($row = $products->fetch_assoc()) { ?>
              <tr>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td><?php echo htmlspecialchars($row['Product_name']); ?></td>
                <td><?php echo htmlspecialchars($row['Quantity']); ?></td>
                <td>₹<?php echo number_format($row['Total_price'],2); ?></td>
                <td><a class="btn btn-secondary small" href="customer_order_detail.php?order_id=<?php echo urlencode($row['order_id']); ?>">View</a></td>
              </tr>
            <?php }} ?>
          </tbody>
        </table>
      </div>
    </div>

    <div style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
        <a href="customer_review.php" class="btn" style="padding: 10px 25px; font-size: 1.1em;">Add Your Review</a>
    </div>

    <div style="margin-top:20px;"><a href="/Website/home.php">← Back to Home</a> | <a href="/Website/user/logout.php">Logout</a></div>
  </div>

</body>
</html>