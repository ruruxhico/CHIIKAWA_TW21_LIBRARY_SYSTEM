<?php
include "db.php";
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        $message = "Username already exists.";
    } else {
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', 'user')");
        $message = "Registration successful. <a href='index.php'>Login here</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
  <script src="script.js" defer></script>
</head>
<body>
  <div class="container">
    <h2>Student Registration</h2>
    <?php if ($message): ?>
      <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST">
      <label>Username:</label>
      <input name="username" required>

      <label>Password:</label>
      <input type="password" name="password" id="reg-password" required>

    <div style="margin: 10px 0; display: flex; justify-content: flex-end;">
    <label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">
        <input type="checkbox" onclick="togglePassword('reg-password')">
         Show Password
        </label>
    </div>


      <button type="submit">Register</button>
    </form>
    <p style="margin-top: 15px;">Already have an account? <a href="index.php">Login here</a></p>
  </div>
</body>
</html>
