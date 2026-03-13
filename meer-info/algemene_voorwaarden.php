<?php
include '../bin/init.php';

$sessionExpired = '';
if (isset($_COOKIE['session_exp']) && $_COOKIE['session_exp'] === 'X') {
    $sessionExpired = 'X';
    setcookie("session_exp", "", time() - 3600, "/");
} else {
    $sessionExpired = '';
}

$pageTitle = 'Algemene voorwaarden';
$prefix = '../';
?>
<!DOCTYPE html>
<html lang="nl">

<?php include '../includes/head.php'; ?>

<style>
	h5 {
		margin-left: 1.5rem;
	}
	
    .subcontent {
        font-size: 0.95rem;
        margin-left: 1.5rem;
    }
</style>

<body class="bg-gradient-light">
    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-10">

                <section class="mb-5">
                    <h3 class="fw-bold mb-4">Algemene Voorwaarden</h3>
					
					<article class="mb-4">
						<h4>1. Partijen, contactinformatie en wijzigingen</h4>
						<p>
							Deze Algemene Voorwaarden regelen het gebruik van de diensten van FiMar Consulting BV 
							(hierna: “Wapobel”) door u als gebruiker of klant (hierna: “Gebruiker” of “Klant”). 
							Door toegang te krijgen tot of gebruik te maken van onze diensten verklaart u zich akkoord met deze voorwaarden.
						</p>
						<p>
							Wapobel behoudt zich het recht voor de Algemene Voorwaarden te wijzigen. Gebruikers 
							worden hiervan op de hoogte gebracht. Indien u de wijzigingen niet wenst te aanvaarden, 
							kunt u de overeenkomst beëindigen en het gebruik van de Wapobel-diensten stopzetten. 
							Indien u de diensten blijft gebruiken na de wijziging, wordt dit beschouwd als aanvaarding 
							van de aangepaste voorwaarden.
						</p>
						<p>De meest recente versie is steeds van toepassing en de Gebruiker dient hiervan een eigen kopie te bewaren.</p>

						<h5>a. Wapobel</h5>
						<p class="subcontent">
							FiMar Consulting BV<br>
							Schavotstraat 5C<br>
							3130 Begijnendijk<br>
							België<br>
							Ondernemingsnummer: 0567.561.351<br>
							BTW: BE0567.561.351<br>
							E-mail: <a href="mailto:info@wapobel.be">info@wapobel.be</a>
						</p>

						<h5>b. Klant – Gebruiker</h5>
						<p class="subcontent">
							Per Klant wordt een gebruikersaccount aangemaakt voor beroepsmatig gebruik van de Wapobel-diensten. 
							Een Klant kan meerdere Gebruikers aanduiden, elk met een individueel account. Gebruikers handelen steeds 
							voor rekening van de Klant die zij vertegenwoordigen.
						</p>
						<p class="subcontent">
							De Gebruiker verbindt zich ertoe alle gevraagde gegevens correct, volledig en naar waarheid te verstrekken. 
							Wijzigingen in deze gegevens moeten tijdig worden meegedeeld, via brief of e-mail conform bovenstaande contactgegevens.
						</p>

						<h5>c. Gebruikersaccount</h5>
						<p class="subcontent">
							De Gebruiker krijgt toegang tot het Wapobel Platform via een persoonlijke gebruikersnaam en wachtwoord. 
							Accounts zijn strikt persoonlijk, niet-overdraagbaar en mogen niet gedeeld worden. De Gebruiker is 
							verantwoordelijk voor alle handelingen die via zijn/haar account worden uitgevoerd en dient de toegangsgegevens 
							vertrouwelijk te bewaren.
						</p>
					</article>

					<article class="mb-4">
						<h4>2. Beschrijving van de diensten</h4>
						<p>
							Wapobel biedt een softwareplatform voor online administratie, enkelvoudige boekhouding en koppeling 
							met het Billit-platform. De oplossing is specifiek ontwikkeld voor Vlaamse wateringen en polders.
						</p>

						<h5>a. Dagboek</h5>
						<p class="subcontent">
							Het dagboek laat toe dagelijkse ontvangsten en uitgaven te registreren per post. Post, datum, omschrijving, 
							factuurnummer en bedragen blijven nadien aanpasbaar.
						</p>

						<h5>b. Posten</h5>
						<p class="subcontent">
							Overzicht van Ontvangsten- en Uitgavenposten. Per post kan een begroting worden ingevoerd en worden de 
							geboekte totalen weergegeven. Posten kunnen actief/inactief worden gezet, subposten kunnen worden aangemaakt 
							of verwijderd (indien nog niet geboekt). Ook het reservefonds en totalenoverzicht zijn hier beschikbaar.
						</p>

						<h5>c. Rekeningen</h5>
						<p class="subcontent">
							De rekeningenmodule toont de rekeningen binnen het huidige boekjaar. Rekeningen kunnen worden aangepast, 
							inactief geplaatst en in volgorde verschoven. Inactieve rekeningen worden niet overgenomen naar een nieuw boekjaar.
						</p>

						<h5>d. Billit Klanten</h5>
						<p class="subcontent">
							Indien Billit geactiveerd is in het profiel toont deze module een overzicht van Billit-klanten.
						</p>

						<h5>e. Billit Leveranciers</h5>
						<p class="subcontent">
							Bij activatie van Billit, wordt hier een overzicht getoond van leveranciers binnen Billit.
						</p>

						<h5>f. Billit Facturen</h5>
						<p class="subcontent">
							Bij activatie van Billit, biedt deze module een overzicht van ontvangen en uitgaande facturen. Facturen kunnen worden 
							ingeboekt door ze toe te wijzen aan een post en rekening, waarna ze automatisch in het dagboek worden opgenomen.
						</p>

						<h5>g. Documenten</h5>
						<p class="subcontent">
							Gebruikers kunnen verschillende rapporten genereren uit het dagboek, begroting, rekeningen en algemene overzichten, 
							in Excel- of PDF-formaat. De gebruikershandleiding is eveneens beschikbaar in deze module.
						</p>
					</article>

					<article class="mb-4">
						<h4>3. Algemeen</h4>

						<h5>a. Profiel</h5>
						<p class="subcontent">
							In het profiel kunnen persoonlijke gegevens en instellingen gewijzigd worden. De Gebruiker kan onder meer 
							factuurnummering activeren en hiervoor een prefix instellen, het al dan niet gebruik van een KAS beheren en de koppeling met Billit 
							activeren, door middel van de Billit API-key (die je terug kan vinden in je Billit profiel). Je kan tevens je wachtwoord aanpassen.
						</p>

						<h5>b. Privacybeheer</h5>
						<p class="subcontent">
							Hier zijn het privacybeleid en cookiebeleid raadpleegbaar.
						</p>

						<h5>c. Contact</h5>
						<p class="subcontent">
							Voor vragen of opmerkingen kan het contactformulier worden gebruikt.
						</p>
					</article>

					<article class="mb-4">
						<h4>4. Kwaliteit van de Wapobel-diensten</h4>

						<h5>a. Gebruiksvriendelijkheid</h5>
						<p class="subcontent">
							Wapobel streeft naar correcte en actuele informatie binnen het platform, maar kan dit niet garanderen. 
							Aansprakelijkheid voor fouten of onvolledige informatie is uitgesloten.
						</p>

						<h5>b. Niveau van dienstverlening</h5>
						<p class="subcontent">
							Wapobel tracht haar diensten zo continu mogelijk beschikbaar te houden, maar garandeert geen ononderbroken gebruik. 
							Onderhoud, storingen of andere omstandigheden kunnen de beschikbaarheid beïnvloeden. In geval van storingen doet 
							Wapobel wat commercieel redelijk is om deze zo snel mogelijk op te lossen.
						</p>

						<h5>c. Technische vereisten</h5>
						<p class="subcontent">
							De Gebruiker dient een recente versie van de internetbrowser naar keuze te gebruiken. Oudere browsers kunnen 
							functieverlies veroorzaken. Een minimale schermresolutie van 1920×1080 wordt aanbevolen.
						</p>
					</article>

					<article class="mb-4">
						<h4>5. Duur van de overeenkomst</h4>
						<p>
							De overeenkomst gaat in op de inschrijvingsdatum van de Gebruiker en geldt minimaal één jaar. Nadien wordt de 
							overeenkomst automatisch verlengd.
						</p>
					</article>

					<article class="mb-4">
						<h4>6. Prijzen, facturering en betaling</h4>

						<h5>a. Basisprijs</h5>
						<p class="subcontent">
							Het gebruik van de Wapobel-diensten wordt jaarlijks aangerekend volgens de geldende tarieven. Wapobel mag deze 
							tarieven wijzigen en brengt Gebruikers hiervan per e-mail op de hoogte. De aangepaste tarieven zijn onmiddellijk 
							van toepassing.
						</p>

						<h5>b. Facturatie</h5>
						<p class="subcontent">
							Facturen worden jaarlijks opgesteld en dienen binnen 10 werkdagen of 15 kalenderdagen betaald te worden. 
							Bij laattijdige betaling is automatisch een forfaitaire schadevergoeding van 15% verschuldigd.
						</p>

						<h5>c. Betaling</h5>
						<p class="subcontent">
							Betalingen gebeuren via overschrijving.
						</p>
					</article>

					<article class="mb-4">
						<h4>7. Rechtmatig en wettelijk gebruik</h4>
						<p>
							Gebruikers mogen de Wapobel-diensten uitsluitend voor rechtmatige doeleinden gebruiken. In communicatie via het 
							platform dient men zich respectvol en volgens algemene fatsoensnormen te gedragen.
						</p>
					</article>

					<article class="mb-4">
						<h4>8. Aansprakelijkheid</h4>
						<p>
							De Gebruiker is volledig verantwoordelijk voor de correctheid van ingegeven data en de opgestelde documenten. 
							Wapobel biedt geen resultaatsverbintenis en garandeert niet dat alle functies steeds foutloos functioneren.
						</p>
						<p>
							De Gebruiker en/of Klant zijn aansprakelijk voor alle gevolgen van foutieve ingave, fiscale gevolgen of claims 
							van derden. Wapobel kan hiervoor niet aansprakelijk worden gesteld.
						</p>
						<p>
							Wapobel is enkel aansprakelijk voor schade die rechtstreeks voortvloeit uit haar opzettelijke fout of bedrog. 
							Iedere andere aansprakelijkheid, ook voor zware fout, wordt uitgesloten. De aansprakelijkheid is in elk geval 
							beperkt tot het totaalbedrag van de facturen betaald in de laatste zes maanden. Indirecte schade, zoals 
							gevolgschade, winstderving of schade door bedrijfsstilstand, is uitgesloten.
						</p>
					</article>

					<article class="mb-4">
						<h4>9. Bescherming van persoonsgegevens</h4>

						<h5>a. Verwerking door de Gebruiker</h5>
						<p class="subcontent">
							De Gebruiker of Klant verwerkt via het platform persoonsgegevens onder eigen verantwoordelijkheid en dient 
							conform de wetgeving te handelen. Wapobel treedt op als verwerker en voert geen bewerkingen uit zonder instructies.
						</p>

						<h5>b. Verwerking door Wapobel</h5>
						<p class="subcontent">
							Bij registratie verstrekt de Gebruiker persoonsgegevens aan Wapobel. Deze worden enkel gebruikt voor de diensten, 
							accountbeheer, facturatie en verbetering van de werking. Gegevens worden nooit doorgegeven aan derden en niet langer 
							bewaard dan noodzakelijk.
						</p>
					</article>

					<article class="mb-4">
						<h4>10. Beveiliging</h4>
						<p>
							Wapobel neemt technische en organisatorische maatregelen om ongeoorloofde toegang, verlies of wijzigingen van gegevens te voorkomen. Toch kunnen incidenten nooit volledig worden uitgesloten.
						</p>
					</article>

					<article class="mb-4">
						<h4>11. Intellectuele rechten</h4>
						<p>
							Het Wapobel-platform en bijhorende materialen zijn beschermd door intellectuele eigendomsrechten. Reproductie of openbaarmaking zonder schriftelijke toestemming is verboden.
						</p>
					</article>

					<article class="mb-4">
						<h4>12. Eigendom van data</h4>
						<p>
							De Klant of Gebruiker blijft eigenaar van alle ingevoerde data. Gegevens worden in standaardformaat opgeslagen en kunnen op elk moment worden gedownload of afgedrukt.
						</p>
					</article>

					<article class="mb-4">
						<h4>13. Schorsing van gebruikersaccounts</h4>
						<p>
							Wapobel kan accounts blokkeren indien de Gebruiker de overeenkomst schendt of bij vermoeden van illegaal gebruik. Gebruikers beschikken over 30 dagen om bezwaar in te dienen.
						</p>
					</article>

					<article class="mb-4">
						<h4>14. Beëindiging van de overeenkomst</h4>

						<h5>a. Gronden voor beëindiging</h5>
						<p class="subcontent">
							De Klant kan op elk moment opzeggen met een opzegtermijn van één maand via <a href="mailto:info@wapobel.be">info@wapobel.be</a>.
						</p>

						<h5>b. Gevolgen</h5>
						<p class="subcontent">
							De toegang tot Wapobel en de Wapobel diensten wordt onmiddellijk geblokkeerd. Data blijft één maand beschikbaar voor overdracht. Daarna kan Wapobel alle gegevens verwijderen. Er vindt geen terugbetaling plaats.
						</p>
					</article>
					
					<article class="mb-4">
						<h4>15. Overige bepalingen</h4>

						<h5>a. Klachten</h5>
						<p class="subcontent">
							Facturen moeten binnen 10 werkdagen of 15 kalenderdagen worden geprotesteerd. Andere klachten kunnen gemeld worden 
							via <a href="mailto:info@wapobel.be">info@wapobel.be</a>.
						</p>

						<h5>b. Overmacht</h5>
						<p class="subcontent">
							Bij overmacht (dit zijn omstandigheden buiten de wil van Wapobel) is Wapobel vrijgesteld van alle verplichtingen en de daaruit volgende aansprakelijkheid. Het volgende wordt onder meer als overmacht beschouwd: natuuromstandigheden, brand, overstroming, inbeslagname, embargo, algemene schaarste aan goederen, ziekte en in het algemeen elke onvoorziene omstandigheid die het contractuele evenwicht in aanzienlijke mate verstoort, en dit onafhankelijk of de overmacht zich voordoet bij Wapobel, bij één van haar leveranciers of één van haar medewerkers.
						</p>

						<h5>c. Nietigheid</h5>
						<p class="subcontent">
							Bij het nietig verklaren van één of meer bepalingen uit deze algemene voorwaarden, blijven de overige bepalingen gelden. Partijen zullen de nietige bepaling vervangen door een rechtsgeldige bepaling die het oorspronkelijke doel het dichtst benadert.
						</p>

						<h5>d. Toepasselijk recht en bevoegde rechtbank</h5>
						<p class="subcontent">
							Op de overeenkomst is Belgisch recht van toepassing. Enkel de rechtbank van Leuven is bevoegd bij geschillen.
						</p>
					</article>
                </section>
            </div>
        </div>

    </div>

    <?php include $prefix . 'includes/scripts.php'; ?>
</body>
</html>
