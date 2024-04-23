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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_SESSION['user_id'])) {
                $selectedShippingID = $_POST['shipping'];
                $selectedPaymentID = $_POST['payment'];
                $statusID = 2;
                $updateOrderQuery = "UPDATE orders SET Status_ID = $statusID , Shipping_ID = '$selectedShippingID', Payment_ID = '$selectedPaymentID' WHERE Client_ID = '" . $_SESSION['user_id'] . "' AND Status_ID <> 3 AND Status_ID <> 4";

                if ($mysqli->query($updateOrderQuery)) {
                    header("Location: ./checkout3.php");
                } else {
                    echo "Błąd aktualizacji danych zamówienia: " . $mysqli->error;
                }
            }
        }
        ?>

        <?php
        // Pobranie opcji dostawy z bazy danych
        $shippingOptionsQuery = "SELECT * FROM shipping";
        $shippingOptionsResult = $mysqli->query($shippingOptionsQuery);

        // Pobranie opcji płatności z bazy danych
        $paymentOptionsQuery = "SELECT * FROM payments";
        $paymentOptionsResult = $mysqli->query($paymentOptionsQuery);

        if ($shippingOptionsResult && $paymentOptionsResult) {
            // Wyświetlanie opcji dostawy
            echo '<section class="shipping-options">';
            echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
            echo '<label for="shipping">Wybierz opcję dostawy:</label>';
            echo '<select name="shipping" id="shipping">';
            while ($shippingOption = $shippingOptionsResult->fetch_assoc()) {
                echo '<option value="' . $shippingOption['ID'] . '">' . $shippingOption['Name'] . ' - $' . $shippingOption['Price'] . '</option>';
            }
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

            // Przycisk potwierdzający zamówienie
            echo '<button type="submit">Zamawiam i płacę</button>';
            echo '</form>';

        } else {
            echo 'Błąd pobierania opcji dostawy i płatności.';
        }
        ?>

    </main>
    <style>
    .booksInCart {
        margin-top: 20px;
    }

    .bookInCart {
        width: 45%;
        display: inline-block;
        margin-bottom: 20px;
    }

    .bookInCart img {
        width: 100%;
        height: auto;
    }

    .shipping-options,
    .payment-options {
        margin-top: 20px;
    }

    label {
        display: block;
        margin-left: 2rem;
        margin-bottom: 5px;
    }

    select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }

    button {
        margin: 2rem;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    select {
        margin-left: 2rem;
        width: 10rem;
    }
    </style>
    <footer>
        <?php include './partials/footer.html'; ?>
    </footer>
</body>
</html>