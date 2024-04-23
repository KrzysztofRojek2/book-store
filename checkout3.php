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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel-order'])) {
            $order_id = $_POST['order_id'];
            $user_id = $_SESSION['user_id'];
            $orderDataQuery = "DELETE FROM orders WHERE Client_ID = '" . $user_id . "' AND Status_ID NOT IN (3, 4)";
            
            $orderDataResult = $mysqli->query($orderDataQuery);

            if ($orderDataResult) {
                header("Location: ./shopping_cart.php");
            }
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm-order'])) {
            $order_id = $_POST['order_id'];
            $user_id = $_SESSION['user_id'];
            $orderDataQuery = "SELECT * FROM orders WHERE Client_ID = '" . $user_id . "' AND Status_ID <> 3 AND Status_ID <> 4";

            $orderDataResult = $mysqli->query($orderDataQuery);

            if ($orderDataResult) {
                $orderData = $orderDataResult->fetch_assoc();


                // Przygotowanie danych do zapisu do pliku JSON
                $orderData2 = array(
                    'client_id' => $user_id,
                    'books' => array(), 
                    'payment_method' => '', 
                    'shipping_method' => '', 
                    'contact' => array(), 
                    'address' => array() 
                );

                // Odczytanie listy książek w zamówieniu
                $booksQuery = "SELECT books.Book_Title, books.Price, booksInCarts.Quantity FROM books
                            JOIN booksInCarts ON books.ID = booksInCarts.Book_ID
                            JOIN clients ON booksInCarts.ShoppingCart_ID = clients.ShoppingCart_ID
                            WHERE clients.ID = $user_id";
                $booksResult = $mysqli->query($booksQuery);

                while ($book = $booksResult->fetch_assoc()) {
                    $orderData2['books'][] = array(
                        'quantity' => $book['Quantity'],
                        'title' => $book['Book_Title'],
                        'price' => $book['Price']
                    );
                }

                $paymentDetailsQuery = "SELECT * FROM payments WHERE Payment_ID = " . $orderData['Payment_ID'];
                $paymentDetailsResult = $mysqli->query($paymentDetailsQuery);
                $paymentDetails = $paymentDetailsResult->fetch_assoc();
                $orderData2['payment_method'] = $paymentDetails['Payment_Type'];

                // Dodanie informacji o dostawie do danych zamówienia
                $shippingDetailsQuery = "SELECT * FROM shipping WHERE ID = " . $orderData['Shipping_ID'];
                $shippingDetailsResult = $mysqli->query($shippingDetailsQuery);
                $shippingDetails = $shippingDetailsResult->fetch_assoc();
                $orderData2['shipping_method'] = $shippingDetails['Name'];

                // Dodanie informacji o kontakcie do danych zamówienia
                $contactDetailsQuery = "SELECT * FROM contacts WHERE ID = " . $orderData['Contact_ID'];
                $contactDetailsResult = $mysqli->query($contactDetailsQuery);
                $contactDetails = $contactDetailsResult->fetch_assoc();
                $orderData2['contact'] = array(
                    'telephone_number' => $contactDetails['Telephone_Number'],
                    'email' => $contactDetails['Email_address']
                );

                // Dodanie informacji o adresie do danych zamówienia
                $addressDetailsQuery = "SELECT * FROM addresses WHERE ID = " . $orderData['Address_ID'];
                $addressDetailsResult = $mysqli->query($addressDetailsQuery);
                $addressDetails = $addressDetailsResult->fetch_assoc();
                $orderData2['address'] = array(
                    'city' => $addressDetails['City'],
                    'postcode' => $addressDetails['Postcode'],
                    'street' => $addressDetails['Street'],
                    'street_number' => $addressDetails['Street_Number']
                );

                // Serializacja danych do formatu JSON
                $orderJsonData = json_encode($orderData2, JSON_PRETTY_PRINT);

                $directory = 'C:/xampp/htdocs/projekt/finished_transactions/';
                $filePath = $directory . '' . $orderData['Order_ID'] . '.json';

                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);  // 0755 to przykładowe uprawnienia
                }


                if (file_put_contents($filePath, $orderJsonData, LOCK_EX)) {
                    $updateOrderQuery = "UPDATE orders SET Status_ID = 3 WHERE Order_ID = '" . $order_id . "'";
                    $mysqli->query($updateOrderQuery);

                    
                    $shoppingCartIdQuery = "SELECT ShoppingCart_ID FROM clients WHERE ID = '" . $_SESSION['user_id'] . "'";
                    $shoppingCartIdResult = $mysqli->query($shoppingCartIdQuery);

                    if ($shoppingCartIdResult->num_rows > 0) {
                        $shoppingCartIdRow = $shoppingCartIdResult->fetch_assoc();
                        $shoppingCartId = $shoppingCartIdRow['ShoppingCart_ID'];

                        $deleteBooksInCartsQuery = "DELETE FROM booksInCarts WHERE ShoppingCart_ID = $shoppingCartId";
                        $mysqli->query($deleteBooksInCartsQuery);


                    } else {
                        echo "Błąd podczas pobierania ShoppingCart_ID.";
                    }

                

                    header("Location: ./purchase_confirmation.php");
                    exit;    
                } else {
                    echo "Błąd podczas zapisu zamówienia do pliku JSON.";
                }

                }

        }

        if (isset($_SESSION['user_id'])) {
            echo '<div class="finalize-purchase-wrapper">';
            $query = "SELECT books.Book_Title, books.Price, books.ID, booksInCarts.Quantity, books.ImagePath
            FROM books
            JOIN booksInCarts ON books.ID = booksInCarts.Book_ID
            JOIN clients ON booksInCarts.ShoppingCart_ID = clients.ShoppingCart_ID
            WHERE clients.ID = '" . $_SESSION['user_id'] . "'";

        echo '<div class="finalize-purchase__book-list">';
        $result = $mysqli->query($query);
            $totalPrice=0;
        while ($row = $result->fetch_assoc()) {
            $bookTitle = $row['Book_Title'];
            $price = $row['Price'];
            // $imagePath = './resources/' . $row['ID'] . '.png';
            $imagePath = $row['ImagePath'];
            $IDfield = $row['ID'];
            $quantity = $row['Quantity'];

            $price = $row['Price'];
            $totalPriceForBook = $price*$quantity;
            $totalPrice += $price*$quantity;

            //Wyświetlanie książek pojedynczo
            echo '<article class="finalize-purchase__book-in-cart">';
            echo '<input type="hidden" name="book_id" value="' . $IDfield . '">';
            echo '<img src="' . $imagePath . '" alt="' . $bookTitle . '">';
            echo '<h2>' . $bookTitle . '</h2>';
            echo '<div class="finalize-purchase__price-tag">';
            echo '<p>Ilość: ' . $quantity . '</p>';
            echo '<p>Cena za szt: ' . $price . 'zł</p>';
            if($quantity>1){
                echo '<p style="color:grey">Łączna cena: ' . $totalPriceForBook . 'zł</p>';
            }
            echo '</div>';
            echo '</article>';
            }
            echo '<p style="font-weight:bold"> Łączna cena: ' . $totalPrice . 'zł';
           echo '</div>';
            }
            echo '<div class="gap">';
            echo '</div>';
            $orderDataQuery = "SELECT Order_ID, Payment_ID, Shipping_ID, Contact_ID, Address_ID FROM orders WHERE Client_ID = '" . $_SESSION['user_id'] . "' AND Status_ID <> 3 AND Status_ID <> 4";

            $orderDataResult = $mysqli->query($orderDataQuery);

            if ($orderDataResult) {
                $orderData = $orderDataResult->fetch_assoc();
        
                // Wyświetlanie szczegółów płatności
                echo '<div class="finalize-purchase__order-details">';
                echo '<h1>Szczegóły zamówienia:</h1>';
                    echo '<div class="order-details__field">';
                        echo '<h2>Szczegóły płatności:</h2>';
                        $paymentDetailsQuery = "SELECT * FROM payments WHERE Payment_ID = " . $orderData['Payment_ID'];
                        $paymentDetailsResult = $mysqli->query($paymentDetailsQuery);
                        $paymentDetails = $paymentDetailsResult->fetch_assoc();
                        echo '<p>Metoda płatności: ' . $paymentDetails['Payment_Type'] . '</p>';
                    echo '</div>';

                    echo '<div class="order-details__field">';
                        // Wyświetlanie szczegółów dostawy
                        echo '<h2>Szczegóły dostawy:</h2>';
                        $shippingDetailsQuery = "SELECT * FROM shipping WHERE ID = " . $orderData['Shipping_ID'];
                        $shippingDetailsResult = $mysqli->query($shippingDetailsQuery);
                        $shippingDetails = $shippingDetailsResult->fetch_assoc();
                        echo '<p>Metoda dostawy: ' . $shippingDetails['Name'] . '</p>';
                    echo '</div>';
                    
                    echo '<div class="order-details__field">';
                        // Wyświetlanie szczegółów kontaktu
                        echo '<h2>Szczegóły kontaktu:</h2>';
                        $contactDetailsQuery = "SELECT * FROM contacts WHERE ID = " . $orderData['Contact_ID'];
                        $contactDetailsResult = $mysqli->query($contactDetailsQuery);
                        $contactDetails = $contactDetailsResult->fetch_assoc();
                        echo '<p>Telefon: ' . $contactDetails['Telephone_Number'] . '</p>';
                        echo '<p>Email: ' . $contactDetails['Email_address'] . '</p>';
                    echo '</div>';

                    echo '<div class="order-details__field">';
                        // Wyświetlanie szczegółów adresu
                        echo '<h2>Szczegóły adresu:</h2>';
                        $addressDetailsQuery = "SELECT * FROM addresses WHERE ID = " . $orderData['Address_ID'];
                        $addressDetailsResult = $mysqli->query($addressDetailsQuery);
                        $addressDetails = $addressDetailsResult->fetch_assoc();
                        echo '<p>Miasto: ' . $addressDetails['City'] . '</p>';
                        echo '<p>Kod pocztowy: ' . $addressDetails['Postcode'] . '</p>';
                        echo '<p>Ulica: ' . $addressDetails['Street'] . ' ' . $addressDetails['Street_Number'] . '</p>';
                    echo '</div>';
                
                    // Przycisk "confirm-order"
                    echo '<div class="finalize-order-buttons">';
                        echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                        echo '<input type="hidden" name="order_id" value="' . $orderData['Order_ID'] . '">';
                        echo '<input class="finalize-purchase__button" type="submit" name="confirm-order" value="Potwierdź zamówienie">';
                        echo '</form>';

                        echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                        echo '<input class="finalize-purchase__button delete-button" type="submit" name="cancel-order" value="Anuluj zamówienie">';
                        echo '</form>';
                    echo '</div>';
                    echo '<div class="terms-table">';
                        echo '<p style="border-top: 1px solid gainsboro;">Sprzedaje: Księgarnia Livre</p>';
                        echo '<p>Wysyłka w 1 dzień roboczy</p>';
                        echo '<p>Dostępny w salonie</p>';
                        echo '<p>Zwrot do 30 dni</p>';
                    echo '</div>';
                echo '</div>';

            } else {
                echo "Błąd odczytu danych zamówienia po aktualizacji: " . $mysqli->error;
            }
            echo '</div>';

    

    $mysqli->close();
?>

    </main>

    <footer>
        <?php include './partials/footer.html'; ?>
    </footer>
</body>
<style>
    @media (max-width: 1100px) {
        .gap {
            display: none;
        }
    }

    @media (max-width: 950px) {
        .finalize-purchase-wrapper {
        flex-direction: column;
    }
    .finalize-purchase__order-details {
        flex: 1;
        margin: 1rem;
        width: auto;

    }
    .finalize-purchase__book-list {
        margin: 1rem;
    }
    }
    
    @media (max-width: 550px) {
        h1 {
            font-size: 1.5rem;
        }
        h2 {
            font-size: 1rem;
        }
        .terms-table p {
        font-size: 1rem;
        padding: 0.25rem;
    }
        .finalize-purchase__book-list h2, .finalize-purchase__book-list p {
        font-size: 0.7rem;
    }
    }

    @media (max-width: 430px) {

        h1 {
            font-size: 0.9rem;
        }
        h2 {
            font-size: 0.7rem;
        }
        .terms-table p {
            font-size: 0.7rem;
            padding: 0.25rem;
        }

        .finalize-purchase__book-list h2, .finalize-purchase__book-list p {
            font-size: 0.5rem;
        }
        .finalize-purchase__button {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>
</html>