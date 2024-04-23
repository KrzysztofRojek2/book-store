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

    <main>
    <?php include '../post_requests/post_delete_book.php'; ?>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "bookstore");


    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }
    ?>
    <div id="filter-section">
                <h3>Filtrowanie</h3>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <label for="category">Kategoria:</label>
                    <select name="category">
                        <?php
                            $mysqli = new mysqli("localhost", "root", "", "bookstore");

                            // Pobieranie kategorii z bazy danych i tworzenie opcji w polu wyboru
                            $categoriesQuery = "SELECT * FROM categories";
                            $categoriesResult = $mysqli->query($categoriesQuery);
                            echo '<option value="">-- Wybierz kategorię --</option>';
                            while ($category = $categoriesResult->fetch_assoc()) {
                                echo '<option value="' . $category['Category_ID'] . '">' . $category['Category_Name'] . '</option>';
                            }
                        ?>
                    </select>

                    <label for="author">Autor:</label>
                    <select name="author">
                        <?php
                            // Pobieranie autorów z bazy danych i tworzenie opcji w polu wyboru
                            $authorsQuery = "SELECT * FROM authors";
                            $authorsResult = $mysqli->query($authorsQuery);
                            echo '<option value="">-- Wybierz autora --</option>';
                            while ($author = $authorsResult->fetch_assoc()) {
                                echo '<option value="' . $author['ID'] . '">' . $author['Author_Name'] . '</option>';
                            }
                        ?>
                    </select>

                    <label for="minPrice">Cena od:</label>
                    <input type="number" name="minPrice" step="1" value="<?php echo isset($_POST['minPrice']) ? $_POST['minPrice'] : ''; ?>">

                    <label for="maxPrice">Cena do:</label>
                    <input type="number" name="maxPrice" step="1" value="<?php echo isset($_POST['maxPrice']) ? $_POST['maxPrice'] : ''; ?>">

                    <button type="submit" name="filterBooks">Filtruj</button>
                </form>
        </div>
    <?php 

    // Wyświetlanie książek z uwzględnieniem filtrów TODO przeanalizować bo ściągnięte z index.php i nw co tu sie dzieje dla admina
    $whereConditions = array();

    if (!empty($_POST['category'])) {
        $categoryFilter = "categories.Category_ID = " . $mysqli->real_escape_string($_POST['category']);
        $whereConditions[] = $categoryFilter;
    }

    if (!empty($_POST['author'])) {
        $authorFilter = "authors.ID = " . $mysqli->real_escape_string($_POST['author']);
        $whereConditions[] = $authorFilter;
    }

    $minPrice = isset($_POST['minPrice']) ? $mysqli->real_escape_string($_POST['minPrice']) : null;
    $maxPrice = isset($_POST['maxPrice']) ? $mysqli->real_escape_string($_POST['maxPrice']) : null;

    if ($minPrice !== null || $maxPrice !== null) {
        $priceFilter = "books.Price BETWEEN " . ($minPrice ?: '0') . " AND " . ($maxPrice ?: '999999');
        $whereConditions[] = $priceFilter;
    }
    echo '<section class="book-list-admin">';

    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

    $query = "SELECT books.ID, books.Book_Title, books.Price, books.ImagePath,
                     GROUP_CONCAT(DISTINCT authors.Author_Name) as Authors,
                     GROUP_CONCAT(DISTINCT categories.Category_Name) as Categories
              FROM books
              LEFT JOIN bookauthors ON books.ID = bookauthors.Book_ID
              LEFT JOIN authors ON bookauthors.Author_ID = authors.ID
              LEFT JOIN bookcategories ON books.ID = bookcategories.Book_ID
              LEFT JOIN categories ON bookcategories.Category_ID = categories.Category_ID
              $whereClause
              GROUP BY books.ID";

    $result = $mysqli->query($query);

        while ($row = $result->fetch_assoc()) {
            $bookID = $row['ID'];
            $bookTitle = $row['Book_Title'];
            $price = $row['Price'];
            $authors = $row['Authors'];
            // $imagePath = '../resources/' . $bookID . '.png';
            $imagePath = $row['ImagePath'];


            // Wyświetlanie książki pojedynczo
            echo '<article class="book-admin">';
            echo '<form action="' . ($_SERVER["PHP_SELF"]) . '" method="post">';
            
            echo '<a href="./admin_book.php?id=' . $bookID . '">';
            echo '<img class="admin-book-image" src=".' . $imagePath . '" alt="' . $bookTitle . '">';
            echo '</a>';
            echo '<h2>' . $bookTitle . '</h2>';
            echo '<p>Cena: $' . $price . '</p>';
            // echo '<p>Autorzy: ' . $authors . '</p>';
            echo '<input type="hidden" name="book_id" value="' . $bookID . '">';
            echo '<div class="admin-index-buttons">';
            echo '<button class="edit-button" type="submit" formaction="admin_edit_book_picture.php?id=' . $bookID . '" name="editBookPicture">Edytuj zdjęcie</button>';
            echo '<button class="edit-button" type="submit" formaction="admin_edit_book.php?id=' . $bookID . '" name="editBook">Edytuj książkę</button>';
            echo '<button class="delete-button" type="submit" name="deleteBook">Usuń książkę</button>';
            echo '</div>';
            echo '</form>';
            echo '</article>';

        }
    echo '</section">';

    $mysqli->close();
    ?>
    </main>

    <?php include '../partials/footer.html'; ?>

</body>
</html>