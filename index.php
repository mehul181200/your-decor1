<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Decore</title>
    <link rel="stylesheet" href="style/style.css">
      <link rel="stylesheet" href="style/footer.css">
    <link rel="icon" type="image/png" href="icon.png">

    <style>
       
       body {
            margin: 0;
            padding: 0;
            background-image: url('images/office1.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

    
        

        @media (max-width: 768px) {
            nav {
                display: none;
                flex-direction: column;
                background-color: white;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
            }

            .menu-toggle {
                display: block;
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>


<?php $currentPage = 'index'; ?>
<?php include 'includes/navbar.php'; ?>
<!-- Your index page content here -->

<main>
    <div class="decor-section">
       
        <h2>Decorate your home and office with Your Decore</h2>
        <h2>Imagine your dream with us</h2>
    </div>
   <div class="feature-section reverse">
    <div class="feature-image">
        <img src="images/p1.png" alt="Stylish Living Room">
    </div>
    <div class="feature-text">
        <h3>Elevate Your Space</h3>
        <p>Discover the perfect blend of comfort and style with our curated interiors. This modern living room setup features a sleek sectional sofa, warm wood accents, and ambient lighting—designed to inspire relaxation and sophistication in every corner.</p>
    </div>
</div>
</main>
<?php include 'includes/footer.php'; ?>


</body>
</html>