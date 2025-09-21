<?php
$to = "mehul63@gmail.com";
$subject = "Test Mail";
$message = "This is a test email from Your Decore.";
$headers = "From: info@yourdecore.com";

if (mail($to, $subject, $message, $headers)) {
    echo "Mail sent successfully.";
} else {
    echo "Mail failed.";
}
?>