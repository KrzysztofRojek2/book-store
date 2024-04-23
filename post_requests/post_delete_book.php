<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteBook'])) {
    $mysqli = new mysqli("localhost", "root", "", "bookstore");

    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    // Obsługa usuwania książki
    $book_id_to_delete = $_POST['book_id'];

    // Usunięcie recenzji powiązanych z książką
    $deleteReviewsQuery = "DELETE FROM reviews WHERE Book_ID = $book_id_to_delete";
    if ($mysqli->query($deleteReviewsQuery)) {
        echo "Recenzje związane z książką zostały usunięte.";
    } else {
        echo "Błąd podczas usuwania recenzji: " . $mysqli->error;
    }

    $deleteBookCategoriesQuery = "DELETE FROM bookcategories WHERE Book_ID = $book_id_to_delete";

    if ($mysqli->query($deleteBookCategoriesQuery)) {
        $deleteBookQuery = "DELETE FROM books WHERE ID = $book_id_to_delete";

        if ($mysqli->query($deleteBookQuery)) {
            header('Location: admin_index.php');
        } else {
            echo "Błąd podczas usuwania książki: " . $mysqli->error;
        }
    } else {
        echo "Błąd podczas usuwania powiązanych rekordów z bookcategories: " . $mysqli->error;
    }

    $mysqli->close();
}
?>