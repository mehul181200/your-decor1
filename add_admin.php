<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "your_decor");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $admin_id = $_POST["admin_id"];
  $name = $_POST["Name"];
  $password = $_POST["password"];
  $credential_id = $_POST["credential_id"];
  $public_key = $_POST["public_key"];

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO admin (Admin_id, Name, password, credential_id, public_key) VALUES (?, ?, ?, ?, ?)");
  if ($stmt) {
    $stmt->bind_param("issss", $admin_id, $name, $hashed_password, $credential_id, $public_key);

    if ($stmt->execute()) {
      $success = "✅ Admin registered with fingerprint!";
    } else {
      $error = "❌ Error: " . $stmt->error;
    }

    $stmt->close();
  } else {
    $error = "❌ Prepare failed: " . $conn->error;
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Admin with Fingerprint</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .form-box {
      background: #fff;
      padding: 25px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      width: 360px;
    }
    .form-box h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .form-box input {
      width: 100%;
      padding: 10px;
      margin-bottom: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .form-box button {
      width: 100%;
      padding: 10px;
      background-color: #024950;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      cursor: pointer;
    }
    .form-box button:hover {
      background-color: #036d76;
    }
    .message {
      text-align: center;
      margin-bottom: 10px;
      color: green;
    }
    .error {
      text-align: center;
      margin-bottom: 10px;
      color: red;
    }
  </style>
</head>
<body>

<div class="form-box">
  <h2>Add Admin with Fingerprint</h2>
  <?php if (!empty($success)) echo "<p class='message'>$success</p>"; ?>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  <form method="POST" id="adminForm">
    <input type="number" name="admin_id" placeholder="Admin ID" required />
    <input type="text" name="Name" placeholder="Admin Name" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="hidden" name="credential_id" id="credential_id" />
    <input type="hidden" name="public_key" id="public_key" />
    <button type="button" onclick="registerFingerprint()">Register Fingerprint</button>
  </form>
</div>

<script>
async function registerFingerprint() {
  if (!window.PublicKeyCredential) {
    alert("WebAuthn not supported on this browser.");
    return;
  }

  const challenge = new Uint8Array(32);
  window.crypto.getRandomValues(challenge);

  const publicKey = {
    challenge: challenge,
    rp: { name: "YourDecor" },
    user: {
      id: new Uint8Array(16),
      name: "admin@example.com",
      displayName: "Admin"
    },
    pubKeyCredParams: [{ type: "public-key", alg: -7 }],
    authenticatorSelection: {
      authenticatorAttachment: "platform",
      userVerification: "required"
    },
    timeout: 60000,
    attestation: "none"
  };

  try {
    const credential = await navigator.credentials.create({ publicKey });

    const credId = btoa(String.fromCharCode(...new Uint8Array(credential.rawId)));
    const pubKey = JSON.stringify({
      clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
      attestationObject: btoa(String.fromCharCode(...new Uint8Array(credential.response.attestationObject)))
    });

    document.getElementById("credential_id").value = credId;
    document.getElementById("public_key").value = pubKey;

    document.getElementById("adminForm").submit();
  } catch (err) {
    alert("Fingerprint registration failed: " + err.message);
  }
}
</script>

</body>
</html>