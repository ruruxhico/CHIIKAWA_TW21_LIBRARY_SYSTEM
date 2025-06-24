<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

$username = $_SESSION['user'];
$role = $_SESSION['role'];

// Get current user's borrowed books
$query = "
  SELECT b.title, br.date_borrowed, br.date_returned
  FROM borrows br
  JOIN books b ON br.book_id = b.book_id
  WHERE br.borrower_name = '$username'
  ORDER BY br.date_borrowed DESC
";

?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
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
  </style>
</head>
<body>
  <div class="logout-top">
    <a href="logout.php">Logout</a>
  </div>

  <div class="container dashboard-box">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>You are logged in as <strong><?php echo htmlspecialchars($role); ?></strong>.</p>

    <div class="button-grid">
      <?php if ($role === 'admin'): ?>
        <a href="add_book.php" class="dash-button">Add Book</a>
      <?php endif; ?>
      <a href="view_books.php" class="dash-button">View Books</a>
      <a href="borrow_book.php" class="dash-button">Borrow Book</a>
      <a href="return_book.php" class="dash-button">Return Book</a>
    </div>

    <h3>Your Borrowed Books</h3>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $borrowed_date = new DateTime($row['date_borrowed']);
          $today = new DateTime();
          $due_date = clone $borrowed_date;
          $due_date->modify('+7 days');

          $is_returned = !empty($row['date_returned']);
          $returned_date = $is_returned ? new DateTime($row['date_returned']) : null;

          // Determine fine if overdue
          $effective_return_date = $is_returned ? $returned_date : $today;
          $overdue_days = $due_date < $effective_return_date ? $due_date->diff($effective_return_date)->days : 0;
          $fine = $overdue_days * 10;
        ?>

        <div class="borrowed-card">
          <div class="borrowed-title"><?php echo htmlspecialchars($row['title']); ?></div>
          <div class="meta">
            Date Borrowed: <?php echo $borrowed_date->format('F j, Y'); ?><br>
            Due: <?php echo $due_date->format('F j, Y'); ?><br>
            Status: 
            <?php if ($is_returned): ?>
              Returned on <?php echo $returned_date->format('F j, Y'); ?>
            <?php else: ?>
              <strong style="color: red;">Not Returned</strong>
            <?php endif; ?><br>
            <?php if (!$is_returned && $fine > 0): ?>
              Fine: <span class="fine">₱<?php echo $fine; ?></span>
            <?php elseif (!$is_returned): ?>
              Days Left: <?php echo $today->diff($due_date)->format('%r%a'); ?>
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
