<?php
include "db.php";
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $book_id = $_POST['book_id'];
    $borrower = $_POST['borrower'];

    // Check book availability
    $check = $conn->query("SELECT * FROM books WHERE book_id = '$book_id' AND status = 'Available'");
    if ($check->num_rows === 1) {
        // Borrow the book
        $conn->query("UPDATE books SET status = 'Borrowed' WHERE book_id = '$book_id'");
        $conn->query("INSERT INTO borrowed_books (book_id, borrower_name, date_borrowed) 
                      VALUES ('$book_id', '$borrower', NOW())");
        $message = "Book successfully borrowed!";
    } else {
        $message = "Book not available.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Borrow Book</title>
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

    form input, form select {
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
    <h2>Borrow a Book</h2>

    <?php if ($message): ?>
      <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Book ID:</label>
      <input type="text" name="book_id" required>

      <label>Your Name:</label>
      <input type="text" name="borrower" required>

      <button type="submit">Borrow</button>
    </form>
  </div>
</body>
</html>
