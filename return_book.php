<?php
include "db.php";
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $borrow_id = $_POST['borrow_id'];

    $result = $conn->query("SELECT book_id FROM borrows WHERE id = '$borrow_id' AND returned = 0");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $book_id = $row['book_id'];

        $conn->query("UPDATE borrows SET returned = 1 WHERE id = '$borrow_id'");

        // Set book status back to Available
        $conn->query("UPDATE books SET status = 'Available' WHERE book_id = '$book_id'");

        $message = "Book successfully returned!";
    } else {
        $message = "Invalid borrow ID or book already returned.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Return Book</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="return-books-container">
  <p class="back-link"><a href="dashboard.php">Back to Dashboard</a></p>
<h2 class="heading">Return a Book</h2>

<form method="post">
  <label for="borrow_id">Enter Borrow ID:</label>
  <input type="text" id="borrow_id" name="borrow_id" required>

  <input type="submit" value="Return Book">
</form>

<?php if (!empty($message)): ?>
  <div class="message"><?php echo $message; ?></div>
<?php endif; ?>
</div>

</body>
</html>
