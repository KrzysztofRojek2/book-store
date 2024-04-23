<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mysqli = new mysqli("localhost", "root", "", "bookstore");

    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    $review_id = $_POST['review_id'];
    $book_id = $_POST['book_id'];


    $deleteQuery = "DELETE FROM reviews WHERE Review_ID = $review_id";

    if ($mysqli->query($deleteQuery)) {
        header('Location: ../book.php?id=' . $book_id);
    } else {
        echo "Błąd podczas usuwania recenzji: " . $mysqli->error;
    }

    $mysqli->close();
} else {
    header("Location: index.php"); // Przekieruj na stronę główną
    exit();
}
?>