<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Decore</title>
    <link rel="icon" type="image/png" href="icon.png">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/footer.css">
    <style>
        /* About section styles */
        .About-section {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            gap: 50px;
            flex-wrap: wrap;
            background-color: #f7f7f7;
        }

        .about-image {
            width: 300px;
            max-width: 100%;
            border-radius: 10px;
            animation: fadeIn 1.5s ease-out forwards;
        }

        .about-text {
            max-width: 600px;
            text-align: left;
            animation: slideInText 1.2s ease-out forwards;
        }

        .about-text h1 {
            font-size: 2.8rem;
            color: #024950;
            margin-bottom: 10px;
        }

        .about-text h2 {
            font-size: 1.6rem;
            color: #026970;
            margin-bottom: 10px;
        }

        .about-text h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #666;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideInText {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Responsive behavior */
        @media (max-width: 768px) {
            .About-section {
                flex-direction: column;
                text-align: center;
            }

            .about-text {
                text-align: center;
            }

            nav {
                display: none;
                flex-direction: column;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
<?php $currentPage = 'about'; ?>
<?php include 'includes/navbar.php'; ?>

<main>
    <section class="About-section">
        <img src="images/about.png" alt="Wall Panel Showcase" class="about-image">
        <div class="about-text">
            <h1>Wall Paneling</h1>
            <h2>Style Meets Function</h2>
            <h3>Crafted to Elevate Every Space</h3>
            <p>At <strong>Your Decore</strong>, we believe walls should do more than divide space—they should define it. Our curated wall panel solutions blend craftsmanship with character, offering everything from sleek vertical wooden slats that add warmth and rhythm to modern conference rooms, to moisture-resistant PVC panels perfect for kitchens and bathrooms.</p>
            <p>Choose from acoustic fabric panels for cozy bedrooms, reflective glass for modern lounges, or geometric MDF designs for bold feature walls. Each panel is crafted to inspire, built to last, and tailored to your space’s personality. Whether you're designing a serene workspace or a vibrant retail corner, our wall panels bring your vision to life.</p>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>


</body>
</html>