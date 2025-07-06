<?php
session_start();
// Include db.php for database connection
include "db.php";

// Redirect if user is not logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: index.php"); // Assuming index.php is your login page
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id']; // Get the user_id from session
$user_type = $_SESSION['user_type'];

// Fetch user's borrowed books using prepared statement for security
// Check if return_date is NULL to indicate not yet returned
$query = "
    SELECT b.title, b.author, br.borrow_date, br.due_date, br.return_date, br.fine_amount
    FROM borrowings br
    JOIN books b ON br.book_id = b.book_id
    WHERE br.user_id = ?
    ORDER BY br.borrow_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // 'i' for integer user_id
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing styles here */
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

        .dashboard-box {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            max-width: 800px;
            margin: auto;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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
        }

        .dash-button:hover {
            background: #155db2;
        }

        .borrowed-card {
            background: #f9f9f9;
            border-left: 5px solid #1a73e8;
            padding: 15px 20px;
            margin-bottom: 12px;
            text-align: left;
            border-radius: 6px;
        }

        .borrowed-title {
            font-weight: bold;
            color: #1a4c96;
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
        }
        .admin-button:hover {
            background-color: #d17814;
        }
    </style>
</head>
<body>
    <div class="logout-top">
        <a href="logout.php">Logout</a>
    </div>

    <div class="container dashboard-box">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

        <div class="button-grid">
            <?php if ($user_type === 'admin'): ?>
                <a href="add_book.php" class="dash-button admin-button">Add Book</a>
                <a href="archive_book.php" class="dash-button admin-button">Archive Book</a>
            <?php endif; ?>
            <a href="view_books.php" class="dash-button">View Books</a>
            <a href="borrow_book.php" class="dash-button">Borrow Book</a>
            <a href="return_book.php" class="dash-button">Return Book</a>
            </div>

        <h3>Your Borrowed Books</h3>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $borrowed_date = new DateTime($row['borrow_date']);
                $due_date = new DateTime($row['due_date']); // Use due_date from DB
                $today = new DateTime();

                // Determine if the book is returned
                $is_returned = ($row['return_date'] !== null);

                // Calculate current status and potential fine if not returned
                $status_text = "Not Returned";
                $fine_display = "";

                if ($is_returned) {
                    $status_text = "Returned";
                    if ($row['fine_amount'] > 0) {
                        $fine_display = "Fine Paid: <span class='fine'>₱" . number_format($row['fine_amount'], 2) . "</span>";
                    }
                } else {
                    // Book is not returned, check for overdue
                    if ($today > $due_date) {
                        $overdue_days = $today->diff($due_date)->days;
                        // For display, calculate potential fine based on current date
                        $potential_fine = $overdue_days * 10; // Assuming ₱10/day from config
                        $fine_display = "Overdue! Potential Fine: <span class='fine'>₱" . number_format($potential_fine, 2) . "</span>";
                    } else {
                        $remaining_days = $today->diff($due_date)->days;
                        $status_text = "Days Left: " . $remaining_days;
                    }
                }
                ?>

                <div class="borrowed-card">
                    <div class="borrowed-title"><?php echo htmlspecialchars($row['title']); ?></div>
                    <div class="meta">
                        Author: <?php echo htmlspecialchars($row['author']); ?><br>
                        Date Borrowed: <?php echo $borrowed_date->format('F j, Y'); ?><br>
                        Due Date: <?php echo $due_date->format('F j, Y'); ?><br>
                        Status: <strong><?php echo $status_text; ?></strong><br>
                        <?php echo $fine_display; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have not borrowed any books.</p>
        <?php endif; ?>
    </div>
</body>
</html>