<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php include './partials/header.php'; ?>

    <?php $mysqli = new mysqli("localhost", "root", "", "bookstore");?>

    <div class="login-container">
        <h1>Logowanie</h1>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <div class="input-group">
                <label for="username">Nazwa użytkownika</label>
                <input type="text" id="username" name="username" maxlength="30" style="width: 200px;" placeholder="Wprowadź nazwę użytkownika" required>
            </div>
            <div class="input-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" maxlength="30" style="width: 200px;" placeholder="Wprowadź hasło" required>
            </div>
            <input type="submit" name="sumbit" class=inputbox value="Zaloguj się">
        </form>
        <button><a style="color: white" href="./register.php" class=register>Rejestracja</a ></button>

    </div>
    <?php
        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['username'])));
        $password = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['password'])));
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // Szukanie clienta w bazie danych
        $query = "SELECT * FROM clients WHERE username = '$username'";
        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Sprawdzenie hasła
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['ID'];
                $_SESSION['IsAdmin'] = $row['IsAdmin'];

                // Sprawdzenie pola isAdmin
                if ($row['IsAdmin'] == true) {
                    header('Location: ./admin_panel/admin_index.php');
                } else {
                    header('Location: index.php');
                }

            } else {
                echo '<p class="error-info">Błędne hasło</p>';
            }
        } else {
            echo '<p class="error-info">Użytkownik nie istnieje</p>';
        }
        }
        $mysqli->close();
?>

    <?php include './partials/footer.html'; ?>
</body>
</html>

