<?php
include '../bin/init.php';

$pageTitle = 'Wapobel - Meer info';
$prefix = '../';
?>
<!DOCTYPE html>
<html lang="en">

<?php include $prefix.'includes/head.php';?>

<body class="bg-light">
	<div class="container my-5">

		<div class="row justify-content-center">
			<div class="col-lg-10">

				<!-- Banner -->
				<div class="mb-4">
					<img src="https://wapobel.be/img/logo-horizontal.png" height="100" alt="Wapobel">
				</div>

				<!-- Intro -->
				<div class="card shadow mb-4">
					<div class="card-body p-4">
						<h2 class="text-primary font-weight-bold mb-3">Waarom aansluiten bij Wapobel?</h2>
						<p class="lead">
							Wapobel is het digitaal beheersplatform van je boekhouding voor polders en wateringen in Vlaanderen.  
							Vereenvoudig je administratie, verbeter samenwerking en behoud volledig overzicht 
							over je cijfers.
						</p>
					</div>
				</div>

				<!-- Voordelen + Prijs naast elkaar -->
				<div class="row mb-4">
					
					<!-- Voordelen -->
					<div class="col-md-6">
						<div class="card shadow mb-4 h-100">
							<div class="card-body p-4">
								<h4 class="text-primary font-weight-bold mb-3">◆ Belangrijkste voordelen</h4>

								<ul class="list-group list-group-flush">
									<li class="list-group-item">✓ Efficiënt beheer van je boekingen, posten en rekeningen</li>
									<li class="list-group-item">✓ Integratie met Billit om je facturen rechstreeks in te boeken</li>
									<li class="list-group-item">✓ Overzicht van je ontvangsten en uitgaven samen met je begrotingscijfers</li>
									<li class="list-group-item">✓ Documenten in Excel en PDF formaat die je ten allen tijde kan genereren</li>
								</ul>
							</div>
						</div>
					</div>

					<!-- Prijzen -->
					<div class="col-md-6">
						<div class="card shadow mb-4 h-100">
							<div class="card-body p-4">
								<h4 class="text-success font-weight-bold mb-3">¤ Prijs & aansluitformule</h4>

								<p>
									Wapobel biedt een eenvoudige en transparante prijsformule op maat van
									polders en wateringen. Je betaalt een <span class="text-bold">vast jaarlijks bedrag</span> van <span class="text-bold">€200</span> (excl. BTW).
								</p>

								<ul class="list-group list-group-flush mb-3">
									<li class="list-group-item">• Jaarlijkse vaste bijdrage</li>
									<li class="list-group-item">• Gebruikslicentie voor de boekhoudmodule inclusief integratie met <span class="text-bold">Billit</span></li>
									<li class="list-group-item">• Updates & support inbegrepen</li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<!-- Call to action -->
				<div class="card shadow mb-4">
					<div class="card-body p-4 text-center">
						<h4 class="font-weight-bold mb-3">Klaar om te starten?</h4>
						<p class="mb-4">
							Registreer je watering of polder en ontdek hoe Wapobel jouw werking kan vereenvoudigen.
						</p>

						<a href="../registreer" class="btn btn-primary btn-lg px-5 mr-2">Registreren</a>
						<a href="mailto:info@wapobel.be?subject=Meer%20info%20over%20Wapobel" class="btn btn-outline-secondary btn-lg px-4">Contact opnemen</a>
					</div>
				</div>

				<!-- Footer -->
				<div class="text-center mt-4 opacity-75">
					<img src="https://wapobel.be/img/logo-horizontal.png" height="45" alt="Wapobel">
					<p class="mt-2 small text-muted">Copyright &copy; FiMar Consulting | <a href="mailto:info@wapobel.be?subject=Wapobel - Ik heb een vraag of een probleem">info@wapobel.be</p>
				</div>

			</div>
		</div>
	</div>

	<?php include $prefix.'includes/scripts.php';?>

</body>
</html>