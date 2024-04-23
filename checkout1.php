<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <main>
        <?php include './partials/header.php'; ?>
        <div class="checkout-first-stage">
        <?php
            $mysqli = new mysqli("localhost", "root", "", "bookstore");

if ($mysqli->connect_error) {
    die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
}

if (isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['selected_address_id']) && isset($_POST['selected_contact_id'])) {
            $selectedAddressID = $_POST['selected_address_id'];
            $selectedContactID = $_POST['selected_contact_id'];
            $clientID = $_SESSION['user_id'];
            $statusID = 1; 

            $insertOrderQuery = "INSERT INTO orders (Status_ID, Client_ID, Contact_ID, Address_ID)
                                 VALUES ('$statusID', '$clientID', '$selectedContactID', '$selectedAddressID')";

            if ($mysqli->query($insertOrderQuery)) {
                echo "Zamówienie dodane do bazy danych.";
                header("Location: ./checkout2.php");
                exit();
            } else {
                echo "Błąd dodawania zamówienia do bazy danych: " . $mysqli->error;
            }
        }
    }

    $query = "SELECT addresses.ID AS address_id, Client_ID, City, Postcode, Street, Street_Number FROM addresses
        JOIN clients ON addresses.Client_ID = clients.ID
        WHERE clients.ID = '" . $_SESSION['user_id'] . "'";

    $result = $mysqli->query($query);
    
    echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
    echo '<div class="address-checkout">';
    echo '<h2>Wybierz adres dostawy: </h2>';
    while ($row = $result->fetch_assoc()) {
        $city = $row['City'];
        $postcode = $row['Postcode'];
        $street = $row['Street'];
        $streetNumber = $row['Street_Number'];
        $IDfield = $row['address_id'];
        echo '<div class="checkout-option">';
        echo '<input type="radio" name="selected_address_id" value="' . $IDfield . '">';
        echo '<article class="addressAndCategory">';
        echo '<p>Miasto: ' . $city . '</p>';
        echo '<p>Kod pocztowy: ' . $postcode . '</p>';
        echo '<p>Ulica: ' . $street . ' ' . $streetNumber . '</p>';
        echo '</div>';
        echo '</article>';
    }
    echo '</div>';

    $query = "SELECT contacts.ID AS contact_id, Client_ID, Telephone_Number, Email_address FROM contacts
        JOIN clients ON contacts.Client_ID = clients.ID
        WHERE clients.ID = '" . $_SESSION['user_id'] . "'";

    $result = $mysqli->query($query);
    echo '<div class="contact-checkout">';
    echo '<h2>Wybierz kontakt: </h2>';
    while ($row = $result->fetch_assoc()) {
        $telephoneNumber = $row['Telephone_Number'];
        $email = $row['Email_address'];
        $IDfield = $row['contact_id'];
        echo '<div class="checkout-option">';
        echo '<input type="radio" name="selected_contact_id" value="' . $IDfield . '">';
        echo '<article class="addressAndCategory">';
        echo '<p>Numer telefonu: ' . $telephoneNumber . '</p>';
        echo '<p>Adres email: ' . $email . '</p>';
        echo '</div>';
        echo '</article>';
    }
    echo '</div>';
    echo '<div class="first-checkout-btn">';
    echo '<button type="submit">Kontynuuj</button>';
    echo '</div>';
    echo '</form>';
}

?>
    </div>
    </main>

    <footer>
    <?php include './partials/footer.html'; ?>
    </footer>
</body>
<style>
    @media (max-width: 550px) {

        .checkout-option {
            padding: 8px;
            width: auto;
        }
        .first-checkout-btn {
            display: flex;
            justify-content: center;
        }
        h2 {
            font-size: 1rem;
        }
        p {
            font-size: 0.6rem;
        }
        input[type="radio"] {
        width: 15px;
        height: 15px;
    }

}
@media (max-width: 420px) {
    .checkout-first-stage {
        margin: 1rem 0.5rem;
    }

    p {
        font-size: 0.5rem;
    }


}
</style>
</html>