<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Obsługa przycisku "Usuń autora"
if (isset($_POST['deleteAuthor'])) {
    $authorIDToDelete = $_POST['authorID'];

    $mysqli = new mysqli("localhost", "root", "", "bookstore");

    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    // Usuń autora z bazy danych
    $deleteAuthorQuery = "DELETE FROM authors WHERE ID = $authorIDToDelete";

    if ($mysqli->query($deleteAuthorQuery)) {
        header('Location: admin_authors.php');
    } else {
        echo "Błąd podczas usuwania autora: " . $mysqli->error;
    }

    $mysqli->close();
}
}
?>