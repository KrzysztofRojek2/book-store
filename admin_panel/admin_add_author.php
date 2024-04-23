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
        <h1>Dodaj Autora</h1>
        <form action="admin_add_author.php" method="post">
            <div class="input-group">
                <label for="autor">Imie i nazwisko autora</label>
                <input type="text"  id="autor" name="autor" maxlength="30" style="width: 200px;" required>
            </div>
            
            <input type="submit" name="submit"class=inputbox value="Dodaj autora">
        </form>
    </div>
    <?php include '../partials/footer.html'; ?>
</body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $autor = $_POST["autor"];

    $mysqli = new mysqli("localhost", "root", "", "bookstore");


    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    $insertQuery = "INSERT INTO authors (Author_Name)
                   VALUES (?)";

    if ($stmt = $mysqli->prepare($insertQuery)) {
        $stmt->bind_param("s", $autor);

        if ($stmt->execute()) {
            echo "Autor został dodany do bazy danych.";
        } else {
            echo "Błąd podczas dodawania autora. Spróbuj ponownie później.";
        }

        $stmt->close();
    } else {
        echo "Błąd przy przygotowaniu zapytania.";
    }

    $mysqli->close();
}
?>
</html>