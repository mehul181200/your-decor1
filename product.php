<?php
session_start();
$customer_id = $_SESSION['Customer_id'] ?? null;
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "your_decor");
$customer_id = $_SESSION['customer_id'] ?? null;
$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$product_id = intval($_POST['Product_id'] ?? 0);

// ✅ Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$customer_id) {
        echo "<script>alert('Please login to add items to cart.'); window.location.href='/Website/user/user_login.php?id=$product_id&redirected=true';</script>";
        exit;
    }

    $quantity = intval($_POST['quantity'] ?? 1);
    $price = floatval($_POST['price'] ?? 0);
    $total_cost = $quantity * $price;
    $cart_status = 'active';
    echo "<script>alert('customer_id: $customer_id, product_id: $product_id, quantity: $quantity, status: $cart_status');</script>";
    //"customer_id: $customer_id, product_id: $product_id, quantity: $quantity, status: $cart_status";
$stmt = $conn->prepare("INSERT INTO cart (Customer_id, Product_id, Quantity, Cart_status) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $customer_id, $product_id, $quantity, $cart_status);
if ($stmt->execute()) {
    header("Location: cart.php");
    exit;
} else {
    echo "<script>alert('❌ Failed to add product to cart');</script>";
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($product['Name'] ?? 'Product') ?> - Your Decore</title>
  <link rel="stylesheet" href="style/footer.css" />
  <link rel="icon" type="image/png" href="icon.png" />
  <style>
  body { margin:0; font-family: 'Segoe UI', Tahoma, sans-serif; background:#f8f9fa; }

    header {
      background:#004d4d; color:#fff; padding:10px 20px;
      display:flex; align-items:center; justify-content:space-between;
    }
    .logo { display:flex; align-items:center; gap:10px; }
    .logo img { height:40px; }

    /* Navbar Buttons */
    .nav-actions { display:flex; gap:10px; }
    .nav-actions form { margin:0; }
    .nav-actions button {
      padding:8px 10px; border:none; border-radius:6px;
      font-size:14px; cursor:pointer; transition:0.3s;
    }
    .nav-add-cart { background:#ff9800; color:white; }
    .nav-add-cart:hover { background:#e68900; }
    .nav-buy-now { background:#28a745; color:white; }
    .nav-buy-now:hover { background:#1e7e34; }

    /* Product container */
    .product-detail {
      max-width:1200px; margin:30px auto; display:grid;
      grid-template-columns: 1fr 1.2fr; gap:10px; background:#fff;
      padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);
    }

    /* Left: Image Section */
    .image-gallery {
      display:flex; flex-direction:column; gap:10px;
    }
    .main-image {
      background:#f5f5f5; border-radius:10px;
      display:flex; align-items:center; justify-content:center;
      padding:5px;
    }
    .main-image img { max-width:50%; max-height:400px; object-fit:contain; }

    /* Right: Info Section */
    .product-info h2 { font-size:20px; margin:10px 0; color:#222; }
    .price { font-size:20px; font-weight:bold; color:#008000; margin:10px 10px; }
    .desc { color:#444; margin-bottom:10px; }

    .details p { margin:4px 0; font-size:1px; }
    .details strong { color:#222; }

    /* Wall size + quantity */
    .calc-box {
      background:#f5f5f5; padding:10px; border-radius:8px; margin:15px 0;
    }
    .calc-box label { display:block; font-weight:600; margin:6px 0; }
    .calc-box input {
      width:80%; padding:6px; margin-bottom:10px;
      border:1px solid #ccc; border-radius:6px; font-size:12px;
    }
    .calc-box button {
       height: 50%;width:50%; padding:10px; border:none; border-radius:6px;
      background:#007bff; color:white; font-size:12px; cursor:pointer;
    }
    .calc-box button:hover { background:#0056b3; }

    #calcResult { margin-top:12px; font-weight:bold; color:#004d4d; }

    /* Action Buttons (Inside Product Detail) */
    .actions { display:flex; gap:15px; margin-top:20px; }
    .actions button {
      flex:2; padding:30px 30px; font-size:16px; border:none;
      border-radius:5px; cursor:pointer; transition:0.3s;
    }
    .add-cart { background:#ff9800; color:white; padding:20px 25px; border-radius:10px; font-size:16px; align:center; }
    .add-cart:hover { background:#e68900; }
    .buy-now { background:#28a745; color:white; }
    .buy-now:hover { background:#1e7e34;  }

    .back-home {
  display: inline-block;
  
  margin: 10px 600px;
  padding: 10px 25px;
  background: #000000ff;
  color: #fff;
  font-size: 16px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 6px;
  transition: background 0.3s, transform 0.2s;
  text-align: center;
}

.back-home:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

.back-home:active {
  background: #444;
  transform: translateY(0);
}

 

  </style>
</head>
<body>
<header>
  <div class="logo">
    <img src="icon.png" alt="Your Decore Logo" />
    <h1>Your Decore</h1>
  </div>

  <!-- Navbar Actions -->
  <div class="nav-actions">
    <form method="post" action="cart.php">
      <input type="hidden" name="Product_id" value="<?= $product['Product_id'] ?>">
      <input type="hidden" name="price" value="<?= $product['Price'] ?>">
      <input type="hidden" name="quantity" value="1">
      <input type="hidden" name="total_cost" value="<?= $product['Price'] ?>">
      <button type="submit" class="nav-add-cart" name="add_to_cart">Add to Cart</button>
    </form>
    
  </div>
</header>

<main>
  <div class="product-detail">
    <!-- Left Side: Images -->
    <div class="image-gallery">
      <div class="main-image">
        <img src="<?= htmlspecialchars($product['Product_image']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" />
      </div>
    </div>

    <!-- Right Side: Info -->
    <div class="product-info">
      <h2><?= htmlspecialchars($product['Name']) ?></h2>
      <p class="price">₹<?= htmlspecialchars($product['Price']) ?> per panel</p>
      <p class="desc"><?= htmlspecialchars($product['Description']) ?></p>

      <div class="details">
        <p><strong>Material:</strong> <?= htmlspecialchars($product['Material']) ?></p>
        <p><strong>Dimension:</strong> <?= htmlspecialchars($product['Dimension']) ?></p>
        <p><strong>Stock:</strong> <?= htmlspecialchars($product['Stock_quantity']) ?> available</p>
      </div>

      <!-- Calculation Box -->
      <div class="calc-box">
        <label>Wall Length (ft):</label>
        <input type="number" id="length" step="0.1" placeholder="Enter wall length">
        <label>Wall Height (ft):</label>
        <input type="number" id="height" step="0.1" placeholder="Enter wall height">
        <button onclick="calculatePanels()">Calculate Panels</button>
        <div id="calcResult"></div>
      </div>

      <!-- Quantity -->
      <form method="post" action="Cart.php">
        <div>
          <label><strong>Quantity (Panels Required):</strong></label>
          <input type="number" id="qty" value="1" min="1" max="<?= htmlspecialchars($product['Stock_quantity']) ?>" readonly>
        </div>
        <input type="hidden" name="Product_id" value="<?= $product['Product_id'] ?>">
        <input type="hidden" name="price" id="hiddenPrice" value="<?= $product['Price'] ?>">
        <input type="hidden" name="quantity" id="hiddenQty" value="1">
        <input type="hidden" name="total_cost" id="hiddenTotal" value="<?= $product['Price'] ?>">
        <button type="submit" class="add-cart" name="add_to_cart">Add to Cart</button>
      </form>
    </div>
  </div>
  <a href="home.php" class="back-home">Back to Home</a>
  
</main>

<script>
function calculatePanels() {
  const length = parseFloat(document.getElementById('length').value);
  const height = parseFloat(document.getElementById('height').value);
  const price = parseFloat(<?= json_encode($product['Price']) ?>);
  const stock = parseInt(<?= json_encode($product['Stock_quantity']) ?>);

  if(isNaN(length) || isNaN(height) || length <= 0 || height <= 0){
    alert("Please enter valid wall size.");
    return;
  }

  const wallArea = length * height;
  const panelSize = 32; // 8ft x 4ft
  const panels = Math.ceil(wallArea / panelSize);

  const finalPanels = panels > stock ? stock : panels;
  document.getElementById('qty').value = finalPanels;

  const totalCost = finalPanels * price;
  document.getElementById('calcResult').innerHTML = `
    Wall Area: ${wallArea.toFixed(2)} sq.ft <br>
    Panels Required: ${finalPanels} <br>
    Total Cost: ₹${totalCost.toFixed(2)}
  `;

  document.getElementById('hiddenQty').value = finalPanels;
  document.getElementById('hiddenTotal').value = totalCost.toFixed(2);
}
</script>

<?php include 'includes/footer.php'; ?>
<?php $conn->close(); ?>

</body>
</html>
