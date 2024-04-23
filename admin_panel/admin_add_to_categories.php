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
    <?php
    $mysqli = new mysqli("localhost", "root", "", "bookstore");


    if ($mysqli->connect_error) {
        die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
    }

    echo '<section class="book-list-admin">';

    $categoryID = isset($_GET['categoryID']) ? $_GET['categoryID'] : null;

    if ($categoryID !== null) {
        // Zapytanie do wykluczenia książek powiązanych z wybraną kategorią
        $query = "SELECT books.Book_Title, books.Price, books.ID
                  FROM books
                  WHERE books.ID NOT IN (SELECT Book_ID FROM bookcategories WHERE Category_ID = ?)";
    
        $stmt = $mysqli->prepare($query);
    
        if ($stmt) {
            $stmt->bind_param("i", $categoryID);
            $stmt->execute();
            $result = $stmt->get_result();
    
            while ($row = $result->fetch_assoc()) {
                $bookTitle = $row['Book_Title'];
                $price = $row['Price'];
                $imagePath = '../resources/' . $row['ID'] . '.png';
                $IDfield = $row['ID'];
    
                // Wyświetlanie książek pojedynczo
                echo '<form style="width: 100%" action="' . ($_SERVER["PHP_SELF"]) . '" method="post">';
                
                    echo '<article class="book-admin">';
                        echo '<div class="book-admin-wrapper">';
                            echo '<div class="book-admin-left-side">';
                                echo '<img class="admin-book-image" src="' . $imagePath . '" alt="' . $bookTitle . '">';
                                echo '<h2>' . $bookTitle . '</h2>';
                                echo '<input type="hidden" name="book_id" value="' . $IDfield . '">';
                                echo '<input type="hidden" name="category_id" value="' . $categoryID . '">';
                            echo '</div>';
                            echo '<div class="book-admin-right-side">';
                                echo '<button class="add-button" type="submit" formaction="admin_add_to_categories.php?id=' . $IDfield . '" name="addBook">Dodaj książkę</button>';
                            echo '</div>';
                        echo '</div>';
                    echo '</article>';
               
                echo '</form>';
            }
    
            $stmt->close();
        } else {
            echo "Błąd przygotowywania zapytania SQL: " . $mysqli->error;
        }
    }
    
    echo '</section>';
    $mysqli->close();
    ?>
    
    <?php include '../post_requests/post_add_to_categories.php'; ?>
    </main>

    <?php include '../partials/footer.html'; ?>

</body>
</html>;