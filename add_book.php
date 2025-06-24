<?php
include "db.php";
include "functions.php";
session_start();
if ($_SESSION['role'] != 'admin') die("Unauthorized");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $pubMonth = $_POST['pubMonth'];
    $dayAdded = date('d');
    $yearPub = $_POST['yearPub'];
    $category = $_POST['category'];
    $count = $conn->query("SELECT COUNT(*) as cnt FROM books")->fetch_assoc()['cnt'] + 1;

    $bookID = generateBookID($title, $pubMonth, $dayAdded, $yearPub, $category, $count);

    $conn->query("INSERT INTO books (book_id, title, pub_month, pub_year, category, status) VALUES 
    ('$bookID', '$title', '$pubMonth', '$yearPub', '$category', 'available')");
    echo "Book Added: $bookID";
}
?>
<form method="POST">
    Title: <input name="title" required><br>
    Month Published: <input name="pubMonth" required><br>
    Year Published: <input name="yearPub" required><br>
    Category: <input name="category" required><br>
    <button type="submit">Add Book</button>
</form>
