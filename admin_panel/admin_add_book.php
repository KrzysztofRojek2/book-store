<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodawanie Książek</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <div class="login-container">
        <h1>Dodaj Książkę</h1>
        <form action="admin_add_book.php" method="post">
            <div class="input-group">
                <label for="tytul">Tytuł</label>
                <input type="text"  id="tytul" name="tytul" maxlength="30" style="width: 200px;" required>
            </div>
            <div class="input-group">
                <label for="strony">Ilość Stron</label>
                <input type="number" id="strony" name="strony" maxlength="30" style="width: 200px;" required>
            </div>
            <div class="input-group">
                <label for="cena">Cena</label>
                <input type="number" id="cena" name="cena" maxlength="3" style="width: 200px;" required>
            </div>
            <div class="input-group">
                <label for="opis">Opis</label>
                <textarea id="opis" name="opis"  required></textarea>
            </div>
            <div class="input-group">
                <label for="autor">Autor</label>
                <select id="autor" name="autor">
                    <?php
                    // Pobieranie autorów z bazy danych i generowanie opcji do wyboru
                    $mysqli = new mysqli("localhost", "root", "", "bookstore");

                    $query = "SELECT * FROM authors";
                    $result = $mysqli->query($query);

                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['ID'] . '">' . $row['Author_Name'] . '</option>';
                    }
                    $mysqli->close();
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label for="kategoria">Kategoria</label>
                <select id="kategoria" name="kategoria">
                    <?php
                    // Pobieranie kategorii z bazy danych i generowanie opcji do wyboru
                    $mysqli = new mysqli("localhost", "root", "", "bookstore");

                    $query = "SELECT * FROM categories";
                    $result = $mysqli->query($query);

                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['Category_ID'] . '">' . $row['Category_Name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <input type="submit" name="submit"class=inputbox value="Dodaj książkę">
        </form>
    </div>
    <?php include '../partials/footer.html'; ?>
    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tytul = $_POST["tytul"];
    $strony = $_POST["strony"];
    $cena = $_POST["cena"];
    $opis = $_POST["opis"];
    
    $autor = $_POST["autor"];//to id autora nie autor
    $kategoria = $_POST["kategoria"];// to samo co z autorem


    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    $insertQuery = "INSERT INTO books (Book_Title, NumberOfPages, Price, Description)
               VALUES (?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($insertQuery)) {
    $stmt->bind_param("siis", $tytul, $strony, $cena, $opis);

    if ($stmt->execute()) {
        $bookID = $mysqli->insert_id;  //ID nowo dodanej książki


        echo "<p>! $autor !</p>";

        $insertAuthorQuery = "INSERT INTO bookAuthors (Book_ID, Author_ID) VALUES (?, ?)";
        if ($stmtAuthor = $mysqli->prepare($insertAuthorQuery)) {
            $stmtAuthor->bind_param("ii", $bookID, $autor);
            $stmtAuthor->execute();
            $stmtAuthor->close();
        } else {
            echo "Błąd przy dodawaniu autora do książki.";
        }

        //echo "<p>! $kategoria !</p>";

        $insertCategoryQuery = "INSERT INTO bookCategories (Book_ID, Category_ID) VALUES (?, ?)";
        if ($stmtCategory = $mysqli->prepare($insertCategoryQuery)) {
            $stmtCategory->bind_param("ii", $bookID, $kategoria);
            $stmtCategory->execute();
            $stmtCategory->close();
        } else {
            echo "Błąd przy dodawaniu kategorii do książki.";
        }

        echo "Książka została dodana do bazy danych z autorem i kategorią.";
    } else {
        echo "Błąd podczas dodawania książki. Spróbuj ponownie później.";
    }

    $stmt->close();
} else {
    echo "Błąd przy przygotowaniu zapytania.";
}
    $mysqli->close();
}
?>
</body>
<style>
    textarea {
    margin-top: 1rem;
    width: 60%;
    height: 6rem;
}
</style>
</html>