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

// Fetch latest book details
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
    <style>
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .book-image {
            width: 150px;
            height: 200px;
            object-fit: cover;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .book-title {
            font-size: 24px;
            color: #1a4c96;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        .book-details {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .actions button,
        .actions a {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
        }
        .btn-primary { background-color: #007bff; color: #fff;}
        .btn-primary:hover { background-color: #0056b3; }
        .btn-success { background-color: #28a745; color: #fff; }
        .btn-success:hover { background-color: #218838; }
        .btn-danger { background-color: #dc3545; color: #fff; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn-warning:hover { background-color: #e0a800; }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .message {
            margin: 15px 0;
            padding: 10px;
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
    <p><a href="view_books.php">Back to Book List</a></p>

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