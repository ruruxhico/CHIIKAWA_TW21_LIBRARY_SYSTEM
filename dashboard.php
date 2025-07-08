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
    <link rel="stylesheet" href="script.js">
</head>
<body>
    <div class="logout-top">
        <a href="logout.php">Logout</a>
    </div>

    <div class="container-dashboard-box">
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
                $today = new DateTime($today->format('Y-m-d')); // Remove time part


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
                    } 
                        else {
                        if ($today == $due_date) {
                            $status_text = "Due Today";
                        } elseif ($today > $due_date) {
                            $overdue_days = $today->diff($due_date)->days;
                            $status_text = "Overdue by " . $overdue_days . " day" . ($overdue_days > 1 ? "s" : "");
                            $potential_fine = $overdue_days * 10;
                            $fine_display = "Overdue! Potential Fine: <span class='fine'>₱" . number_format($potential_fine, 2) . "</span>";
                        } else {
                            $remaining_days = $today->diff($due_date)->days;
                            if ($remaining_days === 1) {
                                $status_text = "Due Tomorrow";
                            } else {
                                $status_text = "Days Left: " . $remaining_days;
                            }
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
                            <form action="" method="POST" class="inline-form"
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