<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Księgarnia</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <?php include './partials/header.php'; ?>

    <main>
    <div class="user-panel-wrapper">
        
        <div class="address-wrapper">
        <h2>Adresy:</h2>
    <?php
        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
        if (isset($_SESSION['user_id'])) {
            $query = "SELECT addresses.ID AS address_id,Client_ID,City,Postcode,Street,Street_Number  FROM addresses
            JOIN clients ON addresses.Client_ID = clients.ID
            WHERE clients.ID = '" . $_SESSION['user_id'] . "'";

        $result = $mysqli->query($query);

        while ($row = $result->fetch_assoc()) {
            $city = $row['City'];
            $postcode = $row['Postcode'];
            $street = $row['Street'];
            $streetNumber = $row['Street_Number'];
            $IDfield = $row['address_id'];
            
            echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
            echo '<article class="address">';
            echo '<p>Miasto: ' . $city . '</p>';
            echo '<p>Kod pocztowy: ' . $postcode . '</p>';
            echo '<p>Ulica: ' . $street . ' ' . $streetNumber . '</p>';
            echo '<input type="hidden" name="address_id" value="' . $IDfield . '">'; 
            echo '<button class="delete-button" type="submit" name="deleteAddressFromList">Usuń z listy</button>';
            echo '</article>';
            echo '</form>';
        }
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteAddressFromList'])) {
            if (isset($_SESSION['user_id'])) {

                    // Usuń książkę z koszyka
                    $removeQuery = "DELETE FROM addresses WHERE ID = ?";

                    if ($stmtRemove = $mysqli->prepare($removeQuery)) {
                        $stmtRemove->bind_param("i", $IDfield);

                        if ($stmtRemove->execute()) {
                            header('Location: user_panel.php');
                        } else {
                            echo "Błąd podczas usuwania.";
                        }

                        $stmtRemove->close();
                        }
                    
            } else {
                echo "Musisz być zalogowany, aby usunąć adres z listy.";
            }
        }
        if ($result->num_rows <= 0) {
            echo'<p class="no-contact-found">Nie ma żadnego adresu!</p>';
        }
        ?>
        <a href="./add_address.php"><button class="add-button";>Dodaj adres</button></a>

        </div>
        <div class="contact-wrapper">
        <h2>Kontakty:</h2>

        <?php

            if (isset($_SESSION['user_id'])) {
                $query = "SELECT contacts.ID AS contact_id,Client_ID,Telephone_Number,Email_address  FROM contacts
                JOIN clients ON contacts.Client_ID = clients.ID
                WHERE clients.ID = '" . $_SESSION['user_id'] . "'";

            $result = $mysqli->query($query);

            while ($row = $result->fetch_assoc()) {
                $telephoneNumber = $row['Telephone_Number'];
                $email = $row['Email_address'];
                $IDfield = $row['contact_id'];
                
                echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
                echo '<article class="contact">';
                echo '<p>Adres email: ' . $email . '</p>';
                echo '<p>Numer telefonu: ' . $telephoneNumber . '</p>';
                echo '<input type="hidden" name="address_id" value="' . $IDfield . '">'; 
                echo '<button class="delete-button" type="submit" name="deleteContactFromList">Usuń z listy</button>';
                echo '</article>';
                echo '</form>';
            }
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteContactFromList'])) {
                if (isset($_SESSION['user_id'])) {

                        // Usuń książkę z koszyka
                        $removeQuery = "DELETE FROM contacts WHERE ID = ?";

                        if ($stmtRemove = $mysqli->prepare($removeQuery)) {
                            $stmtRemove->bind_param("i", $IDfield);

                            if ($stmtRemove->execute()) {
                                // header('Location:user_panel.php');
                            } else {
                                echo "Błąd podczas usuwania.";
                            }

                            $stmtRemove->close();
                            }
                        
                } else {
                    echo "Musisz być zalogowany, aby usunąć kontakt z listy.";
                }
            }
            if ($result->num_rows <= 0) {
                echo'<p class="no-contact-found">Nie ma żadnego kontaktu!</p>';
            }
            $mysqli->close();
            ?>        
            <a href="./add_contact.php"><button class="add-button">Dodaj kontakt</button></a>
        </div>
    </div>
    </main>

    <?php include './partials/footer.html'; ?>

</body>
<style>
    @media (max-width: 1100px) {
        .contact p,
        .address p {
            font-size: 0.8rem;
        }
        .contact,
        .address {
        padding: 1.5rem;
        margin: 1.5rem auto;
        min-width: 190px;
        /* max-width: 800px; */

    }
    .user-panel-wrapper form,
        .user-panel-wrapper h2,
        .user-panel-wrapper .no-contact-found,
        .user-panel-wrapper .no-address-found,
        .user-panel-wrapper button {
            margin: 2rem;
        }

}
    @media (max-width: 800px) {
        .user-panel-wrapper form,
        .user-panel-wrapper h2,
        .user-panel-wrapper .no-contact-found,
        .user-panel-wrapper .no-address-found,
        .user-panel-wrapper button {
            margin: 1rem;
        }

    }

    @media (max-width: 600px) {

    .user-panel-wrapper {
        flex-direction: column;
        align-items: center;
    }
    .contact,
    .address {
        min-width: 300px;

    }

    }

    @media (max-width: 350px) {

    .user-panel-wrapper {
        flex-direction: column;
        align-items: center;
    }
    .contact,
    .address {
        min-width: 200px;

    }

    }
</style>
</html>