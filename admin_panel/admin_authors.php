<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Księgarnia - Kategorie</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <main>
    <?php include '../post_requests/post_delete_book_from_author.php'; ?>
    <?php include '../post_requests/post_delete_author.php'; ?>
    <?php
        $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }

$authorsQuery = "SELECT ID, Author_Name FROM authors";
$authorsResult = $mysqli->query($authorsQuery);

if ($authorsResult) {
    while ($authorRow = $authorsResult->fetch_assoc()) {
        $authorID = $authorRow['ID'];
        $authorName = $authorRow['Author_Name'];

        echo '<section class="author-section">';
        echo '<h2>' . $authorName . '</h2>';

        $booksQuery = "SELECT books.Book_Title, books.Price, books.ID
                       FROM books
                       JOIN bookauthors ON books.ID = bookauthors.Book_ID
                       WHERE bookauthors.Author_ID = $authorID";

        $booksResult = $mysqli->query($booksQuery);

        if ($booksResult) {
            if ($booksResult->num_rows > 0) {
                while ($bookRow = $booksResult->fetch_assoc()) {
                    $bookTitle = $bookRow['Book_Title'];
                    $price = $bookRow['Price'];
                    $bookID = $bookRow['ID'];
                    $imagePath = '../resources/' . $bookID . '.png';

                    echo '<article class="article-author-or-category">';
                        echo '<form style="width: 100%;" action="' . $_SERVER["PHP_SELF"] . '" method="post">';
                        echo '<div class="section">';
                            echo '<div class="left-section">';
                                echo '<img class="admin-book-image" src="' . $imagePath . '" alt="' . $bookTitle . '">';
                                echo '<h3 style="max-width: 60%;">' . $bookTitle . '</h3>';
                            echo '</div>';

                            echo '<div class="right-section">';
                                echo '<input type="hidden" name="authorID" value="' . $authorID . '">';
                                echo '<input type="hidden" name="bookID" value="' . $bookID . '">';
                                echo '<button class="delete-button" type="submit" name="deleteBookFromAuthor">Usuń dla tego autora</button>';
                            echo '</div>';
                        echo '</div>';
                        echo '</form>';
                    echo '</article>';
                }
            } else {
                echo '<p>Brak książek tego autora.</p>';
                echo '<div class="delete-author-button">';
                    echo '<form action="' . $_SERVER["PHP_SELF"] . '" method="post">';
                        echo '<input type="hidden" name="authorID" value="' . $authorID . '">';
                        echo '<button style="margin: 1rem 0;" class="delete-button" type="submit" name="deleteAuthor">Usuń autora</button>';
                    echo '</form>';
                echo '</div>';
            }
        } else {
            echo "Błąd podczas pobierania książek autora: " . $mysqli->error;
        }
        echo '<div class="add-book-to-author-button">';
            echo '<a href="./admin_add_to_authors.php?authorID=' . $authorID . '">';
                echo '<button class="add-button" name="addBookToAuthor">Dodaj książkę</button>';
            echo '</a>';
        echo '</div>';
        echo '</section>';
    }
} else {
    echo "Błąd podczas pobierania autorów: " . $mysqli->error;
}

$mysqli->close();
?>
    </main>

    <?php include '../partials/footer.html'; ?>

</body>
</html>