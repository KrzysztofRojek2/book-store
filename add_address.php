<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodawanie Adresu</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <?php include './partials/header.php'; ?>
    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mysqli = new mysqli("localhost", "root", "", "bookstore");


    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    $miasto = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST["miasto"])));
    $kodPocztowy = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST["kodPocztowy"])));
    $ulica = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST["ulica"])));
    $nrBudynku = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST["nrBudynku"])));
    $user_id = $_SESSION['user_id'];


    
    $insertQuery = "INSERT INTO addresses (Client_ID,City,Postcode,Street,Street_Number)
                   VALUES (?,?,?,?,?)";

    if ($stmt = $mysqli->prepare($insertQuery)) {
        $stmt->bind_param("isssi",$user_id, $miasto, $kodPocztowy, $ulica, $nrBudynku);

        if ($stmt->execute()) {
            header('Location: user_panel.php');
        } else {
            echo "Błąd podczas dodawania kategorii. Spróbuj ponownie później.";
        }

        $stmt->close();
    } else {
        echo "Błąd przy przygotowaniu zapytania.";
    }

    $mysqli->close();
}
?>


    <div class="login-container">
        <h1>Dodaj Adres</h1>
        <form action="add_address.php" method="post">
            <div class="input-group">
                <label for="miasto">Miasto</label>
                <input type="text"  id="miasto" name="miasto" maxlength="30" style="width: 200px;" required>
                <label for="kodPocztowy">Kod Pocztowy</label>
                <input type="text"  id="kodPocztowy" name="kodPocztowy" maxlength="30" style="width: 200px;" required>
                <label for="ulica">Ulica</label>
                <input type="text"  id="ulica" name="ulica" maxlength="30" style="width: 200px;" required>
                <label for="nrBudynku">Numer budynku</label>
                <input type="text"  id="nrBudynku" name="nrBudynku" maxlength="30" style="width: 200px;" required>
            </div>
            
            <input type="submit" name="submit"class=inputbox value="Dodaj adres">
        </form>
    </div>
    <?php include './partials/footer.html'; ?>
</body>

</html>