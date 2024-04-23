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
        <?php
            $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
        if (isset($_SESSION['user_id'])) {
            echo '<section class="booksInCart">';
            $query = "SELECT books.Book_Title, books.Price, books.ID
            FROM books
            JOIN booksInCarts ON books.ID = booksInCarts.Book_ID
            JOIN clients ON booksInCarts.ShoppingCart_ID = clients.ShoppingCart_ID
            WHERE clients.ID = '" . $_SESSION['user_id'] . "'";

        $result = $mysqli->query($query);
            $totalPrice=0;
        while ($row = $result->fetch_assoc()) {
            $bookTitle = $row['Book_Title'];
            $price = $row['Price'];
            $imagePath = './resources/' . $row['ID'] . '.png';
            $IDfield = $row['ID'];
            
            $price = $row['Price'];
            $totalPrice += $price;

            //Wyświetlanie książek pojedynczo
            echo '<article style="width: 45%;" class="bookInCart">';
            echo '<a href="book.php?id=' . $IDfield . '">';
            echo '<img src="' . $imagePath . '" alt="' . $bookTitle . '">';
            echo '</a>';
            echo '<h2>' . $bookTitle . '</h2>';
            echo '<p>Cena: $' . $price . '</p>';
            echo '</article>';
            }
            echo '</section">';
            }?>

            <?php
            // Pobranie opcji dostawy z bazy danych
            $shippingOptionsQuery = "SELECT * FROM shipping";
            $shippingOptionsResult = $mysqli->query($shippingOptionsQuery);

            // Pobranie opcji płatności z bazy danych
            $paymentOptionsQuery = "SELECT * FROM payments";
            $paymentOptionsResult = $mysqli->query($paymentOptionsQuery);

            // Pobranie adresów klienta
            $addressesQuery = "SELECT ID, City, Postcode, Street, Street_Number FROM addresses WHERE Client_ID = $user_id";
            $addressesResult = $mysqli->query($addressesQuery);

            // Pobranie kontaktów klienta
            $contactsQuery = "SELECT ID, Telephone_Number, Email_address FROM contacts WHERE Client_ID = $user_id";
            $contactsResult = $mysqli->query($contactsQuery);

            if ($shippingOptionsResult && $paymentOptionsResult) {
                // Wyświetlanie opcji dostawy
                echo '<section class="shipping-options">';
                echo '<form action="checkout.php" method="post">';
                echo '<label for="shipping">Wybierz opcję dostawy:</label>';
                echo '<select name="shipping" id="shipping">';
                while ($shippingOption = $shippingOptionsResult->fetch_assoc()) {
                    echo '<option value="' . $shippingOption['ID'] . '">' . $shippingOption['Name'] . ' - $' . $shippingOption['Price'] . '</option>';
                }
                $totalPrice += $shippingOption['Price'];
                echo '</select>';
                echo '</section>';

                // Wyświetlanie opcji płatności
                echo '<section class="payment-options">';
                echo '<label for="payment">Wybierz opcję płatności:</label>';
                echo '<select name="payment" id="payment">';
                while ($paymentOption = $paymentOptionsResult->fetch_assoc()) {
                    echo '<option value="' . $paymentOption['Payment_ID'] . '">' . $paymentOption['Payment_Type'] . '</option>';
                }
                echo '</select>';
                echo '</section>';


// Wyświetlanie opcji dostawy
echo '<section class="address-options">';
echo '<form action="checkout.php" method="post">';
echo '<label for="address">Wybierz adres dostawy:</label>';
echo '<select name="address" id="address">';

while ($addressRow = $addressesResult->fetch_assoc()) {
    // Przypisanie wartości do zmiennej $address
    $address = $addressRow;
    echo '<option value="address_' . $address['ID'] . '">';
    echo $address['City'] . ', ' . $address['Postcode'] . ', ' . $address['Street'] . ' ' . $address['Street_Number'];
    echo '</option>';
}

echo '</select>';
echo '</section>';

// Wyświetlanie opcji płatności
echo '<section class="contact-options">';
echo '<label for="contact">Wybierz kontakt:</label>';
echo '<select name="contact" id="contact">';

while ($contactRow = $contactsResult->fetch_assoc()) {
    // Przypisanie wartości do zmiennej $contact
    $contact = $contactRow;
    echo '<option value="contact_' . $contact['ID'] . '">';
    echo 'Tel: ' . $contact['Telephone_Number'] . ', Email: ' . $contact['Email_address'];
    echo '</option>';
}

echo '</select>';
echo '</section>';


                // Wyświetlanie łącznej ceny
                echo '<section class="total-price">';
                echo '<p>Łączna cena: $' . $totalPrice . '</p>';
                echo '</section>';


                // Przycisk potwierdzający zamówienie
                echo '<button type="submit" name="confirm-order">Zamawiam i płace</button>';
                echo '</form>';
            } else {
                echo 'Błąd pobierania opcji dostawy i płatności.';
            }
        ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm-order'])) {

    $shippingOptionId = $_POST['shipping'];
    $paymentOptionId = $_POST['payment'];

    // Pobranie danych o dostawie z bazy danych
    $shippingQuery = "SELECT * FROM shipping WHERE ID = $shippingOptionId";
    $shippingResult = $mysqli->query($shippingQuery);
    $shippingOption = $shippingResult->fetch_assoc();

    // Pobranie danych o płatności z bazy danych
    $paymentQuery = "SELECT * FROM payments WHERE Payment_ID = $paymentOptionId";
    $paymentResult = $mysqli->query($paymentQuery);
    $paymentOption = $paymentResult->fetch_assoc();


     // Dodanie nowego zamówienia do tabeli orders
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Dodanie zamówienia z wykorzystaniem prepared statement
        $status_id = 1; // ID statusu "Zamówienie złożone"
        $orderInsertQuery = "INSERT INTO orders (Client_ID, Status_ID, Shipping_ID, Payment_ID) VALUES (?, ?, ?, ?)";
        $orderStmt = $mysqli->prepare($orderInsertQuery);
        $orderStmt->bind_param("iiii", $user_id, $status_id, $shippingOption['ID'], $paymentOption['Payment_ID']);
        
        if ($orderStmt->execute()) {
            
                        // Odczytanie danych klienta
            $clientQuery = "SELECT * FROM clients WHERE ID = $user_id";
            $clientResult = $mysqli->query($clientQuery);
            $clientData = $clientResult->fetch_assoc();

            // Przygotowanie danych do zapisu do pliku JSON
            $orderData = array(
                'client_id' => $user_id,
                'first_name' => $clientData['First_Name'],
                'last_name' => $clientData['Last_Name'],
                'books' => array(),
                'payment_method' => $paymentOption['Payment_Type'],
                'shipping_method' => $shippingOption['Name'],
                'total_price' => $totalPrice
            );

            // Odczytanie listy książek w zamówieniu
            $booksQuery = "SELECT books.Book_Title, books.Price FROM books
                        JOIN booksInCarts ON books.ID = booksInCarts.Book_ID
                        JOIN clients ON booksInCarts.ShoppingCart_ID = clients.ShoppingCart_ID
                        WHERE clients.ID = $user_id";
            $booksResult = $mysqli->query($booksQuery);

            while ($book = $booksResult->fetch_assoc()) {
                $orderData['books'][] = array(
                    'title' => $book['Book_Title'],
                    'price' => $book['Price']
                );
            }
            // Dodanie informacji o adresie do danych zamówienia
       $orderData['address'] = array(
            'city' => $address['City'],
            'postcode' => $address['Postcode'],
            'street' => $address['Street'],
            'street_number' => $address['Street_Number']
        );

        // Dodanie informacji o kontakcie do danych zamówienia
        $orderData['contact'] = array(
            'telephone_number' => $contact['Telephone_Number'],
            'email' => $contact['Email_address']
        );

             // Serializacja danych do formatu JSON
             $orderJsonData = json_encode($orderData, JSON_PRETTY_PRINT);

             $filePath = 'finished_transactions.json';
             if (!file_exists($filePath)) {
                 file_put_contents($filePath, "[" . PHP_EOL, LOCK_EX);
             } else {
                 $fileContent = file_get_contents($filePath);
                 $fileContent = rtrim($fileContent, "]" . PHP_EOL) . "," . PHP_EOL;
                 file_put_contents($filePath, $fileContent, LOCK_EX);
             }
 
             if (file_put_contents($filePath, $orderJsonData, FILE_APPEND | LOCK_EX)) {
                 echo "Zamówienie zostało dodane.";
             } else {
                 echo "Błąd podczas zapisu zamówienia do pliku JSON.";
             }
 
             file_put_contents($filePath, "]" . PHP_EOL, FILE_APPEND | LOCK_EX);
 
         } else {
             echo "Błąd podczas dodawania zamówienia: " . $mysqli->error;
         }
 
         $orderStmt->close();
    }
    
    $mysqli->close();
}
?>

    </main>

    <footer>
    <?php include './partials/footer.html'; ?>
    </footer>
</body>
</html>