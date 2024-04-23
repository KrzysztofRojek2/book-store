<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obsługa przycisku "Usuń dla tego autora"
    if (isset($_POST['deleteBookFromAuthor'])) {
        $authorIDToDelete = $_POST['authorID'];
        $bookIDToDelete = $_POST['bookID'];

        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }

        // Usuń wpis z bookauthors dla danego autora i książki
        $deleteQuery = "DELETE FROM bookauthors WHERE Author_ID = $authorIDToDelete AND Book_ID = $bookIDToDelete";

        if ($mysqli->query($deleteQuery)) {
            header('Location: admin_authors.php');
            exit();
        } else {
            echo "Błąd podczas usuwania książki dla tego autora: " . $mysqli->error;
        }

        $mysqli->close();
    }
}
?>