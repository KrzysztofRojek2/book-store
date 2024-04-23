<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - Księgarnia</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

    <?php include './partials/header.php'; ?>

    <main>
        <section class="bookstore-contact">
            <h2>Kontakt</h2>
            <p>Skontaktuj się z nami, jeśli masz pytania lub potrzebujesz pomocy. Jesteśmy dostępni przez poniższe dane kontaktowe:</p>

            <ul>
                <li>Email: <a href="mailto:info@ksiegarnia.pl">info@ksiegarnia.pl</a></li>
                <li>Telefon: <a href="tel:+48123456789">+48 12 345 67 89</a></li>
                <li>Adres: ul. Książkowa 123, 00-000 Miastowo</li>
            </ul>
        </section>
    </main>

    <?php include './partials/footer.html'; ?>
</body>
<style>
    @media (max-width: 900px) {
        .bookstore-contact {
            max-width: 600px;
        }
        .bookstore-contact h2 {
            font-size: 1rem;
        } 

        .bookstore-contact p {
            font-size: 0.8rem;
        } 
        .bookstore-contact li {
            font-size: 0.8rem;
        }

    }
    @media (max-width: 600px) {
        .bookstore-contact {
            max-width: 400px;
        }

    }

    @media (max-width: 450px) {
        .bookstore-contact {
            max-width: 300px;
        }
        .bookstore-contact h2 {
            font-size: 0.8rem;
        } 

        .bookstore-contact p {
            font-size: 0.6rem;
        } 
        .bookstore-contact li {
            font-size: 0.6rem;
        }
    }

    @media (max-width: 340px) {
        .bookstore-contact {
            padding: 1rem;
        }
}
</style>
</html>
