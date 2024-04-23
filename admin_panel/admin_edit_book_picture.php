<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "bookstore");

        if ($mysqli->connect_error) {
            die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
        }
if(isset($_GET['id'])) {

    
    $book_id_to_edit = $_GET['id'];
    // echo "!" . $book_id_to_edit .  "!";
    $query = "SELECT * FROM books WHERE ID = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("i", $book_id_to_edit);
        $stmt->execute();
        $result = $stmt->get_result();
        $bookData = $result->fetch_assoc();
        $stmt->close();
    }
}
    if (isset($_POST['submit'])) {

        echo "*" . $book_id_to_edit .  "*";
        $uploadDir = '../resources/';
        $uploadedFile = $uploadDir . basename($_FILES['file']['name']);
        
        $uploadedFileDir = './resources/' . basename($_FILES['file']['name']);
        if (file_exists($uploadedFile)) {
            echo "Plik o tej nazwie już istnieje.";
        } else {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFile)) {
                echo "Zdjęcie zostało przesłane i zapisane w folderze 'resources'.";

                $updateImagePathQuery = "UPDATE books SET ImagePath = ? WHERE ID = ?";
                if ($updateStmt = $mysqli->prepare($updateImagePathQuery)) {
                    $updateStmt->bind_param("si", $uploadedFileDir, $_GET['id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                    echo "!" . $uploadedFileDir .  "!";

                    echo "!" . $book_id_to_edit .  "!";
                } else {
                    echo "Błąd przy aktualizacji pola ImagePath: " . $mysqli->error;
                }
            } else {
                echo "Wystąpił błąd podczas przesyłania zdjęcia.";
            }
        }
    }
    $mysqli->close();
?>


    <form action="admin_edit_book_picture.php?id=<?php echo $book_id_to_edit; ?>" method="post" enctype="multipart/form-data">
        <label for="file">Wybierz zdjęcie:</label>
        <input type="file" name="file" id="file">
        <button type="submit" name="submit">Prześlij</button>
    </form>
    <?php include '../partials/footer.html'; ?>

</body>
<style>
    form{
        margin: 31vh;
    }
</style>
</html>