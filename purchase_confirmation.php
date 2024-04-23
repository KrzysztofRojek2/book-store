<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
    <?php include './partials/header.php'; ?>
    <div class="purchase-confirmation-wrapper">

        <h1>Dziękujemy za zamówienie!</h1>
        <p>Na twoją pocztę zostanie wysłany e-mail z potwierdzeniem zamówienia wraz z informacjami o zakupie.
            Jeśli masz jakiekolwiek pytania na temat zakupu skontaktuj się z nami poprzez e-mail <a href="#">livre.ksiegarnia@wp.pl </a>
            lub zadzwoń pod numer <a href="#">123-1234-9876</a>.
        </p>
        <div><a href="./index.php"><button class="return-to-main-btn">Wróć do strony głównej</button></a></div>
        <div><a href="./my_orders.php"><button class="browse-orders-btn">Przeglądaj zamówienia</button></a></div>
    </div>
</body>
    <?php include './partials/footer.html'; ?>
    <style>
        @media (max-width: 900px) {
            h1 {
                font-size: 1.5rem;
            }
            .purchase-confirmation-wrapper p {
                font-size: 1rem;
            }

            .return-to-main-btn, .browse-orders-btn {
            font-size: 1rem;
            padding: 0.8rem;
        }
    }

        @media (max-width: 600px) {
            h1 {
                font-size: 1rem;
            }
            .purchase-confirmation-wrapper p {
                font-size: 0.7rem;
            }

            .return-to-main-btn, .browse-orders-btn {
                font-size: 0.8rem;
                padding: 0.6rem;
            }
            .purchase-confirmation-wrapper {
                height: 250px;
                margin: 2rem;
            }
        }
    @media (max-width: 360px) {
        .purchase-confirmation-wrapper p {
                font-size: 0.6rem;
            }
            .return-to-main-btn, .browse-orders-btn {
            font-size: 0.6rem;
            padding: 0.4rem;
        }
    }

        
    </style>
</html>