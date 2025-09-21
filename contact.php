<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us – Your Decore</title>
    <link rel="icon" type="image/png" href="icon.png">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/footer.css">
    <style>
        .contact-section {
            background-color: #f7f7f7;
            padding: 60px 40px;
            max-width: 800px;
            margin: 60px auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            animation: fadeIn 1.5s ease-out forwards;
        }

        .contact-section h1 {
            font-size: 2.5rem;
            color: #024950;
            margin-bottom: 20px;
        }

        .contact-section p {
            font-size: 1rem;
            line-height: 1.6;
            color: #444;
            margin-bottom: 10px;
        }

        .contact-section a {
            color: #026970;
            text-decoration: none;
        }

        .contact-section form input,
        .contact-section form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .contact-section form button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #024950;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

      

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 768px) {
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

<?php $currentPage = 'contact'; ?>
<?php include 'includes/navbar.php'; ?>
<main>
    <section class="contact-section">
        <h1>Contact Us</h1>
        <p><strong>Your Decor – Decorative Wall Panel</strong></p>
        <p><strong>Phone:</strong> +91 999786 08310</p>
        <p><strong>Email:</strong> <a href="mailto:info@localhost">info@localhost</a></p>
        <p><strong>Address:</strong> </p>

        <!-- 🗺️ Updated Google Map -->
        <div style="margin: 30px 0;">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3992.210569145544!2d70.75666353195257!3d22.8661040767687!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3959f5eb819f4705%3A0xb5bfac3c6c8a458b!2sYour%20Decor!5e1!3m2!1sen!2sin!4v1756826703751!5m2!1sen!2sin" 
                width="100%" 
                height="450" 
                style="border:0; border-radius: 8px;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

        <!-- 📩 Contact Form -->
        <form action="submit-contact.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
            <button type="submit">Send Message</button>
        </form>
    </section>
</main>
<?php include 'includes/footer.php'; ?>



</body>
</html>