<?php
include "db.php";
session_start();
if ($_SESSION['role'] != 'admin') die("Unauthorized");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookID = $_POST['bookID'];
    $conn->query("UPDATE books SET status='archived' WHERE book_id='$bookID'");
    echo "Book archived.";
}
?>
<form method="POST">
    Book ID: <input name="bookID" required><br>
    <button type="submit">Archive</button>
</form>
