<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Księgarnia</title>
    <link rel="stylesheet" href="./styles.css">
    <script src="./script.js" defer></script>
</head>
<body>
    <?php include './partials/header.php'; ?>
    <?php
        if (isset($_GET['order_success']) && $_GET['order_success'] == 1) {
            echo '<script>alert("Zamówienie zostało złożone.");</script>';
        }
    ?>
    <main>
        <div id="filter-section">
            <h3>Filtrowanie</h3>
            <form class="filter-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

            <div class="filter-odd-col">
            <div class="filter-even-col">

            
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
                </div>

            <div class="filter-even-col">

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

            </div>
            </div>
            <div class="filter-odd-col">
            <div class="filter-even-col">

                <label for="minPrice">Cena od:</label>
                <input type="number" name="minPrice" step="1" value="<?php echo isset($_POST['minPrice']) ? $_POST['minPrice'] : ''; ?>">
                </div>

            <div class="filter-even-col">

                <label for="maxPrice">Cena do:</label>
                <input type="number" name="maxPrice" step="1" value="<?php echo isset($_POST['maxPrice']) ? $_POST['maxPrice'] : ''; ?>">
            </div>
            </div>

            <div class="filter-btn">
                <button type="submit" name="filterBooks">Filtruj</button>
            </div>

            
            </form>
    </div>

    <div class="slider-wrapper">
        <div class="slider">
            <img id="slide-1" src="./assets/slider1.jpg" alt="slide-1">
            <img id="slide-2" src="./assets/slider2.jpg" alt="slide-2">
            <img id="slide-3" src="./assets/slider3.jpg" alt="slide-3">
        </div>
        <div class="slider-nav">
            <a href="#slide-1"></a>
            <a href="#slide-2"></a>
            <a href="#slide-3"></a>
        </div>
    </div>
    <?php
        // Wyświetlanie książek z uwzględnieniem filtrów
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
        echo '<section class="book-list">';

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
        $categories = $row['Categories'];
        $imagePath = $row['ImagePath'];

        $authorsArray = explode(",", $authors);
        $categoriesArray = explode(",", $categories);

        $firstAuthor = reset($authorsArray);
        $firstCategory = reset($categoriesArray);

        echo '<article class="book">';
        echo '<form action="' . ($_SERVER["PHP_SELF"]) . '" method="post">';
        echo '<a href="book.php?id=' . $bookID . '">';
        echo '<img class="index-book-image" src="' . $imagePath . '" alt="' . $bookTitle . '">';
        echo '</a>';
        echo '<h2>' . $bookTitle . '</h2>';
        echo '<p>Cena: ' . $price . 'zł</p>';
        echo '<p>Autor: ' . $firstAuthor . '</p>';
        echo '<p>Kategoria: ' . $firstCategory . '</p>';
        echo '<input type="hidden" name="book_id" value="' . $bookID . '">';
        echo '<button type="submit" name="addToCart">Dodaj do koszyka</button>'; 
        echo '</form>';
        echo '</article>';
    }
    echo '</section">';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addToCart'])) {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $book_id = $_POST['book_id'];
    
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
                                                            echo '<script>alert("Książka została dodana do koszyka.");</script>';
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
                                                        echo '<script>alert("Książka została dodana do koszyka.");</script>';
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
        }
    }

    $mysqli->close();
    ?>


    <div class="cookies-wrapper">
        <header class="cookies-header">
            <h2>Polityka Cookies</h2>
        </header>
        <div class="cookies-data">
            <p>Czy zgadzasz się na wykorzystywanie plików cookies? Pamiętaj ,że wyłączenie plików cookies może wpłynąć na funkcjonalność naszej strony.</p>
        </div>
        <div class="buttons">
            <button class="cookie-button" id="acceptBtn">Zgadzam się</button>
            <button class="cookie-button">Nie zgadzam się</button>
        </div>

    </div>
    </main>
    
    <?php include './partials/footer.html'; ?>
</body>
<style>
    .slider img {
        width: 600px;
        height: auto;
    }
    
    @media (max-width: 1480px) {
        
    .book p {
        /* font-size: 0.90rem; */
    }
    button {
        min-width: 149px;
    }
    .book {
        min-height: 400px;
    }
    .book-list {
        /* grid-template-columns: repeat(3, 1fr); */
    }

    #filter-section select {
        max-width: 100px;
    }

    #filter-section input {
        max-width: 100px;
    }

}

@media (max-width: 1260px) {

    .book {
        min-height: 450px;
    }

    .book-list {
        grid-template-columns: repeat(3, 1fr);
    }
    button {
        min-height: 60px;
        font-size: 1.1rem;
    }
    #filter-section button {
        padding: 5px 10px;
        min-height: 40px;
        min-width: 80px;
        font-size: 0.9rem;
    }
}
@media (max-width: 970px) {
    .book-list {
        grid-template-columns: repeat(2, 1fr);
    }

    #filter-section input {
        max-width: 80px;
    }

    .filter-odd-col,
    .filter-btn {
        flex-direction: column;
        /* align-items: stretch; */
    }
    .filter-btn{
        justify-content: flex-end;
    }
    

}

@media (max-width: 640px) {

    .slider img {
    width: 280px;
    height: auto;
}

    .book-list {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        /* grid-template-columns: repeat(1, 1fr); */
        /* max-width: 350px; */
    }
    .book {
        min-width: 400px;
    }
    .cookies-wrapper {
        width: 220px;
        height: auto;
        gap: 5px;

    }
    .buttons {
    flex-direction: column;
    gap: 10px;
    }
    .cookies-header {
    font-size: 0.8rem;
}
}

@media (max-width: 470px){

    .book {
    min-width: 300px;
    padding: 1rem;
    min-height: 370px;

    }
    .book p{
    font-size: 0.8rem;
    }
    .book h2{
        font-size: 1.2rem;
    }
    .filter-form{
        flex-direction: column;
    }
    .filter-even-col{
        justify-content: center;
    }

}

@media (max-width: 360px){

.book {
    min-width: 220px;
}

}
</style>
</html>