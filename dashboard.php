<?php
session_start();
include "db.php";
include "functions.php";

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$message = ''; // For displaying feedback messages

// --- Handle Return Action ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'return' && isset($_POST['borrow_id']) && isset($_POST['book_id'])) {
        $borrow_id_to_return = $_POST['borrow_id'];
        $book_id_to_return = $_POST['book_id'];

        // Call the returnBook function from functions.php
        $response = returnBook($conn, $borrow_id_to_return, $book_id_to_return);
        $message = $response['message'];
    }
}

// Fetch user's borrowed books including borrow_id and book_id
$query = "
    SELECT b.title, b.author, br.borrow_date, br.due_date, br.return_date, br.fine_amount, br.borrow_id, br.book_id
    FROM borrowings br
    JOIN books b ON br.book_id = b.book_id
    WHERE br.user_id = ?
    ORDER BY br.borrow_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ... (Your existing CSS styles, including the new ones for .container, .borrowed-details, .btn-return, .btn-returned) ... */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .logout-top {
            display: flex;
            justify-content: flex-end;
            padding: 16px;
        }

        .logout-top a {
            background: #e53935;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        .logout-top a:hover {
            background: #c62828;
        }

        .container { /* Added for consistent container styling */
            max-width: 900px;
            margin: 20px auto;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .dashboard-box { /* Keeping this for the main dashboard content */
             /* no specific styles here if container does the job */
        }

        h2, h3 {
            text-align: center;
            color: #1a4c96;
            margin-bottom: 20px;
        }

        .button-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 14px;
            margin-bottom: 30px;
        }

        .dash-button {
            background: #1a73e8;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
            min-width: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .dash-button:hover {
            background: #155db2;
        }

        .borrowed-card {
            display: flex; /* Added flex for better layout */
            align-items: center; /* Center items vertically */
            justify-content: space-between; /* Space between details and button */
            background: #f9f9f9;
            border-left: 5px solid #1a73e8;
            padding: 15px 20px;
            margin-bottom: 12px;
            text-align: left;
            border-radius: 6px;
        }
        /* Style for returned cards */
        .borrowed-card.returned {
            opacity: 0.7; /* Slightly dim returned cards */
            border-left: 5px solid #6c757d; /* Grey border */
        }


        .borrowed-details { /* New wrapper for text details */
            flex-grow: 1; /* Allows details to take available space */
        }

        .borrowed-title {
            font-weight: bold;
            color: #1a4c96;
            font-size: 18px; /* Added font size for clarity */
        }

        .meta {
            font-size: 14px;
            margin-top: 5px;
            color: #333;
        }

        .fine {
            color: red;
            font-weight: bold;
        }

        /* New style for admin-specific button, if desired */
        .admin-button {
            background-color: #f7931e; /* Orange color for admin actions */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .admin-button:hover {
            background-color: #d17814;
        }

        /* Styles for Return button */
        .btn-return {
            background-color: #28a745; /* Green for return */
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
            white-space: nowrap; /* Keep text on one line */
        }
        .btn-return:hover {
            background-color: #218838;
        }
        .btn-returned { /* Style for when book is already returned */
            background-color: #6c757d; /* Grey */
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: not-allowed;
            opacity: 0.8;
            white-space: nowrap;
        }

        /* Message styling */
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
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
        function confirmReturn(bookTitle) {
            return confirm("Are you sure you want to return '" + bookTitle + "'?");
        }
    </script>
</head>
<body>
    <div class="logout-top">
        <a href="logout.php">Logout</a>
    </div>

    <div class="container dashboard-box">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

        <div class="button-grid">
            <?php if ($user_type === 'admin' || $user_type === 'librarian'): ?>
                <a href="add_book.php" class="dash-button admin-button">Add Book</a>
                <a href="archive_book.php" class="dash-button admin-button">Archived Books</a>
            <?php endif; ?>
            <a href="view_books.php" class="dash-button">View Books</a>
        </div>

        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Error') !== false) ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <h3>Your Borrowed Books</h3>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $borrowed_date = new DateTime($row['borrow_date']);
                $due_date = new DateTime($row['due_date']);
                $today = new DateTime();

                $is_returned = ($row['return_date'] !== null);

                $status_text = "Not Returned";
                $fine_display = "";
                $card_class = "";

                if ($is_returned) {
                    $status_text = "Returned on " . (new DateTime($row['return_date']))->format('F j, Y');
                    $card_class = "returned";
                    if ($row['fine_amount'] > 0) {
                        $fine_display = "Fine Paid: <span class='fine'>₱" . number_format($row['fine_amount'], 2) . "</span>";
                    }
                } else {
                    if ($today > $due_date) {
                        $interval = $today->diff($due_date);
                        $overdue_days = $interval->days;
                        $potential_fine = $overdue_days * 10;
                        $fine_display = "Overdue! Potential Fine: <span class='fine'>₱" . number_format($potential_fine, 2) . "</span>";
                        $status_text = "Overdue by " . $overdue_days . " days";
                    } else {
                        $interval = $today->diff($due_date);
                        $remaining_days = $interval->days;
                        $status_text = ($remaining_days === 0) ? "Due Today" : "Days Left: " . $remaining_days;
                    }
                }
                ?>

                <div class="borrowed-card <?php echo $card_class; ?>">
                    <div class="borrowed-details">
                        <div class="borrowed-title"><?php echo htmlspecialchars($row['title']); ?></div>
                        <div class="meta">
                            Author: <?php echo htmlspecialchars($row['author']); ?><br>
                            Date Borrowed: <?php echo $borrowed_date->format('F j, Y'); ?><br>
                            Due Date: <?php echo $due_date->format('F j, Y'); ?><br>
                            Status: <strong><?php echo $status_text; ?></strong><br>
                            <?php echo $fine_display; ?>
                        </div>
                    </div>
                    <div class="borrowed-actions">
                        <?php if (!$is_returned): ?>
                            <form action="" method="POST" style="display:inline-block;"
                                  onsubmit="return confirmReturn('<?php echo htmlspecialchars($row['title']); ?>');">
                                <input type="hidden" name="borrow_id" value="<?php echo htmlspecialchars($row['borrow_id']); ?>">
                                <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($row['book_id']); ?>">
                                <button type="submit" name="action" value="return" class="btn-return">Return</button>
                            </form>
                        <?php else: ?>
                            <button disabled class="btn-returned">Returned</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have not borrowed any books.</p>
        <?php endif; ?>
    </div>
</body>
</html>