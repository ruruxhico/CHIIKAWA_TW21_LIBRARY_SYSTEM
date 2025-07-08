<?php
include "db.php";
include "functions.php";
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    die("Unauthorized access.");
}

$message = '';

$months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

//associative array for month abbreviations
$monthAbbreviations = [
    'January' => 'JAN', 'February' => 'FEB', 'March' => 'MAR', 'April' => 'APR',
    'May' => 'MAY', 'June' => 'JUN', 'July' => 'JUL', 'August' => 'AUG',
    'September' => 'SEP', 'October' => 'OCT', 'November' => 'NOV', 'December' => 'DEC'
];


$currentYear = date('Y');
$years = range($currentYear, $currentYear - 100);

$categories = [
    'Fiction', 'Science', 'History', 'Biography', 'Fantasy', 'Mystery',
    'Technology', 'Art', 'Cooking', 'Travel', 'Self-Help', 'Children', 'Dystopian',
    'Romance', 'Horror', 'Comics', 'Graphic Novels', 'Poetry', 'Philosophy', 'Religion', 'Sports',
    'Health', 'Business', 'Politics', 'Education', 'Science Fiction', 'Adventure', 'Classics', 'Young Adult',
    'Thriller', 'Western', 'True Crime', 'Anthology', 'Memoir', 'Essays', 'Short Stories', 'Drama', 'Mythology',
    'Psychology', 'Economics', 'Environment', 'Nature', 'Parenting', 'Pets', 'Gardening', 'Home Improvement',
    'Music', 'Film', 'Television', 'Dance', 'Theater'
];
sort($categories);

$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$pubMonth = $_POST['pubMonth'] ?? '';
$yearPub = $_POST['yearPub'] ?? '';
$category = $_POST['category'] ?? '';
$numCopies = (int)($_POST['numCopies'] ?? 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author'] ?? '');
    $pubMonth = trim($_POST['pubMonth']);
    $yearPub = trim($_POST['yearPub']);
    $category = trim($_POST['category']);
    $numCopies = (int)$_POST['numCopies'];
    $dayAdded = date('d');

    //Validation
    if (empty($title) || empty($author) || empty($pubMonth) || empty($yearPub) || empty($category) || empty($numCopies)) {
        $message = "Please fill in all required fields.";
    } elseif (!in_array($pubMonth, $months)) { // Uses $months for validation
        $message = "Invalid month selected.";
    } elseif (!is_numeric($yearPub) || !in_array($yearPub, $years)) {
        $message = "Invalid year selected.";
    } elseif (!in_array($category, $categories)) {
        $message = "Invalid category selected.";
    } elseif ($numCopies <= 0) {
        $message = "Number of Copies must be at least 1.";
    } else {
        //File Upload Logic (no changes here)
        $uploadDir = 'uploads/covers/';
        $cover_image_path = null;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['cover_image']['tmp_name'];
            $fileName = $_FILES['cover_image']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $cover_image_path = $destPath;
                } else {
                    $message = "Error uploading file. Check directory permissions.";
                }
            } else {
                $message = "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
            }
        } elseif (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] != UPLOAD_ERR_NO_FILE) {
            $message = "File upload error: " . $_FILES['cover_image']['error'];
            switch ($_FILES['cover_image']['error']) {
                case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE: $message = "Uploaded file is too large. Max size is " . ini_get('upload_max_filesize'); break;
                case UPLOAD_ERR_PARTIAL: $message = "File was only partially uploaded."; break;
                case UPLOAD_ERR_NO_TMP_DIR: $message = "Missing a temporary folder."; break;
                case UPLOAD_ERR_CANT_WRITE: $message = "Failed to write file to disk."; break;
                case UPLOAD_ERR_EXTENSION: $message = "A PHP extension stopped the file upload."; break;
                default: $message = "Unknown upload error."; break;
            }
        }
        //End File Upload Logic

        if (empty($message) || (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == UPLOAD_ERR_NO_FILE)) {
            $count_query = $conn->query("SELECT COUNT(*) as cnt FROM books");
            if ($count_query) {
                $book_sequence_number = $count_query->fetch_assoc()['cnt'] + 1;
            } else {
                $message = "Error getting book count: " . $conn->error;
                $book_sequence_number = 0;
            }

            if ($book_sequence_number > 0 || $message == "File upload error: 4") {
                // *** THIS IS THE KEY CHANGE for month abbreviation ***
                // Look up the abbreviation using the full month name from the form
                $monthAbbrForID = $monthAbbreviations[$pubMonth] ?? $pubMonth; // Fallback to full name if somehow not found

                // Generate book ID using the LOOKED-UP ABBREVIATION
                $bookID = generateBookID($title, $monthAbbrForID, $dayAdded, $yearPub, $category, $numCopies);

                // Use prepared statement for security
                $stmt = $conn->prepare("INSERT INTO books (book_id, title, author, month_published, year_published, category, cover_image, date_added, count_in_library, available_copies, status) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, 'available')");

                // *** THIS IS THE KEY CHANGE for bind_param string ***
                $stmt->bind_param("ssssissii",
                    $bookID,
                    $title,
                    $author,
                    $pubMonth,
                    $yearPub,            
                    $category,
                    $cover_image_path,
                    $numCopies,
                    $numCopies
                );

                if ($stmt->execute()) {
                    $message = "Book Added: " . htmlspecialchars($title) . " (ID: $bookID) with $numCopies copies.";
                    // Clear form fields after successful submission
                    $title = $author = $pubMonth = $yearPub = $category = '';
                    $numCopies = 1; // Reset numCopies to default
                    $_POST = array(); // Clear $_POST to prevent resubmission on refresh
                } else {
                    $message = "Error adding book to database: " . $stmt->error;
                    // If DB insert fails, consider deleting the uploaded file to clean up
                    if ($cover_image_path && file_exists($cover_image_path)) {
                        unlink($cover_image_path);
                    }
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-add-book">
        <h2>Add New Book</h2>
        <?php if ($message): ?>
            <p class="message <?php echo (strpos($message, 'Error') !== false || strpos($message, 'Invalid') !== false || strpos($message, 'failed') !== false) ? 'error' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label form="title">Title:</label>
            <input type="text" name="title" id="title" required value="<?php echo htmlspecialchars($title); ?>"><br>

            <label form="author">Author:</label>
            <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($author); ?>"><br>

            <label for="pubMonth">Month Published:</label>
            <select name="pubMonth" id="pubMonth" required>
                <option value="">Select Month</option>
                <?php foreach ($months as $month): ?>
                    <option value="<?php echo htmlspecialchars($month); ?>" <?php echo ($pubMonth == $month) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($month); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="yearPub">Year Published:</label>
            <select name="yearPub" id="yearPub" required>
                <option value="">Select Year</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?php echo htmlspecialchars($year); ?>" <?php echo ($yearPub == $year) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($year); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="category">Category/Genre:</label>
            <select name="category" id="category" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat_name): ?>
                    <option value="<?php echo htmlspecialchars($cat_name); ?>" <?php echo ($category == $cat_name) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat_name); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="numCopies">Number of Copies:</label>
            <input type="number" name="numCopies" id="numCopies" required min="1" value="<?php echo htmlspecialchars($numCopies); ?>"><br>

            <label for="cover_image">Book Cover Image:</label>
            <input type="file" name="cover_image" id="cover_image" accept="image/jpeg, image/png, image/gif"><br>

            <button type="submit">Add Book</button>
        </form>
        <p class="back-link"><a href="dashboard.php">← Back to Dashboard</a></p>
    </div>
</body>
</html>