<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Książkę</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <main>
        <?php
        $mysqli = new mysqli("localhost", "root", "", "bookstore");


        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
        
        if(isset($_GET['id'])) {
            $book_id_to_edit = $_GET['id'];

            $query = "SELECT * FROM books WHERE ID = ?";
            if ($stmt = $mysqli->prepare($query)) {
                $stmt->bind_param("i", $book_id_to_edit);
                $stmt->execute();
                $result = $stmt->get_result();
                $bookData = $result->fetch_assoc();
                $stmt->close();
            }

            if ($bookData) {
                echo '<div class="edit-book-div">';
                    echo '<form action="' . ($_SERVER["PHP_SELF"]) . '" method="post">';
                        echo '<div class="input-group">';
                            echo '<label for="tytul">Tytuł</label>';
                            echo '<input type="text" id="tytul" name="tytul" value="' . $bookData['Book_Title'] . '" maxlength="30" style="width: 200px;" required>';
                        echo '</div>';
                        echo '<div class="input-group">';
                            echo '<label for="strony">Ilość Stron</label>';
                            echo '<input type="number" id="strony" name="strony" value="' . $bookData['NumberOfPages'] . '" maxlength="30" style="width: 200px;" required>';
                        echo '</div>';
                        echo '<div class="input-group">';
                            echo '<label for="cena">Cena</label>';
                            echo '<input type="number" id="cena" name="cena" value="' . $bookData['Price'] . '" maxlength="3" style="width: 200px;" required>';
                        echo '</div>';
                        echo '<div class="input-group">';
                            echo '<label for="opis">Opis</label>';
                            echo '<textarea id="opis" name="opis" required>' . $bookData['Description'] . '</textarea>';
                        echo '</div>';
                        echo '<div class="pseudo-buttons">';
                            echo '<input type="hidden" name="book_id" value="' . $book_id_to_edit . '">';
                            echo '<input type="submit" name="submit" class="inputbox" value="Zapisz zmiany">';
                        echo '</div>';
                    
                    echo '</form>';
                } else {
                    echo 'Błąd: Książka o podanym ID nie istnieje.';
                }
            } else {
                // Obsługa błędu braku ID książki w parametrze URL
                echo "Błąd: Brak ID książki w parametrze URL.";
            }
                echo '<div class="pseudo-buttons">';
                    echo '<a href="./admin_index.php"><button>Anuluj zmiany</button></a>';
                echo '</div>';
            echo '</div>';
            // Obsługa zapisu zmian w bazie danych
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
                $tytul = $_POST["tytul"];
                $strony = $_POST["strony"];
                $cena = $_POST["cena"];
                $opis = $_POST["opis"];
                $book_id_to_edit = $_POST["book_id"];

                $updateQuery = "UPDATE books SET Book_Title=?, NumberOfPages=?, Price=?, Description=? WHERE ID=?";
                if ($stmtUpdate = $mysqli->prepare($updateQuery)) {
                    $stmtUpdate->bind_param("siisi", $tytul, $strony, $cena, $opis, $book_id_to_edit);
                    if ($stmtUpdate->execute()) {
                        header('Location: admin_index.php');
                    } else {
                        echo 'Błąd podczas aktualizacji książki.';
                    }
                    $stmtUpdate->close();
                } else {
                    echo 'Błąd przy przygotowaniu zapytania.';
                }
            }
        

        $mysqli->close();
        ?>
    </main>

    <?php include '../partials/footer.html'; ?>

</body>
<style>
    textarea {
    margin-top: 1rem;
    width: 60%;
    min-height: 6rem;
}
</style>
</html>