<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk</title>
    <link rel="stylesheet" href="./styles.css">
</head>

<body>
    <?php include './partials/header.php'; ?>

    <main>
    <?php

    $mysqli = new mysqli("localhost", "root", "", "bookstore");

    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['increment'])) {
        $book_in_cart_ID = $_POST['book_in_cart_ID'];
        $quantity = $_POST['quantity'];
        $increasedQuantity = $quantity + 1;
        $incrementQuery = "UPDATE booksInCarts SET Quantity = ? WHERE ID = ?";
            if ($stmtUpdate = $mysqli->prepare($incrementQuery)) {
                $stmtUpdate->bind_param("ii", $increasedQuantity, $book_in_cart_ID);
                if ($stmtUpdate->execute()) {
                    header('Location: shopping_cart.php');
                } else {
                    echo "Błąd podczas dodawania książki do koszyka.";
                }
        }

    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['decrement'])) {
        $book_in_cart_ID = $_POST['book_in_cart_ID'];
        $quantity = $_POST['quantity'];
        $decreasedQuantity = $quantity - 1;
        $decrementQuery = "UPDATE booksInCarts SET Quantity = ? WHERE ID = ?";
            if ($stmtUpdate = $mysqli->prepare($decrementQuery)) {
                $stmtUpdate->bind_param("ii", $decreasedQuantity, $book_in_cart_ID);
                if ($stmtUpdate->execute()) {
                    header('Location: shopping_cart.php');
                } else {
                    echo "Błąd podczas dodawania książki do koszyka.";
                }
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteFromCart'])) {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $book_id = $_POST['book_id'];

            $getShoppingCartIdQuery = "SELECT ShoppingCart_ID FROM clients WHERE ID = ?";

            if ($stmtShoppingCartId = $mysqli->prepare($getShoppingCartIdQuery)) {
                $stmtShoppingCartId->bind_param("i", $user_id);
                $stmtShoppingCartId->execute();
                $stmtShoppingCartId->store_result();
                $stmtShoppingCartId->bind_result($shoppingCartId);

                if ($stmtShoppingCartId->fetch()) {
                    $removeQuery = "DELETE FROM booksInCarts WHERE ShoppingCart_ID = ? AND Book_ID = ?";

                    if ($stmtRemove = $mysqli->prepare($removeQuery)) {
                        $stmtRemove->bind_param("ii", $shoppingCartId, $book_id);

                        if ($stmtRemove->execute()) {
                            header('Location: shopping_cart.php');
                        } else {
                            echo "Błąd podczas usuwania książki z koszyka.";
                        }

                        $stmtRemove->close();
                    }
                } else {
                    echo "Błąd podczas pobierania ShoppingCart_ID.";
                }

                $stmtShoppingCartId->close();
            } else {
                echo "Błąd przy przygotowaniu zapytania.";
            }
        } else {
            echo "Musisz być zalogowany, aby usunąć książkę z koszyka.";
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
        $orderIDToDelete = $_POST['order_id'];

        $deleteOrderQuery = "DELETE FROM orders WHERE Order_ID = ?";
        $stmt = $mysqli->prepare($deleteOrderQuery);

        if ($stmt) {
            $stmt->bind_param("i", $orderIDToDelete);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header('Location: shopping_cart.php');
            } else {
                echo "Błąd podczas usuwania zamówienia. Prawdopodobnie zamówienie o podanym ID nie istnieje.";
            }

            $stmt->close();
        } else {
            echo "Błąd przy przygotowaniu zapytania.";
        }
    }


    if (isset($_SESSION['user_id'])) {
        $query = "SELECT books.Book_Title, books.Price, books.ID, booksInCarts.Quantity, books.ImagePath, booksInCarts.ID AS bookInCartID
        FROM books
        JOIN booksInCarts ON books.ID = booksInCarts.Book_ID
        JOIN clients ON booksInCarts.ShoppingCart_ID = clients.ShoppingCart_ID
        WHERE clients.ID = '" . $_SESSION['user_id'] . "'";

        $result = $mysqli->query($query);
        $totalPrice = 0;

        if ($result->num_rows > 0) {
            echo '<section class="booksInCart">';
            while ($row = $result->fetch_assoc()) {
                $bookTitle = $row['Book_Title'];
                $bookInCartID = $row['bookInCartID'];
                $price = $row['Price'];
                // $imagePath = './resources/' . $row['ID'] . '.png';
                $imagePath = $row['ImagePath'];
                $IDfield = $row['ID'];
                $user_id = $_SESSION['user_id'];
                $quantity = $row['Quantity'];

                $price = $row['Price'];
                $totalPrice += $price * $quantity;

               
                

                // Wyświetlanie książek pojedynczo
                echo '<form class="bookInCart-wrapper" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
                echo '<article class="bookInCart">';
                echo '<a href="book.php?id=' . $IDfield . '">';
                echo '<img class="cart-book-image" src="' . $imagePath . '" alt="' . $bookTitle . '">';
                echo '</a>';
                echo '<h2>' . $bookTitle . '</h2>';
                echo '<div class="quantity-wrapper">';
                if ($quantity>1){
                   echo '<button class="delete-button" type="submit" name="decrement">-</button>' ;
                } else {
                    echo '<button class="delete-button" type="submit" name="deleteFromCart">-</button>' ;
                }
                echo '<p>Ilość: ' . $quantity . '</p>';
                echo '<input type="hidden" name="quantity" value="' . $quantity . '">';
                echo '<button class="add-button" type="submit" name="increment">+</button>';
                echo '</div>';
                echo '<p>Cena: ' . $price . 'zł</p>';
                echo '<input type="hidden" name="book_id" value="' . $IDfield . '">';
                echo '<input type="hidden" name="book_in_cart_ID" value="' . $bookInCartID . '">';
                echo '<button class="delete-button" type="submit" name="deleteFromCart">Usuń z koszyka</button>';
                echo '</article>';
                echo '</form>';


            }
                
            // Wyświetl łączną cenę
            echo '<section class="total-price">';
                echo '<h3>Łączna cena: ' . $totalPrice . 'zł</h3>';
            echo '</section>';
            echo '</section>';

            $ordersQuery = "SELECT Status_ID, Client_ID, Order_ID FROM orders WHERE Client_ID = $user_id AND Status_ID != 3 AND Status_ID != 4";

            $result = $mysqli->query($ordersQuery);

            if ($result->num_rows > 0) {
                echo '<div class="cart-buttons">';
                while ($row = $result->fetch_assoc()) {
                    $statusID = $row['Status_ID'];
                    $orderID = $row['Order_ID'];
                    if ($statusID == 1) {
                        echo '<div><a class="cart-anchor" href="./checkout2.php"><button class="cart-button" >Kontynuuj zamówienie</button></a></div>';
                        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
                        echo '<input type="hidden" name="order_id" value="' . $orderID . '">';
                        echo '<button class="cart-button" type="submit" name="cancel_order">Usuń zamówienie</button>';
                        echo '</form>';
                    } elseif ($statusID == 2) {
                        echo '<div><a class="cart-anchor" href="./checkout3.php"><button class="cart-button">Kontynuuj zamówienie</button></a></div>';
                        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
                        echo '<input type="hidden" name="order_id" value="' . $orderID . '">';
                        echo '<button class="cart-button" type="submit" name="cancel_order">Usuń zamówienie</button>';
                        echo '</form>';
                    }
                }
            } else {
                echo '<div class="cart-buttons">';
                echo '<a class="cart-anchor" href="./checkout1.php"><button class="cart-button">Przejdź do realizacji</button></a>';
            }
        } else {
            echo '<div style="display:flex; justify-content:center; margin-top:5%; margin-bottom:23%">';
            echo '<h1 class="empty-cart-info">Koszyk jest pusty!</h1>';
            echo '</div>';
        }
    } else {
        echo '<div style="display: flex; align-items: center; justify-content: center; flex-direction: column;">';
        echo '<p style="margin-top: 50px; margin-bottom: 50px;">Zaloguj się aby uzyskać dostęp do koszyka</p>';
        echo '</div>';
    }
    echo '</div>';


    

    $mysqli->close();
    ?>
    </main>

    <?php include './partials/footer.html'; ?>

</body>
<style>
    @media (max-width: 800px) {
        .bookInCart {
            flex-direction: column;
            justify-content: space-between;
            min-height: 400px;
        }
        .bookInCart h2 {
            min-width: 0px;
        }
        .bookInCart-wrapper {
            min-width: 50%;
        }
        .total-price {
            justify-content: center;
        }
        .total-price h3 {
        margin-right: 0rem;
        }
        .cart-buttons{
        justify-content: center;
        align-items: center;
        }
        .empty-cart-info{
            font-size: 1.5rem;
        }
   
}

@media (max-width: 400px) {
        .bookInCart h2 {
            font-size: 1rem;
        }    
        .empty-cart-info{
            font-size: 1rem;
        }    
}
</style>
</html>