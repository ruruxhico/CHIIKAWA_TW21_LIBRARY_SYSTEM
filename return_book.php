<?php
include "db.php";
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $borrow_id = $_POST['borrow_id'];

    // Get book_id from borrows table
    $result = $conn->query("SELECT book_id FROM borrows WHERE id = '$borrow_id' AND returned = 0");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $book_id = $row['book_id'];

        // Mark borrow as returned
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
  <style>
    form {
      max-width: 400px;
      margin: auto;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    input[type="text"], input[type="submit"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    input[type="submit"] {
      background-color: #1a73e8;
      color: white;
      cursor: pointer;
      transition: background 0.3s;
    }

    input[type="submit"]:hover {
      background-color: #155db2;
    }

    .message {
      text-align: center;
      margin-top: 20px;
      color: #333;
    }
  </style>
</head>
<body>

<h2 style="text-align:center;">Return a Book</h2>

<form method="post">
  <label for="borrow_id">Enter Borrow ID:</label>
  <input type="text" id="borrow_id" name="borrow_id" required>

  <input type="submit" value="Return Book">
</form>

<?php if (!empty($message)): ?>
  <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

</body>
</html>
