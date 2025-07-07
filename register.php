<?php
include "db.php";
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs (basic example)
    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        // Check if username already exists using a prepared statement for security
        $check_sql = "SELECT user_id FROM users WHERE username = ?";
        if ($stmt_check = $conn->prepare($check_sql)) {
            $stmt_check->bind_param("s", $username);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $message = "Username already exists.";
            } else {
                // Hash the password before storing it
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Default user type for registration is 'student'
                $user_type = 'student';

                // Insert the new user with hashed password and default user_type
                // Use a prepared statement to prevent SQL injection
                $insert_sql = "INSERT INTO users (username, password, user_type) VALUES (?, ?, ?)";
                if ($stmt_insert = $conn->prepare($insert_sql)) {
                    $stmt_insert->bind_param("sss", $username, $hashed_password, $user_type);

                    if ($stmt_insert->execute()) {
                        $message = "Registration successful. <a href='index.php'>Login here</a>";
                    } else {
                        $message = "Error registering user: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                } else {
                    $message = "Database error preparing insert statement: " . $conn->error;
                }
            }
            $stmt_check->close();
        } else {
            $message = "Database error preparing check statement: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
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

      <div class="checkbox-options">
        <label>
            <input type="checkbox" onclick="saveCredentials()" >
            Remember Me
            <input type="checkbox" onclick="togglePassword('reg-password')">
            Show Password
            
        </label>
      </div>

      <button type="submit">Register</button>
    </form>
    <p class="login-link">Already have an account? <a href="index.php">Login here</a></p>
  </div>
</body>
</html>
