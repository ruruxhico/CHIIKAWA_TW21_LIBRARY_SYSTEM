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
    <style>
        /* Reusing and adapting styles from view_books.php */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #1a4c96;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .book-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f0f0f0; /* Grey out archived books */
            border-left: 5px solid #dc3545; /* Red border for archived */
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

        /* Button Colors */
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-warning { /* Used for disabled "Archived" button */
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
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

        /* Message styling */
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
    <script>
        function confirmUnarchive(bookTitle) {
            return confirm("Are you sure you want to unarchive '" + bookTitle + "'?");
        }
    </script>
</head>
<body>
    <div class="container">
        <p style="margin-top: 15px;"><a href="dashboard.php">Back to Dashboard</a></p>
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
                        <form action="" method="POST" style="display:inline-block; width:100%;"
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