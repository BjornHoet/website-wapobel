<?php
include 'bin/init.php';
$pageTitle = 'Invoeren dagboek';
$prefix = '';
$activeDagboek = 'active';
$activePost = '';
$activeRekening = '';

if (loggedIn() === false) {
//	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

$month = date('m');
$day = date('d');

$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, 'X', 'X', 'A');
$boekingen = writeBoekingen($wateringData['wateringId'], $wateringJaar, $_SESSION['wateringMaand'], $useNummering, $sortering);

$types = getTypes($wateringJaar);
$hoofdPostenAll = getHoofdPostenActief($wateringJaar, $wateringData['wateringId']);

$dataFile = $wateringData['wateringId'] . '_boekingen.json';
?>
<!DOCTYPE html>
<html lang="nl">

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
					<?php if ($boekjaarOpen === false) { ?>
						<div class="d-sm-flex align-items-center justify-content-center mb-1">
							<?php if ($wateringJaar === strval(date("Y"))) { ?>
								<div class="alert alert-warning text-center font-weight-bold py-2 px-3">
									<i class="fas fa-exclamation-triangle mr-2"></i>
									Het vorige boekjaar is nog niet afgesloten. Er kunnen geen wijzigingen worden aangebracht.
									<i class="fas fa-exclamation-triangle ml-2"></i>
								</div>
							<?php } else { ?>
								<div class="alert alert-warning text-center font-weight-bold py-2 px-3">
									<i class="fas fa-lock mr-2"></i>
									Dit boekjaar is afgesloten. Aanpassingen zijn niet meer mogelijk.
									<i class="fas fa-lock ml-2"></i>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-1 d-flex flex-row align-items-center justify-content-between">
									<div class="d-flex justify-content-between align-items-center headerButtons">

										<!-- Left: Selected Month/Year -->
										<div class="headerFirstPart d-flex align-items-center" style="white-space: nowrap;">
											<h5 class="m-0 font-weight-bold text-dark">
												<span id="selectedMonthYear"></span>
											</h5>
										</div>

										<!-- Middle: Navigation + Year Actions -->
										<div class="d-flex align-items-center headerMiddle">

											<!-- Month Navigation -->
											<div class="d-flex align-items-center">
												<button type="button" id="buttonPreviousMaand" class="btn btn-outline-primary btn-sm ml-2" data-toggle="tooltip" data-placement="top" title="Vorige maand"><i class="fas fa-arrow-left"></i></button>
												<button type="button" id="buttonHuidigeMaand" class="btn btn-outline-primary btn-sm ml-2" data-toggle="tooltip" data-placement="top" title="Huidige maand"><i class="fas fa-calendar-alt"></i> Huidige maand</button>
												<button type="button" id="buttonNextMaand" class="btn btn-outline-primary btn-sm ml-2" data-toggle="tooltip" data-placement="top" title="Volgende maand"><i class="fas fa-arrow-right"></i>
												</button>
											</div>

											<!-- Year Closing / Opening Buttons -->
											<?php // Toon knop alleen wanneer er meer dan 1 rekening is of wanneer we KAS gebruiken
												if ($boekjaarOpen === true) { ?>
												<?php if($rekeningen->num_rows > 2 || ( $rekeningen->num_rows === 2 && $useKAS === 'X' ) ) { ?>
												<div class="border-left pl-3 ml-3 d-flex align-items-center">
													<button type="button" id="buttonRekening" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#rekeningOverdrachtModal"><i class="fas fa-file-import mr-1"></i> Rekeningoverdracht</button>
												</div>
												<?php } ?>

												<?php
													if($wateringMaand === '12')
														$toonBoekjaarSluiten = true;
													else
														$toonBoekjaarSluiten = false;
													// || checkNieuwJaarBestaat($wateringData['wateringId'], ($wateringJaar + 1)));
												?>
												<div id="toonBoekjaarSluiten" class="border-left pl-3 ml-3 align-items-center <?= $toonBoekjaarSluiten ? 'd-flex' : 'd-none'; ?>">
													<button type="button" id="buttonBoekjaarAfsluiten" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#boekjaarAfsluiten"><i class="fas fa-lock mr-1"></i> Boekjaar afsluiten</button>
												</div>
											<?php } else { ?>
												<?php if ($wateringJaar === strval(date("Y") - 1)) { ?>
													<div class="border-left pl-3 ml-3 d-flex align-items-center">
														<button type="button" id="buttonBoekjaarOpenen"	class="btn btn-success btn-sm" data-toggle="modal" data-target="#boekjaarOpenen"><i class="fas fa-unlock mr-1"></i> Boekjaar openen</button>
													</div>
												<?php } ?>
											<?php } ?>
										</div>

										<!-- Right: Save Button -->
										<?php if ($boekjaarOpen === true) { ?>
											<div class="pr-3 ml-3 d-flex align-items-center">
												<button type="button" id="buttonOpslaan" class="btn btn-success btn-sm" data-toggle="tooltip" title="Opslaan"><i class="fas fa-save mr-1"></i> Opslaan</button>
											</div>
										<?php } else { ?>
										  <!-- Right: Dummy / Dropdown -->
										  <div class="d-flex align-items-center">
											<div class="dropdown"></div>
										  </div>										
										<?php } ?>
									</div>

									<div class="dropdown">
										<button class="btn btn-light btn-sm p-2 dropdown-toggle" type="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #ddd;"><i class="fas fa-ellipsis-v text-secondary"></i></button>

										<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink"><div class="dropdown-header">Acties</div>
											<a target="_blank" class="dropdown-item" href="documenten/dagboekxls.php">Dagboek van huidige maand (Excel)</a>
											<a target="_blank" class="dropdown-item" href="documenten/dagboekpdf.php">Dagboek van huidige maand (PDF)</a>
										</div>
									</div>
                                </div>

                                <div class="card-body">
									<div class="d-flex align-items-center flex-nowrap mb-3">
									  <!-- Zoeken -->
									  <div class="d-flex align-items-center pl-3 gap-2 mr-4">
										<h6 class="m-0 text-black font-weight-bold mr-2">
										  Zoeken
										</h6>
										<input type="search" class="form-control" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()" style="width: 400px; max-height: 35px;">
									  </div>

									  <!-- Checkbox Vaste hoofding -->
										<div class="d-flex align-items-center gap-2 text-bold text-black">
										  <i id="fixedHeaderIcon" class="fa fa-thumbtack text-muted mr-2"></i>

										  <label for="showFixedHeader" class="mb-0 mr-3">
											Zet tabel vast
										  </label>

										  <div class="custom-control custom-switch">
											<input type="checkbox"
												   class="custom-control-input"
												   id="showFixedHeader">
											<label class="custom-control-label" for="showFixedHeader"></label>
										  </div>
										</div>
									</div>

									<div id="myGrid" class="ag-theme-balham"></div>
								</div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

			<?php // include $prefix.'includes/footer.php';?>
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <!-- <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a> -->

	<?php include $prefix.'includes/modals.php';?>
	
	<!-- Toevoegen Boeking Modal -->
	<div class="modal fade" id="addBoekingModal" tabindex="-1" role="dialog" aria-labelledby="addBoekingLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="addBoekingLabel">
			  Toevoegen van een boeking
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <form id="addBoekingForm" action="<?php echo $prefix ?>bin/pages/addBoekingAG.php" method="post" role="form">
			<div class="modal-body">

			  <!-- Boekinginformatie -->
			  <div class="card">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Boekinginformatie</h6>
				</div>
				<div class="card-body">

				  <!-- Datum -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Datum
					</label>
					<div class="col-sm-2">
					  <input id="addBoekingDatum" type="text" name="boekingDatum" class="form-control form-control-sm datepicker readonlyInput" data-provide="datepicker" readonly required>
					</div>
				  </div>

				  <!-- Factuurnummer -->
				  <?php if($useNummering === 'X') { ?>
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Factuurnummer
					</label>
					<div class="col-sm-2">
					  <input id="addBoekingNummer" type="text" name="boekingNummering" class="form-control form-control-sm" required>
					</div>
				  </div>
				  <?php } else { ?>
					<input type="hidden" name="boekingNummering" id="addBoekingNummerHidden">
				  <?php } ?>

				  <!-- Post zoeken -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Post zoeken
					</label>
					<div class="col-sm-9">
					  <div class="dropdown hierarchy-select" id="postSearch">
						<button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="boekingPostZoeken" data-toggle="dropdown"></button>

						<div class="dropdown-menu" aria-labelledby="boekingPostZoeken">
						  <div class="hs-searchbox">
							<input type="text" id="inputBoekingReferentie" name="boekingReferentie" class="form-control form-control-sm" autocomplete="off">
						  </div>

						  <div class="hs-menu-inner">
							<a class="dropdown-item dropdown-action-add" data-value="|" href="#">&nbsp;</a>
							<?php foreach($types as $type) {
							  $refValue = $type['typeId'];
							  $description = $type['typeOmschrijving'];
							  $hoofdPosten = getHoofdPostenSub($type['typeId'], $wateringJaar);
							?>
							  <a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>|" data-level="1" href="#">
								<span class="font-weight-bold"><?php echo $description ?></span>
							  </a>

							  <?php foreach($hoofdPosten as $hoofdPost) {
								$posten = getPostenActiefGeenOverdracht(
								  $wateringData['wateringId'],
								  $wateringJaar,
								  $hoofdPost['hoofdpostId']
								);

								if ($posten->num_rows !== 0) {
							  ?>
								<a class="dropdown-item dropdown-action-add" data-value="<?php echo $hoofdPost['hoofdpostId'] ?>|" data-level="2" href="#">
								  <span class="font-weight-bold">
									<?php echo $hoofdPost['referentie'] ?>.	<?php echo $hoofdPost['omschrijving'] ?>
								  </span>
								</a>

								<?php foreach($posten as $post) {
								  if($post['actief'] === 'X' && $post['overdrachtPost'] !== 'X') {
								?>
								  <a class="dropdown-item dropdown-action-add" data-value="<?php echo $hoofdPost['hoofdpostId'].'|'.$post['postId'] ?>" data-level="3" href="#">
									<?php echo $post['referentie'] ?>. <?php echo $post['omschrijving'] ?>
								  </a>
								<?php }

								  $subposten = getSubPosten(
									$wateringData['wateringId'],
									$wateringJaar,
									$post['postId']
								  );

								  foreach($subposten as $subpost) {
									if($subpost['actief'] === 'X') {
								?>
								  <a class="dropdown-item dropdown-action-add" data-value="<?php echo $hoofdPost['hoofdpostId'].'|'.$post['postId'].'|'.$subpost['subpostId'] ?>" data-level="4" href="#">
									<?php echo $subpost['referentie'] ?>. <?php echo $subpost['omschrijving'] ?>
								  </a>
							<?php }}}}}} ?>
						  </div>
						</div>
					  </div>
					</div>
				  </div>

				  <!-- Hoofdpost -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Hoofdpost
					</label>
					<div class="col-sm-9">
					  <select id="inputBoekingHoofdpost" name="boekingHoofdpost" class="form-control form-control-sm" required>
					    <option value=""></option>
						<?php foreach ($hoofdPostenAll as $hoofdPost) { ?>
						  <option value="<?php echo $hoofdPost['hoofdpostId'] ?>">
							<?php echo ($hoofdPost['useKey'] === 'O' ? 'ONT' : 'UIT'); ?> -
							<?php echo $hoofdPost['referentie'] ?>.
							<?php echo $hoofdPost['omschrijving'] ?>
						  </option>
						<?php } ?>
					  </select>
					</div>
				  </div>

				  <!-- Post -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Post
					</label>
					<div class="col-sm-9">
					  <select id="inputBoekingPost" name="boekingPost" class="form-control form-control-sm" required></select>
					</div>
				  </div>

				  <!-- Subpost -->
				  <div class="form-group row mb-2" id="addSubpost">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Subpost
					</label>
					<div class="col-sm-9">
					  <select id="inputBoekingSubpost" name="boekingSubpost" class="form-control form-control-sm" required></select>
					</div>
				  </div>

				</div>
			  </div>
			</div>

			<!-- Footer -->
			<div class="modal-footer bg-light">
			  <button type="submit" class="btn btn-primary">Opslaan</button>
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
	
	<!-- Wijzigen Boeking Modal -->
	<div class="modal fade" id="changeBoekingModal" tabindex="-1" role="dialog" aria-labelledby="changeBoekingLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="changeBoekingLabel">
			  Wijzigen van een boeking
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <form id="wijzigBoekingForm" action="<?php echo $prefix ?>bin/pages/changeBoeking.php" method="post" role="form">
			<div class="modal-body">

			  <!-- Boekinginformatie -->
			  <div class="card">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Boekinginformatie</h6>
				</div>

				<div class="card-body">

				  <!-- Datum -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Datum
					</label>
					<div class="col-sm-2">
					  <input type="hidden" id="inputBoekingIdChange" name="boekingId">
					  <input type="text" id="inputBoekingDatumChange" name="boekingDatum" class="form-control form-control-sm datepicker readonlyInput" data-provide="datepicker" readonly required>
					</div>
				  </div>

				  <!-- Factuurnummer -->
				  <?php if($useNummering === 'X') { ?>
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Factuurnummer
					</label>
					<div class="col-sm-2">
					  <input type="text" id="inputBoekingNummeringChange" name="boekingNummering" class="form-control form-control-sm" required>
					</div>
				  </div>
				  <?php } else { ?>
					<input type="hidden" id="inputBoekingNummeringChange" name="boekingNummering">
				  <?php } ?>

				  <!-- Billitnummering -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Billitnummering
					</label>
					<div class="col-sm-2">
					  <input type="text" id="inputBoekingBillitNrChange" name="boekingBillitNr" class="form-control form-control-sm" pattern="[0-9]+" inputmode="numeric" title="Alleen cijfers zijn toegestaan">
					</div>
				  </div>

				  <!-- Post zoeken -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Post zoeken
					</label>
					<div class="col-sm-9">
					  <div class="dropdown hierarchy-select" id="postSearchChange">
						<button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="boekingPostZoekenChange" data-toggle="dropdown"></button>

						<div class="dropdown-menu" aria-labelledby="boekingPostZoekenChange">
						  <div class="hs-searchbox">
							<input type="text" id="inputBoekingReferentieChange" name="boekingReferentie" class="form-control form-control-sm" autocomplete="off">
						  </div>

						  <div class="hs-menu-inner">
							<a class="dropdown-item dropdown-action-change" data-value="|" href="#">&nbsp;</a>
							<?php foreach($types as $type) {
							  $refValue = $type['typeId'];
							  $description = $type['typeOmschrijving'];
							  $hoofdPosten = getHoofdPostenSub($type['typeId'], $wateringJaar);
							?>
							  <a class="dropdown-item dropdown-action-change" data-value="<?php echo $refValue ?>|" data-level="1" href="#">
								<span class="font-weight-bold"><?php echo $description ?></span>
							  </a>

							  <?php foreach($hoofdPosten as $hoofdPost) {
								$posten = getPostenActiefGeenOverdracht(
								  $wateringData['wateringId'],
								  $wateringJaar,
								  $hoofdPost['hoofdpostId']
								);

								if ($posten->num_rows !== 0) {
							  ?>
								<a class="dropdown-item dropdown-action-change" data-value="<?php echo $hoofdPost['hoofdpostId'] ?>|" data-level="2" href="#">
								  <span class="font-weight-bold">
									<?php echo $hoofdPost['referentie'] ?>. <?php echo $hoofdPost['omschrijving'] ?>
								  </span>
								</a>

								<?php foreach($posten as $post) {
								  if($post['actief'] === 'X' && $post['overdrachtPost'] !== 'X') {
								?>
								  <a class="dropdown-item dropdown-action-change" data-value="<?php echo $hoofdPost['hoofdpostId'].'|'.$post['postId'] ?>" data-level="3" href="#">
									<?php echo $post['referentie'] ?>. <?php echo $post['omschrijving'] ?>
								  </a>
								<?php }

								  $subposten = getSubPosten(
									$wateringData['wateringId'],
									$wateringJaar,
									$post['postId']
								  );

								  foreach($subposten as $subpost) {
									if($subpost['actief'] === 'X') {
								?>
								  <a class="dropdown-item dropdown-action-change" data-value="<?php echo $hoofdPost['hoofdpostId'].'|'.$post['postId'].'|'.$subpost['subpostId'] ?>" data-level="4" href="#">
									<?php echo $subpost['referentie'] ?>.<?php echo $subpost['omschrijving'] ?>
								  </a>
							<?php }}}}}} ?>
						  </div>
						</div>
					  </div>
					</div>
				  </div>

				  <!-- Hoofdpost -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Hoofdpost
					</label>
					<div class="col-sm-9">
					  <select id="inputBoekingHoofdpostChange" name="boekingHoofdpost" class="form-control form-control-sm" required>
						<option value=""></option>
						<?php foreach ($hoofdPostenAll as $hoofdPost) { ?>
						  <option value="<?php echo $hoofdPost['hoofdpostId'] ?>">
							<?php echo ($hoofdPost['useKey'] === 'O' ? 'ONT' : 'UIT'); ?> -
							<?php echo $hoofdPost['referentie'] ?>.
							<?php echo $hoofdPost['omschrijving'] ?>
						  </option>
						<?php } ?>
					  </select>
					</div>
				  </div>

				  <!-- Post -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Post
					</label>
					<div class="col-sm-9">
					  <select id="inputBoekingPostChange" name="boekingPost" class="form-control form-control-sm" required></select>
					</div>
				  </div>

				  <!-- Subpost -->
				  <div class="form-group row mb-2" id="addSubpostChange">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
					  Subpost
					</label>
					<div class="col-sm-9">
					  <select id="inputBoekingSubpostChange" name="boekingSubpost" class="form-control form-control-sm" required></select>
					</div>
				  </div>

				</div>
			  </div>
			</div>

			<!-- Footer -->
			<div class="modal-footer bg-light">
			  <button type="submit" class="btn btn-primary">Opslaan</button>
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
			</div>

		  </form>
		</div>
	  </div>
	</div>
		
	<!-- Overdracht Rekening Modal -->
	<div class="modal fade" id="rekeningOverdrachtModal" tabindex="-1" role="dialog" aria-labelledby="rekeningOverdrachtLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="rekeningOverdrachtLabel">
			  Beweging tussen twee rekeningen
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <form id="rekeningForm" class="user" action="<?php echo $prefix ?>bin/pages/rekeningOverdracht.php" method="post" role="form">
			<div class="modal-body">

			  <!-- Rekeninginformatie -->
			  <div class="card mb-3">
				  <div class="card-header bg-light">
					<h6 class="mb-0 font-weight-bold text-gray-900">Overdrachtgegevens</h6>
				  </div>
				  <div class="card-body">
					<div class="form-group row mb-1">
					  <!-- Datum -->
					  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold">
						Datum
					  </label>
					  <div class="col-sm-2">
						<?php 
						if($month === sprintf('%02d', $wateringMaand)) {
							$addDay = $day . '/' . $month;
						} else { 
							$addDay = '01/' . sprintf('%02d', $wateringMaand);
						}?>
						<input type="text" name="rekeningDatum" class="form-control form-control-sm datepicker readonlyInput" value="<?php echo $addDay ?>" readonly required>
					  </div>

					  <!-- Bedrag -->
					  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold text-right">
						Bedrag
					  </label>
					  <div class="col-sm-2">
						<input type="text" id="rekeningNaarBedrag" name="rekeningNaarBedrag" class="form-control form-control-sm" autocomplete="off" pattern="^(\d+)?(\.\d{1,2})?$" title="Voer een geldig bedrag in, bijvoorbeeld 123, 123.4, 123.45 of .45" required>
					  </div>
					</div>
				  </div>
			  </div>

			  <div class="card mb-3">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Rekening van - naar</h6><small>Klik op de rekeningen om de link ertussen te leggen</small>
				</div>
				<div class="card-body">
				  <!-- Rekening Van -->
				  <div class="form-group row mb-1">
					<div class="col-sm-12 d-flex flex-wrap">
						<div id="container-rek">
							<svg id="svgConns" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"></svg>
						
							<div class="row-rek" id="row1">
								<input type="hidden" id="rekeningVanHidden" name="rekeningVanNr">
								<?php $rekeningVanCounter = 0;
									foreach($rekeningen as $rekening) {
										if ($rekening['rekening'] === 'KAS' && $useKAS !== 'X') {
											continue;
											}

										/* if($rekening['rekening'] !== 'KAS') { */
											$rekeningVanCounter = $rekeningVanCounter + 1;
											?>
										<button type="button" class="link-btn" id="rekeningVan<?php echo $rekeningVanCounter ?>" data-key="rek-<?php echo $rekeningVanCounter ?>" data-rek="<?php echo $rekening['rekeningId'] ?>"><?php echo $rekening['rekening'] ?><br><?php echo $rekening['omschrijving'] ?></button>
									<?php /* }  */
										} ?>
							</div>
						
							<div class="row-rek" id="row2">
								<input type="hidden" id="rekeningNaarHidden" name="rekeningNaarNr">
								<?php $rekeningNaarCounter = 0;
									foreach($rekeningen as $rekening) {
										if ($rekening['rekening'] === 'KAS' && $useKAS !== 'X') {
											continue;
											}

										/* if($rekening['rekening'] !== 'KAS') { */
											$rekeningNaarCounter = $rekeningNaarCounter + 1;
											?>
										<button type="button" class="link-btn" id="labelRekeningNaar<?php echo $rekeningVanCounter ?>" data-key="rek-<?php echo $rekeningNaarCounter ?>" data-rek="<?php echo $rekening['rekeningId'] ?>"><?php echo $rekening['rekening'] ?><br><?php echo $rekening['omschrijving'] ?></button>
										<?php /* } */
										} ?>
							</div>
						</div>
					</div>
				  </div>
				</div>
			  </div>
			</div>
			<!-- Footer -->
			<div class="modal-footer bg-light">
			  <button type="submit" class="btn btn-primary" id="rekeningOverdrachtSubmit" disabled>Opslaan</button>
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
			</div>
		  </form>
		</div>
	  </div>
	</div>

    <!-- Boekjaar afsluiten Modal -->
	<div class="modal fade" id="boekjaarAfsluiten" tabindex="-1" role="dialog"
		 aria-labelledby="boekjaarAfsluitenLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="boekjaarAfsluitenLabel">
			  Boekjaar afsluiten
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <!-- Body -->
		  <div class="modal-body">

			<!-- Bevestiging -->
			<p class="font-weight-bold text-dark mb-3">
			  Ben je zeker dat je het huidige boekjaar wil afsluiten?
			  Hiermee wordt er automatisch een nieuw boekjaar gestart.
			</p>

			<!-- Acties card -->
			<div class="card mb-3">
			  <div class="card-header bg-light">
				<h6 class="mb-0 font-weight-bold text-gray-900">
				  Wat gebeurt er bij het afsluiten?
				</h6>
			  </div>
			  <div class="card-body">
				<ul class="mb-0 pl-3">
				  <li>Je kan geen wijzigingen meer aanbrengen aan je boekingen</li>
				  <li>De posten van dit jaar worden overgezet naar het nieuwe jaar</li>
				  <li>De actieve rekeningen van dit jaar worden overgezet</li>
				  <li>Over te dragen bedragen worden aan de rekeningen gekoppeld</li>
				</ul>
			  </div>
			</div>

			<!-- Belangrijk -->
			<div class="alert alert-warning d-flex align-items-start mb-0" role="alert">
			  <div class="mr-3" style="font-size: 1.5rem; line-height: 1;">
				⚠️
			  </div>
			  <div>
				<strong>BELANGRIJK</strong><br>
				Je begrotingscijfers van dit jaar worden overgenomen naar het nieuwe jaar.
				Controleer deze en pas ze aan indien nodig.
			  </div>
			</div>

		  </div>

		  <!-- Footer -->
		  <div class="modal-footer bg-light">
			<form id="boekjaarSluitenForm" class="mb-0">
			  <button type="submit" class="btn btn-danger" id="boekjaarAfsluitenOk">Boekjaar afsluiten</button>
			  <button type="button" class="btn btn-secondary" data-dismiss="modal" id="boekjaarAfsluitenCancel">Annuleren</button>
			</form>
		  </div>

		</div>
	  </div>
	</div>
	
	<!-- Boekjaar openen Modal -->
	<div class="modal fade" id="boekjaarOpenen" tabindex="-1" role="dialog"
		 aria-labelledby="boekjaarOpenenLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="boekjaarOpenenLabel">
			  Boekjaar openen
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <div class="alert alert-warning d-flex align-items-start mb-0 mt-4 mb-4 ml-4 mr-4" role="alert">
			  <div class="mr-3" style="font-size: 1.5rem; line-height: 1;">
				⚠️
			  </div>
			  <div>
				<strong>BELANGRIJK</strong><br>
				Dit is enkel bedoeld om nog <strong>kleine correcties</strong> uit te voeren.
				Vergeet niet om het boekjaar nadien opnieuw af te sluiten zodat het volgende
				boekjaar correct verder kan werken.
			  </div>
		  </div>

		  <!-- Footer -->
		  <div class="modal-footer bg-light">
			<button type="button" class="btn btn-secondary" data-dismiss="modal" id="boekjaarOpenenCancel">Annuleren</button>
			<button type="submit" class="btn btn-success" id="boekjaarOpenenOk">Boekjaar openen</button>
		  </div>
		</div>
	  </div>
	</div>

	<!-- Wat is er nieuw -->
	<div class="modal fade" id="newsModal" tabindex="-1" role="dialog" aria-labelledby="newsLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<div>
			  <h4 class="modal-title text-primary font-weight-bold mb-0" id="newsLabel">
				Wat is er nieuw?
				<span class="text-muted text-s ml-1" id="newsVersion"></span>
			  </h4>
			</div>

			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <!-- Body -->
		  <div class="modal-body">
			<h5 id="newsTitle" class="text-muted font-weight-semibold mb-4 text-black"></h5>

			<img id="newsImage"
				 class="img-fluid rounded mb-3"
				 alt="News Image" />

			<p id="newsText" class="mb-4 text-gray-800"></p>

			<div class="d-flex justify-content-between align-items-center">
			  <button id="newsPrev" class="btn btn-secondary btn-sm">
				&laquo; Vorige
			  </button>

			  <span id="newsCounter" class="font-weight-bold text-muted"></span>

			  <button id="newsNext" class="btn btn-secondary btn-sm">
				Volgende &raquo;
			  </button>
			</div>

		  </div>

		  <!-- Footer -->
		  <div class="modal-footer bg-light">
			<button id="closeNewsModal" class="btn btn-primary" data-dismiss="modal">
			  Sluiten
			</button>
		  </div>

		</div>
	  </div>
	</div>

	<div id="cellPopup">
	</div>

	<?php include $prefix.'includes/scripts.php';?>
	<?php include $prefix.'includes/apiError.php';?>
	<?php include $prefix.'includes/scriptsVariables.php';?>

	<script>		
// ---   AG Grid   ---
// -------------------
		// Grid API: Access to Grid API methods
	var boekJaarOpen = "<?php echo $boekjaarOpen; ?>";
	const useNummering = <?= json_encode($useNummering !== 'X'); ?>;
	const toonWhatsNew = <?= $showNews ? 'true' : 'false' ?>;

// v2.1
/* 	const newsPages = [
		{
			version: "(v 2.1)",
			title: "Gebruik van KAS",
			text: "Wanneer je geen <strong>KAS</strong> wenst te gebruiken, kan je dit afzetten in je <strong>Profiel</strong>. KAS verdwijnt dan van je dagboek en in de documenten.<br>&nbsp",
			image: "img/news/2.1.kas.png"
		},
		{
			version: "(v 2.1)",
			title: "Prefix bij factuurnummers",
			text: "Als je factuurnummers gebruikt, kan je nu ook een <strong>prefix</strong> opgeven in je <strong>Profiel</strong>. Deze wordt bij nieuwe boekingen toegevoegd aan je factuurnummer. <br>Let wel: Het laatste karakter mag geen cijfer zijn. <strong>Voorbeeld: 2025-</strong>",
			image: "img/news/2.1.prefix.png"
		}
	];	 */

// v2.2
/*	const newsPages = [
		{
			version: "(v 2.2)",
			title: "Postkeuze bij Billit factuur/transactie",
			text: "Wanneer je een Billit factuur of transactie gaat inboeken, zal de keuze van de posten beperkt worden tot <strong>Uitgaven</strong> of <strong>Ontvansten</strong> afhankelijk van het soort transactie dat je verwerkt. Dit om een <strong>foutieve postkeuze</strong> te voorkomen.",
			image: "img/news/2.2.billitfacturen2.png"
		},
		{
			version: "(v 2.2)",
			title: "Visualisatie toevoegen Billit factuur/transactie",
			text: "Het toevoegen van een Billit factuur is duidelijker geworden door het scherm te reorganiseren.<br>&nbsp<br>&nbsp",
			image: "img/news/2.2.billitfacturen.png"
		},
		{
			version: "(v 2.2)",
			title: "Informatie postkeuze in je dagboek",
			text: "Wanneer je in je dagboek over de referentie van je post gaat met je muis, zal je de omschrijvingen te zien krijgen van je gekozen sub(post).<br>&nbsp",
			image: "img/news/2.2.posthover.png"
		},
		{
			version: "(v 2.2)",
			title: "Suggesties indienen",
			text: "Naast een vraag of probleem, kan je nu ook een suggestie indienen om het programma te verbeteren. Ga naar de contactpagina, via je profiel, en dien een suggestie in.<br>&nbsp",
			image: "img/news/2.2.contact.png"
		}
	];	*/

// v2.3
/* 	const newsPages = [
		{
			version: "(v 2.3)",
			title: "Billitnummering beschikbaar op je dagboek",
			text: "Werd de <strong>Billitnummering</strong> niet automatisch overgenomen bij het inboeken van een Billit-factuur of -transactie? Dan kan je deze nu alsnog toevoegen in je <strong>dagboekoverzicht</strong>. Ga hiervoor naar je <strong>profiel</strong> en activeer ‘<strong>Toon Billitnummering</strong>’.",
			image: "img/news/2.3.billitnummer1.png"
		},
		{
			version: "(v 2.3)",
			title: "Billitnummering beschikbaar op je dagboek",
			text: "De <strong>Billitnummering</strong> wordt zichtbaar in je dagboek en kan daar ook aangepast worden. Dit kan zowel <strong>rechtstreeks</strong> in het overzicht als door op het <strong>pennetje</strong> van de boeking te klikken en daar de nummering te wijzigen.",
			image: "img/news/2.3.billitnummer2.png"
		},
		{
			version: "(v 2.3)",
			title: "Nieuwe rij 'Rekeningtotaal' in je dagboek",
			text: "In je dagboek is een <strong>nieuwe rij</strong> toegevoegd, bij de totalen, die de stand van je rekening weergeeft. Hiervoor gebruiken we het <strong>beginsaldo van het huidige boekjaar</strong> en tellen we de <strong>over te dragen bedragen</strong> erbij.<br>&nbsp",
			image: "img/news/2.3.rekeningtotaal.png"
		},
		{
			version: "(v 2.3)",
			title: "Visuele wijzigingen",
			text: "Er zijn <strong>algemene visuele verbeteringen</strong> doorgevoerd zodat alles <strong>compacter</strong> en <strong>duidelijker</strong> is. In de komende updates wordt hier verder aan gewerkt.<br>&nbsp",
			image: "img/news/2.3.visuelewijzigingen.png"
		}
	]; */

// v2.4
/* 	const newsPages = [
		{
			version: "(v 2.4)",
			title: "Hoofding en totalen van dagboektabel vastzetten",
			text: "Als je <strong>veel gegevens</strong> in de dagboek hebt, kan je de <strong>hoofding</strong> en de <strong>totaalrijen vastzetten</strong>. Je kan dan door de gegevens scrollen. Zo heb je steeds een <strong>duidelijk totaaloverzicht</strong>.<br>&nbsp;",
			image: "img/news/2.4.boekingstabel.png"
		},
		{
			version: "(v 2.4)",
			title: "Negatief startbedrag op je rekeningen",
			text: "Je kan nu ook een <strong>negatief startbedrag</strong> zetten op je rekening.<br>&nbsp;<br>&nbsp;",
			image: "img/news/2.4.negatiefbedrag.png"
		},
		{
			version: "(v 2.4)",
			title: "Betaaldatum bij Billitboekingen",
			text: "De <strong>betaaldatum van Billit</strong> is toegevoegd in de tabel en bij het inboeken van je factuur/transactie. Als de betaaldatum aanwezig is, zal deze gebruikt worden om de <strong>boekingsdatum</strong> op te vullen.<br>&nbsp;",
			image: "img/news/2.4.betaaldopdatum.png"
		},
		{
			version: "(v 2.4)",
			title: "Omschrijving bij Billitboekingen",
			text: "Indien er geen <strong>factuurreferentie van Billit</strong> beschikbaar is, wordt de <strong>omschrijving van de post</strong> gebruikt als <strong>boekingsomschrijving</strong>.<br>&nbsp;",
			image: "img/news/2.4.billitomschrijving.png"
		},
		{
			version: "(v 2.4)",
			title: "Omschrijving blijft bij het wijzigen van een post",
			text: "Wanneer je de post van een <strong>bestaande boeking wijzigt</strong>, wordt de <strong>omschrijving niet opnieuw overschreven</strong> door die van de nieuwe (sub)post. Eventuele aanpassingen aan de omschrijving blijven behouden.",
			image: "img/news/2.4.wijzigenpost.png"
		}
	]; */

// v2.5
	const newsPages = [
		{
			version: "(v 2.5)",
			title: "Niet relevante transacties/facturen",
			text: "Als je bijvoorbeeld de <strong>CODA-bestanden in Billit</strong> actief hebt, kan het zijn dat er <strong>veel transacties</strong> binnenkomen die <strong>niet relevant</strong> zijn in Wapobel. Je kunt deze <strong>selecteren</strong> en op de knop ‘<strong>Niet relevant</strong>’ klikken. Ze krijgen dan ook deze status.",
			image: "img/news/2.5.nietrelevant1.png"
		},
		{
			version: "(v 2.5)",
			title: "Niet relevante transacties/facturen",
			text: "Je kunt de transacties altijd terugvinden door ‘<strong>Toon alle facturen</strong>’ te selecteren. Ze worden in het <strong>oranje</strong> weergegeven. Je kunt ze bovendien <strong>nog steeds inboeken</strong> in Wapobel door erop te klikken.<br>&nbsp;",
			image: "img/news/2.5.nietrelevant2.png"
		},
		{
			version: "(v 2.5)",
			title: "Inactief zetten van een rekening",
			text: "Een rekening kan <strong>niet meer inactief worden gezet</strong> als er al <strong>bedragen op zijn geboekt</strong>. Dit zie je ook wanneer je de rekening wijzigt.<br>&nbsp;",
			image: "img/news/2.5.rekening1.png"
		},
		{
			version: "(v 2.5)",
			title: "Rekeningen verwijderen",
			text: "<strong>Inactieve rekeningen</strong> kan je nu ook <strong>verwijderen</strong>. Je zal nog even moeten bevestigen na het klikken op '<strong>Verwijder rekening</strong>'.<br>&nbsp;",
			image: "img/news/2.5.rekening2.png"
		},
		{
			version: "(v 2.5)",
			title: "Performantie inboeken",
			text: "Het inboeken van een transactie of factuur gaat nu <strong>veel sneller</strong>, wat het <strong>gebruiksgemak aanzienlijk verbetert</strong>.<br>&nbsp;",
			image: "img/news/2.5.performantie.png"
		}
	];	
	
	function PostTooltip() {}

	PostTooltip.prototype.init = function (params) {
		this.eGui = document.createElement('div');
		this.eGui.className = 'custom-tooltip';

		var html = String(params.data.postDetail || '').replace(/\n/g, '<br>');

		this.eGui.innerHTML = html;
	};

	PostTooltip.prototype.getGui = function () {
		return this.eGui;
	};
	
	let gridApi;
	let currentGridOptions;

/* -------------------------
   Grid (re)initializer
------------------------- */
// Initialiseer grid
	function initGrid(showFixedHeader = false) {
	  const $grid = $('#myGrid');

	  // Destroy bestaande grid
	  if (gridApi) {
		gridApi.destroy();
		$grid.empty();
	  }

	  // Smooth hoogte transition
	  $grid.css({
		transition: 'height 0.3s ease',
		height: showFixedHeader ? 'calc(100vh - 259px)' : '100%'
	  });
	
	// Grid Options: Contains all of the grid configurations
	currentGridOptions  = {
	  // Columns to be displayed (Should match rowData properties)
	  columnDefs: [
	  {
		  headerName: "",
		  field: "actions",
		  minWidth: 65, maxWidth: 65,
		  suppressMovable: true,
		  cellStyle: { display: "flex", justifyContent: "center", alignItems: "center", gap: "8px" }, // space between icons
		  cellClass: params => params.node.rowPinned ? 'thick-right-border' : '',

		headerComponent: class {
		  init(params) {
			this.eGui = document.createElement("div");
			this.eGui.style.display = "flex";
			this.eGui.style.justifyContent = "center";
			this.eGui.style.alignItems = "center";

			// Create button element
			const btn = document.createElement('button');
			btn.id = 'addRowBtn';
			btn.className = 'btn btn-success btn-sm';
			btn.style.cssText = `
			  width: 26px;
			  height: 26px;
			  padding: 0;
			  border-radius: 4px;
			  display: flex;
			  justify-content: center;
			  align-items: center;
			  position: relative;
			`;

			// Create icon
			const icon = document.createElement('i');
			icon.className = 'fa fa-plus';
			icon.style.fontSize = '12px';

			// Append icon to button
			btn.appendChild(icon);

			if (boekJaarOpen === '1') {
				// Append button to the cell
				this.eGui.appendChild(btn);
			}

			// --- Create tooltip element once ---
			const tooltip = document.createElement("div");
			tooltip.textContent = "Boeking toevoegen";
			tooltip.style.position = "fixed";
			tooltip.style.background = "rgba(0, 0, 0, 0.7)"; // <-- semi-transparante achtergrond
			tooltip.style.color = "#fff"; // tekst blijft volledig zichtbaar
			tooltip.style.padding = "4px 8px";
			tooltip.style.borderRadius = "4px";
			tooltip.style.fontSize = "12px";
			tooltip.style.fontWeight = "bold";
			tooltip.style.whiteSpace = "nowrap";
			tooltip.style.transition = "transform 0.15s ease, opacity 0.15s ease"; // fade effect
			tooltip.style.pointerEvents = "none";
			tooltip.style.zIndex = "99999";
			tooltip.style.display = "block";
			tooltip.style.transform = "translateY(0)"; // startpositie
			tooltip.style.opacity = "0"; // fade-in voor hover
			document.body.appendChild(tooltip);

			// --- Tooltip hover behavior ---
			btn.addEventListener("mouseenter", e => {
			  const rect = e.target.getBoundingClientRect();
			  tooltip.style.left = rect.right + 6 + "px";
			  tooltip.style.top = rect.top - 10 + "px";
			  tooltip.style.opacity = "1";           // fade-in
			  tooltip.style.transform = "translateY(-2px)";
			});

			btn.addEventListener("mouseleave", () => {
			  tooltip.style.opacity = "0";           // fade-out
			  tooltip.style.transform = "translateY(0)";
			});

			// --- Add button click behavior ---
			btn.addEventListener("click", () => {
				if (gridApi) gridApi.stopEditing(); // commit actieve edits

				$.ajax({
					url: '<?php echo($prefix);?>bin/selects/getLastBoekingNr.php',
					type: 'GET',
					dataType: 'json',
					success: function(response) {
						// Eerst de modal openen
						$('#addBoekingModal').modal('show');

						// Wacht tot de modal volledig zichtbaar is
						$('#addBoekingModal').off('shown.bs.modal').on('shown.bs.modal', function() {
							const today = new Date();
							const addBoekingDay = String(today.getDate()).padStart(2, '0');
							const addBoekingMonth = String(today.getMonth() + 1).padStart(2, '0');
							const addBoekingCurrentMonth = String(currentMonth).padStart(2, '0');

							let dayPart = (parseInt(addBoekingMonth) === parseInt(currentMonth)) ? addBoekingDay : '01';
							const formattedDate = `${dayPart}/${addBoekingCurrentMonth}`;
							const updatePicker = `${currentYear}.${addBoekingCurrentMonth}.${dayPart}`;
							const d = new Date(updatePicker);
							$('#addBoekingDatum').datepicker('update', d);
							$('#addBoekingDatum').val(formattedDate);

							const boekingNummer = '<?php echo $nummeringPrefix ?>' + response;
							
							$('#addBoekingNummer').val(boekingNummer);
							$('#addBoekingNummerHidden').val(boekingNummer);

							$("div#addSubpost").hide();

							// Nu is de hierarchy-select veilig aan te roepen
							var $dropdown = $('#postSearch');
							$('#inputBoekingReferentie').val('');
							$dropdown.find('.selected').removeClass('selected');
							$dropdown.find('.dropdown-toggle').text('Zoeken...');
							$dropdown.data('value', '');
							if ($dropdown.length && typeof $dropdown.hierarchySelect === 'function') {
								//$dropdown.hierarchySelect('clearSelection');
								$("#inputBoekingReferentie").val("").trigger("keyup");
								$("#postSearch .dropdown-item").removeClass("disabled");
								$("#postSearch .dropdown-item").removeClass("d-none");
								$("#postSearch .dropdown-item").removeClass("active");
								$("#postSearch .dropdown-item[data-value='|']").first().addClass("active");
								}
						});
					},
					error: function(xhr, status, error) {
						console.error(error);
					}
				});
			});
		  }

		  getGui() {
			return this.eGui;
		  }
		},
		  
		  cellRenderer: params => {
			// hide buttons in pinned rows
			if (params.node.rowPinned || boekJaarOpen !== '1') {
			  return null; // or return '' to leave cell empty
			}	
			
			const container = document.createElement("div");
			container.style.display = "flex";
			container.style.justifyContent = "center";
			container.style.alignItems = "center";
			container.style.gap = "8px";

			// ----- Conditional Pencil / Edit Icon -----
			const postValue = params.data.post?.trim() || "";
			if ((postValue.startsWith("O") || postValue.startsWith("U"))) {
			  const pencil = document.createElement("i");
			  pencil.className = "fa fa-edit text-primary"; // FA5 edit icon
			  pencil.style.cursor = "pointer";
			  pencil.style.fontSize = "12px";
			  pencil.title = "Boeking wijzigen";

			  pencil.addEventListener("click", () => {
				// console.log("Edit row:", params.node.data);
				vulWijzigingsModal(params.node.data.boekId, params.node.data.date, params.node.data.hoofdPostId, params.node.data.postId, params.node.data.subPostId, params.node.data.nummering, params.node.data.billitnr)
			  });

			  container.appendChild(pencil);
			}

			// ----- Trash icon (delete) -----
			if(params.data.boekId != '') {
				const trash = document.createElement("i");
				trash.className = "fa fa-trash icon-grey";
				trash.style.cursor = "pointer";
				trash.style.fontSize = "12px";
				trash.title = "Boeking verwijderen";

				trash.addEventListener("click", () => {
				  // remove row
				  params.api.applyTransaction({ remove: [params.node.data] });

				  // optional: notify server
				  fetch("<?php echo($prefix);?>bin/pages/deleteBoekingAG.php", {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify({ id: params.node.data.boekId })
				  })
				  .then(res => res.json())
				  .then(data => console.log("Deleted:", data))
				  .catch(err => console.error("Error deleting:", err));
				});

				// add icons to container
				container.appendChild(trash);
			}

			return container;
		  }
		},
		{ field: "boekId", hide: true },
		<?php
		// Bepaal dynamisch het aantal kolommen
		$colSpan = 3; // standaard: datum, post, omschrijving
		$mergedFields = '${params.data.datum} ${params.data.post} ${params.data.omschrijving}'; // standaard

		// Voeg nummering toe als $useNummering aan staat
		if ($useNummering === 'X') {
			$colSpan++;
			$mergedFields = '${params.data.datum} ${params.data.post} ${params.data.nummering} ${params.data.omschrijving}';
		}

		// Voeg Billit toe direct na datum als $showBillit aan staat
		if ($showBillit === 'X') {
			$colSpan++;
			// Plaats billitNr direct na post
			if ($useNummering === 'X') {
				$mergedFields = '${params.data.datum} ${params.data.post} ${params.data.billitnr} ${params.data.nummering} ${params.data.omschrijving}';
			} else {
				$mergedFields = '${params.data.datum} ${params.data.post} ${params.data.billitnr} ${params.data.omschrijving}';
			}
		}
		?>	
		  {
			field: "datum",
			headerName: "Datum",
			minWidth: 50,
			maxWidth: 60,
			colSpan: params => params.node.rowPinned ? <?php echo $colSpan ?> : 1,
			valueGetter: params => {
				if (params.node.rowPinned) {
					return `<?php echo $mergedFields ?>`;
				}
				return params.data.datum;
			},
			cellClass: params => params.node.rowPinned ? 'last-row-merged text-right pr-4 thick-right-border' : ''
		  },
		  { field: "post", headerName: "Post", minWidth: 50, maxWidth: 100, cellClass: params => params.node.rowPinned ? 'last-row-hide' : '',
			  tooltipComponent: 'postTooltip',
			  tooltipValueGetter: params => {
				if (params.node.rowPinned) return null;
					return params.data?.postDetail;
				}
		  },
		<?php if($showBillit === 'X' ) { ?>
			{ field: "billitnr", headerName: "Billitnr", minWidth: 80, maxWidth: 100, cellClass: params => params.node.rowPinned ? 'last-row-hide' : ''
			<?php if($boekjaarOpen === true) { ?>
			  , editable: params => params.data.post && params.data.post.trim() !== ""
			  , cellClassRules: { "editable-highlight": params => params.data.post && params.data.post.trim() !== "" }
			<?php } ?>		
			},
		<?php } ?>		
		<?php if($useNummering === 'X' ) { ?>
			{ field: "nummering", headerName: "Factuurnr", minWidth: 80, maxWidth: 100, cellClass: params => params.node.rowPinned ? 'last-row-hide' : '',
			  <?php if($boekjaarOpen === true) { ?>
				editable: params => params.data.post && params.data.post.trim() !== "",
				cellClassRules: { "editable-highlight": params => params.data.post && params.data.post.trim() !== "" },
			  <?php } ?>
			  comparator: (valueA, valueB) => {
				// Vind het laatste getal in de string
				const numA = parseInt(valueA.match(/\d+$/)?.[0] || 0, 10);
				const numB = parseInt(valueB.match(/\d+$/)?.[0] || 0, 10);

				// Vind prefix
				const prefixA = valueA.replace(/\d+$/, '');
				const prefixB = valueB.replace(/\d+$/, '');

				// Eerst alfabetisch op prefix
				if (prefixA < prefixB) return -1;
				if (prefixA > prefixB) return 1;

				// Dan numeriek op laatste getal
				return numA - numB;
			  }
			},

		<?php } ?>		
		{ field: "omschrijving", headerName: "Omschrijving", minWidth: 300, maxWidth: 700, wrapText: true, cellClass: params => params.node.rowPinned ? 'last-row-hide-last-column' : 'thick-right-border'
		<?php if($boekjaarOpen === true) { ?>
		  , editable: params => params.data.post && params.data.post.trim() !== ""
		  , cellClassRules: { "editable-highlight": params => params.data.post && params.data.post.trim() !== "" }
		  , singleClickEdit: true
		  , suppressClickEdit: false
		<?php } ?>
        },
		<?php foreach ($rekeningen as $rekening) { 
		    // Overslaan als het rekening KAS is en $useKAS = 'X'
			if ($rekening['rekening'] === 'KAS' && $useKAS !== 'X') {
				continue;
			}	?>
		{ headerName: "<?php echo $rekening['rekening'] ?>\n<?php echo $rekening['omschrijving'] ?>", headerClass: ["header-center", "header-multiline"],
		    children: [ 
			  { field: "rek_<?php echo $rekening['rekeningId'] ?>_O", suppressMovable: true, headerName: "Ontvangsten", headerClass: "header-center"
				<?php if($boekjaarOpen === true) { ?>
				  , editable: params => params.data.post?.trim().startsWith("O")
				  , cellClassRules: { "editable-highlight": params => params.data.post?.trim().startsWith("O") }
				<?php } ?>
			  },
			  { field: "rek_<?php echo $rekening['rekeningId'] ?>_U", suppressMovable: true, headerName: "Uitgaven", headerClass: "header-center", cellClass: "thick-right-border"
			    <?php if($boekjaarOpen === true) { ?>
				  , editable: params => params.data.post?.trim().startsWith("U")
				  , cellClassRules: { "editable-highlight": params => params.data.post?.trim().startsWith("U") }
				<?php } ?>
			  },
			  ]
			},
		<?php } ?>
	  ],
	  rowData: [],  
	  domLayout: showFixedHeader ? 'normal' : 'autoHeight',
	  suppressScrollOnNewData: true, 
	  rowHeight: 25,
	  components: {
		postTooltip: PostTooltip
		},
	  tooltipShowDelay: 500,
      tooltipHideDelay: 3000,
	  getRowClass: params => {
		// Check the flag from your PHP-generated JSON
		if (params.data?.allEmpty) {
		  return 'row-empty'; // a CSS class we will define
		}
		return '';
	  },
	  defaultColDef: {
		  resizable: true,
		  wrapText: false,
		  autoHeight: false,
		  filter: false,
	  <?php if($boekjaarOpen === true) { ?>
		  editable: false,
	  <?php } ?>
	  cellStyle: { 
			color: '#858796'
			}
	      },
	  getRowStyle: params => {
		if (params.node.rowPinned) {
		  return { background: '#5a5c69', color: '#fff', fontWeight: 'bold' };
		}
		return null;
		},	  
	  suppressRowTransform: true,
	  localeText: {
		noRowsToShow: 'Geen boekingen'
		},	
	  onGridReady: params => {
		  gridApi = params.api;

		  // Column state herstellen
		  const savedState = JSON.parse(localStorage.getItem('gridColumnState') || '[]');
		  if (savedState.length) gridApi.applyColumnState({ state: savedState, applyOrder: false });

		  // Pas columns aan NA height transition
		  const applySize = () => params.api.sizeColumnsToFit();
		  $grid.on('transitionend.gridResize', applySize);

		  // fallback als transitionend niet fired
		  setTimeout(applySize, 100);
		},
      onSortChanged: saveColumnState,
      onColumnMoved: saveColumnState,
      onColumnPinned: saveColumnState,
      onColumnVisible: saveColumnState,
      onColumnResized: saveColumnState,	  
	  singleClickEdit: true,
		onCellEditingStopped: function(event) {
			const newValue = event.value?.toString().trim();
			const oldValue = event.oldValue?.toString().trim();

			// Do nothing if value didn't change
			if (newValue === oldValue) return;

			// Helper: show temporary popup message
			const showPopup = (message, cellElement) => {
				const popup = document.getElementById("cellPopup");
				if (!popup || !cellElement) return;

				const rect = cellElement.getBoundingClientRect();
				popup.textContent = message;
				popup.style.left = rect.right - 150 + window.scrollX + "px";
				popup.style.top = rect.top - 20 + window.scrollY + "px";
				popup.style.display = "block";
				requestAnimationFrame(() => popup.style.opacity = "1");

				setTimeout(() => {
					popup.style.opacity = "0";
					setTimeout(() => popup.style.display = "none", 300);
				}, 2000);
			};

			// If the field is numeric (rek_*), validate it
			if (event.colDef.field.startsWith("rek_")) {
				const isNumeric = /^-?\d*(\.\d+)?$/.test(newValue) && newValue !== '';
				if (!isNumeric && newValue) {
					// revert invalid value
					event.node.setDataValue(event.colDef.field, event.oldValue);

					const colId = event.column.getColId();
					const cellElement = document.querySelector(
						`[row-index='${event.rowIndex}'] [col-id='${colId}']`
					);
					showPopup("Vul enkel numerieke waardes in aub. (vb: 1200.30)", cellElement);
					return; // stop further processing
				}

				// Update the allEmpty flag
				const rowData = event.node.data;
				const rekFields = Object.keys(rowData).filter(key => key.startsWith('rek_'));
				const allEmpty = rekFields.every(key => !rowData[key]);
				rowData.allEmpty = allEmpty;
				event.node.setDataValue('allEmpty', allEmpty);
				event.api.redrawRows({ rowNodes: [event.node] });
			}
			
			// If the field is Billitnr, validate numeric only
			if (event.colDef.field === "billitnr") {
				const isNumeric = /^\d*$/.test(newValue); // alleen cijfers, lege string toegestaan
				if (!isNumeric && newValue) {
					// revert invalid value
					event.node.setDataValue(event.colDef.field, event.oldValue);

					const colId = event.column.getColId();
					const cellElement = document.querySelector(
						`[row-index='${event.rowIndex}'] [col-id='${colId}']`
					);

					const showPopup = (message, cellElement) => {
						const popup = document.getElementById("cellPopup");
						if (!popup || !cellElement) return;

						const rect = cellElement.getBoundingClientRect();
						popup.textContent = message;
						popup.style.left = rect.right - 150 + window.scrollX + "px";
						popup.style.top = rect.top - 20 + window.scrollY + "px";
						popup.style.display = "block";
						requestAnimationFrame(() => popup.style.opacity = "1");

						setTimeout(() => {
							popup.style.opacity = "0";
							setTimeout(() => popup.style.display = "none", 300);
						}, 2000);
					};

					showPopup("Vul enkel cijfers in aub. (vb: 12345)", cellElement);
					return; // stop further processing
				}
			}			

			// Prepare field for database update: always start with boekId
			const updatedField = [
				{ key: 'boekId', value: event.data.boekId }, // first element
				{ key: event.colDef.field, value: newValue } // edited field
			];
			
			// Send to server
			fetch("<?php echo($prefix);?>bin/pages/changeBoekingAG.php", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify(updatedField)
			})
			.then(response => response.json())
			.then(data => console.log("Server response:", data))
			.catch(error => console.log("Error:", error));
		},
	  };

	//function initialFilter(){
	//	gridApi.setFilterModel({ status: { type: 'notEqual', filter: 'Inactief' } });
	//}


	// Create Grid: Create new grid within the #myGrid div, using the Grid Options object
	gridApi = agGrid.createGrid($grid[0], currentGridOptions);

	// Fetch Remote Data
	refreshAll();
	}

	function saveColumnState() {
		if (!gridApi) return;
		const state = gridApi.getColumnState();
		localStorage.setItem('gridColumnState', JSON.stringify(state));
	}

	function onFilterTextBoxChanged() {
		gridApi.setGridOption("quickFilterText", document.getElementById("filter-text-box").value,
		);
	}

	function refreshAll() {
		fetch("data/<?php echo $dataFile ?>", { cache: "no-cache" })
			  .then((response) => response.json())
			  .then((data) => {
				// Check if data is valid
				if (Array.isArray(data) && data.length > 0) {
				  // Extract the last row as pinned bottom row
				  const pinnedBottomRow = data.slice(-3);

				  // Remove it from normal rowData
				  const normalRows = data.slice(0, data.length - 3);

				  // Set normal rows
				  gridApi.setGridOption("rowData", normalRows);

				  // Set pinned bottom row
				  gridApi.setGridOption("pinnedBottomRowData", pinnedBottomRow);
				} else {
				  gridApi.setGridOption("rowData", []);
				  gridApi.setGridOption("pinnedBottomRowData", []);
				}
			  });		  
		
		// Set dropdowns and values
		$("#selectMaand").val(currentMonth);
		var monthName = new Date(currentYear, currentMonth - 1).toLocaleString("nl-BE", { month: "long" });
		monthName = monthName.charAt(0).toUpperCase() + monthName.slice(1);	
		var result = monthName + " " + currentYear;
		$("#selectedMonthYear").text(result);
		if(currentMonth == 12) {
			$("#toonBoekjaarSluiten").removeClass('d-none');
			$("#toonBoekjaarSluiten").addClass('d-flex');
			$("#toonBoekjaarSluiten").show();
			}
		else {
			$("#toonBoekjaarSluiten").addClass('d-none');
			$("#toonBoekjaarSluiten").removeClass('d-flex');
			$("#toonBoekjaarSluiten").hide();
			}
		
		$("#selectJaar").val(currentYear);
		
		if($("#selectWatering option:selected").text() != '')
			$("#wateringOmschrijving").text($("#selectWatering option:selected").text());
			
		$.hideLoader();
		}

	document.addEventListener("click", function(e) {
		const clickedInGrid = e.target.closest('.ag-root');
		const clickedAddRowBtn = e.target.closest('#addRowBtn');

		if (!clickedInGrid && !clickedAddRowBtn && gridApi) {
			gridApi.stopEditing();
		}
	});
	

// ---   Toon wat is nieuw   ---
// ---------------------------------------------	
	let currentPage = 0;

	const versionEl = document.getElementById("newsVersion");
	const titleEl = document.getElementById("newsTitle");
	const textEl = document.getElementById("newsText");
	const imgEl = document.getElementById("newsImage");
	const counterEl = document.getElementById("newsCounter");

	const prevBtn = document.getElementById("newsPrev");
	const nextBtn = document.getElementById("newsNext");

	const closeBtn = document.getElementById("closeNewsModal");
	const disableCheckbox = document.getElementById("disableShowNews");

	function renderNewsPage() {
		const p = newsPages[currentPage];

		versionEl.textContent = p.version;
		titleEl.textContent = p.title;
		textEl.innerHTML = p.text;
		imgEl.src = p.image;

		counterEl.textContent = `${currentPage + 1} / ${newsPages.length}`;

		prevBtn.disabled = currentPage === 0;
		nextBtn.disabled = currentPage === newsPages.length - 1;
	}

	function showNewsModal() {
		currentPage = 0;
		renderNewsPage();
		$("#newsModal").modal("show");
	}

	// ----- Navigatie -----

	prevBtn.addEventListener("click", () => {
		if (currentPage > 0) {
			currentPage--;
			renderNewsPage();
		}
	});

	nextBtn.addEventListener("click", () => {
		if (currentPage < newsPages.length - 1) {
			currentPage++;
			renderNewsPage();
		}
	});

	// ----- Sluiten + voorkeur opslaan -----
	document.addEventListener("DOMContentLoaded", function () {
		if (typeof toonWhatsNew !== 'undefined' && toonWhatsNew === true) {
			showNewsModal();
		}
	});	

	$('#newsModal').on('hidden.bs.modal', function () {
		$.ajax({
			url: 'bin/login/updateShowNews.php',
			type: 'POST',
			data: { showNews: 0 },
			success: function(response) {
			},
			error: function(xhr, status, error) {
				console.error("Update mislukt:", status, error);
			}
		});
	});
	
// ---   Watering, jaar en maand dropdowns   ---
// ---------------------------------------------	
		$("#selectWatering").change(function() {
			$.showLoader({ message: 'De gegevens worden geladen…' });
			var wateringId = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeWatering.php",
				type: "post",
				data: { wateringId: wateringId }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				var select = $("#selectJaar");
				select.empty();

				if (response.jaren && response.jaren.length > 0) {
					$.each(response.jaren, function(index, jaar) {
						select.append('<option value="' + jaar + '">' + jaar + '</option>');
					});
					select.val(response.jaren[0]); // eerste automatisch selecteren
					currentYear = response.jaren[0];
				} else {
					select.append('<option value="">Geen boekjaren gevonden</option>');
				}

				// Toon of verberg de Billit-div
				if (response.enableBillit) {
					$("#billitEnabled").show();   // tonen
				} else {
					$("#billitEnabled").hide();   // verbergen
				}
				
				currentWatering = wateringId;
				refreshAll();
			});
		});	

		$("#selectJaar").change(function() {
			$.showLoader({ message: 'De gegevens worden geladen…' });
			var jaar = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeJaar.php",
				type: "post",
				data: { jaar: jaar }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				currentYear = jaar;
				window.location.href = window.location.href;
				location.reload(true);
				//refreshAll();
			});
		});

		$("#selectMaand").change(function() {
			$.showLoader({ message: 'De gegevens worden geladen…' });
			var maand = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
				type: "post",
				data: { maand: maand }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				currentMonth = maand;
				refreshAll();
			});
		});
		
		$('#verversBillit').click(function () {
			$.showLoader({ message: 'De gegevens worden geladen…' });
			request = $.ajax({
				url: "bin/selects/refreshBillit.php",
				type: "post"
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});		

		$("#buttonHuidigeMaand").click(function(){
			$.showLoader({ message: 'De gegevens worden geladen…' });
			var maand = new Date().getMonth() + 1;
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
				type: "post",
				data: { maand: maand }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				currentMonth = maand;
				refreshAll();
			});
		});
		
		$("#buttonPreviousMaand").click(function(){
			var maand = Number(currentMonth);
			maand = maand - 1;
			if(maand >= 1) {
				$.showLoader({ message: 'De gegevens worden geladen…' });
				request = $.ajax({
					url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
					type: "post",
					data: { maand: maand }
				});
				// callback handler that will be called on success
				request.done(function (response, textStatus, jqXHR){
					// log a message to the console
					currentMonth = maand;
					refreshAll();
				});
			}
		});

		$("#buttonNextMaand").click(function(){
			var maand = Number(currentMonth);
			maand = maand + 1;
			if(maand <= 12) {
				$.showLoader({ message: 'De gegevens worden geladen…' });
				request = $.ajax({
					url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
					type: "post",
					data: { maand: maand }
				});
				// callback handler that will be called on success
				request.done(function (response, textStatus, jqXHR){
					// log a message to the console
					currentMonth = maand;
					refreshAll();
				});
			}
		});

		$("#boekjaarAfsluitenOk").click(function(){
			var jaar = 
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/sluitenBoekjaar.php",
				type: "post"
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				console.log(response);
				//window.location.href = window.location.href;
				//location.reload(true);
			});
		});

		$("#boekjaarOpenenOk").click(function(){
			var jaar = 
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/openBoekjaar.php",
				type: "post"
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});

		$("#buttonOpslaan").click(function(){
			$.showLoader({ message: 'De gegevens worden geladen…' });
			gridApi.stopEditing();
			setTimeout(() => {
				request = $.ajax({
					url: "<?php echo($prefix);?>bin/selects/refreshData.php",
					type: "post"
				});
				// callback handler that will be called on success
				request.done(function (response, textStatus, jqXHR){
					// log a message to the console
					refreshAll();
				});
			}, 150); 
		});

		$(document).ready(function() {
		  $("#rekeningForm").on("submit", function(e) {
			$.showLoader({ message: 'De gegevens worden geladen…' });
			e.preventDefault(); // prevent normal form submit

			const $form = $(this);
			const url = $form.attr("action");   // PHP endpoint
			const formData = $form.serialize(); // gather form fields
			console.log(formData);

			$.ajax({
			  url: url,
			  type: "POST",
			  data: formData,
			  success: function(response) {
				refreshAll();
				$("#rekeningOverdrachtModal").modal("hide");

				// Optional: clear the form
				$("#rekeningForm")[0].reset();
			  },
			  error: function(xhr, status, error) {
				console.log("Error:", error);
			  }
			});
		  });
		});

		$(document).ready(function() {
		  $("#wijzigBoekingForm").on("submit", function(e) {
			$.showLoader({ message: 'De gegevens worden geladen…' });
			e.preventDefault(); // prevent normal form submit

			const $form = $(this);
			const url = $form.attr("action");   // PHP endpoint
			const formData = $form.serialize(); // gather form fields

			$.ajax({
			  url: url,
			  type: "POST",
			  data: formData,
			  success: function(response) {
				refreshAll();
				$("#changeBoekingModal").modal("hide");

				// Optional: clear the form
				$("#wijzigBoekingForm")[0].reset();
			  },
			  error: function(xhr, status, error) {
				console.log("Error:", error);
			  }
			});
		  });
		});

		$(document).ready(function() {
		  $("#addBoekingForm").on("submit", function(e) {
			$.showLoader({ message: 'De gegevens worden geladen…' });
			e.preventDefault(); // prevent normal form submit

			const $form = $(this);
			const url = $form.attr("action");   // PHP endpoint
			const formData = $form.serialize(); // gather form fields

			$.ajax({
			  url: url,
			  type: "POST",
			  data: formData,
			  success: function(response) {
				refreshAll();
				$("#addBoekingModal").modal("hide");

				// Optional: clear the form
				$("#addBoekingForm")[0].reset();
			  },
			  error: function(xhr, status, error) {
				console.log("Error:", error);
			  }
			});
		  });
		});		

		/* -------------------------
		   Toggle handler
		------------------------- */
		// Toggle checkbox handler
		$('#showFixedHeader').change(function () {
		  const checked = $(this).prop('checked');

		  // Opslaan
		  localStorage.setItem('showFixedHeader', checked ? '1' : '0');

		  // Re-init grid
		  initGrid(checked);
		});

		// Init op page load
		$(document).ready(() => {
		  const showFixedHeader = localStorage.getItem('showFixedHeader') === '1';
		  $('#showFixedHeader').prop('checked', showFixedHeader);
		  initGrid(showFixedHeader);
		});

		function updateFixedHeaderIcon(enabled) {
		  $('#fixedHeaderIcon').toggleClass('active', enabled);
		}

		$(document).ready(function () {
		  const enabled = localStorage.getItem('showFixedHeader') === '1';

		  $('#showFixedHeader').prop('checked', enabled);
		  updateFixedHeaderIcon(enabled);

		  $('#showFixedHeader').on('change', function () {
			const checked = this.checked;
			localStorage.setItem('showFixedHeader', checked ? '1' : '0');
			updateFixedHeaderIcon(checked);
		  });
		});

// ---   Search Dropdowns   ---
// ----------------------------
	$(document).ready(function(){
		$("div#addSubpost").hide();
		$('#postSearch').hierarchySelect({
			width: '100%'
		   });
		});

	$(document).ready(function(){
		$("div#addSubpostChange").hide();
		$('#postSearchChange').hierarchySelect({
			width: '100%'
		   });
		});		

// ---   Modals leegmaken   ---
// ----------------------------
	$(document).on('shown.bs.modal', '#addBoekingModal', function () {
		$('#inputBoekingHoofdpost').val('').change();
		$('select[name="boekingPost"]').empty();
		$('select[name="boekingSubpost"]').empty();
	});


// ---   Datepicker   ---
// ----------------------
	$('.datepicker').datepicker({
			format: "dd/mm",
			todayBtn: "linked",
			language: "nl-BE",
			autoclose: true,
			todayHighlight: true
		});	


// Wijzigen post - Data vullen
	function vulWijzigingsModal(boekId, datum, hoofdpostId, postId, subpostId, nummering, billitnr) {
		$('#inputBoekingHoofdpost').val("").change();
		$('select[name="boekingPost"]').empty();
		$('select[name="boekingSubpost"]').empty();
		$("div#addSubpostChange").hide();
		$('#inputBoekingIdChange').val(boekId);
		document.getElementById("inputBoekingSubpostChange").required = false;
		var $dropdown = $('#postSearchChange');
		$('#inputBoekingReferentieChange').val('');
		$dropdown.find('.selected').removeClass('selected');
		$dropdown.find('.dropdown-toggle').text('Zoeken...');
		$dropdown.data('value', '');
		if ($dropdown.length && typeof $dropdown.hierarchySelect === 'function') {
			//$dropdown.hierarchySelect('clearSelection');
			$("#inputBoekingReferentieChange").val("").trigger("keyup");
			$("#postSearchChange .dropdown-item").removeClass("disabled");
			$("#postSearchChange .dropdown-item").removeClass("d-none");
			$("#postSearchChange .dropdown-item").removeClass("active");
			$("#postSearchChange .dropdown-item[data-value='|']").first().addClass("active");
			}		
		
		$('#inputBoekingHoofdpostChange').val(hoofdpostId).change();
			document.getElementById('inputBoekingDatumChange').value = datum;
			const d = new Date(datum);
			$('#inputBoekingDatumChange').datepicker('update', d);
			
			document.getElementById('inputBoekingNummeringChange').value = nummering;
			document.getElementById('inputBoekingBillitNrChange').value = billitnr;
			
			if(hoofdpostId && hoofdpostId !="") {
				var hoofdpostEl = document.getElementById('inputBoekingHoofdpostChange');
				hoofdpostEl.value = hoofdpostId;
				
				if(hoofdpostId !== '') {
					$.ajax({
							url: "bin/selects/getPosten.php",
							dataType: 'Json',
							data: {'id':hoofdpostId},
							success: function(data) {
								$('select[name="boekingPost"]').empty();
								$('select[name="boekingPost"]').append('<option value=""></option>');
								$.each(data, function(key, value) {
									$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							var postIdEl = document.getElementById('inputBoekingPostChange')
							postIdEl.value = postId;

							if(postId !== '') {
								$.ajax({
										url: "bin/selects/getSubposten.php",
										dataType: 'Json',
										data: {'id':postId},
										success: function(data) {
											$('select[name="boekingSubpost"]').empty();
											$('select[name="boekingSubpost"]').append('<option value=""></option>');
											if(data && data !="") {
												$("div#addSubpostChange").show();
												document.getElementById("inputBoekingSubpostChange").required = true;
												$.each(data, function(key, value) {
													$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
												});
											var subpostIdEl = document.getElementById('inputBoekingSubpostChange')
											subpostIdEl.value = subpostId;												
											}
										}
									});
								} else { 
									$('select[name="boekingSubpost"]').empty();
									$("div#addSubpostChange").hide();
								}							
							}
						});
					} else { 			
						$('select[name="boekingPost"]').empty();
						$('select[name="boekingSubpost"]').empty();
					}
				}
		$('#changeBoekingModal').modal('show', true);
	}

// ---   Posten - DropDowns   ---
// ------------------------------

// Wijziging Hoofdpost
	$( "select[name='boekingHoofdpost']" ).change(function () {
			var hoofdpostId = $(this).val();
			$("div#addSubpost").hide();
			$("div#addSubpostChange").hide();
			document.getElementById("inputBoekingSubpost").required = false;

			if(hoofdpostId !== '') {
				$.ajax({
						url: "bin/selects/getPosten.php",
						dataType: 'Json',
						data: {'id':hoofdpostId},
						success: function(data) {
							$('select[name="boekingPost"]').empty();
							$('select[name="boekingPost"]').append('<option value=""></option>');
							$.each(data, function(key, value) {
								$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
							});
						}
					});
			} else { 			
				$('select[name="boekingPost"]').empty();
				$('select[name="boekingSubpost"]').empty();
			}
		});

// Wijziging Post
		$( "select[name='boekingPost']" ).change(function () {
			var postId = $(this).val();
			$("div#addSubpost").hide();
			$("div#addSubpostChange").hide();
			document.getElementById("inputBoekingSubpost").required = false;
			document.getElementById("inputBoekingSubpostChange").required = false;
			
			if(postId !== '') {
				$.ajax({
						url: "bin/selects/getSubposten.php",
						dataType: 'Json',
						data: {'id':postId},
						success: function(data) {
							$('select[name="boekingSubpost"]').empty();
							$('select[name="boekingSubpost"]').append('<option value=""></option>');
							if(data && data !="") {
								$("div#addSubpost").show();
								$("div#addSubpostChange").show();
								document.getElementById("inputBoekingSubpost").required = true;
								$.each(data, function(key, value) {
									$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							}
						}
					});
			} else { 
				$('select[name="boekingSubpost"]').empty();
				$("div#addSubpost").hide();
				$("div#addSubpostChange").hide();
			}
		});


// ---   Search Dropdown - Toevoegen   ---
// ---------------------------------------
		$(function(){
		  $(".dropdown-action-add").on("click",function(e){
			e.preventDefault(); 
			var referentie = $(this).data("value");
			const refArray = referentie.split('|');
			var hoofdpostId = refArray[0];
			var postId = refArray[1];
			var subpostId = refArray[2];
			
			$('select[name="boekingPost"]').empty();
			$('select[name="boekingSubpost"]').empty();
			document.getElementById("inputBoekingSubpost").required = false;
			$("div#addSubpost").hide();
			
			if(hoofdpostId && hoofdpostId !="") {
				var hoofdpostEl = document.getElementById('inputBoekingHoofdpost')
				hoofdpostEl.value = hoofdpostId;
				
				if(hoofdpostId !== '') {
					$.ajax({
							url: "bin/selects/getPosten.php",
							dataType: 'Json',
							data: {'id':hoofdpostId},
							success: function(data) {
								$('select[name="boekingPost"]').empty();
								$('select[name="boekingPost"]').append('<option value=""></option>');
								$.each(data, function(key, value) {
									$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							var postIdEl = document.getElementById('inputBoekingPost')
							postIdEl.value = postId;

							if(postId !== '') {
								$.ajax({
										url: "bin/selects/getSubposten.php",
										dataType: 'Json',
										data: {'id':postId},
										success: function(data) {
											$('select[name="boekingSubpost"]').empty();
											$('select[name="boekingSubpost"]').append('<option value=""></option>');
											if(data && data !="") {
												$("div#addSubpost").show();
												document.getElementById("inputBoekingSubpost").required = true;
												$.each(data, function(key, value) {
													$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
												});
											var subpostIdEl = document.getElementById('inputBoekingSubpost')
											subpostIdEl.value = subpostId;												
											}
										}
									});
								} else { 
									$('select[name="boekingSubpost"]').empty();
									$("div#addSubpost").hide();
								}							
							}
						});
					} else { 			
						$('select[name="boekingPost"]').empty();
						$('select[name="boekingSubpost"]').empty();
					}
				}
		    });
		});
		
// ---   Search Dropdown - Wijzigen   ---
// ---------------------------------------
		$(function(){
		  $(".dropdown-action-change").on("click",function(e){
			e.preventDefault(); 
			var referentie = $(this).data("value");
			const refArray = referentie.split('|');
			var hoofdpostId = refArray[0];
			var postId = refArray[1];
			var subpostId = refArray[2];
			
			$('select[name="boekingPost"]').empty();
			$('select[name="boekingSubpost"]').empty();
			document.getElementById("inputBoekingSubpostChange").required = false;
			$("div#addSubpostChange").hide();
			
			if(hoofdpostId && hoofdpostId !="") {
				var hoofdpostEl = document.getElementById('inputBoekingHoofdpostChange')
				hoofdpostEl.value = hoofdpostId;
				
				if(hoofdpostId !== '') {
					$.ajax({
							url: "bin/selects/getPosten.php",
							dataType: 'Json',
							data: {'id':hoofdpostId},
							success: function(data) {
								$('select[name="boekingPost"]').empty();
								$('select[name="boekingPost"]').append('<option value=""></option>');
								$.each(data, function(key, value) {
									$('select[name="boekingPost"]').append('<option value="'+ key +'">'+ value +'</option>');
								});
							var postIdEl = document.getElementById('inputBoekingPostChange')
							postIdEl.value = postId;

							if(postId !== '') {
								$.ajax({
										url: "bin/selects/getSubposten.php",
										dataType: 'Json',
										data: {'id':postId},
										success: function(data) {
											$('select[name="boekingSubpost"]').empty();
											$('select[name="boekingSubpost"]').append('<option value=""></option>');
											if(data && data !="") {
												$("div#addSubpostChange").show();
												document.getElementById("inputBoekingSubpostChange").required = true;
												$.each(data, function(key, value) {
													$('select[name="boekingSubpost"]').append('<option value="'+ key +'">'+ value +'</option>');
												});
											var subpostIdEl = document.getElementById('inputBoekingSubpostChange')
											subpostIdEl.value = subpostId;												
											}
										}
									});
								} else { 
									$('select[name="boekingSubpost"]').empty();
									$("div#addSubpostChange").hide();
								}							
							}
						});
					} else { 			
						$('select[name="boekingPost"]').empty();
						$('select[name="boekingSubpost"]').empty();
					}
				}
		    });
		});


// ---   Rekeningen overdracht   ---
// ---------------------------------
	let selectedFrom = null;
	let currentConnection = null; // {fromId, toId}

	const container = document.getElementById('container-rek');
	const svg = document.getElementById('svgConns');
	const row1Buttons = document.querySelectorAll('#row1 .link-btn');
	const row2Buttons = document.querySelectorAll('#row2 .link-btn');

	// Prevent Enter from submitting prematurely
	document.querySelector('#rekeningForm').addEventListener('keydown', function (e) {
	  if (e.key === 'Enter' && e.target.id === 'rekeningNaarBedrag') {
		e.preventDefault(); // stop the form from submitting
	  }
	});

	function centerRelative(el) {
	  const elRect = el.getBoundingClientRect();
	  const cRect = container.getBoundingClientRect();
	  return {
		x: elRect.left - cRect.left + elRect.width / 2,
		y: elRect.top - cRect.top + elRect.height / 2
	  };
	}

	function drawConnection(fromEl, toEl) {
	  svg.innerHTML = ""; // clear any old line
	  const p1 = centerRelative(fromEl);
	  const p2 = centerRelative(toEl);

	  const deltaY = Math.max(40, Math.abs(p2.y - p1.y) / 2);
	  const d = `M ${p1.x} ${p1.y} C ${p1.x} ${p1.y + deltaY}, ${p2.x} ${p2.y - deltaY}, ${p2.x} ${p2.y}`;

	  const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
	  path.setAttribute("d", d);
	  path.setAttribute("stroke", "rgba(60,120,200,0.95)");
	  path.setAttribute("stroke-width", "3");
	  path.setAttribute("fill", "none");
	  svg.appendChild(path);
	}

	function clearConnection() {
	  svg.innerHTML = "";
	  currentConnection = null;
	  selectedFrom = null;
	  document.querySelectorAll('.link-btn').forEach(b => {
		b.classList.remove('linked', 'selected', 'available');
		b.disabled = false;
	  });
	  document.getElementById('rekeningVanHidden').value = '';
	  document.getElementById('rekeningNaarHidden').value = '';
	  checkButtonSubmit();
	}

	function showAvailableTargets(fromBtn) {
	  row2Buttons.forEach(btn => {
		btn.classList.remove('available');
		btn.disabled = false;
		if (btn.dataset.key !== fromBtn.dataset.key) {
		  btn.classList.add('available');
		} else {
		  btn.disabled = true;
		}
	  });
	}

	function checkButtonSubmit() {
	  const van = document.getElementById('rekeningVanHidden').value;
	  const naar = document.getElementById('rekeningNaarHidden').value;
	  $("#rekeningOverdrachtSubmit").attr("disabled", van && naar ? false : 'disabled');
	}

	$('#rekeningOverdrachtModal').on('show.bs.modal', function (e) {
	  clearConnection();
	});

	// Row1 click → select source
	row1Buttons.forEach(btn => {
	  btn.addEventListener('click', () => {
		clearConnection(); // reset everything visually
		selectedFrom = btn;
		btn.classList.add('selected');
		document.getElementById('rekeningVanHidden').value = btn.dataset.rek;
		showAvailableTargets(btn);
		checkButtonSubmit();
	  });
	});

	// Row2 click → draw/replace connection
	row2Buttons.forEach(btn => {
	  btn.addEventListener('click', () => {
		if (!selectedFrom) return;
		if (btn.disabled) return;

		// update hidden field
		document.getElementById('rekeningNaarHidden').value = btn.dataset.rek;

		// clear previous highlights and 'available' classes
		row2Buttons.forEach(b => b.classList.remove('linked', 'selected', 'available'));

		// mark the newly selected target
		btn.classList.add('linked', 'selected');

		currentConnection = { fromId: selectedFrom.id, toId: btn.id };
		drawConnection(selectedFrom, btn);
		checkButtonSubmit();
	  });
	});

	// keep line positioned correctly
	window.addEventListener('resize', () => {
	  if (currentConnection) {
		drawConnection(
		  document.getElementById(currentConnection.fromId),
		  document.getElementById(currentConnection.toId)
		);
	  }
	});

	window.addEventListener('scroll', () => {
	  if (currentConnection) {
		drawConnection(
		  document.getElementById(currentConnection.fromId),
		  document.getElementById(currentConnection.toId)
		);
	  }
	});
</script>	
</body>
</html>