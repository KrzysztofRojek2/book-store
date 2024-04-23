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
        <nav class="header-navbar">
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
                                    echo '<ul class="standard-navbar">';
                                    echo '<li><a href="index.php">Strona główna</a></li>';
                                    echo '<li><a href="./about.php">O nas</a></li>';
                                    echo '<li><a href="./contact.php">Kontakt</a></li>';
                                    echo '<li><a href="user_panel.php">Witaj ' . $username . '</a></li>';
                                    echo '<li><a href="./my_orders.php">Moje zamówienia</a></li>';//to teraz robie
                                    echo '<li><a href="./shopping_cart.php">Koszyk</a></li>';
                                    echo '<li><a href="logout.php">Wyloguj się</a></li>';
                                    echo '</ul>';
                                    echo '<ul class="mini-navbar">';
                                    echo '<li>';
                                    
                                    echo '<label class="hamburger-menu"><input type="checkbox"></label>';
                                    echo '<aside class="sidebar">';
                                        echo '<nav>';
                                            echo '<div><a href="index.php">Strona główna</a></div>';
                                            echo '<div><a href="./about.php">O nas</a></div>';
                                            echo '<div><a href="./contact.php">Kontakt</a></div>';
                                            echo '<div><a href="user_panel.php">Witaj ' . $username . '</a></div>';
                                            echo '<div><a href="./my_orders.php">Moje zamówienia</a></div>';
                                        echo '</nav>';
                                    echo '</aside>';
                                    
                                    echo '</li>';
                                    echo '<div class="navbar-icons-wrapper">';
                                        echo '<li><a href="./shopping_cart.php"><img src="assets\icons8-cart.png" alt="menu"></a></li>';
                                        echo '<li><a href="logout.php"><img src="assets\icons8-login.png" alt="menu"></a></li>';
                                    echo '</div>';
                                    echo '</ul>';

                            } else {
                                echo '<ul class="standard-admin-navbar">';
                                echo '<li><a href="../admin_panel/admin_index.php">Książki</a></li>';
                                echo '<li><a href="./admin_authors.php">Autorzy</a></li>';
                                echo '<li><a href="./admin_categories.php">Kategorie</a></li>';
                                echo '<li><a href="./admin_add_book.php">Dodaj książkę</a></li>';
                                echo '<li><a href="./admin_add_author.php">Dodaj autora</a></li>';
                                echo '<li><a href="./admin_add_category.php">Dodaj kategorię</a></li>';
                                echo '<li>Witaj Administratorze </a></li>';
                                echo '<li><a href="./admin_orders.php">Przeglądaj zamówienia</a></li>';
                                echo '<li><a href="logout.php">Wyloguj się</a></li>';
                                echo '</ul>';
                                }
                        $stmt->close();

                        }


                    }
                    } else {
                        echo '<ul class="standard-navbar">';
                        echo '<li><a href="index.php">Strona główna</a></li>';
                        echo '<li><a href="./about.php">O nas</a></li>';
                        echo '<li><a href="./contact.php">Kontakt</a></li>';
                        echo '<li><a href="login.php">Logowanie</a></li>';
                        echo '</ul>';
// 
                        echo '<ul class="mini-navbar">';
                        echo '<li>';
                        
                        echo '<label class="hamburger-menu"><input type="checkbox"></label>';
                        echo '<aside class="sidebar">';
                            echo '<nav>';
                                echo '<div><a href="index.php">Strona główna</a></div>';
                                echo '<div><a href="./about.php">O nas</a></div>';
                                echo '<div><a href="./contact.php">Kontakt</a></div>';
                            echo '</nav>';
                        echo '</aside>';
                        
                        echo '</li>';
                        echo '<li><a href="login.php"><img src="assets\icons8-login.png" alt="menu"></a></li>';
                        // echo '<li><a href="#"><img src="assets\icons8-cart.png" alt="menu"></a></li>';
                        echo '</ul>';
                        // 
                }
                $mysqli->close();
                ?>
                
            </ul>
        </nav>
    </header>
</body>
<style>
    .mini-navbar {
        display: none;
    }
    img {
        height: 30px;
        width: auto;
    }

    @media (max-width: 1000px) {
    .header-navbar ul li {
        margin: 0 1rem; 
        font-size: 0.8rem; 
    }
}

/* @media (max-width: 900px) {
    nav ul li {
        margin: 0; 
        font-size: 0.8rem; 
    }
} */
@media (max-width: 706px) {
    .standard-navbar {
        display: none;
    }
    .mini-navbar {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        margin: 0 1.5rem;
    }
    .header-navbar ul {
    margin-bottom: 0;
}
}

@media (max-width: 400px) {
    .mini-navbar div {
        margin-bottom: 0.2rem;
    }
    img {
        height: 20px;
        width: auto;
    }
    .hamburger-menu::before,
    .hamburger-menu::after,
    .hamburger-menu input {
        width: 20px;
    }
    .hamburger-menu {
        gap: 4px;
    }
}
</style>
</html>