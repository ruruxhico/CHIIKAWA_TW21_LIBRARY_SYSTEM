<?php
include "db.php";
$result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Books</title>
  <link rel="stylesheet" href="style.css">
  <style>
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

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(10px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>View Books</h2>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="book-card">
          <a href="<?php echo $row['thumbnail'] ?? '#'; ?>" target="_blank">
            <img class="book-thumb" src="<?php echo $row['thumbnail'] ?? 'default.jpg'; ?>" alt="Thumbnail">
          </a>

          <div class="book-details">
            <div class="book-title"><?php echo htmlspecialchars($row['title']); ?></div>
            <div class="book-meta">
              Book ID: <?php echo $row['book_id']; ?><br>
              Category: <?php echo htmlspecialchars($row['category']); ?><br>
              Status: <?php echo htmlspecialchars($row['status']); ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No books found.</p>
    <?php endif; ?>
  </div>
</body>
</html>
