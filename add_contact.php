<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodawanie Kontaktu</title>
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

        $telefon = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST["telefon"])));
        $email = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST["email"])));
        $user_id = $_SESSION['user_id'];

        

        $insertQuery = "INSERT INTO contacts (Client_ID,Telephone_Number,Email_address)
                    VALUES (?,?,?)";

        if ($stmt = $mysqli->prepare($insertQuery)) {
            $stmt->bind_param("iis",$user_id, $telefon, $email);

            if ($stmt->execute()) {
                header('Location: user_panel.php');
            } else {
                echo "Błąd podczas dodawania kontaktu. Spróbuj ponownie później.";
            }

            $stmt->close();
        } else {
            echo "Błąd przy przygotowaniu zapytania.";
        }

        $mysqli->close();
    }
?>
    <div class="login-container">
        <h1>Dodaj Kontakt</h1>
        <form action="add_contact.php" method="post">
            <div class="input-group">
                <label for="telefon">Numer telefonu</label>
                <input type="text"  id="telefon" name="telefon" maxlength="30" style="width: 200px;" required>
                <label for="email">Adres e-mail</label>
                <input type="text"  id="email" name="email" maxlength="30" style="width: 200px;" required>
            </div>
            
            <input type="submit" name="submit"class=inputbox value="Dodaj kontakt">
        </form>
    </div>
    <?php include './partials/footer.html'; ?>
</body>

</html>