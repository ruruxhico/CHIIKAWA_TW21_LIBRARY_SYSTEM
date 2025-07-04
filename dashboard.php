<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

$username = $_SESSION['username'];
$user_type = $_SESSION['user_type'];

// Fetch user's borrowed books
$query = "
  SELECT b.title, br.borrow_date, br.return_date
  FROM borrowings br
  JOIN books b ON br.book_id = b.book_id
  WHERE br.user_id = '$username'
  ORDER BY br.borrow_date DESC
";

$result = $conn->query($query);
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

    
    <div class="button-grid">
      <?php if ($user_type === 'admin'): ?>
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
          $borrowed_date = new DateTime($row['borrow_date']);
          $today = new DateTime();
          $due_date = clone $borrowed_date;
          $due_date->modify('+7 days');

          $is_returned = $row['returned'] == 1;

          // Fine logic: ₱10/day after due date if not returned
          $effective_date = $is_returned ? $due_date : $today;
          $overdue_days = $due_date < $effective_date ? $due_date->diff($effective_date)->days : 0;
          $fine = (!$is_returned && $overdue_days > 0) ? $overdue_days * 10 : 0;
        ?>

        <div class="borrowed-card">
          <div class="borrowed-title"><?php echo htmlspecialchars($row['title']); ?></div>
          <div class="meta">
            Date Borrowed: <?php echo $borrowed_date->format('F j, Y'); ?><br>
            Due: <?php echo $due_date->format('F j, Y'); ?><br>
            Status: 
            <?php if ($is_returned): ?>
              Returned
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
