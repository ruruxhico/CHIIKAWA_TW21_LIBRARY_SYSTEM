<?php
include "db.php";
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $book_id = $_POST['book_id'];

    // Check if the book is currently borrowed
    $check = $conn->query("SELECT * FROM books WHERE book_id = '$book_id' AND status = 'Borrowed'");
    if ($check->num_rows === 1) {
        $conn->query("UPDATE books SET status = 'Available' WHERE book_id = '$book_id'");
        $conn->query("UPDATE borrowed_books SET date_returned = NOW() 
                      WHERE book_id = '$book_id' AND date_returned IS NULL");
        $message = "Book successfully returned!";
    } else {
        $message = "Book is not currently borrowed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Return Book</title>
  <link rel="stylesheet" href="style.css">
  <style>
    form {
      max-width: 400px;
      margin: auto;
      background: #fff;
      padding: 24px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    form input {
      width: 100%;
      padding: 10px;
      margin: 12px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    form button {
      width: 100%;
      background-color: #1a73e8;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    form button:hover {
      background-color: #155db2;
    }

    .message {
      text-align: center;
      color: #1a4c96;
      font-weight: bold;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Return a Book</h2>

    <?php if ($message): ?>
      <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Book ID:</label>
      <input type="text" name="book_id" required>

      <button type="submit">Return</button>
    </form>
  </div>
</body>
</html>
