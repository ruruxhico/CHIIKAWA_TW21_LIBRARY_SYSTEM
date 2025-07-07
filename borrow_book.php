<?php
include "db.php";
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $book_id = $_POST['book_id'];
    $borrower = $_POST['borrower'];

    // Check book availability
    $check = $conn->query("SELECT * FROM books WHERE book_id = '$book_id' AND status = 'Available'");
    if ($check->num_rows === 1) {
        // Update book status
        $conn->query("UPDATE books SET status = 'Borrowed' WHERE book_id = '$book_id'");

        // Insert into borrows (not borrowed_books)
        $conn->query("INSERT INTO borrows (book_id, student, borrow_date, returned) 
                      VALUES ('$book_id', '$borrower', NOW(), 0)");

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
