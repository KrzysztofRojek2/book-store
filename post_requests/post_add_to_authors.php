<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addBook'])) {
        $bookIDToAdd = $_POST['book_id'];
        $authorIDToAdd = $_POST['author_id'];

        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }

        // Przygotowanie przygotowanej instrukcji SQL
        $insertQuery = "INSERT INTO bookauthors (Author_ID, Book_ID) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insertQuery);

        // Sprawdzenie, czy przygotowanie zapytania zakończyło się powodzeniem
        if ($stmt) {
            // Przypisanie wartości i wykonanie przygotowanego zapytania
            $stmt->bind_param("ii", $authorIDToAdd, $bookIDToAdd);
            $stmt->execute();

            // Zamknięcie przygotowanego zapytania
            $stmt->close();

            header('Location: admin_add_to_authors.php?authorID=' . $authorIDToAdd);
        } else {
            echo "Błąd przygotowywania zapytania SQL: " . $mysqli->error;
        }
        
        $mysqli->close();
    }
}
    
?>