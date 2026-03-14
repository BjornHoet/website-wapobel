<?php
$prefix = '../';
$activeDagboek = '';
$activePost = '';
$activeRekening = '';

include $prefix.'/bin/init.php';
$pageTitle = 'Contact';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}
?>
<!DOCTYPE html>
<html lang="en">

<?php include $prefix.'includes/head.php';?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

		<?php include $prefix.'includes/sidebar.php';?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

				<?php include $prefix.'includes/topbar.php';?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Body -->
                                <div class="card-body">
									<div class="row">

									  <!-- Linker kolom: info -->
										<div class="col-md-6 p-4">
										  <h5 class="text-primary font-weight-bold mb-3">
											Contacteer Wapobel
										  </h5>

										  <p class="text-dark mb-3">
											Heb je een vraag of een probleem met het online programma?
											Stuur gerust een e-mail. We proberen je zo snel mogelijk te contacteren.
											Voor dringende problemen mag je altijd bellen.
										  </p>

										  <p class="mb-3">
											<ul>
											    <li><strong>Mail</strong>: <a href="mailto:info@wapobel.be?subject=Wapobel - Ik heb een vraag of een probleem">info@wapobel.be</a></li>
											    <li><strong>Telefoon</strong>: 0498/61.31.81</li>
											</ul>
										  </p>

										  <p class="text-dark mb-4">
											Je kan ook het contactformulier hiernaast invullen.
											Zo beschikken we meteen over alle nodige gegevens.
										  </p>

										  <div class="d-flex align-items-center">
											<img class="logo-sm mr-2" src="<?php echo $prefix ?>img/logo-horizontal.png">
											<span class="text-muted">v2.6</span>
										  </div>
										</div>


									  <!-- Rechter kolom: formulier -->
										<div class="col-md-6 p-4">
										  <h5 class="text-primary font-weight-bold mb-3">
											Contactformulier
										  </h5>

										  <form id="contactForm">
											<div class="mb-4">
											  <div><strong>Gebruiker:</strong> <?php echo $userName ?></div>
											  <div><strong>Naam:</strong> <?php echo $firstName ?> <?php echo $lastName ?></div>
											</div>

											<div class="form-group row">
											  <label class="col-sm-3 col-form-label font-weight-bold">
												Type melding
											  </label>
											  <div class="col-sm-6">
												<select class="form-control" name="contactType" required>
												  <option value="">Kies type</option>
												  <option value="vraag">Vraag of probleem</option>
												  <option value="suggestie">Suggestie ter verbetering</option>
												</select>
											  </div>
											</div>

											<div class="form-group row">
											  <label class="col-sm-3 col-form-label font-weight-bold">
												Toelichting
											  </label>
											  <div class="col-sm-6">
												<textarea id="userMessage" name="contactMessage" rows="4" class="form-control" placeholder="Geef hier extra uitleg..." required></textarea>
											  </div>
											</div>

											<button type="submit" class="btn btn-primary px-4">
											  Verzenden
											</button>

											<div class="mt-3" id="success"></div>
										  </form>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>
				</div>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Body -->
                                <div class="card-body">
									<div class="row">

									  <!-- Linker kolom: info -->
										<div class="col-md-12 p-4">
										  <h5 class="text-primary font-weight-bold mb-3">
											Versiebeheer
										  </h5>

										  <div id="newsAccordion"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

			<?php include $prefix.'includes/footer.php';?>       
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

	<?php include $prefix.'includes/modals.php';?>

	<?php include $prefix.'includes/scripts.php';?>

	<script>
	const newsPages = [

		// v2.6
		{
			version: "2.6",
			date: "15 maart 2026",
			title: "Restyling wijzigen posten",
			text: "Het <strong>wijzigen van posten</strong> is <strong>gebruiksvriendelijker</strong> geworden. In plaats van een nieuwe pagina te openen, tonen we een <strong>popup venster</strong>. De posten en subposten worden nu weergegeven in de <strong>gekende tabelvorm</strong> die we gebruiken bij het dagboek en de rekeningen.",
			image: "img/news/2.6.wijzigPosten.png"
		},
		{
			version: "2.6",
			date: "15 maart 2026",
			title: "Dagboek documenten",
			text: "In de <strong>dagboek documenten</strong> (PDF en Excel), is er onderaan ook de rij '<strong>Rekeningtotaal</strong>' toegevoegd. Dit geeft de stand van de rekening weer voor die maand.<br><br>",
			image: "img/news/2.6.dagboekDocumenten.png"
		},
		{
			version: "2.6",
			date: "15 maart 2026",
			title: "Versiebeheer",
			text: "Je kan bekijken wat er <strong>gewijzigd is doorheen de versies</strong>. Deze informatie kan je terugvinden op de <strong>contactpagina</strong>.",
			image: "img/news/2.6.versiebeheer.png"
		},

		// v2.5
		{
			version: "2.5",
			date: "7 februari 2026",
			title: "Niet relevante transacties/facturen",
			text: "Als je bijvoorbeeld de <strong>CODA-bestanden in Billit</strong> actief hebt, kan het zijn dat er <strong>veel transacties</strong> binnenkomen die <strong>niet relevant</strong> zijn in Wapobel. Je kunt deze <strong>selecteren</strong> en op de knop ‘<strong>Niet relevant</strong>’ klikken. Ze krijgen dan ook deze status.",
			image: "img/news/2.5.nietrelevant1.png"
		},
		{
			version: "2.5",
			date: "7 februari 2026",
			title: "Niet relevante transacties/facturen",
			text: "Je kunt de transacties altijd terugvinden door ‘<strong>Toon alle facturen</strong>’ te selecteren. Ze worden in het <strong>oranje</strong> weergegeven. Je kunt ze bovendien <strong>nog steeds inboeken</strong> in Wapobel door erop te klikken.<br>&nbsp;",
			image: "img/news/2.5.nietrelevant2.png"
		},
		{
			version: "2.5",
			date: "7 februari 2026",
			title: "Inactief zetten van een rekening",
			text: "Een rekening kan <strong>niet meer inactief worden gezet</strong> als er al <strong>bedragen op zijn geboekt</strong>. Dit zie je ook wanneer je de rekening wijzigt.<br>&nbsp;",
			image: "img/news/2.5.rekening1.png"
		},
		{
			version: "2.5",
			date: "7 februari 2026",
			title: "Rekeningen verwijderen",
			text: "<strong>Inactieve rekeningen</strong> kan je nu ook <strong>verwijderen</strong>. Je zal nog even moeten bevestigen na het klikken op '<strong>Verwijder rekening</strong>'.<br>&nbsp;",
			image: "img/news/2.5.rekening2.png"
		},
		{
			version: "2.5",
			date: "7 februari 2026",
			title: "Performantie inboeken",
			text: "Het inboeken van een transactie of factuur gaat nu <strong>veel sneller</strong>, wat het <strong>gebruiksgemak aanzienlijk verbetert</strong>.<br>&nbsp;",
			image: "img/news/2.5.performantie.png"
		},

		// v2.4
		{
			version: "2.4",
			date: "28 januari 2026",
			title: "Hoofding en totalen van dagboektabel vastzetten",
			text: "Als je <strong>veel gegevens</strong> in de dagboek hebt, kan je de <strong>hoofding</strong> en de <strong>totaalrijen vastzetten</strong>. Je kan dan door de gegevens scrollen. Zo heb je steeds een <strong>duidelijk totaaloverzicht</strong>.<br>&nbsp;",
			image: "img/news/2.4.boekingstabel.png"
		},
		{
			version: "2.4",
			date: "28 januari 2026",
			title: "Negatief startbedrag op je rekeningen",
			text: "Je kan nu ook een <strong>negatief startbedrag</strong> zetten op je rekening.<br>&nbsp;<br>&nbsp;",
			image: "img/news/2.4.negatiefbedrag.png"
		},
		{
			version: "2.4",
			date: "28 januari 2026",
			title: "Betaaldatum bij Billitboekingen",
			text: "De <strong>betaaldatum van Billit</strong> is toegevoegd in de tabel en bij het inboeken van je factuur/transactie. Als de betaaldatum aanwezig is, zal deze gebruikt worden om de <strong>boekingsdatum</strong> op te vullen.<br>&nbsp;",
			image: "img/news/2.4.betaaldopdatum.png"
		},
		{
			version: "2.4",
			date: "28 januari 2026",
			title: "Omschrijving bij Billitboekingen",
			text: "Indien er geen <strong>factuurreferentie van Billit</strong> beschikbaar is, wordt de <strong>omschrijving van de post</strong> gebruikt als <strong>boekingsomschrijving</strong>.<br>&nbsp;",
			image: "img/news/2.4.billitomschrijving.png"
		},
		{
			version: "2.4",
			date: "28 januari 2026",
			title: "Omschrijving blijft bij het wijzigen van een post",
			text: "Wanneer je de post van een <strong>bestaande boeking wijzigt</strong>, wordt de <strong>omschrijving niet opnieuw overschreven</strong> door die van de nieuwe (sub)post. Eventuele aanpassingen aan de omschrijving blijven behouden.",
			image: "img/news/2.4.wijzigenpost.png"
		},

		// v2.3
		{
			version: "2.3",
			date: "19 januari 2026",
			title: "Billitnummering beschikbaar op je dagboek",
			text: "Werd de <strong>Billitnummering</strong> niet automatisch overgenomen bij het inboeken van een Billit-factuur of -transactie? Dan kan je deze nu alsnog toevoegen in je <strong>dagboekoverzicht</strong>. Ga hiervoor naar je <strong>profiel</strong> en activeer ‘<strong>Toon Billitnummering</strong>’.",
			image: "img/news/2.3.billitnummer1.png"
		},
		{
			version: "2.3",
			date: "19 januari 2026",
			title: "Billitnummering beschikbaar op je dagboek",
			text: "De <strong>Billitnummering</strong> wordt zichtbaar in je dagboek en kan daar ook aangepast worden. Dit kan zowel <strong>rechtstreeks</strong> in het overzicht als door op het <strong>pennetje</strong> van de boeking te klikken en daar de nummering te wijzigen.",
			image: "img/news/2.3.billitnummer2.png"
		},
		{
			version: "2.3",
			date: "19 januari 2026",
			title: "Nieuwe rij 'Rekeningtotaal' in je dagboek",
			text: "In je dagboek is een <strong>nieuwe rij</strong> toegevoegd, bij de totalen, die de stand van je rekening weergeeft. Hiervoor gebruiken we het <strong>beginsaldo van het huidige boekjaar</strong> en tellen we de <strong>over te dragen bedragen</strong> erbij.<br>&nbsp;",
			image: "img/news/2.3.rekeningtotaal.png"
		},
		{
			version: "2.3",
			date: "19 januari 2026",
			title: "Visuele wijzigingen",
			text: "Er zijn <strong>algemene visuele verbeteringen</strong> doorgevoerd zodat alles <strong>compacter</strong> en <strong>duidelijker</strong> is. In de komende updates wordt hier verder aan gewerkt.<br>&nbsp;",
			image: "img/news/2.3.visuelewijzigingen.png"
		},

		// v2.2
		{
			version: "2.2",
			date: "8 januari 2026",
			title: "Postkeuze bij Billit factuur/transactie",
			text: "Wanneer je een Billit factuur of transactie gaat inboeken, zal de keuze van de posten beperkt worden tot <strong>Uitgaven</strong> of <strong>Ontvangsten</strong> afhankelijk van het soort transactie dat je verwerkt. Dit om een <strong>foutieve postkeuze</strong> te voorkomen.",
			image: "img/news/2.2.billitfacturen2.png"
		},
		{
			version: "2.2",
			date: "8 januari 2026",
			title: "Visualisatie toevoegen Billit factuur/transactie",
			text: "Het toevoegen van een Billit factuur is duidelijker geworden door het scherm te reorganiseren.<br>&nbsp;<br>&nbsp;",
			image: "img/news/2.2.billitfacturen.png"
		},
		{
			version: "2.2",
			date: "8 januari 2026",
			title: "Informatie postkeuze in je dagboek",
			text: "Wanneer je in je dagboek over de referentie van je post gaat met je muis, zal je de omschrijvingen te zien krijgen van je gekozen sub(post).<br>&nbsp;",
			image: "img/news/2.2.posthover.png"
		},
		{
			version: "2.2",
			date: "8 januari 2026",
			title: "Suggesties indienen",
			text: "Naast een vraag of probleem, kan je nu ook een suggestie indienen om het programma te verbeteren. Ga naar de contactpagina via je profiel en dien een suggestie in.<br>&nbsp;",
			image: "img/news/2.2.contact.png"
		},

		// v2.1
		{
			version: "2.1",
			date: "3 december 2025",
			title: "Gebruik van KAS",
			text: "Wanneer je geen <strong>KAS</strong> wenst te gebruiken, kan je dit afzetten in je <strong>Profiel</strong>. KAS verdwijnt dan van je dagboek en in de documenten.<br>&nbsp;",
			image: "img/news/2.1.kas.png"
		},
		{
			version: "2.1",
			date: "3 december 2025",
			title: "Prefix bij factuurnummers",
			text: "Als je factuurnummers gebruikt, kan je nu ook een <strong>prefix</strong> opgeven in je <strong>Profiel</strong>. Deze wordt bij nieuwe boekingen toegevoegd aan je factuurnummer.<br>Let wel: Het laatste karakter mag geen cijfer zijn. <strong>Voorbeeld: 2025-</strong>",
			image: "img/news/2.1.prefix.png"
		}

	];

	$(document).ready(function(){
		const versions = {};

		// groepeer per versie
		newsPages.forEach(item => {
			if(!versions[item.version]){
				versions[item.version] = [];
			}
			versions[item.version].push(item);
		});

		let accordionHTML = '';
		let first = true;

		Object.keys(versions).sort().reverse().forEach(version => {

			let bodyHTML = '';

			versions[version].forEach(item => {

				bodyHTML += `
					<div class="mb-4">
						<h5 class="font-weight-bold">${item.title}</h5>
						<p>${item.text}</p>
						<img src="../${item.image}" class="img-fluid rounded mb-3" style="max-height: 400px">
					</div>
				`;

			});

			accordionHTML += `
			<div class="card shadow mb-2">
				<div class="card-header py-2">

					<h6 class="m-0 font-weight-bold text-primary">
						<a data-toggle="collapse"
						href="#version${version.replace('.','')}"
						${first ? '' : 'class="collapsed"'}
						style="text-decoration:none">

						Versie ${version}
						<span class="text-muted text-xs font-weight-normal ml-2">
								(${versions[version][0].date})
						</span>						
						</a>
					</h6>

				</div>

				<div id="version${version.replace('.','')}"
					class="collapse ${first ? 'show' : ''}"
					data-parent="#newsAccordion">

					<div class="card-body">
						${bodyHTML}
					</div>

				</div>
			</div>
			`;

			first = false;

		});

		$("#newsAccordion").html(accordionHTML);
	});

		$("#selectWatering").change(function() {
			var wateringId = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeWatering.php", { wateringId: wateringId }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});			

		$("#selectJaar").change(function() {
			var jaar = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeJaar.php", { jaar: jaar }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});	

		$("#selectMaand").change(function() {
			var maand = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeMaand.php", { maand: maand }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});	

	$(function() {
    $("#contactForm input,#contactForm textarea").jqBootstrapValidation({
        preventSubmit: true,
        submitError: function($form, event, errors) {
            // additional error messages or events
        },
        submitSuccess: function($form, event) {
            event.preventDefault(); // prevent default submit behaviour
            // get values from FORM
            var message = $("textarea#userMessage").val();
			var type = $("select[name='contactType']").val();
            $.ajax({
                url: "../bin/pages/contactMe.php",
                type: "POST",
                data: {
                        contactType: type,
						contactMessage: message
                },
                cache: false,
                success: function(data) {
					if (data != '') {
						console.log(data);
                    // Fail message
                    $('#success').html("<div class='alert alert-danger'>");
                    $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");
                    $('#success > .alert-danger').append("<strong>Er is iets misgegaan bij het versturen van het bericht. Probleer later opnieuw of stuur een e-mail.");
                    $('#success > .alert-danger').append('</div>');					
					}
					else {
                    // Success message
                    $('#success').html("<div class='alert alert-success'>");
                    $('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");
                    $('#success > .alert-success')
                        .append("<strong>Je bericht is verzonden. We behandelen dit zo snel mogelijk. </strong>");
                    $('#success > .alert-success')
                        .append('</div>');

                    //clear all fields
                    $('#contactForm').trigger("reset");
					}
                },
                error: function(data) {
                    // Fail message
                    $('#success').html("<div class='alert alert-danger'>");
                    $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");
                    $('#success > .alert-danger').append("<strong>Er is iets misgegaan bij het versturen van het bericht. Probleer later opnieuw of stuur een e-mail.");
                    $('#success > .alert-danger').append('</div>');
                    //clear all fields
                    $('#contactForm').trigger("reset");
                },
            })
        },
			filter: function() {
				return $(this).is(":visible");
			},
		});

		$("a[data-toggle=\"tab\"]").click(function(e) {
			e.preventDefault();
			$(this).tab("show");
		});
	});


	/*When clicking on Full hide fail/success boxes */
	$('#name').focus(function() {
		$('#success').html('');
	});
	
	</script>
</body>
</html>