<?php
session_start();
include "db.php";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
  <script src="script.js" defer></script>
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <?php if ($error): ?>
      <p class="message"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
      <label>Username:</label>
      <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" id="password" required>

<div style="margin: 10px 0;">
  <label style="display: inline-flex; align-items: baseline; font-weight: normal; cursor: pointer;">
    <input type="checkbox" onclick="togglePassword('password')" style="line-height: 1.5; margin-left: 0px;">
    Show Password
  </label>
</div>

      <button type="submit">Login</button>
    </form>
    <p style="margin-top: 15px;">No account? <a href="register.php">Register here</a></p>
  </div>
</body>
</html>
