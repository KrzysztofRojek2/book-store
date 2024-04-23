<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php $mysqli = new mysqli("localhost", "root", "", "bookstore");?>

    <?php include './partials/header.php'; ?>

    <div class="login-container">
        <h1>Rejestracja</h1>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <div class="input-group">
                <label for="username">Nazwa użytkownika</label>
                <input type="text" id="username" name="username" maxlength="30" style="width: 200px;" placeholder="Wprowadź nazwę użytkownika" required>
            </div>
            <div class="input-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" maxlength="30" style="width: 200px;" placeholder="Wprowadź hasło" required>
            </div>
            <div class="input-group">
                <label for="imie">Imię</label>
                <input type="text" id="imie" name="imie" maxlength="30" style="width: 200px;" placeholder="Wprowadź imię" required>
            </div>
            <div class="input-group">
                <label for="nazwisko">Nazwisko</label>
                <input type="text" id="nazwisko" name="nazwisko" maxlength="30" style="width: 200px;" placeholder="Wprowadź nazwisko" required>
            </div>
            

                <input type="submit" name="sumbit" class=inputbox value="Zarejestruj się">
        </form>
    </div>
    <?php include './partials/footer.html'; ?>
</body>
</html>

<?php

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $mysqli = new mysqli("localhost", "root", "", "bookstore");
//     $password = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['password'])));
//     $username = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['username'])));
//     $name = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['imie'])));
//     $lastName = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['nazwisko'])));
//     // $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
//     // $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
//     // $name = filter_input(INPUT_POST, "imie", FILTER_SANITIZE_SPECIAL_CHARS);  // Poprawka
//     // $lastName = filter_input(INPUT_POST, "nazwisko", FILTER_SANITIZE_SPECIAL_CHARS);  // Poprawka


//     $errors = [];

//     if (empty($username) || empty($password)) {
//         $errors[] = "Wszystkie pola są wymagane.";
//     }

//     if (!empty($password)) {
//         // Hashowanie
//         $hash = password_hash($password, PASSWORD_DEFAULT);
//     }
//     // Wyświetlanie bledow
//     if (!empty($errors)) {
//         foreach ($errors as $error) {
//             echo '<p>' . $error . '</p>';
//         }
//     } else {
        
//     $insertQuery = "INSERT INTO clients (username, password, first_name, last_name) VALUES (?, ?,?,?)";

//     // Prepared statements, aby uniknąć SQL Injection
//     if ($stmt = $mysqli->prepare($insertQuery)) {
//         $stmt->bind_param("ssss", $username, $hash,$name,$lastName);

//         if ($stmt->execute()) {
//             // Pobierz ID ostatnio wstawionego rekordu (nowo zarejestrowanego klienta)
//             $clientID = $stmt->insert_id;

//             // Stwórz rekord "shoppingCart" i przypisz go do nowo zarejestrowanego klienta
//             $createCartQuery = "INSERT INTO shoppingCart (Client_ID) VALUES (?)";
//             if ($stmt = $mysqli->prepare($createCartQuery)) {
//                 $stmt->bind_param("i", $clientID);
//                 if ($stmt->execute()) {
//                     $getShoppingCart = "SELECT * FROM shoppingCart WHERE Client_ID = ?";
                    
//                     $getShoppingCart->bind_param("i", $clientID);//do poprawy
//                     $result = $mysqli->query($getShoppingCart);

//                     while ($row = $result->fetch_assoc()) {
//                         $cart_id = $row['Cart_ID'];
//                         $updateQuery = "UPDATE clients SET shoppingCart_ID = ? WHERE username = ?";



//                         if ($stmtUpdate = $mysqli->prepare($updateQuery)) {
//                             $stmtUpdate->bind_param("ii", $cart_id, $username);

//                             if ($stmtUpdate->execute()) {
//                                 echo "Rejestracja zakończona sukcesem. Możesz teraz zalogować się na swoje konto.";
//                             } else {
//                                 echo "Błąd podczas dodawania książki do koszyka.";
//                             }
//                     }
//                 }


//                 } else {
//                     echo "Błąd podczas tworzenia koszyka. Spróbuj ponownie później.";
//                 }
//             } else {
//                 echo "Błąd przy przygotowaniu zapytania.";
//             }
//         } else {
//             echo "Błąd podczas rejestracji. Spróbuj ponownie później.";
//         }

//         $stmt->close();
//     } else {
//         echo "Błąd przy przygotowaniu zapytania.";
//     }
//         }

// $mysqli->close();
// }
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mysqli = new mysqli("localhost", "root", "", "bookstore");

    // Przygotowanie danych wejściowych
    $password = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['password'])));
    $username = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['username'])));
    $name = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['imie'])));
    $lastName = trim(htmlspecialchars(mysqli_real_escape_string($mysqli, $_POST['nazwisko'])));

    $errors = [];

    // Walidacja danych
    if (empty($username) || empty($password)) {
        $errors[] = "Wszystkie pola są wymagane.";
    }

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p>' . $error . '</p>';
        }
    } else {
        $insertQuery = "INSERT INTO clients (username, password, first_name, last_name) VALUES (?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($insertQuery)) {
            $stmt->bind_param("ssss", $username, $hash, $name, $lastName);
            if ($stmt->execute()) {
                $clientID = $mysqli->insert_id;
                $stmt->close();

                // Tworzenie koszyka
                $createCartQuery = "INSERT INTO shoppingCart (Client_ID) VALUES (?)";
                if ($stmt = $mysqli->prepare($createCartQuery)) {
                    $stmt->bind_param("i", $clientID);
                    if ($stmt->execute()) {
                        $cartID = $mysqli->insert_id;
                        $stmt->close();
                        // Aktualizacja rekordu klienta o ID koszyka
                    $updateClientQuery = "UPDATE clients SET shoppingCart_ID = ? WHERE ID = ?";
                    if ($updateStmt = $mysqli->prepare($updateClientQuery)) {
                        $updateStmt->bind_param("ii", $cartID, $clientID);
                        if ($updateStmt->execute()) {
                            echo '<script>alert("Rejestracja zakończona sukcesem. Możesz teraz zalogować się na swoje konto.");</script>';
                            // echo "Rejestracja zakończona sukcesem. Możesz teraz zalogować się na swoje konto.";
                        } else {
                            echo "Błąd podczas aktualizacji klienta z ID koszyka.";
                        }
                        $updateStmt->close();
                    } else {
                        echo "Błąd przy przygotowaniu zapytania do aktualizacji klienta.";
                    }
                } else {
                    echo "Błąd podczas tworzenia koszyka. Spróbuj ponownie później.";
                }
            } else {
                echo "Błąd przy przygotowaniu zapytania do tworzenia koszyka.";
            }
        } else {
            echo "Błąd podczas rejestracji. Spróbuj ponownie później.";
        }
    } else {
        echo "Błąd przy przygotowaniu zapytania do rejestracji klienta.";
    }
}
$mysqli->close();
}
?>