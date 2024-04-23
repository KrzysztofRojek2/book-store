<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <h1>Księgarnia Livre</h1>
        <div class="navbar-divider"></div>
        <nav>
            <ul>
                
                <?php
                    $db_server = "localhost";
                    $db_user = "root";
                    $db_pass = "";
                    $db_name = "bookstore";
                    $mysqli = new mysqli($db_server, $db_user, $db_pass, $db_name);

                session_start();
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $getUsernameQuery = "SELECT username, IsAdmin FROM clients WHERE ID = ?";

                    if ($stmt = $mysqli->prepare($getUsernameQuery)) {
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $stmt->bind_result($username, $isAdmin);

                            if ($stmt->fetch()) {
                                if($isAdmin==0){
                            echo '<li><a href="index.php">Strona główna</a></li>';
                            echo '<li><a href="./about.php">O nas</a></li>';
                            echo '<li><a href="./contact.php">Kontakt</a></li>';
                            echo '<li><a href="user_panel.php">Witaj ' . $username . '</a></li>';
                            echo '<li><a href="logout.php">Wyloguj się</a></li>';
                            echo '<li><a href="./shopping_cart.php">Koszyk</a></li>';
                            } else {
                            echo '<li><a href="./admin_panel/admin_index.php">Książki</a></li>';
                            echo '<li><a href="./admin_authors.php">Autorzy</a></li>';
                            echo '<li><a href="./admin_categories.php">Kategorie</a></li>';
                            echo '<li><a href="./admin_add_book.php">Dodaj książkę</a></li>';
                            echo '<li><a href="./admin_add_author.php">Dodaj autora</a></li>';
                            echo '<li><a href="./admin_add_category.php">Dodaj kategorię</a></li>';
                            echo '<li>Witaj Administratorze </a></li>';
                            echo '<li><a href="logout.php">Wyloguj się</a></li>';
                                }
                        $stmt->close();

                        }


                    }
                    } else {
                    echo '<li><a href="index.php">Strona główna</a></li>';
                    echo '<li><a href="./about.php">O nas</a></li>';
                    echo '<li><a href="./contact.php">Kontakt</a></li>';
                    echo '<li><a href="login.php">Logowanie</a></li>';
                }
                $mysqli->close();
                ?>
                
            </ul>
        </nav>
    </header>
</body>

</html>