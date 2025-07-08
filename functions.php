<?php
function generateBookID($title, $monthAbbreviations, $dayAdded, $yearPub, $category, $count) {
    $prefix = strtoupper(substr($title, 0, 2));
    $categoryAbbr = strtoupper(substr($category, 0, 5));
    return $prefix . $monthAbbreviations . $dayAdded . $yearPub . '-' . $categoryAbbr . str_pad($count, 5, '0', STR_PAD_LEFT);
}

function borrowBook($conn, $book_id, $user_id) {
    $borrowed_count_stmt = $conn->prepare("SELECT COUNT(*) AS borrowed_count FROM borrowings WHERE user_id = ? AND status = 'borrowed'");
    $borrowed_count_stmt->bind_param("i", $user_id);
    $borrowed_count_stmt->execute();
    $borrowed_count_result = $borrowed_count_stmt->get_result();
    $borrowed_data = $borrowed_count_result->fetch_assoc();
    $current_borrowed_books = $borrowed_data['borrowed_count'];
    $borrowed_count_stmt->close();

    //Define the borrowing limit
    $borrow_limit = 2; 

    //Check if the user has reached the limit
    if ($current_borrowed_books >= $borrow_limit) {
        return ["success" => false, "message" => "You have reached the maximum borrowing limit of " . $borrow_limit . " books."];
    }

    $stmt = $conn->prepare("SELECT available_copies FROM books WHERE book_id = ? AND status = 'available'");
    $stmt->bind_param("s", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    if ($book && $book['available_copies'] > 0) {
        $conn->begin_transaction();
        try {
            $update_stmt = $conn->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE book_id = ?");
            $update_stmt->bind_param("s", $book_id);
            $update_stmt->execute();
            $update_stmt->close();

            $borrow_stmt = $conn->prepare("INSERT INTO borrowings (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'borrowed')");
            $borrow_stmt->bind_param("is", $user_id, $book_id);
            $borrow_stmt->execute();
            $borrow_stmt->close();

            $check_copies_stmt = $conn->prepare("SELECT available_copies FROM books WHERE book_id = ?");
            $check_copies_stmt->bind_param("s", $book_id);
            $check_copies_stmt->execute();
            $check_copies_result = $check_copies_stmt->get_result();
            $remaining_copies = $check_copies_result->fetch_assoc()['available_copies'];
            $check_copies_stmt->close();

            if ($remaining_copies == 0) {
                $update_status_stmt = $conn->prepare("UPDATE books SET status = 'unavailable' WHERE book_id = ?");
                $update_status_stmt->bind_param("s", $book_id);
                $update_status_stmt->execute();
                $update_status_stmt->close();
            }

            $conn->commit();
            return ["success" => true, "message" => "Book borrowed successfully!"];
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            return ["success" => false, "message" => "Error borrowing book: " . $e->getMessage()];
        }
    } else {
        return ["success" => false, "message" => "Book not available or does not exist."];
    }
}

function archiveBook($conn, $book_id) {
    $stmt = $conn->prepare("UPDATE books SET status = 'archived' WHERE book_id = ?");
    $stmt->bind_param("s", $book_id);
    if ($stmt->execute()) {
        $stmt->close();
        return ["success" => true, "message" => "Book archived successfully."];
    } else {
        return ["success" => false, "message" => "Error archiving book: " . $stmt->error];
    }
}

function unarchiveBook($conn, $book_id) {
    $stmt = $conn->prepare("UPDATE books SET status = 'available' WHERE book_id = ?");
    $stmt->bind_param("s", $book_id);
    if ($stmt->execute()) {
        $stmt->close();
        return ["success" => true, "message" => "Book unarchived successfully."];
    } else {
        return ["success" => false, "message" => "Error unarchiving book: " . $stmt->error];
    }
}

function returnBook($conn, $borrow_id, $book_id) {
    $fine_per_day = 10; //your fine per day here

    $conn->begin_transaction();
    try {
        //1. Get borrowing details (due_date)
        $stmt = $conn->prepare("SELECT due_date FROM borrowings WHERE borrow_id = ? AND status = 'borrowed'");
        $stmt->bind_param("i", $borrow_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $borrowing_data = $result->fetch_assoc();
        $stmt->close();

        if (!$borrowing_data) {
            $conn->rollback();
            return ["success" => false, "message" => "Error: Borrowing record not found or already returned."];
        }

        $due_date = new DateTime($borrowing_data['due_date']);
        $return_date = new DateTime(); // Current date or time of return

        $fine_amount = 0;
        if ($return_date > $due_date) {
            $interval = $return_date->diff($due_date);
            $overdue_days = $interval->days;
            $fine_amount = $overdue_days * $fine_per_day;
        }

        //2. Update the borrowings table
        $update_borrowing_stmt = $conn->prepare("UPDATE borrowings SET return_date = CURDATE(), fine_amount = ?, status = 'returned' WHERE borrow_id = ?");
        $update_borrowing_stmt->bind_param("di", $fine_amount, $borrow_id); // 'd' for double/float, 'i' for int
        $update_borrowing_stmt->execute();
        if ($update_borrowing_stmt->affected_rows === 0) {
            $conn->rollback();
            return ["success" => false, "message" => "Error updating borrowing record. Book might already be returned."];
        }
        $update_borrowing_stmt->close();

        //3. Increment copies in books table
        $update_book_copies_stmt = $conn->prepare("UPDATE books SET available_copies = available_copies + 1 WHERE book_id = ?");
        $update_book_copies_stmt->bind_param("s", $book_id); // 's' for string book_id
        $update_book_copies_stmt->execute();
        $update_book_copies_stmt->close();

        // 4. Update book status
        $check_copies_stmt = $conn->prepare("SELECT available_copies FROM books WHERE book_id = ?");
        $check_copies_stmt->bind_param("s", $book_id);
        $check_copies_stmt->execute();
        $check_copies_result = $check_copies_stmt->get_result();
        $book_current_copies = $check_copies_result->fetch_assoc()['available_copies'];
        $check_copies_stmt->close();

        if ($book_current_copies > 0) {
            $update_book_status_stmt = $conn->prepare("UPDATE books SET status = 'available' WHERE book_id = ? AND status = 'unavailable'");
            $update_book_status_stmt->bind_param("s", $book_id);
            $update_book_status_stmt->execute();
            $update_book_status_stmt->close();
        }

        $conn->commit();

        $fine_message = ($fine_amount > 0) ? " with a fine of ₱" . number_format($fine_amount, 2) : "";
        return ["success" => true, "message" => "Book '" . $book_id . "' returned successfully" . $fine_message . "!"];

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        return ["success" => false, "message" => "Error returning book: " . $e->getMessage()];
    }
}

?>
