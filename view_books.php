<?php
include "db.php";
include "functions.php";
session_start();

//ensure user is logged in
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

//get user type and user id from session
$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'] ?? null;

$message = ''; // For displaying feedback messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['book_id'])) {
        $book_id_action = $_POST['book_id'];
        $action = $_POST['action'];

        if ($action === 'borrow' && ($user_type === 'student' || $user_type === 'admin')) {
            if ($user_id) {
                // Pass $conn to the function
                $response = borrowBook($conn, $book_id_action, $user_id);
                $message = $response['message'];
            } else {
                $message = "Error: User ID not found in session for borrowing. Please log in again.";
            }
        }
        elseif ($action === 'archive' && ($user_type === 'admin')) {
            // Pass $conn to the function
            $response = archiveBook($conn, $book_id_action);
            $message = $response['message'];
        }
        // UNARCHIVE Action (Admin/Librarian only)
        elseif ($action === 'unarchive' && ($user_type === 'admin')) {
            // Pass $conn to the function
            $response = unarchiveBook($conn, $book_id_action);
            $message = $response['message'];
        }
    }
}

// Search books
$search = '';
$filter = "";

if ($user_type === 'student') {
    $filter .= " WHERE status = 'available'";
}

if (isset($_GET['search']) && strlen(trim($_GET['search'])) > 0) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $filter = " WHERE title LIKE '%$search%'";
    
    // Students only see available books even during search
    if ($user_type === 'student') {
        $filter .= " AND status = 'available'";
    }
}

$sql = "SELECT * FROM books" . $filter . " ORDER BY title ASC";

$result = $conn->query($sql);
$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Books</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="view-books-container">
        <p class="back-link"><a href="dashboard.php">← Back to Dashboard</a></p>
        <h2>View Books</h2>
<form method="GET" action="view_books.php" class="mb-2">
  <input type="text" name="search" placeholder="Search book title..." value="<?= htmlspecialchars($search) ?>" />
  <button type="submit">Search</button>
</form>

        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Error') !== false || strpos($message, 'not available') !== false) ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <?php if (empty($books)): ?>
            <p>No books found.</p>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <a href="<?php echo htmlspecialchars($book['cover_image'] ?? '#'); ?>" target="_blank">
                        <img class="book-thumb" src="<?php echo htmlspecialchars($book['cover_image'] ?? 'default.jpg'); ?>" alt="Book Cover">
                    </a>

                    <div class="book-details">
                        <div class="title-book"><?php echo htmlspecialchars($book['title']); ?></div>
                        <div class="book-meta">
                            Book ID: <?php echo htmlspecialchars($book['book_id']); ?><br>
                            Category: <?php echo htmlspecialchars($book['category']); ?><br>
                            Status: <?php echo htmlspecialchars($book['status']); ?><br>
                            Available Copies: <?php echo htmlspecialchars($book['available_copies']); ?>
                        </div>
                    </div>

                    <div class="book-actions">
                        <a href="see_more.php?id=<?php echo htmlspecialchars($book['book_id']); ?>" class="btn-primary">See More</a>

                        <?php
                        if ($user_type === 'student' || $user_type === 'admin'):
                            if ($book['status'] === 'available' && $book['available_copies'] > 0): ?>
                                <form action="" method="POST" class="inline-form">
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                    <button type="submit" name="action" value="borrow" class="btn-primary">Borrow</button>
                                </form>
                            <?php else: ?>
                                <button class="btn-warning" disabled>Unavailable</button>
                            <?php endif;
                        endif;
                        ?>

                        <?php
                        if ($user_type === 'admin'):
                            if ($book['status'] !== 'archived'): ?>
                                <form action="" method="POST" class="inline-form">
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                    <button type="submit" name="action" value="archive" class="btn-danger">Archive</button>
                                </form>
                            <?php else: ?>
                                <button class="btn-warning" disabled>Archived</button>
                                <form action="" method="POST" class="inline-form">
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                    <button type="submit" name="action" value="unarchive" class="btn-success">Unarchive</button>
                                </form>
                            <?php endif;
                        endif;
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>