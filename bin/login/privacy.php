<?php
include '../../bin/init.php';

$sessionExpired = '';
if (isset($_COOKIE['session_exp']) && $_COOKIE['session_exp'] === 'X') {
    $sessionExpired = 'X';
    setcookie("session_exp", "", time() - 3600, "/");
} else {
    $sessionExpired = '';
}

$pageTitle = 'Privacy';
$prefix = '../../';
?>
<!DOCTYPE html>
<html lang="nl">

<?php include '../../includes/head.php'; ?>

<body class="bg-gradient-light">
    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-10">

                <section class="mb-5">
                    <h3 class="fw-bold mb-4">Privacy- en Cookiebeleid</h3>

                    <article class="mb-4">
                        <h4 class="fw-semibold">Privacybeleid</h4>
                        <p>
                            Het gebruik van de website wapobel.be is onderworpen aan onderstaande voorwaarden.
                            Door deze website te bezoeken of te gebruiken, verklaart u zich akkoord met deze voorwaarden.
                            Deze kunnen op elk moment worden aangepast; de geldende versie is deze die van kracht is bij elk bezoek.
                            De website is eigendom van FiMar Consulting, gevestigd te Schavotstraat 5C, 3130 Begijnendijk
                            (hierna: “Wij” of de “Onderneming”).
                        </p>
                    </article>

                    <article class="mb-4">
                        <h4 class="fw-semibold">Gebruik van de website</h4>
                        <p>
                            Wij besteden uiterste zorg aan de samenstelling en inhoud van deze website. Toch kan de
                            Onderneming niet aansprakelijk worden gesteld voor rechtstreekse of onrechtstreekse schade
                            voortvloeiend uit het gebruik van de website of de aangeboden informatie. Wij behouden ons
                            het recht voor om de inhoud op elk moment te wijzigen, aan te vullen of te verwijderen.
                        </p>
                    </article>

                    <article class="mb-4">
                        <h4 class="fw-semibold">Intellectuele eigendom</h4>
                        <p>
                            Het gebruik van de website verleent de gebruiker geen enkel intellectueel eigendomsrecht.
                            Alle inhoud, vormgeving, teksten, logo’s en andere elementen behoren toe aan de Onderneming
                            en zijn beschermd door merkenrecht, auteursrecht en andere toepasselijke wetgeving.
                            Elke reproductie, in welke vorm en met welk middel dan ook, is verboden en kan leiden tot
                            gerechtelijke stappen.
                        </p>
                    </article>

                    <article class="mb-4">
                        <h4 class="fw-semibold">Bescherming van persoonsgegevens</h4>
                        <p>
                            De Onderneming neemt haar wettelijke verplichtingen inzake privacy en gegevensbescherming
                            zeer serieus en behandelt persoonsgegevens met de nodige zorgvuldigheid.
                        </p>
                    </article>

                    <article class="mb-4">
                        <h4 class="fw-semibold">Belgisch recht</h4>
                        <p>
                            De toegang tot en het gebruik van de website, evenals deze voorwaarden, worden beheerst door
                            het Belgisch recht. Bij geschillen zijn uitsluitend de rechtbanken bevoegd van de plaats
                            waar de maatschappelijke zetel van de Onderneming gevestigd is.
                        </p>
                    </article>

                    <article class="mb-4">
                        <h4 class="fw-semibold">Cookiebeleid</h4>
                        <p>
                            Tijdens uw bezoek kan Wapobel informatie op uw toestel plaatsen in de vorm van cookies,
                            mits uw browser dit toestaat. Cookies zijn kleine bestanden die door onze webserver op uw
                            computer, tablet of telefoon worden geplaatst. Wij gebruiken voornamelijk functionele cookies
                            om onze diensten correct te laten werken. Voor sommige cookies kan uw toestemming
                            vereist zijn. Door de website verder te gebruiken geeft u toestemming voor het plaatsen van
                            cookies zoals beschreven in dit beleid.
                        </p>

                        <h5 class="fw-semibold mt-4">Soorten cookies</h5>

                        <h6 class="mt-3 fw-semibold">Functionele cookies</h6>
                        <p>
                            Deze cookies zijn essentieel voor de correcte werking van de website. Ze bewaren onder andere
                            uw cookievoorkeuren, zodat u deze niet telkens opnieuw hoeft te bevestigen.
                        </p>

                        <h6 class="mt-3 fw-semibold">Tracking cookies</h6>
                        <p>
                            Tracking cookies geven ons inzicht in het gebruik van de website. Zo kunnen wij statistieken
                            verzamelen, veel bezochte pagina’s analyseren en de website continu verbeteren.
                        </p>

                        <h6 class="mt-3 fw-semibold">Cookies van derden</h6>
                        <p>
                            Bij marketingcampagnes kunnen derde partijen cookies plaatsen, bijvoorbeeld om te meten
                            hoe vaak op advertenties wordt geklikt of hoeveel bezoekers via campagnes op de site komen.
                        </p>

                        <h5 class="fw-semibold mt-4">Cookies weigeren of verwijderen</h5>
                        <p>
                            Via uw browser kan u cookies verwijderen of het gebruik ervan uitschakelen. Houd er rekening
                            mee dat bepaalde functies van de website hierdoor mogelijks niet correct werken. Daarnaast
                            maakt het ons moeilijker om de website te optimaliseren of gepersonaliseerde inhoud aan te bieden.
                        </p>

                        <p>
                            Op het moment van schrijven maakt deze website gebruik van de hierboven beschreven cookies.
                            Per cookie kan worden nagegaan wat het doel, de geldigheidsduur en de toegangsrechten zijn.
                        </p>
                    </article>

                </section>

            </div>
        </div>

    </div>

    <?php include $prefix . 'includes/scripts.php'; ?>
</body>
</html>
