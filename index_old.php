<?php
include 'bin/init.php';
$pageTitle = 'Dagboek';
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
$aantalRek = 0;
foreach($rekeningen as $rekening) {
	$aantalRek = $aantalRek + 1;
	}	
	
$boekingen = getBoekingen($wateringData['wateringId'], $wateringJaar, $wateringMaand, $useNummering, $sortering);
$lastBoekingNr = getLastBoekingNr($wateringData['wateringId'], $wateringJaar);
$lastBoekingNr = $lastBoekingNr + 1;

$hoofdPosten = getHoofdPostenAll();
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
					<?php if($boekjaarOpen === false) { ?>
						<div class="d-sm-flex align-items-center justify-content-center mb-4">
							<?php if($wateringJaar === strval(date("Y"))) { ?>
								<span class="ml-2 text-red text-bold"><i class="fas fa-exclamation fa-sm fa-fw"></i> Het vorige boekjaar is nog niet afgesloten. Je kan geen wijzigingen aanbrengen <i class="fas fa-exclamation fa-sm fa-fw"></i></span>
							<?php } else { ?>
								<span class="ml-2 text-red text-bold"><i class="fas fa-exclamation fa-sm fa-fw"></i> Dit boekjaar is afgesloten. Je kan geen wijzigingen aanbrengen <i class="fas fa-exclamation fa-sm fa-fw"></i></span>
							<?php } ?>
						</div>
					<?php } ?>

                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
									<div class="headerButtons">
                                    <div class="headerFirstPart vertical-center"><h6 class="m-0 font-weight-bold text-line-height-m">Invoeren dagboek - <b><?php echo monthNames[$maandSel] ?> <?php echo $wateringJaar; ?></b></h6></div>
									<div>
										<?php 
										$previousDis = '';
										$nextDis = '';
										if($wateringMaand === '01' || $wateringMaand === '1') {
											$previousDis = 'disabled';
											}
										if($wateringMaand === '12') {
											$nextDis = 'disabled';
										} ?>
										<span class="border-left-m-cb ml-2 pl-3"><button type="button" id="buttonPreviousMaand" class="btn btn-primary zbtn-xs" <?php echo $previousDis ?>><i class="fas fa-arrow-left fa-sm fa-fw"></i></button></span>
										<span class="ml-2"><button type="button" id="buttonHuidigeMaand" class="btn btn-primary zbtn-xs"><i class="fas fa-calendar-alt fa-sm fa-fw"></i> Huidige maand</button></span>
										<span class="ml-2"><button type="button" id="buttonNextMaand" class="btn btn-primary zbtn-xs" <?php echo $nextDis ?>><i class="fas fa-arrow-right fa-sm fa-fw"></i></button></span>
										<?php if($boekjaarOpen === true) { ?>
											<span class="border-left-m-cb ml-2 pl-3"><button type="button" id="buttonRekening" class="btn btn-primary zbtn-xs" data-toggle="modal" data-target="#rekeningOverdrachtModal"><i class="fas fa-paste fa-sm fa-fw"></i> Rekeningoverdracht</button></span>
											<span class="ml-2"><button type="button" id="buttonOpslaan" class="btn btn-primary zbtn-xs" onclick="opslaan()"><i class="fas fa-save fa-sm fa-fw"></i> Opslaan</button></span>
											<?php if( $wateringMaand === '12' or ( checkNieuwJaarBestaat($wateringData['wateringId'], ($wateringJaar + 1)) === true ) ) { ?>
												<span class="border-left-m-cb ml-2 pl-3"><button type="button" id="buttonBoekjaarAfsluiten" class="btn btn-danger zbtn-xs" data-toggle="modal" data-target="#boekjaarAfsluiten"><i class="fas fa-lock fa-sm fa-fw"></i> Boekjaar afsluiten</button></span>
											<?php } ?>
										<?php } else {
											if($wateringJaar === strval((date("Y") - 1))) { ?>
											<span class="border-left-m-cb ml-2 pl-3"><button type="button" id="buttonBoekjaarOpenen" class="btn btn-success zbtn-xs" data-toggle="modal" data-target="#boekjaarOpenen"><i class="fas fa-save fa-sm fa-fw"></i> Boekjaar openen</button></span>
										<?php }
											}?>
									</div>
									</div>

                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Acties:</div>
                                            <a class="dropdown-item" href="index.php">Verversen</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="documenten/dagboek.php">Dagboek document genereren</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
								<?php $aantalRek = 0;
									  $aantalRekNoKas = 0;
									foreach($rekeningen as $rek) {
										$aantalRek = $aantalRek + 1;
										/* if($rek['rekening'] !== 'KAS') { */
											$aantalRekNoKas = $aantalRekNoKas + 1;
											/* } */
										} ?>
                                <div class="card-body">
									<div class="table-responsive">
										<?php if($useNummering === 'X') {
											$colSpanFooter = 5;
											} else {
											$colSpanFooter = 4;
											} ?>

										<table id="addBoekingenTable" class="table table-bordered table-striped small text-xs text-gray-900" style="table-layout: fixed;" cellspacing="0">
											<thead class="text-center">
												<tr class="text-xs thead-dark">
													<th style="width: 4%">Actie<br>&nbsp;</th>
													<th style="width: 4%">Datum<br>&nbsp;</th>
													<th style="width: 6%">Post<br>&nbsp;</th>
													<?php if($useNummering === 'X') { ?>
														<th style="width: 4%">Factuurnr<br>&nbsp;</th>
													<?php } ?>
													<th style="width: 18%">Omschrijving<br>&nbsp;</th>
													<?php foreach ($rekeningen as $rekening) { ?>
														<th colspan="2"><?php echo $rekening['rekening'] ?><br>
															<?php if($rekening['rekening'] === 'KAS') { ?>&nbsp;
															<?php } else { echo $rekening['omschrijving']; } ?>
														</th>
													<?php } ?>
												</tr>
												<tr class="text-xs">
													<th class="border-right-s" colspan="<?php echo $colSpanFooter ?>"></th>
													<?php $counter = 0;
														  $tdClass = 'border-right-s';
														foreach ($rekeningen as $rekening) {
															if($counter === $aantalRekNoKas) {
																$tdClass = '';
																}
															$counter = $counter + 1;
															?>
														<th>Ontvangsten</th>
														<th class="<?php echo $tdClass ?>">Uitgaven</th>
													<?php } ?>
												</tr>
											</thead>
											<tbody id="_editable_table">
												<tr>
													<td></td>
													<td>01/<?php echo sprintf('%02d', $wateringMaand) ?></td>
													<td>
														<?php if($wateringMaand === '1') {
															echo 'ONT I 1';
														}?>
													</td>
													<?php if($useNummering === 'X') { ?>
														<td></td>
													<?php } ?>
													<td class="border-right-s">
														<?php if($wateringMaand === '1') {
															$postOverdracht = getPostDataOvdrachtPost($wateringData['wateringId'], $wateringJaar);
															echo $postOverdracht['omschrijving'];
														} else { ?>
														Overdracht
														<?php } ?>
													</td>
													<?php $counter = 0;
														  $tdClass = 'border-right-s';
														  foreach ($rekeningen as $rekening) { 
															if($counter === $aantalRekNoKas) {
																$tdClass = '';
																}
															$counter = $counter + 1;
														$overdracht = getOverdracht($wateringData['wateringId'], $wateringJaar, $wateringMaand, $rekening['rekeningId']);
														${'rek_'.$rekening['rekeningId'].'_O'} = ${'rek_'.$rekening['rekeningId'].'_O'} + $overdracht;
														?>
														<td class="table-left-line"><?php echo currencyConv($overdracht) ?></td>
														<td class="<?php echo $tdClass ?>"></td>
													<?php } ?>
												</tr>
												<?php foreach ($boekingen as $boeking) { 
													$postData = getPostData($boeking['postId']); 
													$subPostData = getSubPostData($boeking['subPostId']);
													$hoofdPostData = getHoofdPostData($postData['hoofdpostId']);
													$boekingsDatum = $boeking['jaar'] . '-' . sprintf('%02d', $boeking['maand']) . '-' . sprintf('%02d', $boeking['dag']); ?>
													<tr data-row-id="<?php echo $boeking['boekId'] ?>">
														<td class="text-center">
														<?php if($boekjaarOpen === true) { ?>
															<div id="headerButtons">
																<?php if($boeking['postId'] !== '0') { ?>
																<div class="childButtons">
																	<a href="#" onclick="vulWijzigingsModal('<?php echo $boeking['boekId'] ?>', '<?php echo $boekingsDatum ?>', '<?php echo $hoofdPostData['hoofdpostId'] ?>', '<?php echo $postData['postId'] ?>', '<?php echo $subPostData['subpostId'] ?>', '<?php echo $boeking['nummering'] ?>')" title="Boeking wijzigen" data-toggle="modal">
																		<i class="fas fa-pencil-alt fa-sm fa-fw mr-2 text-warning"></i>
																	</a>
																</div>
																<?php } ?>	
																<div class="childButtons">
																	<a href="bin/pages/deleteBoeking.php?boekId=<?php echo $boeking['boekId'] ?>" title="Boeking verwijderen">
																		<i class="fas fa-trash-alt fa-sm fa-fw mr-2 text-danger"></i>
																	</a>
																</div>
															</div>
														<?php } ?>
														</td>
														<td><?php echo sprintf('%02d', $boeking['dag']); ?>/<?php echo sprintf('%02d', $boeking['maand']); ?></td>
														<td>
															<?php if($boeking['postId'] !== '0') { ?>
																<?php if($hoofdPostData['useKey'] === 'U') { ?>UIT<?php } else { ?>ONT<?php } ?>
																<?php echo $hoofdPostData['referentie'] ?>
																<?php echo $postData['referentie'] ?><?php echo $subPostData['referentie'] ?>
															<?php } ?>
														</td>
														<?php if($boeking['postId'] !== '0' && $boekjaarOpen === true) {
															$contentEditable = 'true';
															if($hoofdPostData['useKey'] === 'U') {
																$contentEditableO = 'false';
																$contentEditableU = 'true';
																}
															if($hoofdPostData['useKey'] === 'O') {
																$contentEditableO = 'true';
																$contentEditableU = 'false';
																}
															} else { 
															$contentEditable = 'false';
															$contentEditableU = 'false';
															$contentEditableO = 'false';
															} ?>
														
														<?php if($useNummering === 'X') { ?>
															<td class="editable-col" contenteditable="true" col-index="0b" col-calc="999999" oldval="<?php echo $boeking['nummering'] ?>"><?php echo $boeking['nummering'] ?></td>
														<?php } ?>
														
														<td class="editable-col border-right-s" contenteditable="<?php echo $contentEditable ?>" col-index="0" col-calc="999999" oldval="<?php echo $boeking['omschrijving'] ?>"><?php echo $boeking['omschrijving'] ?></td>
														<?php $aantalRek = $aantalRek * 2;
															  $columnWidth = 68 / $aantalRek; 
															  $counter = 0;
															  $tdClass = 'border-right-s';
															foreach ($rekeningen as $rekening) {
																if($counter === $aantalRekNoKas) {
																	$tdClass = '';
																	}
																$counter = $counter + 1;
																$boekingsBedragO = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'O');
																${'rek_'.$rekening['rekeningId'].'_O'} = ${'rek_'.$rekening['rekeningId'].'_O'} + $boekingsBedragO['bedrag'];
																$boekingsBedragU = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'U');
																${'rek_'.$rekening['rekeningId'].'_U'} = ${'rek_'.$rekening['rekeningId'].'_U'} + $boekingsBedragU['bedrag'];

																if($boekingsBedragO['bedrag'] === '' or $boekingsBedragO['bedrag'] === '0.00') {
																	$oldValO = '';
																	} else {
																	$oldValO = $boekingsBedragO['bedrag'];
																	}

																if($boekingsBedragU['bedrag'] === '' or $boekingsBedragU['bedrag'] === '0.00') {
																	$oldValU = '';
																	} else {
																	$oldValU = $boekingsBedragU['bedrag'];
																	}
																?>
															<td class="editable-col txtCal table-left-line" col-calc="<?php echo $boekingsBedragO['rekeningId'] ?>" col-key="O" contenteditable="<?php echo $contentEditableO ?>" col-index="<?php echo $boekingsBedragO['boekingId'] ?>" oldval="<?php echo $oldValO ?>" style="width: <?php echo $columnWidth ?>%"><?php echo $oldValO ?></td>
															<td class="editable-col txtCal <?php echo $tdClass ?>" col-calc="<?php echo $boekingsBedragU['rekeningId'] ?>" col-key="U" contenteditable="<?php echo $contentEditableU ?>" col-index="<?php echo $boekingsBedragU['boekingId'] ?>" oldval="<?php echo $oldValU ?>" style="width: <?php echo $columnWidth ?>%"><?php echo $oldValU ?></td>
														<?php } ?>
													</tr>
												<?php } ?>
												<?php if($boekjaarOpen === true) { ?>
													<tr>
														<td class="text-center">
															<a href="#" title="Boeking toevoegen" data-toggle="modal" data-target="#addBoekingModal">
																<i class="fas fa-file fa-sm fa-fw mr-2 text-success"></i>
															</a>
														</td>
														<td></td>
														<td></td>
														<?php if($useNummering === 'X' ) { ?>
															<td></td>
														<?php } ?>
														<?php foreach ($rekeningen as $rekening) { ?>
															<td class="border-right-s"></td>
															<td></td>
														<?php } ?>
														</td>
														<td></td>
													</tr>
												<?php } ?>
											</tbody>
											<tfoot>
												<tr class="thead-dark">
													<th colspan="<?php echo $colSpanFooter ?>">TOTAAL</th>
													<?php foreach ($rekeningen as $rekening) { ?>
														<th id="totalValue<?php echo $rekening['rekeningId'] ?>O"><?php echo currencyConv(${'rek_'.$rekening['rekeningId'].'_O'}); ?></th>
														<th id="totalValue<?php echo $rekening['rekeningId'] ?>U"><?php echo currencyConv(${'rek_'.$rekening['rekeningId'].'_U'}); ?></th>
													<?php } ?>
												</tr>
												<tr class="thead-dark">
													<th colspan="<?php echo $colSpanFooter ?>">OVER TE DRAGEN</th>
													<?php foreach ($rekeningen as $rekening) {
														$overTeDragen = ${'rek_'.$rekening['rekeningId'].'_O'} - ${'rek_'.$rekening['rekeningId'].'_U'}; ?>
														<th id="totalValueOverdracht<?php echo $rekening['rekeningId'] ?>"><?php echo currencyConv($overTeDragen); ?></th>
														<th></th>
													<?php } ?>
												</tr>
											</tfoot>
										</table>
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
	
    <!-- Toevoegen Boeking Modal-->
    <div class="modal fade" id="addBoekingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-primary" id="exampleModalLabel">Toevoegen van een boeking</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
				<form class="user" action="<?php echo $prefix ?>bin/pages/addBoeking.php" method="post" role="form">
					<div class="modal-body">
						<div class="panel panel-default">
						    <div class="panel-heading">
							    <h5 class="panel-title text-gray-900">Boekinginformatie</h5>
							</div>
							<div class="panel-body">
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingDatum" class="control-label">Datum</label>
									</div>
									<div class="col-sm-2">
										<div class="input-group">
											<?php if($month === sprintf('%02d', $wateringMaand)) {
													$addDay = $day . '/' . $month;
												} else { 
													$addDay = '01/' . sprintf('%02d', $wateringMaand);
												}?>
											<input style="width: 100px;" type="text" name="boekingDatum" class="datepicker form-control form-control-user readonlyInput" data-provide="datepicker" value="<?php echo $addDay ?>" required readonly>
										</div>
									</div>
								</div>
								<?php if($useNummering === 'X' ) { ?>
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingDatum" class="control-label">Factuurnummer</label>
									</div>
									<div class="col-sm-2">
										<div class="input-group">
											<input style="width: 100px;" type="text" name="boekingNummering" class="form-control form-control-user" value="<?php echo $lastBoekingNr ?>" required>
										</div>
									</div>
								</div>
								<?php } else { ?>
									<input style="width: 100px;" type="hidden" name="boekingNummering" class="form-control form-control-user" value="<?php echo $lastBoekingNr ?>">
								<?php } ?>
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingReferentie" class="control-label">Post zoeken</label>
									</div>
									<div class="col-sm-8">
										<div class="dropdown hierarchy-select" id="postSearch">
											<button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="boekingPostZoeken" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
											<div class="dropdown-menu" aria-labelledby="boekingPostZoeken">
												<div class="hs-searchbox">
													<input type="text" id="inputBoekingReferentie" name="boekingReferentie" class="form-control" autocomplete="off">
												</div>
												<div class="hs-menu-inner">
													<a class="dropdown-item dropdown-action-add" data-value="|" href="#">&nbsp;</a>
													<?php foreach($hoofdPosten as $hoofdPost) { 
														$refValue = $hoofdPost['hoofdpostId']; 
														$description = '';
														if($hoofdPost['useKey'] === 'O') {
															$description = 'ONT - ' . $hoofdPost['referentie'];
															$descHoofdPost = 'ONT';
															}
														else {
															$description = 'UIT - '. $hoofdPost['referentie'];
															$descHoofdPost = 'UIT';
															}
														
														$description = $description . ' - ' . $hoofdPost['omschrijving']; 
													?>
														<a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>|" href="#"><span class="text-md font-weight-bold"><?php echo $description ?></span></a>
													<?php 
														$posten = getPosten($wateringData['wateringId'], $wateringJaar, $hoofdPost['hoofdpostId']);
														foreach($posten as $post) {
															$refValue = $hoofdPost['hoofdpostId'] . '|' . $post['postId']; 
															$description = $descHoofdPost . ' ' . $hoofdPost['referentie'] . ' ' . $post['referentie'] . ' - ' . $post['omschrijving'];
															if($post['actief'] === 'X' && $post['overdrachtPost'] !== 'X') { ?>
																<a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>" href="#"><span class="text-s">&nbsp;&nbsp;&nbsp;<?php echo $description ?></span></a>
															<?php }
															
															$subposten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);
															foreach($subposten as $subpost) {
																$refValue = $hoofdPost['hoofdpostId'] . '|' . $post['postId'] . '|' . $subpost['subpostId']; 
																$description = $descHoofdPost . ' ' . $hoofdPost['referentie'] . ' ' . $post['referentie']. $subpost['referentie'] . ' - ' . $subpost['omschrijving'];
																if($subpost['actief'] === 'X') { ?>
																	<a class="dropdown-item dropdown-action-add" data-value="<?php echo $refValue ?>" href="#"><span class="text-xs">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $description ?></span></a>
															<?php }
															}
														} 
													} ?>
												</div>
											</div>
										</div>		
									</div>
								</div>								
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingHoofdpost" class="control-label">Hoofdpost</label>
									</div>
									<div class="col-sm-8">
										<select id="inputBoekingHoofdpost" name="boekingHoofdpost" class="form-control form-select text-s" required>
											<option value=""></option>
											<?php foreach ($hoofdPosten as $hoofdPost) { ?>
												<option value="<?php echo $hoofdPost['hoofdpostId'] ?>">
													<?php if($hoofdPost['useKey'] === 'O') { ?>
														ONT - 
													<?php } else { ?>
														UIT - 
													<?php } ?>
													<?php echo $hoofdPost['referentie'] ?>. <?php echo $hoofdPost['omschrijving'] ?>
												</option>
											<?php } ?>
										</select>
									</div>
								</div>								
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingPost" class="control-label">Post</label>
									</div>
									<div class="col-sm-8">
										<select id="inputBoekingPost" name="boekingPost" class="form-control form-select text-s" required>
											<option value=""></option>
										</select>
									</div>
								</div>
								<div id="addSubpost" class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingSubpost" class="control-label">Subpost</label>
									</div>
									<div class="col-sm-8">
										<select id="inputBoekingSubpost" name="boekingSubpost" class="form-control form-select text-s" required>
											<option value=""></option>
										</select>
									</div>
								</div>
							</div>					
						</div>					
					</div>
					<div class="modal-footer">
						<input class="btn btn-primary btn-user btn-size" type="submit" value="Opslaan" id="boekingAddSubmit">
						<input class="btn btn-secondary btn-user btn-size" data-dismiss="modal" value="Annuleren" id="boekingAddCancel">
					</div>
				</form>
            </div>
        </div>
    </div>
	
    <!-- Wijzigen Boeking Modal-->
    <div class="modal fade" id="changeBoekingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-primary" id="exampleModalLabel">Wijzigen van een boeking</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
				<form class="user" action="<?php echo $prefix ?>bin/pages/changeBoeking.php" method="post" role="form">
					<div class="modal-body">
						<div class="panel panel-default">
						    <div class="panel-heading">
							    <h5 class="panel-title text-gray-900">Boekinginformatie</h5>
							</div>
							<div class="panel-body">
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingDatum" class="control-label">Datum</label>
									</div>
									<div class="col-sm-2">
										<div class="input-group">
											<input type="hidden" id="inputBoekingIdChange" name="boekingId">
											<input style="width: 100px;" type="text" id="inputBoekingDatumChange" name="boekingDatum" class="datepicker form-control form-control-user readonlyInput datepicker-radius" data-provide="datepicker" value="" required readonly>
										</div>
									</div>
								</div>
								<?php if($useNummering === 'X' ) { ?>
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingDatum" class="control-label">Factuurnummer</label>
									</div>
									<div class="col-sm-2">
										<div class="input-group">
											<input style="width: 100px;" type="text" id="inputBoekingNummeringChange" name="boekingNummering" class="form-control form-control-user" value="" required>
										</div>
									</div>
								</div>
								<?php } else { ?>
									<input style="width: 100px;" type="hidden" id="inputBoekingNummeringChange" name="boekingNummering" class="form-control form-control-user" value="">
								<?php } ?>								
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingReferentie" class="control-label">Post zoeken</label>
									</div>
									<div class="col-sm-8">
										<div class="dropdown hierarchy-select" id="postSearchChange">
											<button type="button" class="btn btn-primary btn-sm dropdown-toggle" id="boekingPostZoekenChange" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
											<div class="dropdown-menu" aria-labelledby="boekingPostZoekenChange">
												<div class="hs-searchbox">
													<input type="text" id="inputBoekingReferentieChange" name="boekingReferentie" class="form-control" autocomplete="off">
												</div>
												<div class="hs-menu-inner">
													<a class="dropdown-item dropdown-action-change" data-value="|" href="#">&nbsp;</a>
													<?php foreach($hoofdPosten as $hoofdPost) { 
														$refValue = $hoofdPost['hoofdpostId']; 
														$description = '';
														if($hoofdPost['useKey'] === 'O') {
															$description = 'ONT - ' . $hoofdPost['referentie'];
															$descHoofdPost = 'ONT';
															}
														else {
															$description = 'UIT - '. $hoofdPost['referentie'];
															$descHoofdPost = 'UIT';
															}
														
														$description = $description . ' - ' . $hoofdPost['omschrijving']; 
													?>
														<a class="dropdown-item dropdown-action-change" data-value="<?php echo $refValue ?>|" href="#"><span class="text-md font-weight-bold"><?php echo $description ?></span></a>
													<?php 
														$posten = getPosten($wateringData['wateringId'], $wateringJaar, $hoofdPost['hoofdpostId']);
														foreach($posten as $post) {
															$refValue = $hoofdPost['hoofdpostId'] . '|' . $post['postId']; 
															$description = $descHoofdPost . ' ' . $hoofdPost['referentie'] . ' ' . $post['referentie'] . ' - ' . $post['omschrijving'];
															if($post['actief'] === 'X' && $post['overdrachtPost'] !== 'X') { ?>
																<a class="dropdown-item dropdown-action-change" data-value="<?php echo $refValue ?>" href="#"><span class="text-s">&nbsp;&nbsp;&nbsp;<?php echo $description ?></span></a>
															<?php }
															
															$subposten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);
															foreach($subposten as $subpost) {
																$refValue = $hoofdPost['hoofdpostId'] . '|' . $post['postId'] . '|' . $subpost['subpostId']; 
																$description = $descHoofdPost . ' ' . $hoofdPost['referentie'] . ' ' . $post['referentie']. $subpost['referentie'] . ' - ' . $subpost['omschrijving'];
																if($subpost['actief'] === 'X') { ?>
																	<a class="dropdown-item dropdown-action-change" data-value="<?php echo $refValue ?>" href="#"><span class="text-xs">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $description ?></span></a>
															<?php }
															}
														} 
													} ?>
												</div>
											</div>
										</div>		
									</div>
								</div>								
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingHoofdpostChange" class="control-label">Hoofdpost</label>
									</div>
									<div class="col-sm-8">
										<select id="inputBoekingHoofdpostChange" name="boekingHoofdpost" class="form-control form-select text-s" required>
											<option value=""></option>
											<?php foreach ($hoofdPosten as $hoofdPost) { ?>
												<option value="<?php echo $hoofdPost['hoofdpostId'] ?>">
													<?php if($hoofdPost['useKey'] === 'O') { ?>
														ONT - 
													<?php } else { ?>
														UIT - 
													<?php } ?>
													<?php echo $hoofdPost['omschrijving'] ?>
												</option>
											<?php } ?>
										</select>
									</div>
								</div>								
								<div class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingPostChange" class="control-label">Post</label>
									</div>
									<div class="col-sm-8">
										<select id="inputBoekingPostChange" name="boekingPost" class="form-control form-select text-s" required>
											<option value=""></option>
										</select>
									</div>
								</div>
								<div id="addSubpostChange" class="form-group row">
									<div class="col-sm-4 mt-2">
										<label for="boekingSubpostChange" class="control-label">Subpost</label>
									</div>
									<div class="col-sm-8">
										<select id="inputBoekingSubpostChange" name="boekingSubpost" class="form-control form-select text-s" required>
											<option value=""></option>
										</select>
									</div>
								</div>
							</div>					
						</div>					
					</div>
					<div class="modal-footer">
						<input class="btn btn-primary btn-user btn-size" type="submit" value="Opslaan" id="boekingChangeSubmit">
						<input class="btn btn-secondary btn-user btn-size" data-dismiss="modal" value="Annuleren" id="boekingChangeCancel">
					</div>
				</form>
            </div>
        </div>
    </div>			

    <!-- Overdracht rekening Modal-->
    <div class="modal fade" id="rekeningOverdrachtModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-primary" id="exampleModalLabel">Beweging tussen twee rekeningen</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
				<form class="user" action="<?php echo $prefix ?>bin/pages/rekeningOverdracht.php" method="post" role="form">
					<div class="modal-body">
						<div class="panel panel-default">
						    <div class="panel-heading">
							    <h5 class="panel-title text-gray-900">Datum</h5>
							</div>
							<div class="panel-body">
								<div class="form-group row">
									<div class="col-sm-2">
										<div class="input-group">
											<?php if($month === sprintf('%02d', $wateringMaand)) {
													$addDay = $day . '/' . $month;
												} else { 
													$addDay = '01/' . sprintf('%02d', $wateringMaand);
												}?>
											<input style="width: 100px;" type="text" name="rekeningDatum" class="datepicker form-control form-control-user readonlyInput" data-provide="datepicker" value="<?php echo $addDay ?>" required readonly>
										</div>
									</div>
								</div>
							</div>
							
						    <div class="panel-heading">
							    <h5 class="panel-title text-gray-900">Rekening van -> naar</h5>
							</div>
							<div class="panel-body">
								<div id="container-rek">
									<svg id="svgConns" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"></svg>
								
									<div class="row-rek" id="row1">
										<input type="hidden" id="rekeningVanHidden" name="rekeningVanNr">
										<?php $rekeningVanCounter = 0;
											foreach($rekeningen as $rekening) {
												/* if($rekening['rekening'] !== 'KAS') { */
													$rekeningVanCounter = $rekeningVanCounter + 1;
													?>
												<button class="link-btn" id="rekeningVan<?php echo $rekeningVanCounter ?>" data-key="rek-<?php echo $rekeningVanCounter ?>" data-rek="<?php echo $rekening['rekeningId'] ?>"><?php echo $rekening['rekening'] ?><br><?php echo $rekening['omschrijving'] ?></button>
											<?php /* }  */
												} ?>
									</div>
								
									<div class="row-rek" id="row2">
										<input type="hidden" id="rekeningNaarHidden" name="rekeningNaarNr">
										<?php $rekeningNaarCounter = 0;
											foreach($rekeningen as $rekening) {
												/* if($rekening['rekening'] !== 'KAS') { */
													$rekeningNaarCounter = $rekeningNaarCounter + 1;
													?>
												<button class="link-btn" id="labelRekeningNaar<?php echo $rekeningVanCounter ?>" data-key="rek-<?php echo $rekeningNaarCounter ?>" data-rek="<?php echo $rekening['rekeningId'] ?>"><?php echo $rekening['rekening'] ?><br><?php echo $rekening['omschrijving'] ?></button>
												<?php /* } */
												} ?>
									</div>
								</div>
							</div>					
						    <div class="panel-heading">
							    <h5 class="panel-title text-gray-900">Bedrag</h5>
							</div>
							<div class="panel-body">
								<div class="form-group row">
									<div class="col-sm-4">
										<div class="input-group">
											<input type="text" id="rekeningNaarBedrag" name="rekeningNaarBedrag" class="form-control" autocomplete="off" required>
										</div>
									</div>
								</div>
							</div>					
						</div>					
					</div>
					<div class="modal-footer">
						<input class="btn btn-primary btn-user btn-size" type="submit" value="Opslaan" id="rekeningOverdrachtSubmit" disabled>
						<input class="btn btn-secondary btn-user btn-size" data-dismiss="modal" value="Annuleren" id="rekeningOverdrachtCancel">
					</div>
				</form>
            </div>
        </div>
    </div>		

    <!-- Boekjaar afsluiten -->
    <div class="modal fade" id="boekjaarAfsluiten" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-primary" id="exampleModalLabel">Boekjaar afsluiten</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
					<div class="modal-body">
						<div class="panel panel-default">
							<p class="text-bold">Ben je zeker dat je het huidige boekjaar wil afsluiten? Hiermee wordt er een nieuw boekjaar gestart.</p>
							<p>Volgende acties worden uitgevoerd:
								<ul>
									<li>Je kan geen wijzigingen meer aanbrengen aan je boekingen</li>
									<li>De actieve posten van dit jaar worden overgezet naar het nieuwe jaar</li>
									<li>De actieve rekeningen van dit jaar worden overgezet naar het nieuwe jaar</li>
									<li>De over te dragen bedragen worden aan de rekeningen gekoppeld</li>
								</ul>
							</p>
						</div>					
					</div>
					<div class="modal-footer">
						<input class="btn btn-secondary btn-user btn-size" data-dismiss="modal" value="Annuleren" id="boekjaarAfsluitenCancel">
						<input class="btn btn-danger btn-user btn-size" type="submit" value="Ok" id="boekjaarAfsluitenOk">
					</div>
            </div>
        </div>
    </div>	

    <!-- Boekjaar openen -->
    <div class="modal fade" id="boekjaarOpenen" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-primary" id="exampleModalLabel">Boekjaar openen</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
					<div class="modal-body">
						<div class="panel panel-default">
							<p class="text-bold">Je gaat dit boekjaar terug openen. Dit is enkel om nog kleine correcties uit te voeren. Vergeet achteraf niet het boekjaar terug af te sluiten zodat je volgende boekjaar correct gestart kan worden.</p>
						</div>					
					</div>
					<div class="modal-footer">
						<input class="btn btn-secondary btn-user btn-size" data-dismiss="modal" value="Annuleren" id="boekjaarOpenenCancel">
						<input class="btn btn-success btn-user btn-size" type="submit" value="Ok" id="boekjaarOpenenOk">
					</div>
            </div>
        </div>
    </div>	

	<?php include $prefix.'includes/scripts.php';?>

	<script>
// ---   Watering, jaar en maand dropdowns   ---
// ---------------------------------------------
		$("#selectWatering").change(function() {
			var wateringId = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeWatering.php",
				type: "post",
				data: { wateringId: wateringId }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});	

		$("#selectJaar").change(function() {
			var jaar = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeJaar.php",
				type: "post",
				data: { jaar: jaar }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});	

		$("#selectMaand").change(function() {
			var maand = $(this).val();
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
				type: "post",
				data: { maand: maand }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});	

		$('#verversBillit').click(function () {
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
			var maand = <?php echo date("m"); ?>;
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
				type: "post",
				data: { maand: maand }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});
		
		$("#buttonPreviousMaand").click(function(){
			var maand = <?php echo $wateringMaand ?>;
			maand = maand - 1;
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
				type: "post",
				data: { maand: maand }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});

		$("#buttonNextMaand").click(function(){
			var maand = <?php echo $wateringMaand ?>;
			maand = maand + 1;
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeMaand.php",
				type: "post",
				data: { maand: maand }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
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
				window.location.href = window.location.href;
				location.reload(true);
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

		function opslaan() {
			window.location.href = window.location.href;
			location.reload(true);
		}


// ---   Search Dropdowns   ---
// ----------------------------
	$(document).ready(function(){
		$("div#addSubpost").hide();
		$('#postSearch').hierarchySelect({
			hierarchy: false,
			width: '100%'
		   });
		});

	$(document).ready(function(){
		$("div#addSubpostChange").hide();
		$('#postSearchChange').hierarchySelect({
			hierarchy: false,
			width: '100%'
		   });
		});	


// ---   Modals leegmaken   ---
// ----------------------------
	$('#addBoekingModal').on('shown.bs.modal', function (e) {
		$('#inputBoekingHoofdpost').val("").change();
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
	function vulWijzigingsModal(boekId, datum, hoofdpostId, postId, subpostId, nummering) {
		$('#inputBoekingHoofdpost').val("").change();
		$('select[name="boekingPost"]').empty();
		$('select[name="boekingSubpost"]').empty();
		$("div#addSubpostChange").hide();
		$('#inputBoekingIdChange').val(boekId);
		document.getElementById("inputBoekingSubpostChange").required = false;
		
		$('#inputBoekingHoofdpostChange').val(hoofdpostId).change();
			document.getElementById('inputBoekingDatumChange').value = datum;
			const d = new Date(datum);
			$('#inputBoekingDatumChange').datepicker('update', d);
			
			document.getElementById('inputBoekingNummeringChange').value = nummering;
			
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

// ---   Editeerbare tabel boekingen   ---
// ---------------------------------------
	$(document).ready(function(){
	  $('td.editable-col').on('focusout', function() {
		if($(this).attr('col-calc') !== '999999' && $(this).text().trim() != '') {
			if (!isValid($(this).text().trim())) {
				$(this).addClass('cellError'); // Add error 
				return false;
			  } else {
				$(this).removeClass('cellError'); // Remove if it became valid
			  }
			}

		data = {};
		data['val'] = $(this).text();
		data['id'] = $(this).parent('tr').attr('data-row-id');
		data['index'] = $(this).attr('col-index');
		  if($(this).attr('oldVal') === data['val'])
		return false;
		
		var tdElem = $(this);
		var oldValue = data['val'];
		
		if(data['val'] === '')
			data['val'] = 0;
								
		$.ajax({
			  type: "POST",  
			  url: "bin/pages/changeBoekingTD.php",  
			  cache:false,  
			  data: data,
			  dataType: "json",       
			  success: function(response)  
			  {
				//$("#loading").hide();
				if(response.status) {
					tdElem.attr('oldVal', oldValue);
				  // $("#msg").removeClass('alert-danger');
				  // $("#msg").addClass('alert-success').html(response.msg);
				} else {
					console.log(response.msg);
				  // $("#msg").removeClass('alert-success');
				  // $("#msg").addClass('alert-danger').html(response.msg);
				}
			  }   
			});

		   var calcId = $(this).attr('col-calc');
		   var calcKey = $(this).attr('col-key');
		   var calculated_total_sum = 0;

// Bereken de totalen na een wijziging
		   $("#addBoekingenTable .txtCal").each(function () {
			   if($(this).attr('col-calc') == calcId && $(this).attr('col-key') == calcKey) {;
			   var get_textbox_value = $(this).text();
			   if ($.isNumeric(get_textbox_value)) {
				  calculated_total_sum += parseFloat(get_textbox_value);
				  } 
			    }                 
				});
				
				if($(this).attr('col-calc') !== '999999') {
					var myVar;
					myVar = 'totalValue' + calcId + calcKey;
					
					let USDollar = new Intl.NumberFormat('nl-BE', { style: 'currency', currency: 'EUR', });
					document.getElementById(myVar).innerHTML = USDollar.format(calculated_total_sum);
				}
		});
	});	



// ---   Rekeningen overdracht   ---
// ---------------------------------
// Linken van de Rekeningen
    let selectedFrom = null;
    let currentConnection = null; // {fromId, toId}

    const container = document.getElementById('container-rek');
    const svg = document.getElementById('svgConns');
    const row1Buttons = document.querySelectorAll('#row1 .link-btn');
    const row2Buttons = document.querySelectorAll('#row2 .link-btn');

    function centerRelative(el) {
      const elRect = el.getBoundingClientRect();
      const cRect = container.getBoundingClientRect();
      return {
        x: elRect.left - cRect.left + elRect.width / 2,
        y: elRect.top - cRect.top + elRect.height / 2
      };
    }

    function drawConnection(fromEl, toEl) {
      svg.innerHTML = ""; // only one line
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
		if(document.getElementById('rekeningVanHidden').value != '' && document.getElementById('rekeningNaarHidden').value != '')
			$("#rekeningOverdrachtSubmit").attr("disabled", false);
		else
			$("#rekeningOverdrachtSubmit").attr("disabled", 'disabled');
	}
	

	$('#rekeningOverdrachtModal').on('show.bs.modal', function (e) {
		clearConnection();
	});

    // Row1 click → clear old line & select new
    row1Buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('rekeningVanHidden').value = '';
        document.getElementById('rekeningNaarHidden').value = '';
		clearConnection(); // remove previous line immediately
        selectedFrom = btn;
        btn.classList.add('selected');
		document.getElementById('rekeningVanHidden').value = btn.dataset.rek;
        showAvailableTargets(btn);
		checkButtonSubmit();
      });
    });

    // Row2 click → link
    row2Buttons.forEach(btn => {
      btn.addEventListener('click', () => {
		document.getElementById('rekeningNaarHidden').value = '';
        if (!selectedFrom) return;
        if (btn.disabled) return;

        // clear visual highlights only, keep selectedFrom for drawing
        svg.innerHTML = "";
        document.querySelectorAll('.link-btn').forEach(b => {
          if (!b.classList.contains('selected')) {
            b.classList.remove('available');
            b.disabled = false;
          }
        });

        currentConnection = { fromId: selectedFrom.id, toId: btn.id };
        selectedFrom.classList.remove('selected');
        selectedFrom.classList.add('linked');
        btn.classList.add('linked');
		document.getElementById('rekeningNaarHidden').value = btn.dataset.rek;
		checkButtonSubmit();
		
        drawConnection(selectedFrom, btn);
        selectedFrom = null;
      });
    });

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