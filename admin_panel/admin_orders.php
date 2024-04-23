<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Księgarnia</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>

        <div class="orders-table-wrapper">
        <?php include '../post_requests/post_delete_book.php'; ?>
        <?php
        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin-accept-order'])) {
            if (isset($_SESSION['user_id'])) {
                $order_id = $_POST['order_id'];

                $updateOrderQuery = "UPDATE orders SET Status_ID = 4 WHERE Order_ID = '" . $order_id . "'";



                if ($mysqli->query($updateOrderQuery)) {
                    header("Location: ./admin_orders.php");
                } else {
                    echo "Błąd aktualizacji danych zamówienia: " . $mysqli->error;
                }
            }

        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin-cancel-order'])) {
            if (isset($_SESSION['user_id'])) {
                $order_id = $_POST['order_id'];

                $deleteOrderQuery = "DELETE FROM orders WHERE Order_ID = '" . $order_id . "'";
                $filename = "../finished_transactions/". $order_id .".json";
                if (file_exists($filename)) {
                    unlink($filename);
                }

                if ($mysqli->query($deleteOrderQuery)) {
                    header("Location: ./admin_orders.php");
                } else {
                    echo "Błąd aktualizacji danych zamówienia: " . $mysqli->error;
                }
            }

        }

        $allOrdersQuery = "SELECT * FROM orders WHERE Status_ID != 4";
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
                if($is_complete==1 && $status_id==3){
                    echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                    echo '<input type="hidden" name="order_id" value="' . $order_id . '">';
                    echo '<input class="accept-order-button add-button" type="submit" name="admin-accept-order" value="Potwierdź zamówienie">';
                    echo '</form>';
                } else if( $status_id==3 && $is_complete==0){
                    echo '<p style="font-size:15px; font-weight:bolder">Brakujące dane</p>';
                } else {
                    echo '<p style="font-size:15px; font-weight:bolder">W trakcie realizacji</p>';
                }
                echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                echo '<input type="hidden" name="order_id" value="' . $order_id . '">';
                echo '<input class="cancel-order-button delete-button" type="submit" name="admin-cancel-order" value="Anuluj zamówienie">';
                echo '</form>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }

            echo '</table>';
        } else {
            echo "Brak zamówień.";
        }
        $finalisedOrdersQuery = "SELECT * FROM orders WHERE Status_ID = 4";
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

                echo '<button class="check-order-button edit-button" onclick="window.open(\'../finished_transactions/'. $order_id .'.json\')">Sprawdź szczegóły</button>';

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
        

    <?php include '../partials/footer.html'; ?>

</body>
</html>