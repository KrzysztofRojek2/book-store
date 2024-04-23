<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O nas - Księgarnia</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

    <?php include './partials/header.php'; ?>

    <main>
        <section class="about-us">
            <h2>O nas</h2>
            <p>
                Nasza księgarnia to miejsce, w którym pasjonaci literatury mogą znaleźć szeroki wybór książek
                w różnych kategoriach. Od bestsellerów po literaturę klasyczną, oferujemy bogatą gamę tytułów.
                Nasza misja to promowanie czytelnictwa i dostarczanie klientom najlepszych książek.
            </p>
            <p>
                W naszej księgarni pracują wykwalifikowani pracownicy, którzy chętnie pomogą w znalezieniu
                odpowiednich książek i udzielą informacji na temat nowości wydawniczych. Zapraszamy do odwiedzenia
                naszego sklepu i dołączenia do naszej społeczności czytelników.
            </p>
        </section>
    </main>

    <?php include './partials/footer.html'; ?>
</body>
<style>
    @media (max-width: 900px) {
        .about-us {
            max-width: 600px;
        }
        .about-us h2 {
            font-size: 1rem;
        } 

        .about-us p {
            font-size: 0.8rem;
        } 

    }
    @media (max-width: 600px) {
        .about-us {
            max-width: 400px;
        }

    }
    @media (max-width: 450px) {
        .about-us {
            max-width: 300px;
        }
        .about-us h2 {
            font-size: 0.8rem;
        } 

        .about-us p {
            font-size: 0.6rem;
        } 

    }
    @media (max-width: 340px) {
        .about-us {
            padding: 1rem;
        }
    }
</style>
</html>