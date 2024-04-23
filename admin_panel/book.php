<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./styles.css">

</head>
<body>
    //
<?php include '../partials/admin_header.php'; ?>

<?php
$mysqli = new mysqli("localhost", "root", "", "bookstore");


if ($mysqli->connect_error) {
    die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
}

// Pobieranie ID z URL
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    $query = "SELECT books.Book_Title, books.Price, books.ID, books.Description,
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
        $imagePath = './resources/' . $book_id . '.png';
        $description = $row['Description'];
        $authors = $row['Authors'];
        $categories = $row['Categories'];

        echo '<div class="book-details">';
        echo '<div class="left-column">';
        echo '<h1>' . $bookTitle . '</h1>';
        echo '<p>Authorzy: ' . $authors . '</p>';
        echo '<p>Kategorie: ' . $categories . '</p>';
        echo '<img src="' . $imagePath . '" alt="' . $bookTitle . '">';
        echo '</div>';
        echo '<div class="right-column">';
        echo '<p>Cena: $' . $price . '</p>';
        echo '<button>Dodaj do koszyka</button>';
        echo '<div class="description">';
        echo '<p>' . $description . '</p>';
        echo '</div>';

        echo '</div>';

        echo '</div>';
    } else {
        echo "Książka nie istnieje.";
    }
} else {
    echo "Brak id.";
}
    echo'<p>Recenzje:</p>';
    // Zapytanie o recenzje dla konkretnej książki
    $reviewsQuery = "SELECT reviews.Review_Text, reviews.Date, clients.username, clients.ID, reviews.Review_ID as reviews_ID
    FROM reviews
    JOIN clients ON reviews.Client_ID = clients.ID
    WHERE reviews.Book_ID = $book_id";

    $reviewsResult = $mysqli->query($reviewsQuery);

    if ($reviewsResult->num_rows > 0) {
    // Wyświetlanie recenzji
    echo '<ul>';
    while ($reviewRow = $reviewsResult->fetch_assoc()) {
    echo '<h3>' . $reviewRow['username'] . '</h3>';
    echo '<p>Data dodania: ' . $reviewRow['Date'] . '</p>';
    echo '<p>' . $reviewRow['Review_Text'] . '</p>';
    $client_id = $reviewRow['ID'];
    // Sprawdzenie, czy recenzja należy do zalogowanego użytkownika
    
    if (isset($_SESSION['user_id']) && ($client_id == $_SESSION['user_id'] || $_SESSION['IsAdmin'] == 1)) {
        echo '<form action="./post_requests/post_delete_review.php" method="post">';
        echo '<input type="hidden" name="review_id" value="' . $reviewRow['reviews_ID'] . '">';
        echo '<input type="hidden" name="book_id" value="' . $book_id . '">'; 
        echo '<button type="submit">Usuń recenzję</button>';
        echo '</form>';
    }
    }
    echo '</ul>';
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
            echo '<label for="review_text">Recenzja:</label>';
            echo '<textarea name="review_text" required></textarea>';
            echo '<button type="submit">Dodaj recenzję</button>';
            echo '</form>';
            echo '</div>';
    }
}else {
        echo 'Zaloguj się aby dodać recenzję.';
    }
        

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
            echo '<script>alert("Książka została dodana do koszyka.");</script>';
            header('Location: book.php?id='. $book_id );
        } else {
            echo "Błąd podczas dodawania recenzji: " . $mysqli->error;
        }
    
    }    

$mysqli->close();
?>

<?php include './partials/footer.html'; ?>

</body>
</html>