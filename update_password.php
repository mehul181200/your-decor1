<!-- <?php
$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$admin_id = 1; // Replace with actual Admin ID
$plain_password = "mehul"; // Replace with actual plain password

$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE admin_login SET password = ? WHERE Admin_id = ?");
$stmt->bind_param("si", $hashed_password, $admin_id);
$stmt->execute();

echo "✅ Password updated successfully!";
?> -->