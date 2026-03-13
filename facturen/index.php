<?php
$prefix = '../';
$activeFacturen = 'active';

include $prefix.'/bin/init.php';
$pageTitle = 'Billit transacties en facturen';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

//$clients = getClients('');
$dataFileIn = $wateringData['wateringId'] . '_invoicesIn.json';
$dataFileOut = $wateringData['wateringId'] . '_invoicesOut.json';

$types = getTypes($wateringJaar);
$hoofdPostenAll = getHoofdPostenActief($wateringJaar, $wateringData['wateringId']);
$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, 'X', 'X', 'A');
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
				<?php if($boekjaarOpen === false) { ?>
					<div class="d-sm-flex align-items-center justify-content-center mb-4">
						<span class="ml-2 text-red text-bold">
							<i class="fas fa-exclamation fa-sm fa-fw"></i> Dit boekjaar is afgesloten. Je kan geen wijzigingen aanbrengen <i class="fas fa-exclamation fa-sm fa-fw"></i>
						</span>
					</div>
				<?php } ?>

				<div class="row">
					<!-- Area Chart -->
					<div class="col-xl-12">
						<div class="card shadow mb-4">
							<!-- Card Body -->
							<div class="card-body">
								<div class="p-3 mb-4 bg-light rounded border">

								  <!-- klikbare header -->
								  <div class="d-flex align-items-center mb-2"
									   role="button"
									   data-toggle="collapse"
									   data-target="#billitInfo"
									   aria-expanded="false" 
									   aria-controls="billitInfo">

									<i class="fas fa-info-circle text-primary mr-2"></i>
									
									<!-- titel + chevron in één flex container -->
									<h6 class="font-weight-bold mb-0 text-dark d-flex align-items-center">
									  Beheer van je Billit transactie en facturen
									  <i class="fas fa-chevron-up ml-2 toggle-icon"></i>
									</h6>

								  </div>

								  <!-- inhoud, standaard ingeklapt: geen show class -->
								  <div id="billitInfo" class="collapse">
									<ul class="pl-3 mb-0 small text-muted ml-3">
									  <li><strong>Selecteer</strong> een transactie of factuur om de details te openen en deze te <strong>verwerken in je dagboek</strong>.</li>
									  <li>Wil je <strong>oudere transacties en facturen</strong> raadplegen? Pas dan de selectie aan en kies het gewenste <strong>aantal maanden</strong>.</li>
									  <li>Reeds <strong>verwerkte transacties</strong> en <strong>facturen</strong> kunnen altijd opnieuw geraadpleegd worden via ‘<strong>Toon alle facturen</strong>’.</li>
									  <li>
										Transacties of facturen die <strong>niet relevant</strong> zijn kan je <strong>direct verwerken</strong> via '<strong>Niet relevant</strong>'.
										<br>Ze blijven zichtbaar bij '<strong>Toon alle facturen</strong>' en worden <strong>oranje</strong> weergegeven.
									  </li>
									</ul>
								  </div>
								</div>
								
								<!-- Tabs Navigation -->
								<ul class="nav nav-tabs" id="invoicesTab" role="tablist">
									<li class="nav-item" role="presentation">
										<button class="nav-link active" id="ontvangsten-tab" data-toggle="tab" data-target="#ontvangsten" type="button" role="tab" aria-controls="ontvangsten" aria-selected="true">
											Ontvangsten <span class="record-count" id="countIn">0</span>
										</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="uitgaven-tab" data-toggle="tab" data-target="#uitgaven" type="button" role="tab" aria-controls="uitgaven" aria-selected="false">
											Uitgaven <span class="record-count" id="countOut">0</span>
										</button>
									</li>
								</ul>

								<!-- Ontvangsten Tab -->
								<div class="tab-content" id="invoicesTabContent">
									<!-- Ontvangsten Tab -->
									<div class="tab-pane fade show active" id="ontvangsten" role="tabpanel" aria-labelledby="ontvangsten-tab">
										<div class="card shadow mb-0">
											<div class="card-body">
												<div class="form-group row mt-0">
													<div class="col-sm-3 d-flex align-items-center pl-3 gap-2">
														<h6 class="m-0 text-black font-weight-bold text-line-height-m mr-2">Zoeken</h6>
														<input type="search" class="form-control form-control-user" id="filter-text-box-in" placeholder="Filter..." oninput="onFilterTextBoxChangedIn()" style="max-height: 35px;">
													</div>
													<div class="col-sm-3 d-flex text-black text-bold">
														<label class="mr-3 mt-2" style="min-width: 100px;" for="selectInvoicesIn">Facturen van</label>
														<select id="selectInvoicesIn" name="selectInvoicesIn" class="form-control form-select text-s mt-1" required>
															<option value="P1M" <?= ($selectedValueIn ?? '') == 'P1M' ? 'selected' : '' ?>>Laatste maand</option>
															<option value="P3M" <?= ($selectedValueIn ?? '') == 'P3M' ? 'selected' : '' ?>>Laatste 3 maanden</option>
															<option value="P6M" <?= ($selectedValueIn ?? '') == 'P6M' ? 'selected' : '' ?>>Laatste 6 maanden</option>
															<option value="P12M" <?= ($selectedValueIn ?? '') == 'P12M' ? 'selected' : '' ?>>Laatste jaar</option>
														</select>										
													</div>
													<div class="col-sm-2 text-black text-bold">
													  <div class="form-group mb-0 d-flex align-items-center">
														
														<label for="switchAllInvoicesIn" class="mr-3 mt-2" style="min-width: 140px;">Toon alle facturen</label>

														<div class="custom-control custom-switch">
														  <input type="checkbox" class="custom-control-input" id="switchAllInvoicesIn">
														  <label class="custom-control-label" for="switchAllInvoicesIn"></label>
														</div>
													  </div>
													</div>
													<div class="col-sm-2">
														<button class="btn btn-warning btn-sm mb-2 mt-1" id="markInvoicedIn" disabled>Niet relevant</button>
													</div>
												</div>
												<div id="myGridIn" class="ag-theme-balham"></div>
											</div>
										</div>
									</div>

									<!-- Uitgaven Tab -->
									<div class="tab-pane fade" id="uitgaven" role="tabpanel" aria-labelledby="uitgaven-tab">
										<div class="card shadow mb-0">
											<div class="card-body">
												<div class="form-group row mt-0">
													<div class="col-sm-3 d-flex align-items-center pl-3 gap-2">
														<h6 class="m-0 text-black font-weight-bold text-line-height-m mr-2">Zoeken</h6>
														<input type="search" class="form-control form-control-user" id="filter-text-box-out" placeholder="Filter..." oninput="onFilterTextBoxChangedOut()" style="max-height: 35px;">
													</div>
													<div class="col-sm-3 d-flex text-black text-bold">
														<label class="mr-3 mt-2" style="min-width: 100px;" for="selectInvoicesUit">Facturen van</label>
														<select id="selectInvoicesUit" name="selectInvoicesUit" class="form-control form-select text-s mt-1" required>
															<option value="P1M" <?= ($selectedValueUit ?? '') == 'P1M' ? 'selected' : '' ?>>Laatste maand</option>
															<option value="P3M" <?= ($selectedValueUit ?? '') == 'P3M' ? 'selected' : '' ?>>Laatste 3 maanden</option>
															<option value="P6M" <?= ($selectedValueUit ?? '') == 'P6M' ? 'selected' : '' ?>>Laatste 6 maanden</option>
															<option value="P12M" <?= ($selectedValueUit ?? '') == 'P12M' ? 'selected' : '' ?>>Laatste jaar</option>
														</select>										
													</div>									
													<div class="col-sm-2 text-black text-bold">
													  <div class="form-group mb-0 d-flex align-items-center">
														
														<label for="switchAllInvoicesOut" class="mr-3 mt-2" style="min-width: 140px;">Toon alle facturen</label>

														<div class="custom-control custom-switch">
														  <input type="checkbox" class="custom-control-input" id="switchAllInvoicesOut">
														  <label class="custom-control-label" for="switchAllInvoicesOut"></label>
														</div>
													  </div>
													</div>
													<div class="col-sm-2">
														<button class="btn btn-warning btn-sm mb-2 mt-1" id="markInvoicedOut" disabled>Niet relevant</button>
													</div>
												</div>
												<div id="myGridOut" class="ag-theme-balham"></div>
											</div>
										</div>
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

    <!-- Toevoegen Boeking Modal-->
	<div class="modal fade" id="addBoekingModal" tabindex="-1" role="dialog" aria-labelledby="addBoekingLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary text-bold" id="addBoekingLabel">Boeking toevoegen</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <form id="addBoekingForm" action="<?php echo $prefix ?>bin/pages/addBoekingInvoice.php" method="post" role="form">
			<div class="modal-body">

			  <!-- Factuur / Billit gegevens -->
			  <div class="card mb-2">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Billitgegevens</h6>
				</div>
				<div class="card-body">
				<div class="row">
				  <!-- Kolom 1: Factuurinformatie -->
				  <div class="col-md-4 mb-0">
					<div class="mb-2">
					  <div class="small font-weight-bold text-gray-900 text-muted">Factuurnummer</div>
					  <div class="small text-dark field-value" id="billOrderNr"></div>
					</div>
					<div class="mb-0">
					  <div class="small font-weight-bold text-gray-900 text-muted">Datum</div>
					  <div class="small text-dark field-value" id="billDate"></div>
					</div>
				  </div>

				  <!-- Kolom 2: Billit informatie -->
				  <div class="col-md-4 mb-0">
					<div class="mb-2">
					  <div class="small font-weight-bold text-gray-900 text-muted">Billitnummering</div>
					  <div class="small text-dark field-value" id="billOrderID"></div>
					</div>
					<div class="mb-0">
					  <div class="small font-weight-bold text-gray-900 text-muted">Vervaldag</div>
					  <div class="small text-dark field-value" id="billExpDate"></div>
					</div>
				  </div>

				  <!-- Kolom 3: Bedragen -->
				  <div class="col-md-4 mb-0">
					<div class="mb-2">
					  <div class="small font-weight-bold text-gray-900 text-muted">Bedrag</div>
					  <div class="small text-dark text-gray-900 field-value" id="billTotal"></div>
					</div>
					<div class="mb-0">
					  <div class="small font-weight-bold text-gray-900 text-muted">Betaald op</div>
					  <div class="small text-dark field-value" id="billPaidDate"></div>
					</div>
				  </div>
				</div>

				  <hr class="my-3">

				  <!-- Klant & status -->
				  <div class="row">
					<div class="col-md-4 mb-0">
					  <div class="small font-weight-bold text-gray-900 text-muted">Klant</div>
					  <div class="small text-dark field-value" id="billClient"></div>
					</div>

					<div class="col-md-4 mb-0">
					  <div class="small font-weight-bold text-gray-900 text-muted">Referentie</div>
					  <div class="small text-dark field-value" id="billReference"></div>
					</div>

					<div class="col-md-4 mb-0">
					  <div class="small font-weight-bold text-gray-900 text-muted">Status</div>
					  <div class="small text-dark field-value" id="billStatus"></div>
					</div>
				  </div>

				  <!-- Hidden fields -->
				  <input type="hidden" id="inputBoekingTotaal" name="boekingTotaal">
				  <input type="hidden" id="inputBoekingOrderID" name="boekingOrderID">
				  <input type="hidden" id="inputBoekingOrderDate" name="boekingOrderDate">
				  <input type="hidden" id="boekingType" name="boekingType">
				</div>
			  </div>

			  <!-- Boekinginformatie -->
			  <div class="card">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Boekinginformatie</h6>
				</div>
				<div class="card-body">

				  <!-- Datum -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Datum</label>
					<div class="col-sm-2">
					  <?php
						if($month === sprintf('%02d', $wateringMaand)) {
						  $addDay = $day . '/' . $month;
						} else {
						  $addDay = '01/' . sprintf('%02d', $wateringMaand);
						}
					  ?>
					  <input type="text"
							 id="inputBoekingDatum"
							 name="boekingDatum"
							 class="form-control form-control-sm datepicker readonlyInput"
							 value="<?php echo $addDay ?>"
							 readonly
							 required>
					</div>
				  </div>

				  <!-- Factuurnummer (optioneel) -->
				  <?php if($useNummering === 'X') { ?>
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Factuurnummer</label>
					<div class="col-sm-2">
					  <input id="addBoekingNummer" type="text" name="boekingNummering" class="form-control form-control-sm" required>
					</div>
				  </div>
				  <?php } else { ?>
					<input type="hidden" name="boekingNummering" id="addBoekingNummerHidden">
				  <?php } ?>

				  <!-- Post zoeken (ONGEWIJZIGD) -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Post zoeken</label>
					<div class="col-sm-9">
					  <!-- postSearch exact behouden -->
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
									<a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>|" data-level="1" data-type="<?php echo $type['typeId'] ?>" href="#"><span class="text-md font-weight-bold"><?php echo $description ?></span></a>
									<?php foreach($hoofdPosten as $hoofdPost) { 
										$refValue = $hoofdPost['hoofdpostId']; 
										$description = $hoofdPost['referentie'] . '. ' . $hoofdPost['omschrijving'];
										$posten = getPostenActiefGeenOverdracht($wateringData['wateringId'], $wateringJaar, $hoofdPost['hoofdpostId']);
										
										if ($posten->num_rows !== 0) {
									?>
											<a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>|" data-level="2" data-type="<?php echo $type['typeId'] ?>" href="#"><span class="text-md font-weight-bold"><?php echo $description ?></span></a>
										<?php 
											foreach($posten as $post) {
												$refValue = $hoofdPost['hoofdpostId'] . '|' . $post['postId']; 
												$description = $post['referentie'] . '. ' . $post['omschrijving'];
												if($post['actief'] === 'X' && $post['overdrachtPost'] !== 'X') { ?>
													<a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>" data-level="3" data-type="<?php echo $type['typeId'] ?>" href="#"><span class="text-s"><?php echo $description ?></span></a>
												<?php }
												
												$subposten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);
												foreach($subposten as $subpost) {
													$refValue = $hoofdPost['hoofdpostId'] . '|' . $post['postId'] . '|' . $subpost['subpostId']; 
													$description = $subpost['referentie'] . '. ' . $subpost['omschrijving'];
													if($subpost['actief'] === 'X') { ?>
														<a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>" data-level="4" data-type="<?php echo $type['typeId'] ?>" href="#"><span class="text-xs"><?php echo $description ?></span></a>
												<?php }
													}
												} 
											}
										}
									} 
									?>
						  </div>
						</div>
					  </div>
					</div>
				  </div>

				  <!-- Hoofdpost -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Hoofdpost</label>
					<div class="col-sm-9">
					  <select id="inputBoekingHoofdpost" name="boekingHoofdpost" class="form-control form-control-sm" required>
						<option value=""></option>
						<?php foreach ($hoofdPostenAll as $hoofdPost) { ?>
						  <option value="<?php echo $hoofdPost['hoofdpostId'] ?>" data-type="<?php echo $hoofdPost['typeId'] ?>">
							<?php echo ($hoofdPost['useKey'] === 'O' ? 'ONT' : 'UIT'); ?> -
							<?php echo $hoofdPost['referentie'] ?>. <?php echo $hoofdPost['omschrijving'] ?>
						  </option>
						<?php } ?>
					  </select>
					</div>
				  </div>

				  <!-- Post -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Post</label>
					<div class="col-sm-9">
					  <select id="inputBoekingPost" name="boekingPost" class="form-control form-control-sm" required></select>
					</div>
				  </div>

				  <!-- Subpost -->
				  <div class="form-group row mb-2" id="addSubpost">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Subpost</label>
					<div class="col-sm-9">
					  <select id="inputBoekingSubpost" name="boekingSubpost" class="form-control form-control-sm" required></select>
					</div>
				  </div>

				  <!-- Rekening -->
				  <div class="form-group row mb-2" id="addRekening">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Rekening</label>
					<div class="col-sm-9">
					  <select id="inputBoekingRekening" name="boekingRekening" class="form-control form-control-sm" required>
						<option value=""></option>
						<?php foreach($rekeningen as $rekening) {
						  if($rekening['rekening'] !== 'KAS' || $useKAS === 'X') { ?>
							<option value="<?php echo $rekening['rekeningId'] ?>">
							  <?php if($rekening['rekening'] !== 'KAS') { ?>
								<?php echo $rekening['rekening'] ?> - <?php echo $rekening['omschrijving'] ?>
							  <?php } else { ?>
								<?php echo $rekening['rekening'] ?>
							  <?php } ?>
							</option>
						<?php }} ?>
					  </select>
					</div>
				  </div>

				  <!-- Omschrijving -->
				  <div class="form-group row mb-2">
					<label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">Omschrijving</label>
					<div class="col-sm-9">
					  <input type="text" id="inputBoekingOmschrijving" name="boekingOmschrijving" class="form-control form-control-sm" required>
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
	
	
	<!-- Manuele status wijziging Modal-->
	<div class="modal fade" id="confirmMarkProcessedModal" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h5 class="modal-title text-warning font-weight-bold">
			  <i class="fas fa-exclamation-triangle mr-2"></i>
			  Bevestiging vereist
			</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <div class="modal-body">

			<!-- Inhoud -->
			<p class="font-weight-bold text-dark mb-3">
			  Ben je zeker dat je deze <span id="pendingCount">0</span> transacties / facturen als
			  <span class="text-warning">niet relevant</span> wil markeren?
			</p>

			<ul class="mb-3">
			  <li>Er gebeurt <strong>geen boeking</strong> in je dagboek.</li>
			  <li>Je kan deze items later nog <strong>verwerken</strong> door erop te klikken.</li>
			</ul>

			<!-- Waarschuwing -->
			<div class="alert alert-warning d-flex align-items-start mb-0" role="alert">
			  <div class="mr-3" style="font-size: 1.5rem;">⚠️</div>
			  <div>
				Deze actie zet de transacties enkel op <strong>niet relevant</strong>, zonder impact op je dagboek.
			  </div>
			</div>

		  </div>

		  <!-- Footer -->
		  <div class="modal-footer bg-light">
			<form id="confirmProcessedForm" class="user mb-0" role="form">
			  <button type="submit"
					  class="btn btn-warning"
					  id="confirmMarkProcessed">
				Zet op niet relevant
			  </button>
			  <button type="button"
					  class="btn btn-secondary"
					  data-dismiss="modal"
					  id="cancelMarkProcessed">
				Annuleren
			  </button>
			</form>
		  </div>

		</div>
	  </div>
	</div>


	<?php include $prefix.'includes/scripts.php';?>

	<?php include $prefix.'includes/scriptsVariables.php';?>
	<?php include $prefix.'includes/scriptsGeneral.php';?>
	
	<script>
	function applyInvoiceFilter(gridApi, showAll) {
	  if (showAll) {
		gridApi.setFilterModel({ Invoiced: null });
	  } else {
		gridApi.setFilterModel({ Invoiced: { type: 'false' } });
	  }
	}

	// Grid API: Access to Grid API methods
	let gridApiIn;
	let gridApiOut;
	
	let pendingInvoices = [];
	let pendingType = null; // IN | OUT
	
	// Grid Options: Contains all of the grid configurations
	const gridOptions = {
	  // Data to be displayed
	  rowHeight: 28,
	  rowData: [],
	  // Columns to be displayed (Should match rowData properties)
	  columnDefs: [
		{ field: "OrderDate", headerName: "Datum", minWidth: 100, maxWidth: 100, cellRenderer: (data) => { return formatDate(data.value); }, sort: 'desc' },
		{ field: "OrderNumber", headerName: "Factuurnummer", minWidth: 130, maxWidth: 150 },
		{ field: "OrderID", headerName: "Billitnummering", minWidth: 130, maxWidth: 150 },
		{ field: "ExpiryDate", headerName: "Vervaldag", minWidth: 100, maxWidth: 100, cellRenderer: (data) => { return formatDate(data.value); } },
		{ field: "PaidDate", headerName: "Betaald op", minWidth: 100, maxWidth: 100, cellRenderer: (data) => { return formatDate(data.value); } },
		{ field: "Reference", headerName: "Referentie", minWidth: 150, maxWidth: 300 },
		{ field: "CounterParty.DisplayName", headerName: "Klant", minWidth: 350, maxWidth: 400 },
//		{ field: "TotalExcl", headerName: "Excl.", minWidth: 120, maxWidth: 150, cellRenderer: (data) => { return formatEuro(data.value); } },
//		{ field: "TotalVAT", headerName: "BTW", minWidth: 120, maxWidth: 150, cellRenderer: (data) => { return formatEuro(data.value); } },
		{ field: "TotalIncl", headerName: "Bedrag", minWidth: 120, maxWidth: 150, cellRenderer: (data) => { return formatEuro(data.value); } },
		{ field: "OrderStatus", headerName: "Status", minWidth: 120, maxWidth: 150, cellClassRules: { 'status-paid': params => params.value === 'Betaald' } },
		{ field: "Invoiced", headerName: "Verwerkt", minWidth: 85, maxWidth: 85 },		
		{ field: "NotRelevant", headerName: "Niet relevant", minWidth: 85, maxWidth: 85 },		
	  ],
	  defaultColDef: {
		  flex: 2,
		  filter: "agTextColumnFilter",
	  cellStyle: { 
			color: '#858796'
			}
	      },
	  pagination: true,
	  rowSelection: {
		mode: 'multiRow',
		selectAll: 'currentPage', 
		headerCheckbox: true,
		isRowSelectable: (rowNode) => {
		  return rowNode.data && rowNode.data.Invoiced !== true;
		}
	  },	  
	  suppressRowClickSelection: true,
	  paginationPageSize: 20,
      domLayout: 'normal',
	  animateRows: false,
	  onRowClicked: params => addBoeking(params, 'IN'), 
	  onGridReady: initialFilter,
	  getRowStyle: params => {
			if (params.data.NotRelevant === true) {
			  return { backgroundColor: 'rgba(246, 194, 62, 0.7)' };
			}
			if (params.data.Invoiced === true) {
			  return { backgroundColor: 'lightgreen' };
			}
			else {
			  return null;
			}
		},
	  onSelectionChanged: () => {
	    const hasSelection = gridApiIn.getSelectedRows().length > 0;
	    document.getElementById('markInvoicedIn').disabled = !hasSelection;
	    }		
	  };
	
	function initialFilter(){
		gridApiIn.setFilterModel({ Invoiced: { type: 'false' } });
	}
	
	function onFilterTextBoxChangedIn() {
		gridApiIn.setGridOption("quickFilterText", document.getElementById("filter-text-box-in").value,
		);
	}

	function refreshAll() {
	  fetch("../data/<?php echo $dataFileIn ?>", { cache: "no-store", })
		.then((response) => response.json())
		.then((data) => gridApiIn.setGridOption("rowData", data))
		.then((data) => updateInCount())
		.catch((error) => console.error('Error refreshing grid:', error));
	}
			
	// Create Grid: Create new grid within the #myGridIn div, using the Grid Options object
	gridApiIn = agGrid.createGrid(document.querySelector("#myGridIn"), gridOptions);
	//gridApi.autoSizeAllColumns();

	// Fetch Remote Data
	fetch("../data/<?php echo $dataFileIn ?>", { cache: "no-cache" })
	  .then((response) => response.json())
	  .then((data) => gridApiIn.setGridOption("rowData", data))
	  .then((data) => updateInCount());

	$(function () {
	  const key = 'showAllInvoicesIn';
	  const $switch = $('#switchAllInvoicesIn');

	  // 🔁 restore state
	  const saved = localStorage.getItem(key);
	  if (saved === '1') {
		$switch.prop('checked', true);
		applyInvoiceFilter(gridApiIn, true);
	  } else {
		$switch.prop('checked', false);
		applyInvoiceFilter(gridApiIn, false);
	  }

	  // 💾 save on change
	  $switch.on('change', function () {
		const checked = $(this).prop('checked');
		localStorage.setItem(key, checked ? '1' : '0');
		applyInvoiceFilter(gridApiIn, checked);
	  });
	});

	function updateInCount() {
	  let count = 0;

	  gridApiIn.forEachNodeAfterFilterAndSort(node => {
		if (node.data && node.data.Invoiced !== true) {
		  count++;
		}
	  });

	  document.querySelector('#countIn').textContent = count;
	}		
	
	//-------------------- OUT

	const gridOptionsOut = {
	  // Data to be displayed
	  rowHeight: 28,
	  rowData: [],
	  // Columns to be displayed (Should match rowData properties)
	  columnDefs: [
		{ field: "OrderDate", headerName: "Datum", minWidth: 100, maxWidth: 100, cellRenderer: (data) => { return formatDate(data.value); }, sort: 'desc' },
		{ field: "OrderNumber", headerName: "Factuurnummer", minWidth: 130, maxWidth: 150 },
		{ field: "OrderID", headerName: "Billitnummering", minWidth: 130, maxWidth: 150 },
		{ field: "ExpiryDate", headerName: "Vervaldag", minWidth: 100, maxWidth: 100, cellRenderer: (data) => { return formatDate(data.value); } },
		{ field: "PaidDate", headerName: "Betaald op", minWidth: 100, maxWidth: 100, cellRenderer: (data) => { return formatDate(data.value); } },
		{ field: "Reference", headerName: "Referentie", minWidth: 150, maxWidth: 300 },
		{ field: "CounterParty.DisplayName", headerName: "Klant", minWidth: 350, maxWidth: 400 },
//		{ field: "TotalExcl", headerName: "Excl.", minWidth: 120, maxWidth: 150, cellRenderer: (data) => { return formatEuro(data.value); } },
//		{ field: "TotalVAT", headerName: "BTW", minWidth: 120, maxWidth: 150, cellRenderer: (data) => { return formatEuro(data.value); } },
		{ field: "TotalIncl", headerName: "Bedrag", minWidth: 120, maxWidth: 150, cellRenderer: (data) => { return formatEuro(data.value); } },
		{ field: "OrderStatus", headerName: "Status", minWidth: 120, maxWidth: 150, cellClassRules: { 'status-paid': params => params.value === 'Betaald' } },
		{ field: "Invoiced", headerName: "Verwerkt", minWidth: 85, maxWidth: 85 },		
		{ field: "NotRelevant", headerName: "Niet relevant", minWidth: 85, maxWidth: 85 },		
	  ],
	  defaultColDef: {
		  flex: 2,
		  filter: "agTextColumnFilter",
	  cellStyle: { 
			color: '#858796'
			}		  
	      },
	    rowSelection: {
			mode: 'multiRow',
			selectAll: 'currentPage', 
			headerCheckbox: true,
		    isRowSelectable: (rowNode) => {
			  return rowNode.data && rowNode.data.Invoiced !== true;
			}
		  },
	  suppressRowClickSelection: true,  
	  pagination: true,
	  paginationPageSize: 20,
      domLayout: 'normal',	  	  
	  onRowClicked: params => addBoeking(params, 'OUT'),
	  onGridReady: initialFilterOut,
	  getRowStyle: params => {
			if (params.data.NotRelevant === true) {
			  return { backgroundColor: 'rgba(246, 194, 62, 0.7)' };
			}			
			if (params.data.Invoiced === true) {
			  return { backgroundColor: 'lightgreen' };
			}
			else {
			  return null;
			}
		},
	  onSelectionChanged: () => {
	    const hasSelection = gridApiOut.getSelectedRows().length > 0;
	    document.getElementById('markInvoicedOut').disabled = !hasSelection;
	    }
	  };

	function initialFilterOut(){
		gridApiOut.setFilterModel({ Invoiced: { type: 'false' } });
	}
	
	function onFilterTextBoxChangedOut() {
		gridApiOut.setGridOption("quickFilterText", document.getElementById("filter-text-box-out").value,
		);
	}

	function refreshAllUit() {
		fetch("../data/<?php echo $dataFileOut ?>", { cache: "no-store", })
			  .then((response) => response.json())
			  .then((data) => gridApiOut.setGridOption("rowData", data))
			  .then((data) => updateOutCount());
		}
			
	// Create Grid: Create new grid within the #myGridOut div, using the Grid Options object
	gridApiOut = agGrid.createGrid(document.querySelector("#myGridOut"), gridOptionsOut);
	//gridApiOut.autoSizeAllColumns();

	// Fetch Remote Data
	fetch("../data/<?php echo $dataFileOut ?>", { cache: "no-cache" })
	  .then((response) => response.json())
	  .then((data) => gridApiOut.setGridOption("rowData", data))
	  .then((data) => updateOutCount());

	$(function () {
	  const key = 'showAllInvoicesOut';
	  const $switch = $('#switchAllInvoicesOut');

	  // 🔁 restore state
	  const saved = localStorage.getItem(key);
	  if (saved === '1') {
		$switch.prop('checked', true);
		applyInvoiceFilter(gridApiOut, true);
	  } else {
		$switch.prop('checked', false);
		applyInvoiceFilter(gridApiOut, false);
	  }

	  // 💾 save on change
	  $switch.on('change', function () {
		const checked = $(this).prop('checked');
		localStorage.setItem(key, checked ? '1' : '0');
		applyInvoiceFilter(gridApiOut, checked);
	  });
	});

	function updateOutCount() {
	  let count = 0;

	  gridApiOut.forEachNodeAfterFilterAndSort(node => {
		if (node.data && node.data.Invoiced !== true) {
		  count++;
		}
	  });

	  document.querySelector('#countOut').textContent = count;
	}

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

// ---   Datepicker   ---
// ----------------------
	$('.datepicker').datepicker({
			format: "dd/mm",
			todayBtn: "linked",
			language: "nl-BE",
			autoclose: true,
			todayHighlight: true
		});	
		
// ---   Posten - DropDowns   ---
// ------------------------------
	function filterPostSearchByTab(activeTab) {
		const $items = $('#postSearch .dropdown-action-add');

		// First show everything
		$items.show();

		if (activeTab === 'ontvangsten-tab') {
			// Hide BU & GU
			$items.filter('[data-type="BU"], [data-type="GU"]').hide();
		}

		if (activeTab === 'uitgaven-tab') {
			// Hide BO & GO
			$items.filter('[data-type="BO"], [data-type="GO"]').hide();
		}
	}
	
	function filterHoofdpostByTab(activeTab) {
		const $select = $('#inputBoekingHoofdpost');
		const $options = $select.find('option');

		// Always show all first
		$options.show();

		if (activeTab === 'ontvangsten-tab') {
			$options.filter('[data-type="BU"], [data-type="GU"]').hide();
		}

		if (activeTab === 'uitgaven-tab') {
			$options.filter('[data-type="BO"], [data-type="GO"]').hide();
		}

		// If currently selected option is hidden → reset selection
		const selectedOption = $select.find('option:selected');
		if (selectedOption.length && selectedOption.is(':hidden')) {
			$select.val('').trigger('change');
		}
	}

// Wijziging Hoofdpost
	$( "select[name='boekingHoofdpost']" ).change(function () {
			var hoofdpostId = $(this).val();
			$("div#addSubpost").hide();
			$("div#addSubpostChange").hide();
			document.getElementById("inputBoekingSubpost").required = false;

			if(hoofdpostId !== '') {
				$.ajax({
						url: "../bin/selects/getPosten.php",
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
			
			if(postId !== '') {
				$.ajax({
						url: "../bin/selects/getSubposten.php",
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
							} else {
								setOmschrijving($('select[name="boekingPost"] option:selected').text())
								}
						}
					});
			} else { 
				$('select[name="boekingSubpost"]').empty();
				$("div#addSubpost").hide();
				$("div#addSubpostChange").hide();
			}
		});

		$( "select[name='boekingSubpost']" ).change(function () {
			setOmschrijving($('select[name="boekingSubpost"] option:selected').text())
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
							url: "../bin/selects/getPosten.php",
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
							setOmschrijving($('select[name="boekingPost"] option:selected').text());

							if(postId !== '') {
								$.ajax({
										url: "../bin/selects/getSubposten.php",
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
											setOmschrijving($('select[name="boekingSubpost"] option:selected').text())
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
		
		function setOmschrijving(value) {
			var billitRef = document.getElementById('billReference').textContent.trim();
			
			const omschrPostFinal = value.split('. ')[1]; 
			if (billitRef === '' || billitRef === 'Geen Billitfactuur aanwezig')
				document.getElementById('inputBoekingOmschrijving').value = omschrPostFinal;
			}
		
		function addBoeking(params, type) {
			if (params.data.Invoiced === true && params.data.NotRelevant === false) {
				return;
			}
			$.ajax({
				url: '<?php echo($prefix);?>bin/selects/getLastBoekingNr.php',
				type: 'GET',
				dataType: 'json',
				success: function(response) {
					$('#addBoekingModal').modal('show');
					
					var dateOnly = '';
					if (!params.data.PaidDate || params.data.PaidDate.toString().trim() === '') 
						dateOnly = params.data.OrderDate.split("T")[0];
					else
						dateOnly = params.data.PaidDate.split("T")[0];

					document.getElementById('boekingType').value = type;
					document.getElementById('billDate').innerHTML = formatDate(params.data.OrderDate);
					document.getElementById('billOrderNr').innerHTML = params.data.OrderNumber;
					document.getElementById('billOrderID').innerHTML = params.data.OrderID;
					document.getElementById('billExpDate').innerHTML = formatDate(params.data.ExpiryDate);
					document.getElementById('billPaidDate').innerHTML = formatDate(params.data.PaidDate);
//					document.getElementById('billExcl').innerHTML = formatEuro(params.data.TotalExcl);
//					document.getElementById('billVAT').innerHTML = formatEuro(params.data.TotalVAT);
					document.getElementById('billTotal').innerHTML = formatEuro(params.data.TotalIncl);
					document.getElementById('billClient').innerHTML = params.data.CounterParty.DisplayName;
					if (params.data?.Reference) 
						document.getElementById('billReference').innerHTML = params.data.Reference;
					else
						document.getElementById('billReference').innerHTML = '';
					document.getElementById('billStatus').innerHTML = params.data.OrderStatus;

					document.getElementById('inputBoekingDatum').value = dateOnly;

					if (params.data.Reference !== 'Geen Billitfactuur aanwezig' ) {
						$('#inputBoekingOmschrijving').val(params.data.Reference);
					} else {
						$('#inputBoekingOmschrijving').val('');
					}

					$('#inputBoekingTotaal').val(params.data.TotalIncl);
					$('#inputBoekingOrderID').val(params.data.OrderID);
					$('#inputBoekingOrderDate').val(params.data.OrderDate);

					const d = new Date(dateOnly);
					$('#inputBoekingDatum').datepicker('update', d);

					const boekingNummer = '<?php echo $nummeringPrefix ?>' + response;
							
					$('#addBoekingNummer').val(boekingNummer);
					$('#addBoekingNummerHidden').val(boekingNummer);

					$('#inputBoekingHoofdpost').val("").change();
					$('select[name="boekingPost"]').empty();
					$('select[name="boekingSubpost"]').empty();
					
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
						$("#postSearch .dropdown-item").removeAttr("style");
						$("#postSearch .dropdown-item[data-value='|']").first().addClass("active");
						}

					const activeTabId = $('#invoicesTab .nav-link.active').attr('id');

					filterPostSearchByTab(activeTabId);
					filterHoofdpostByTab(activeTabId);
				},
				error: function(xhr, status, error) {
					console.error(error);
				}
			});
		}

	$(document).ready(function () {
	  $('#selectInvoicesIn').on('change', function () {
		$.showLoader({ message: 'De gegevens worden geladen…' });
		const selectedValueIn = $(this).val();

		$.ajax({
		  url: '../bin/selects/setInvoiceSelectionIn.php',   // PHP file (see next step)
		  type: 'POST',
		  data: { selected: selectedValueIn },
		  success: function (response) {
			  refreshAll();
			  $.hideLoader();
		  },
		  error: function (xhr, status, error) {
			console.error('AJAX Error:', error);
			$.hideLoader();
		  }
		});
	  });
	});		
	
	$(document).ready(function () {
	  $('#selectInvoicesUit').on('change', function () {
		$.showLoader({ message: 'De gegevens worden geladen…' });
		const selectedValueIn = $(this).val();

		$.ajax({
		  url: '../bin/selects/setInvoiceSelectionUit.php',   // PHP file (see next step)
		  type: 'POST',
		  data: { selected: selectedValueIn },
		  success: function (response) {
			  refreshAllUit();
			  $.hideLoader();
		  },
		  error: function (xhr, status, error) {
			console.error('AJAX Error:', error);
			$.hideLoader();
		  }
		});
	  });
	});	

	$(document).ready(function () {
		const tabButtons = $('#invoicesTab button[data-toggle="tab"]');

		const activeTabId = localStorage.getItem('activeInvoiceTab') || 'ontvangsten-tab';
		const $tabToShow = $('#' + activeTabId);

		if ($tabToShow.length) {
			$tabToShow.tab('show');
			filterPostSearchByTab(activeTabId);
			filterHoofdpostByTab(activeTabId); // ✅ added
		}

		tabButtons.on('shown.bs.tab', function (e) {
			const id = $(e.target).attr('id');
			localStorage.setItem('activeInvoiceTab', id);

			filterPostSearchByTab(id);
			filterHoofdpostByTab(id); // ✅ added
		});
	});

	$(document).ready(function() {
	  $("#addBoekingForm").on("submit", function(e) {
		$.showLoader({ message: 'De boeking wordt aangemaakt…' });
		e.preventDefault(); // prevent normal form submit

		const $form = $(this);
		const url = $form.attr("action");   // PHP endpoint
		const formData = $form.serialize(); // gather form fields

		$.ajax({
		  url: url,
		  type: "POST",
		  data: formData,
		  success: function(response) {
			$("#addBoekingModal").modal("hide");

			// Optional: clear the form
			$("#addBoekingForm")[0].reset();
		    refreshAll();
			refreshAllUit();
		    $.hideLoader();
		  },
		  error: function(xhr, status, error) {
			console.log("Error:", error);
		  }
		});
	  });
	});	


	// Transacties manueel op verwerkt zetten
	$('#markInvoicedIn').on('click', function () {
	  pendingInvoices = gridApiIn.getSelectedRows().map(r => ({
		orderId: r.OrderID,
		orderDate: r.OrderDate
	  }));
	  pendingType = 'IN';

	  // Update aantal in modal
	  document.getElementById('pendingCount').textContent = pendingInvoices.length;

	  $('#confirmMarkProcessedModal').modal('show');
	});

	$('#markInvoicedOut').on('click', function () {
	  pendingInvoices = gridApiOut.getSelectedRows().map(r => ({
		orderId: r.OrderID,
		orderDate: r.OrderDate
	  }));
	  pendingType = 'OUT';

	  // Update aantal in modal
	  document.getElementById('pendingCount').textContent = pendingInvoices.length;

	  $('#confirmMarkProcessedModal').modal('show');
	});

	$('#confirmMarkProcessed').on('click', function () {
	  if (!pendingInvoices.length || !pendingType) return;
	  
	  $.showLoader({ message: 'De status van de transacties/facturen wordt aangepast…' });
	  $.ajax({
		url: '../bin/updates/markInvoicesProcessed.php',
		type: 'POST',
		data: {
		  type: pendingType,
		  invoices: pendingInvoices
		},
		success: function (response) {
		  console.log(response);
		  $('#confirmMarkProcessedModal').modal('hide');

		  if (pendingType === 'IN') {
			refreshAll();
			gridApiIn.deselectAll();
			document.getElementById('markInvoicedIn').disabled = true;
		    $.hideLoader();
		  } else {
			refreshAllUit();
			gridApiOut.deselectAll();
			document.getElementById('markInvoicedOut').disabled = true;
		    $.hideLoader();
		  }

		  pendingInvoices = [];
		  pendingType = null;
		},
		error: function () {
		  console.log('Fout tijdens zetten van status');
		}
	  });
	});	
</script>
</body>
</html>