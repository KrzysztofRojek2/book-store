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

        <div class="orders-table-wrapper">
         <?php include './post_requests/post_delete_book.php'; ?> 
        <?php
        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user-cancel-order'])) {
            if (isset($_SESSION['user_id'])) {
                $order_id = $_POST['order_id'];

                $deleteOrderQuery = "DELETE FROM orders WHERE Order_ID = '" . $order_id . "'";

                $filename = "./finished_transactions/". $order_id .".json";
                
                if (file_exists($filename)) {
                    unlink($filename);
                }
                if ($mysqli->query($deleteOrderQuery)) {
                    header("Location: ./my_orders.php");
                } else {
                    echo "Błąd aktualizacji danych zamówienia: " . $mysqli->error;
                }
            }

        }

        // Zapytanie do bazy danych w celu pobrania wszystkich zamówień
        $allOrdersQuery = "SELECT * FROM orders WHERE Status_ID != 4 AND Client_ID = '" . $_SESSION['user_id'] . "'";
        $allOrdersResult = $mysqli->query($allOrdersQuery);

        echo '<h1>Oczekujące zamówienia: </h1>';
        if ($allOrdersResult->num_rows > 0) {
            echo '<table class="awaiting-orders-table" border="0">';
            echo '<tr class="top-table-row">';
            echo '<th>ID Zamówienia</th>';
            echo '<th>ID Statusu</th>';
            echo '<th>ID Płatności</th>';
            echo '<th>ID Klienta</th>';
            echo '<th>ID Dostawy</th>';
            echo '<th>ID Kontaktu</th>';
            echo '<th>ID Adresu</th>';
            echo '<th>Status Zamówienia</th>';
            echo '</tr>';

            while ($order = $allOrdersResult->fetch_assoc()) {
                $order_id = $order['Order_ID'];
                $status_id = $order['Status_ID'];
                $payment_id = $order['Payment_ID'];
                $client_ID = $order['Client_ID'];
                $shipping_ID = $order['Shipping_ID'];
                $contact_ID = $order['Contact_ID'];
                $address_ID = $order['Address_ID'];
                $is_complete;

                echo '<tr class="row">';
                echo '<td>' . $order_id . '</td>';
                echo '<td>' . $status_id . '</td>';
                echo '<td>' . $payment_id . '</td>';
                echo '<td>' . $client_ID . '</td>';
                echo '<td>' . $shipping_ID . '</td>';
                echo '<td>' . $contact_ID . '</td>';
                echo '<td>' . $address_ID . '</td>';
                if(empty($order_id) || empty($status_id) ||empty($payment_id) ||  empty($client_ID) || empty($shipping_ID)  || empty($contact_ID) || empty($address_ID)){
                    $is_complete=0;
                } else {
                    $is_complete=1;
                }

                
                echo '<td>';
                echo '<div class="order-buttons">';

                echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                echo '<input type="hidden" name="order_id" value="' . $order_id . '">';
                echo '<input style="margin-left:0px" class="cancel-order-button delete-button" type="submit" name="user-cancel-order" value="Anuluj zamówienie">';
                echo '</form>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo "Brak zamówień.";
        }
        $finalisedOrdersQuery = "SELECT * FROM orders WHERE Status_ID = 4 AND Client_ID = '" . $_SESSION['user_id'] . "'";
        $finalisedOrdersQuery = $mysqli->query($finalisedOrdersQuery);

        echo '<h1>Sfinalizowane zamowienia: </h1>';//taki blueprint
        if ($finalisedOrdersQuery->num_rows > 0) {
            echo '<table class="finalised-orders-table" border="0">';
            echo '<tr class="top-table-row">';
            echo '<th>ID Zamówienia</th>';
            echo '<th>ID Statusu</th>';
            echo '<th>ID Płatności</th>';
            echo '<th>ID Klienta</th>';
            echo '<th>ID Dostawy</th>';
            echo '<th>ID Kontaktu</th>';
            echo '<th>ID Adresu</th>';
            echo '<th>Status Zamówienia</th>';
            echo '</tr>';

            while ($order = $finalisedOrdersQuery->fetch_assoc()) {
                $order_id = $order['Order_ID'];
                echo '<tr class="row">';
                foreach ($order as $key => $value) {
                    if (empty($value)) {
                        echo '<td>USUNIĘTE</td>';
                    } else {
                        echo '<td>' . $value . '</td>';
                    }
                }
                
                echo '<td>';
                echo '<div class="order-buttons">';
                echo '<button class="check-order-button edit-button" onclick="window.open(\'./finished_transactions/'. $order_id .'.json\')">Sprawdź szczegóły</button>';

                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo "Brak zamówień.";
        }
        $mysqli->close();
        ?>
        </div>
        

    <?php include './partials/footer.html'; ?>
    <style>
        @media (max-width: 1200px) {
            .finalised-orders-table td,
            .awaiting-orders-table td {
                padding: 0.5rem 2rem;
            }
            th {
                font-size: 0.75rem;
            }

        }

        @media (max-width: 800px) {
            .orders-table-wrapper {
                margin: 2rem;
            }
            h1 {
                font-size: 1.5rem;
            }
            .finalised-orders-table td,
            .awaiting-orders-table td {
                padding: 0.5rem 1rem;
            }
            th {
                font-size: 0.65rem;
            }

        }

        @media (max-width: 580px) {
            .orders-table-wrapper {
                margin: 1.5rem;
            }
            h1 {
                font-size: 1.25rem;
            }
            .finalised-orders-table td,
            .awaiting-orders-table td {
                padding: 0.25rem 0.5rem;
                font-size: 0.65rem;
            }
            th {
                font-size: 0.55rem;
            }
            td {
                font-size: 1rem;
            }
            .accept-order-button, .cancel-order-button, .check-order-button {
                padding:0.25rem 0.5rem;
                font-size: 0.5rem;
            }

        }

        @media (max-width: 420px) {
            .orders-table-wrapper h1 {
                margin: 1rem;
            }
            h1 {
                font-size: 1rem;
            }
            .finalised-orders-table td,
            .awaiting-orders-table td {
                padding: 0.1rem 0.25rem;
                font-size: 0.5rem;
            }
            th {
                font-size: 0.5rem;
            }
            td {
                font-size: 0.9rem;
            }
            .accept-order-button, .cancel-order-button, .check-order-button {
                padding:0.1rem 0.25rem;
                font-size: 0.4rem;
            }

        }
        @media (max-width: 340px) {
            h1 {
                font-size: 0.8rem;
            }
            th {
                font-size: 0.35rem;
            }
            td {
                font-size: 0.7rem;
            }


        }
    </style>

</body>
</html>