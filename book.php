<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./styles.css">

</head>
<body>
<?php include './partials/header.php'; ?>
<div class="single-book-container">

<?php
$mysqli = new mysqli("localhost", "root", "", "bookstore");


if ($mysqli->connect_error) {
    die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add-review'])) {

    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    // Pobranie danych z formularza
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];
    $review_text = $_POST['review_text'];

    // Wstawienie recenzji do bazy danych
    $insertQuery = "INSERT INTO reviews (Client_ID, Book_ID, Review_Text, Date) VALUES ('$user_id', $book_id, '$review_text', NOW())";

    if ($mysqli->query($insertQuery)) {
        header('Location: book.php?id='. $book_id );
    } else {
        echo "Błąd podczas dodawania recenzji: " . $mysqli->error;
    }

}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToCart'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $book_id = $_POST['book_id'];

        // Sprawdzanie czy książka jest w koszyku
        $checkQuery = "SELECT * FROM booksInCarts
           WHERE ShoppingCart_ID = ? AND Book_ID = ?";

        
                $getShoppingCartIdQuery = "SELECT ShoppingCart_ID FROM clients WHERE ID = ?";

                if ($stmtShoppingCartId = $mysqli->prepare($getShoppingCartIdQuery)) {
                    $stmtShoppingCartId->bind_param("i", $user_id);
                    $stmtShoppingCartId->execute();
                    $stmtShoppingCartId->store_result();
                    $stmtShoppingCartId->bind_result($shoppingCartId);
                        
                    if ($stmtShoppingCartId->fetch()) {
                        $insertQuery = "INSERT INTO booksInCarts (ShoppingCart_ID, Book_ID, Quantity) VALUES (?, ?, ?)";


                        if ($stmt = $mysqli->prepare($checkQuery)) {
                                        $stmt->bind_param("ii", $shoppingCartId, $book_id);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $booksInCartsID = $row['ID'];
                                                $increasedQuantity = $row['Quantity'];
                                                $increasedQuantity = $increasedQuantity + 1;
                                                $updateQuery = "UPDATE booksInCarts SET Quantity = ? WHERE ID = ?";
                                                if ($stmtUpdate = $mysqli->prepare($updateQuery)) {
                                                    $stmtUpdate->bind_param("ii", $increasedQuantity, $booksInCartsID);
                        
                                                    if ($stmtUpdate->execute()) {
                                                        // echo '<script>alert("Książka została dodana do koszyka.");</script>';
                                                        header('Location: shopping_cart.php');
                                                    } else {
                                                        echo "Błąd podczas dodawania książki do koszyka.";
                                                    }

                                            }
                                        }
                                        } else {
                                            if ($stmtInsert = $mysqli->prepare($insertQuery)) {
                                                $quantity = 1;
                                                $stmtInsert->bind_param("iii", $shoppingCartId, $book_id, $quantity);
                    
                                                if ($stmtInsert->execute()) {
                                                    // echo '<script>alert("Książka została dodana do koszyka.");</script>';
                                                    header('Location: shopping_cart.php');
                                                } else {
                                                    echo "Błąd podczas dodawania książki do koszyka.";
                                                }
                    
                                                $stmtInsert->close();
                                            }
                                            }
                                         } else {
                            echo "Błąd przy przygotowaniu zapytania.";
                        }
                    } else {
                        echo "Błąd podczas pobierania ShoppingCart_ID.";
                    }

                    $stmtShoppingCartId->close();
                } else {
                    echo "Błąd przy przygotowaniu zapytania.";
                }

    } else {
        echo '<script>alert("Musisz być zalogowany, aby dodać książkę do koszyka.");</script>';
        header("Location: ./login.php");

    }
}
// Pobieranie ID z URL
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $query = "SELECT books.Book_Title, books.Price, books.ID, books.Description, books.NumberOfPages, books.ImagePath,
                     GROUP_CONCAT(DISTINCT authors.Author_Name) as Authors,
                     GROUP_CONCAT(DISTINCT categories.Category_Name) as Categories
              FROM books
              LEFT JOIN bookauthors ON books.ID = bookauthors.Book_ID
              LEFT JOIN authors ON bookauthors.Author_ID = authors.ID
              LEFT JOIN bookcategories ON books.ID = bookcategories.Book_ID
              LEFT JOIN categories ON bookcategories.Category_ID = categories.Category_ID
              WHERE books.ID = $book_id
              GROUP BY books.ID";

    $result = $mysqli->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        $bookTitle = $row['Book_Title'];
        $price = $row['Price'];
        // $imagePath = './resources/' . $book_id . '.png';
        $imagePath = $row['ImagePath'];
        $description = $row['Description'];
        $authors = $row['Authors'];
        $categories = $row['Categories'];
        $numberOfPages = $row['NumberOfPages'];
        $publisherPrice = $price+23;
        echo '<div class="book-details">';
        echo '<div class="left-column">';

        echo '<form  action="' . ($_SERVER["PHP_SELF"]) . '" method="post">';

        echo '<img class="single-book-container__image" src="' . $imagePath . '" alt="' . $bookTitle . '">';
        echo '</div>';
        echo '<div class="right-column">';

        echo '<div class="right-right-column">';

        echo '<h1>' . $bookTitle . '</h1>';
        echo '<h2>Autorzy:</h2>';
        echo '<h3>' . $authors . '</h3>';
        echo '<h2>Kategorie:</h2>';
        echo '<h3>' . $categories . '</h3>';
        echo '<p>Liczba stron: ' . $numberOfPages . '</p>';

        echo '</div>';
        echo '<div class="right-left-column">';

        echo '<h2 class="price">' . $price . 'zł</h2>';
        echo '<p class="publisher-price">' . $publisherPrice . 'zł - porównanie do ceny sugerowanej przez wydawcę</p>';
        echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
        echo '<button type="submit" name="addToCart" class="add-button">Dodaj do koszyka</button>';

        // echo '<button type="submit" name="addToCart" class="add-button">Dodaj do koszyka</button>';
        echo '<div class="terms-table">';
        echo '<p style="border-top: 1px solid gainsboro;">Sprzedaje: Księgarnia Livre</p>';
        echo '<p>Wysyłka w 1 dzień roboczy</p>';
        echo '<p>Dostępny w salonie</p>';
        echo '<p>Zwrot do 30 dni</p>';
        echo '</div>';
        
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '<div class="description">';
        echo '<p>' . $description . '</p>';

        

        echo '</div>';
        echo '</form>';

    } else {
        echo "Książka nie istnieje.";
    }
} else {
    echo "Brak id.";
}   
    echo '<div class="reviews">';
    echo'<h2>Recenzje:</h2>';
    // Zapytanie o recenzje dla konkretnej książki
    $reviewsQuery = "SELECT reviews.Review_Text, reviews.Date, clients.username, clients.ID, reviews.Review_ID as reviews_ID
    FROM reviews
    JOIN clients ON reviews.Client_ID = clients.ID
    WHERE reviews.Book_ID = $book_id";

    $reviewsResult = $mysqli->query($reviewsQuery);

    if ($reviewsResult->num_rows > 0) {
    // Wyświetlanie recenzji
    
    while ($reviewRow = $reviewsResult->fetch_assoc()) {
    echo '<div class="single-review">';
    echo '<h3>' . $reviewRow['username'] . '</h3>';
    echo '<p class="review-date">' . $reviewRow['Date'] . '</p>';
    echo '<p class="review-content">' . $reviewRow['Review_Text'] . '</p>';
    $client_id = $reviewRow['ID'];
    // Sprawdzenie, czy recenzja należy do zalogowanego użytkownika
    
    if (isset($_SESSION['user_id']) && ($client_id == $_SESSION['user_id'] || $_SESSION['IsAdmin'] == 1)) {
        echo '<form action="./post_requests/post_delete_review.php" method="post">';
        echo '<input type="hidden" name="review_id" value="' . $reviewRow['reviews_ID'] . '">';
        echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
        echo '<div class="delete-review-button-wrapper">';
        echo '<button class="delete-button" type="submit">Usuń recenzję</button>';
        echo '</div>';
        echo '</form>';
    }
    echo '</div>';
    }
    
    } else {
    echo 'Brak recenzji.';
    }

    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['IsAdmin']== false){
            $user_id = $_SESSION['user_id'];
            // Formularz dodawania recenzji
            echo '<div class="review-form">';
            echo '<h3>Dodaj recenzję:</h3>';
            echo '<form action="book.php?id='. $book_id . ' " method="post">'; 
            echo '<input type="hidden" name="book_id" value="' . $book_id . '">';
            echo '<input type="hidden" name="user_id" value="' . $user_id . '">'; 
            echo '<textarea class="review-textarea" name="review_text" required></textarea>';
            echo '<div class="add-review-button-wrapper">';
            echo '<button type="submit" name="add-review">Dodaj recenzję</button>';
            echo '</div>';
            echo '</form>';
            echo '</div>';
            echo '</div>';
    }
}else {
        echo '<div class="review-form">';
        echo 'Zaloguj się aby dodać recenzję.';
        echo '</div>';
        echo '</div>';
    }
        

    
$mysqli->close();
?>
<script>
    let textarea = document.querySelector('textarea');

    textarea.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
});
</script>
</div>
</div>
<?php include './partials/footer.html'; ?>

</body>
<style>
@media (max-width: 1100px) {
    .single-book-container {
        padding: 3rem;
    }
    .book-details {
        flex-direction: column;
    }
    .right-column {
        padding: 0px;
    }
    .right-column {
        max-width: 100%;
        width: 100%;
        flex-direction: row;
        justify-content: space-around;
    }
    .right-right-column {
        flex: 1;
    }

    .right-left-column {
        margin-top: 2rem;
        flex: 1;
    }
    .description {
        padding: 3rem;
    }
    .description p{
        font-size: 0.9rem;
    }

}

@media (max-width: 900px) {

    .single-book-container {
        padding: 1rem;
    }
    .right-right-column h1{
        font-size: 1.8rem;

    }
    .right-right-column h2{
        font-size: 1.2rem;

    }
    .right-right-column h3{
        font-size: 0.9rem;

    }
    .right-right-column p{
        font-size: 0.75rem;

    }

    .right-left-column p{
        font-size: 0.75rem;
    }
    .right-left-column h2{
        font-size: 1.2rem;

    }

    .description p{
        font-size: 0.7rem;
    }


}
@media (max-width: 650px) {
    .terms-table p {
        padding:0.2rem;
    }

    .right-column h1 {
        margin: 1rem 0;
    }

    .right-column h3 {
        margin-top: 0.5rem;
        margin-bottom: 3rem;
    }

}

@media (max-width: 500px) {
    .book-details {
    padding: 1rem;
    }
    .right-column {
        flex-direction: column;
    }

    .description {
        padding: 1.5rem;
    }

    .right-left-column {
        margin-top: 0.5rem;
    }

    .right-column h3 {
    margin-bottom: 1.5rem;
    }
    .description p {
    font-size: 0.6rem;
}
    h2 {
        font-size: 1.2rem;
    }
    h3 {
        font-size: 0.9rem;
    }
    .review-date {
    font-size: 0.6rem;
    }
    .review-content {
        font-size: 0.7rem;

    }
}

@media (max-width: 360px) {
    .right-column h1 {
    font-size: 1rem;
    }

    .right-column h2 {
        font-size: 0.9rem;
    }

    .right-column h3 {
        font-size: 0.7rem;
    }

    .description p {
    font-size: 0.5rem;
    }

    .review-content {
        font-size: 0.5rem;
    }

    .price {
        margin-top: 0.5rem;
        font-size: 0.7rem;
    }

    button {
        font-size: 0.7rem;
        padding: 0.3rem 0.7rem;
    }
    .right-column button {
    height: 2rem;
}
.right-column h3 {
    margin-bottom: 0.5rem;
}
.right-left-column p {
    font-size: 0.6rem;
}

}

</style>
</html>