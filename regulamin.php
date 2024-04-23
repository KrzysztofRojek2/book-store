<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regulamin</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>

    <?php include './partials/header.php'; ?>

    <main>
        <section class="about-us">
            <h2>Regulamin</h2>
            <p>
                Witamy w księgarni internetowej "Livre". Prosimy o dokładne zapoznanie się z poniższymi zasadami zakupów.
            </p>

            <h3>§ 1. Postanowienia ogólne</h3>
            <p>
                1. Niniejszy regulamin określa zasady zakupów w księgarni internetowej "Livre" dostępnej pod adresem www.czytajmadrze.pl.<br>
                2. Księgarnia internetowa prowadzona jest przez firmę "Livre Sp. z o.o." z siedzibą w Warszawie, ul. Książkowa 1, wpisaną do Krajowego Rejestru Sądowego pod numerem KRS 0123456789, NIP 123-456-78-90, REGON 987654321.<br>
                3. Klient jest zobowiązany do zapoznania się z treścią regulaminu przed złożeniem zamówienia. Złożenie zamówienia jest równoznaczne z akceptacją postanowień regulaminu.<br>
                4. Firma "Livre Sp. z o.o." zastrzega sobie prawo do zmiany regulaminu. Zmiany wchodzą w życie w terminie 7 dni od ich opublikowania na stronie internetowej księgarni.
            </p>

            <h3>§ 2. Składanie zamówień</h3>
            <p>
                1. Zamówienia można składać poprzez stronę internetową www.czytajmadrze.pl.<br>
                2. Proces składania zamówienia obejmuje wybór produktów, określenie ilości, podanie danych kontaktowych oraz wybór formy płatności i dostawy.<br>
                3. Po złożeniu zamówienia, Klient otrzymuje potwierdzenie na podany adres e-mail, zawierające informacje dotyczące zamówienia oraz numer referencyjny.
            </p>
            <h3>§ 3. Płatności</h3>
            <p>
                1. Ceny podane na stronie internetowej są cenami brutto i zawierają podatek VAT.<br>
                2. Klient ma możliwość wyboru jednej z dostępnych form płatności: przelew bankowy, karta płatnicza.<br>
                3. Zamówienie zostanie zrealizowane po otrzymaniu pełnej kwoty na rachunek bankowy księgarni.
            </p>

            <h3>§ 4. Dostawa</h3>
            <p>
                1. Księgarnia realizuje dostawy na terenie Polski.<br>
                2. Koszty dostawy są uzależnione od wybranej formy dostawy oraz wagi zamówienia. Szczegółowe informacje na ten temat znajdują się na stronie internetowej księgarni.<br>
                3. Czas realizacji zamówienia wynosi od 2 do 5 dni roboczych od momentu zaksięgowania płatności.<br>
                4. Klient ma prawo do odstąpienia od umowy w ciągu 14 dni od otrzymania zamówienia.
            </p>

            <h3>§ 5. Reklamacje i zwroty</h3>
            <p>
                1. W przypadku otrzymania uszkodzonego lub wadliwego produktu, Klient ma prawo do złożenia reklamacji w terminie 14 dni od daty otrzymania zamówienia.<br>
                2. Zwrot produktu możliwy jest w ciągu 14 dni od daty zakupu, przy zachowaniu oryginalnego opakowania i bez śladów użytkowania.<br>
                3. Koszty zwrotu ponosi Klient, chyba że reklamacja wynika z wadliwego produktu.
            </p>

            <h3>§ 6. Ochrona danych osobowych</h3>
            <p>
                1. Dane osobowe Klienta są przetwarzane zgodnie z obowiązującymi przepisami prawa, w tym z Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 z dnia 27 kwietnia 2016 r. w sprawie ochrony osób fizycznych w związku z przetwarzaniem danych osobowych.<br>
                2. Klient ma prawo do dostępu do swoich danych, ich poprawiania, usuwania oraz wniesienia sprzeciwu wobec przetwarzania.
            </p>

            <h3>§ 7. Postanowienia końcowe</h3>
            <p>
                1. Wszelkie spory wynikłe z realizacji zamówienia będą rozstrzygane polubownie. W przypadku braku porozumienia, właściwy będzie sąd powszechny.<br>
                2. Niniejszy regulamin wchodzi w życie z dniem opublikowania na stronie internetowej księgarni.
            </p>
            
        </section>
    </main>

    <footer>
        <?php include './partials/footer.html'; ?>
    </footer>
</body>
<style>
    @media (max-width: 900px) {
        .about-us {
            max-width: 600px;
        }
        .about-us h2 {
            font-size: 1rem;
        } 
        .about-us h3 {
            font-size: 0.9rem;
        } 

        .about-us p {
            font-size: 0.8rem;
        } 

    }
    @media (max-width: 650px) {
        .about-us {
            max-width: 400px;
        }

    }
    @media (max-width: 450px) {
        .about-us {
            max-width: 300px;
            margin: 1rem auto;
        }
        .about-us h2 {
            font-size: 0.8rem;
        } 
        .about-us h3 {
            font-size: 0.7rem;
        } 

        .about-us p {
            font-size: 0.6rem;
        } 

    }
    @media (max-width: 340px) {
        .about-us {
            width: 240px;
            padding: 0.5rem;
            margin: 0.5rem auto;
        }
        .about-us p {
            font-size: 0.45rem;
        } 
    }
</style>
</html>