<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obsługa przycisku "Usuń dla tej kategorii"
    if (isset($_POST['deleteCategory'])) {
        $categoryIDToDelete = $_POST['categoryID'];

        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
    
        $deleteCategoryQuery = "DELETE FROM categories WHERE Category_ID = $categoryIDToDelete";

        if ($mysqli->query($deleteCategoryQuery)) {
            header('Location: admin_categories.php');
        } else {
            echo "Błąd podczas usuwania książki dla tej kategorii: " . $mysqli->error;
        }
        $mysqli->close();
    }
}
?>