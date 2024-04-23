<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obsługa przycisku "Usuń dla tej kategorii"
    if (isset($_POST['deleteBookFromCategories'])) {
        $categoryIDToDelete = $_POST['categoryID'];
        $bookIDToDelete = $_POST['bookID'];

        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
    
        $deleteQuery = "DELETE FROM bookcategories WHERE Category_ID = $categoryIDToDelete AND Book_ID = $bookIDToDelete";

        if ($mysqli->query($deleteQuery)) {
            header('Location: admin_categories.php');
            exit();
        } else {
            echo "Błąd podczas usuwania książki dla tej kategorii: " . $mysqli->error;
        }
        $mysqli->close();
    }
}
?>