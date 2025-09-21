<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Services – Your Decore</title>
  <link rel="icon" type="image/png" href="icon.png" />
  <link rel="stylesheet" href="style/style.css" />
  <link rel="stylesheet" href="style/footer.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f7f7f7;
    }

    
    .menu-toggle {
      display: none;
    }

    .services-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .service-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .service-card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .service-card:hover {
      transform: translateY(-5px);
    }

    .service-card h3 {
      color: #024950;
      margin-bottom: 10px;
    }

    .service-card p {
      color: #444;
      font-size: 0.95rem;
    }

    .quote-form {
      background: white;
      padding: 30px;
      margin-top: 60px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .quote-form h2 {
      color: #024950;
      margin-bottom: 20px;
    }

    .quote-form input,
    .quote-form textarea,
    .quote-form select {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .quote-form button {
      margin-top: 15px;
      padding: 10px 20px;
      background-color: #024950;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .comparison-table {
      margin-top: 60px;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 15px;
      text-align: center;
      border: 1px solid #ddd;
    }

    th {
      background-color: #024950;
      color: white;
    }

    .gallery {
      margin-top: 60px;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .gallery img {
      width: 300px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
      nav {
        flex-direction: column;
      }

      .menu-toggle {
        display: block;
      }
    }
  </style>
</head>
<body>




<?php $currentPage = 'service'; ?>
<?php include 'includes/navbar.php'; ?>

<main class="services-container">
  <h2 style="color:#024950; text-align:center; margin-bottom:30px;">Our Services</h2>

  <div class="service-grid">
    <div class="service-card">
      <h3>Wall Panel Installation</h3>
      <p>Transform your space with elegant, durable wall panels tailored to your style.</p>
    </div>
    <div class="service-card">
      <h3>Ceiling Decor</h3>
      <p>Enhance your interiors with modern ceiling designs and acoustic solutions.</p>
    </div>
    <div class="service-card">
      <h3>Custom Design Consultation</h3>
      <p>Get expert advice and personalized design plans for your home or office.</p>
    </div>
    <div class="service-card">
      <h3>Commercial Projects</h3>
      <p>We handle large-scale decor solutions for showrooms, offices, and retail spaces.</p>
    </div>
  </div>


  </div>

  <div class="comparison-table">
    <h2 style="color:#024950; text-align:center; margin:40px 0 20px;">Service Packages</h2>
    <table>
      <tr>
        <th>Feature</th>
        <th>Basic</th>
        <th>Premium</th>
        <th>Custom</th>
      </tr>
      <tr>
        <td>Wall Panels</td>
        <td>✅</td>
        <td>✅</td>
        <td>✅</td>
      </tr>
      <tr>
        <td>Ceiling Decor</td>
        <td>❌</td>
        <td>✅</td>
        <td>✅</td>
      </tr>
      <tr>
        <td>On-site Consultation</td>
        <td>❌</td>
        <td>✅</td>
        <td>✅</td>
      </tr>
      <tr>
        <td>Custom Design</td>
        <td>❌</td>
        <td>❌</td>
        <td>✅</td>
      </tr>
    </table>
  </div>

  <div class="gallery">
    <h2 style="width:100%; text-align:center; color:#024950;">Before & After Gallery</h2>
    <img src="images/before1.png" alt="Before Decor" />
    <img src="images/after1.png" alt="After Decor" />
    <img src="images/before2.png" alt="Before Decor" />
    <img src="images/after2.png" alt="After Decor" />
  </div>
</main>

<?php include 'includes/footer.php'; ?>


</body>
</html>