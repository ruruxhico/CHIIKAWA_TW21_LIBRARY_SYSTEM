<?php
include "db.php";
include "functions.php";
session_start();

if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'] ?? null;
$message = "";

// Get book_id from GET (initial load) or POST (form submission)
$book_id = $_GET['id'] ?? ($_POST['book_id'] ?? null);

if (!$book_id) {
    header("Location: view_books.php");
    exit();
}

// Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'borrow' && ($user_type === 'student' || $user_type === 'admin')) {
        if ($user_id) {
            $response = borrowBook($conn, $book_id, $user_id);
            $message = $response['message'];
        } else {
            $message = "Error: User ID not found. Please log in again.";
        }
    } elseif ($action === 'archive' && $user_type === 'admin') {
        $response = archiveBook($conn, $book_id);
        $message = $response['message'];
    } elseif ($action === 'unarchive' && $user_type === 'admin') {
        $response = unarchiveBook($conn, $book_id);
        $message = $response['message'];
    }
}

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->bind_param("s", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$conn->close();

if (!$book) {
    header("Location: view_books.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container-see-more">
    <p><a href="view_books.php">← Back to Book List</a></p>

    <?php if ($message): ?>
        <p class="message <?php echo (strpos($message, 'Error') !== false || strpos($message, 'not available') !== false) ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <img src="<?php echo htmlspecialchars($book['cover_image'] ?? 'default.jpg'); ?>" alt="Book Cover" class="book-image">

    <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>

    <div class="book-details">
        <strong>Book ID:</strong> <?php echo htmlspecialchars($book['book_id']); ?><br>
        <strong>Category:</strong> <?php echo htmlspecialchars($book['category']); ?><br>
        <strong>Author:</strong> <?php echo htmlspecialchars($book['author'] ?? 'N/A'); ?><br>
        <strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher'] ?? 'N/A'); ?><br>
        <strong>Year:</strong> <?php echo htmlspecialchars($book['year_published'] ?? 'N/A'); ?><br>
        <strong>Status:</strong> <?php echo htmlspecialchars($book['status']); ?><br>
        <strong>Available Copies:</strong> <?php echo htmlspecialchars($book['available_copies']); ?><br>
        <strong>Description:</strong><br>
        <p><?php echo nl2br(htmlspecialchars($book['description'] ?? 'No description provided.')); ?></p>
    </div>

    <div class="actions">
        <?php if (($user_type === 'student' || $user_type === 'admin') && $book['status'] === 'available' && $book['available_copies'] > 0): ?>
            <form action="" method="POST">
                <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                <button type="submit" name="action" value="borrow" class="btn-primary">Borrow</button>
            </form>
        <?php elseif ($book['status'] === 'unavailable'): ?>
            <button class="btn-warning" disabled>Unavailable</button>
        <?php endif; ?>

        <?php if ($user_type === 'admin'): ?>
            <?php if ($book['status'] !== 'archived'): ?>
                <form action="" method="POST">
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                    <button type="submit" name="action" value="archive" class="btn-danger">Archive</button>
                </form>
            <?php else: ?>
                <button class="btn-warning" disabled>Archived</button>
                <form action="" method="POST">
                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                    <button type="submit" name="action" value="unarchive" class="btn-success">Unarchive</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>