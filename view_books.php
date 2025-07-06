<?php
include "db.php"; // Establish $conn connection
include "functions.php"; // Include your functions file
session_start(); // Start the session at the very beginning

// Ensure user is logged in
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php"); // Redirect to login if not authenticated
    exit();
}

// Get user type and ID from session AFTER session_start()
$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'] ?? null;

$message = ''; // For displaying feedback messages

// --- Handle Form Submissions for Actions (Borrow/Archive/Unarchive) ---
// This block must come BEFORE fetching books for display,
// as actions might modify the book data.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['book_id'])) {
        $book_id_action = $_POST['book_id'];
        $action = $_POST['action'];

        // BORROW Action
        if ($action === 'borrow' && ($user_type === 'student' || $user_type === 'admin')) {
            if ($user_id) {
                // Pass $conn to the function
                $response = borrowBook($conn, $book_id_action, $user_id);
                $message = $response['message'];
            } else {
                $message = "Error: User ID not found in session for borrowing. Please log in again.";
            }
        }
        // ARCHIVE Action (Admin/Librarian only)
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

// --- Fetch Books to Display ---
// This query now happens AFTER any potential POST actions have been processed.
// The $conn object is still open at this point.
$sql = "SELECT * FROM books";
if ($user_type === 'student') {
    $sql .= " WHERE status = 'available'";
}
// For admin/librarian, they see all books including archived/unavailable unless further filtered
$result = $conn->query($sql);
$books = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}
$conn->close(); // Close connection AFTER all database operations are done
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Books</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing CSS (as provided in the previous correct code block) goes here */
        .book-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            animation: fadeIn 0.3s ease-in-out;
            transition: transform 0.2s ease;
            gap: 20px;
        }
        .book-card:hover {
            transform: scale(1.01);
        }
        .book-thumb {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .book-details {
            flex: 1;
            padding-right: 15px;
        }
        .book-title {
            font-weight: bold;
            color: #1a4c96;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .book-meta {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .book-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 120px;
            align-items: flex-end;
        }
        .book-actions button,
        .book-actions a {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: block;
            width: 100%;
            text-align: center;
            font-size: 14px;
            white-space: nowrap;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.7;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
      <p style="margin-top: 15px;"><a href="dashboard.php">Back to Dashboard</a></p>
        <h2>View Books</h2>

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
                        <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
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
                                <form action="" method="POST" style="display:inline-block; width:100%;">
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                    <button type="submit" name="action" value="borrow" class="btn-primary">Borrow</button>
                                </form>
                            <?php elseif ($book['status'] === 'unavailable'): ?>
                                <button class="btn-warning" disabled>Unavailable</button>
                            <?php endif;
                        endif;
                        ?>

                        <?php
                        if ($user_type === 'admin'):
                            if ($book['status'] !== 'archived'): ?>
                                <form action="" method="POST" style="display:inline-block; width:100%;">
                                    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                    <button type="submit" name="action" value="archive" class="btn-danger">Archive</button>
                                </form>
                            <?php else: ?>
                                <button class="btn-warning" disabled>Archived</button>
                                <form action="" method="POST" style="display:inline-block; width:100%;">
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