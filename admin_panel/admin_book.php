<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles.css">

</head>
<body>
<?php include '../partials/header.php'; ?>
<div class="single-book-container">

<?php
$mysqli = new mysqli("localhost", "root", "", "bookstore");


if ($mysqli->connect_error) {
    die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
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
        // $imagePath = '../resources/' . $book_id . '.png';
        $imagePath = $row['ImagePath'];

        $description = $row['Description'];
        $authors = $row['Authors'];
        $categories = $row['Categories'];
        $numberOfPages = $row['NumberOfPages'];
        $publisherPrice = $price+23;
        echo '<div class="book-details">';
        echo '<div class="left-column">';

        echo '<form  action="' . ($_SERVER["PHP_SELF"]) . '" method="post">';

        echo '<img class="single-book-container__image" src=".' . $imagePath . '" alt="' . $bookTitle . '">';
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
        echo '<button class="edit-button" type="submit" formaction="admin_edit_book.php?id=' . $book_id . '" name="editBook">Edytuj książkę</button>';

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
        echo '<form action="../post_requests/post_delete_review.php" method="post">';
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
            echo '<form action="admin_book.php?id='. $book_id . ' " method="post">'; 
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
<?php include '../partials/footer.html'; ?>

</body>
</html>