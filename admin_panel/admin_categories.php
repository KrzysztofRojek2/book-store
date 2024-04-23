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
    <?php include '../post_requests/post_delete_book_from_category.php'; ?>
    <?php include '../post_requests/post_delete_category.php'; ?>
    
        <?php
        $mysqli = new mysqli("localhost", "root", "", "bookstore");


        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }

        $categoriesQuery = "SELECT Category_ID, Category_Name FROM categories";
        $categoriesResult = $mysqli->query($categoriesQuery);

        if ($categoriesResult) {
            while ($categoryRow = $categoriesResult->fetch_assoc()) {
                $categoryID = $categoryRow['Category_ID'];
                $categoryName = $categoryRow['Category_Name'];
                displayCategory($categoryID, $categoryName, $mysqli);
            }
        } else {
            echo "Błąd podczas pobierania kategorii: " . $mysqli->error;
        }

        function displayCategory($categoryID, $categoryName, $mysqli)
        {
            echo '<section class="category-section">';
            echo '<h2>' . $categoryName . '</h2>';

            $booksQuery = "SELECT books.Book_Title, books.Price, books.ID
                           FROM books
                           JOIN bookcategories ON books.ID = bookcategories.Book_ID
                           WHERE bookcategories.Category_ID = $categoryID";

            $booksResult = $mysqli->query($booksQuery);

            if ($booksResult) {
                if ($booksResult->num_rows > 0) {
                    while ($bookRow = $booksResult->fetch_assoc()) {
                        displayBook($bookRow, $categoryID);
                    }
                } else {
                    echo '<p>Brak książek w tej kategorii.</p>';
                    echo '<form action="' . $_SERVER["PHP_SELF"] . '" method="post">';
                    echo '<input type="hidden" name="categoryID" value="' . $categoryID . '">';
                    echo '<button style="margin: 1rem 0;" class="delete-button" type="submit" name="deleteCategory">Usuń kategorię</button>';
                    echo '</form>';
                }
            } else {
                echo "Błąd podczas pobierania książek z kategorii: " . $mysqli->error;
            }
                echo '<div class="add-book-to-categories-button">';
                    echo '<a href="./admin_add_to_categories.php?categoryID=' . $categoryID . '"><button class="add-button" name="addBookToCategories">Dodaj książkę</button></a>';
                echo '</div>';
            echo '</section>';
        }

        function displayBook($bookRow, $categoryID)
        {
            $bookTitle = $bookRow['Book_Title'];
            $bookID = $bookRow['ID'];
            $imagePath = '../resources/' . $bookID . '.png';

            echo '<article class="article-author-or-category">';
                echo '<form style="width: 100%;" action="' . $_SERVER["PHP_SELF"] . '" method="post">';
                    echo '<div class="section">';
                        // Left section
                        echo '<div class="left-section">';
                            echo '<img class="admin-book-image" src="' . $imagePath . '" alt="' . $bookTitle . '">';
                            echo '<h3 style="max-width: 60%;">' . $bookTitle . '</h3>';
                        echo '</div>';

                        // Right section
                        echo '<div class="right-section">';
                            echo '<input type="hidden" name="categoryID" value="' . $categoryID . '">';
                            echo '<input type="hidden" name="bookID" value="' . $bookID . '">';
                            echo '<button class="delete-button" type="submit" name="deleteBookFromCategories">Usuń dla tej kategorii</button>';
                        echo '</div>';
                    echo '</div>';
                echo '</form>';
            echo '</article>';
        }

        

        $mysqli->close();
        ?>
    </main>

    <?php include '../partials/footer.html'; ?>

</body>

</html>