<?php
session_start();
include "db.php";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_input = trim($_POST['username']);
    $password_input = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, username, password, user_type FROM users WHERE username=?");
    $stmt->bind_param("s", $username_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
        $stored_hashed_password = $user_data['password']; // Get the stored hash

        // 2. Use password_verify() to compare the entered password with the stored hash
        if (password_verify($password_input, $stored_hashed_password)) {
            // Password is correct!
            $_SESSION['user_id'] = $user_data['user_id']; // Store user_id if needed
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['user_type'] = $user_data['user_type']; // Corrected to user_type

            // Redirect based on user_type if needed, or just to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Passwords do not match
            $error = "Invalid username or password.";
        }
    } else {
        // Username not found
        $error = "Invalid username or password.";
    }
    $stmt->close();
    $conn->close(); // Close connection after use
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
