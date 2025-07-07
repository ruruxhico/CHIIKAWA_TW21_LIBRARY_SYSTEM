<?php
include "db.php"; // Your database connection file
include "functions.php"; // Your functions file containing archiveBook() and unarchiveBook()
session_start(); // Start the session

// Ensure user is logged in and has appropriate user_type
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'librarian')) {
    header("Location: login.php"); // Redirect unauthorized users
    exit();
}

$user_type = $_SESSION['user_type']; // Get the user type from session
$message = ''; // For displaying feedback messages

// --- Handle Form Submissions for Unarchive Action ---
// We only expect 'unarchive' action on this page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'unarchive' && isset($_POST['book_id'])) {
        $book_id_action = $_POST['book_id'];

        // Call your unarchiveBook function
        $response = unarchiveBook($conn, $book_id_action);
        $message = $response['message'];
    }
}

// --- Fetch ONLY ARCHIVED Books to Display ---
$sql = "SELECT * FROM books WHERE status = 'archived' ORDER BY title ASC";
$result = $conn->query($sql);
$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
$conn->close(); // Close connection after fetching all data
?>

<!DOCTYPE html>
<html>
<head>
    <title>Archived Books</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <p class="back-link"><a href="dashboard.php">← Back to Dashboard</a></p>
        <h2>Archived Books</h2>

        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Error') !== false) ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <?php if (empty($books)): ?>
            <p>No archived books found.</p>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <div class="book-card archived"> <a href="<?php echo htmlspecialchars($book['cover_image'] ?? '#'); ?>" target="_blank">
                        <img class="book-thumb" src="<?php echo htmlspecialchars($book['cover_image'] ?? 'default.jpg'); ?>" alt="Book Cover">
                    </a>

                    <div class="book-details">
                        <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                        <div class="book-meta">
                            Book ID: <?php echo htmlspecialchars($book['book_id']); ?><br>
                            Category: <?php echo htmlspecialchars($book['category']); ?><br>
                            Status: <?php echo htmlspecialchars($book['status']); ?><br>
                            Available Copies: <?php echo htmlspecialchars($book['available_copies']); ?>
                        </div>
                    </div>

                    <div class="book-actions">
                        <form action="" method="POST" class="inline-form"
                              onsubmit="return confirmUnarchive('<?php echo htmlspecialchars($book['title']); ?>');">
                            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                            <button type="submit" name="action" value="unarchive" class="btn-success">Unarchive</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>