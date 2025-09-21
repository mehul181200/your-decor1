<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM products");
if (!$result) {
    die("SQL Error: " . $conn->error);
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$chunks = array_chunk($products, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Products - Your Decor</title>
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/footer.css" />
  <link rel="icon" type="image/png" href="icon.png" />

  <style>
    body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
 
  background-size: 400% 400%;
  animation: gradientBG 18s ease infinite;
  color: #fff; /* text ko readable banane ke liye */
}



    main {
      max-width: 1200px;
      margin: 30px auto;
      padding: 0 15px;
    }

    main h2 {

      font-size: 40px;
      font-weight: 900;
      padding: 20px 40px;
      text-align: center;
      margin: 30px auto;
      color: #222;
      position: relative;
      display: inline-block;
      padding-bottom: 5px;
    }

   

    .carousel-group {
      margin-bottom: 20px;
    
    }
.carousel-wrapper {
  display: flex;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  gap: 100px;
  padding: 20px 30px;
  scroll-behavior: smooth;

  /* Hide scrollbar across browsers */
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE 10+ */
}

.carousel-wrapper::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Edge */
}

    .product {
      width: 300px;
      background: #fff;
      border-radius: 8px;
      padding: 10px 80px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      flex: 0 0 auto;
      scroll-snap-align: center;
      transition: transform 0.3s ease;
    }

    

    .product img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 6px;
      padding: 20px 30px ;
    }

    .product h3 {
      font-size: 18px;
      margin: 10px 0 5px;
      color: #333;
    }

    .product p {
      font-size: 14px;
      color: #666;
      margin: 5px 0;
    }
  </style>
</head>
<body>

<?php $currentPage = 'home'; ?>
<?php include 'includes/navbar.php'; ?>

<main>
  <h2>Our Products</h2>

  <?php foreach ($chunks as $group): ?>
    <div class="carousel-group">
      <div class="carousel-wrapper">
        <?php foreach ($group as $row): ?>
          <?php
            $id = $row['Product_id'] ?? 0;
            $image = !empty($row['Product_image']) ? $row['Product_image'] : 'images/default.jpg';
            $name = !empty($row['Name']) ? $row['Name'] : 'Unnamed Product';
            $price = !empty($row['Price']) ? $row['Price'] : 'N/A';
            $description = !empty($row['Description']) ? $row['Description'] : '';
          ?>
          <div class="product">
         <?php

$isLoggedIn = isset($_SESSION['customer_id']);
$productLink = $isLoggedIn
    ? "product.php?id=" . htmlspecialchars($id)
    : "user/user_login.php?id=" . htmlspecialchars($id);
?>
<a href="<?= $productLink ?>" style="text-decoration:none; color:inherit;">
              <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>" />
              <h3><?= htmlspecialchars($name) ?></h3>
              <p><strong>₹<?= htmlspecialchars($price) ?></strong></p>
              <p><?= htmlspecialchars($description) ?></p>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

</main>

<?php include 'includes/footer.php'; ?>
<?php $conn->close(); ?>
</body>
</html>