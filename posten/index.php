<?php
$prefix = '../';
$activeDagboek = '';
$activePost = 'active';
$activeRekening = '';

include $prefix.'/bin/init.php';
$pageTitle = 'Beheer van posten';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

$hoofdPostenOpbrengsten = getHoofdPostenSub('GO', $wateringJaar);
$titelGO = getHoofdPostType('GO', $wateringJaar);
$hoofdPostenBO = getHoofdPostenSub('BO', $wateringJaar);
$titelBO = getHoofdPostType('BO', $wateringJaar);
$hoofdPostenUitgaven = getHoofdPostenSub('GU', $wateringJaar);
$titelGU = getHoofdPostType('GU', $wateringJaar);
$hoofdPostenBU = getHoofdPostenSub('BU', $wateringJaar);
$titelBU = getHoofdPostType('BU', $wateringJaar);

//$showOnlyActive = isset($_SESSION['showOnlyActive']) ? $_SESSION['showOnlyActive'] : false;
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
					<?php if ($boekjaarOpen === false) { ?>
						<div class="d-sm-flex align-items-center justify-content-center mb-1">
							<div class="alert alert-warning text-center font-weight-bold py-2 px-3">
								<i class="fas fa-lock mr-2"></i>
								Dit boekjaar is afgesloten. Aanpassingen zijn niet meer mogelijk.
								<i class="fas fa-lock ml-2"></i>
							</div>
						</div>
					<?php } ?>

                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3 p-2 bg-light border rounded">
									  <label for="showOnlyActive" class="mb-0 mr-3 font-weight-semibold text-dark text-bold">
										Toon enkel actieve posten
									  </label>

									  <div class="custom-control custom-switch">
										<input type="checkbox" class="custom-control-input" id="showOnlyActive">
										<label class="custom-control-label" for="showOnlyActive"></label>
									  </div>
									</div>

									
<!------------------------ GEWONE ------------------------>	
									<div class="row mt-3">
										<div class="col-md-6">
											<?php $totaalRamingO = 0;
												  $totaalBedragO = 0;
												  $totaalRamingU = 0;
												  $totaalBedragU = 0;
												  $titleHeader = $titelGO;
												  $hoofdPosten = $hoofdPostenOpbrengsten; 
												  $type = 'GO';
											?>
											<?php include $prefix.'includes/posten.php';?>
										</div>
										
										<div class="col-md-6">
											<?php $titleHeader = $titelGU;
												  $hoofdPosten = $hoofdPostenUitgaven; 
												  $type = 'GU';
											?>
											<?php include $prefix.'includes/posten.php';?>
										</div>									
									</div>


<!------------------------ BUITENGEWONE ------------------------>
								<?php if ($hoofdPostenBO && $hoofdPostenBO->num_rows > 0) { ?>
									<div class="row">
										<div class="col-md-6 mt-5">
											<?php $titleHeader = $titelBO;
												  $hoofdPosten = $hoofdPostenBO; 
												  $type = 'BO';
											?>
											<?php include $prefix.'includes/posten.php';?>
										</div>

										<div class="col-md-6 mt-5">
											<?php $titleHeader = $titelBU;
												  $hoofdPosten = $hoofdPostenBU; 
												  $type = 'BU';
											?>
											<?php include $prefix.'includes/posten.php';?>
										</div>									
									</div>
								<?php } ?>

<!------------------------ SALDO ------------------------>																	
									<div class="row">
										<div class="col-md-6">
											<div class="row">
												<div class="col-md-12 border-top-m border-size-s"></div>
												<div class="col-md-12 mb-4 mt-3">
													<table class="ml-2 text-black" style="width: 100%" >
														<tr>
															<td width="70%" class="text-bold"><h5 class="ml-2 text-black">TOTAAL</h5></td>
															<td width="15%" class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalRamingO); ?></span></td>
															<td width="15%" class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalBedragO); ?></span></td>
														</tr>
													</table>
												</div>	
											</div>											
										</div>
										<div class="col-md-6">
											<div class="row">
												<div class="col-md-12 border-top-m border-size-s"></div>
												<div class="col-md-12 mb-4 mt-3">
													<table class="ml-2 text-black" style="width: 100%" >
														<tr>
															<td width="70%" class="text-bold"><h5 class="ml-2 text-black">TOTAAL</h5></td>
															<td width="15%" class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalRamingU); ?></span></td>
															<td width="15%" class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalBedragU); ?></span></td>
														</tr>
													</table>
												</div>	
											</div>											
										</div>
									</div>
																		
									<div class="row">
										<div class="col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
											<div class="card mb-1 mt-4">
												<div class="card-body">
													<div class="row">
														<div class="col-md-12">
															<div class="row">
																<div class="col-md-12 mb-4 mt-3">
																	<?php $ramingReserve = getReserve($wateringData['wateringId'], $wateringJaar); ?>
																	<h5 class="ml-2 text-black">SALDO</h5>

																	<table class="ml-2 text-black" style="width: 100%" >
																		<thead>
																			<th width="50%"></th>
																			<th width="25%" class="text-right"><span class="mr-2">Begroting</span></th>
																			<th width="25%" class="text-right"><span class="mr-2">Bedrag</span></th>
																		</thead>
																		<tbody>
																			<tr>
																				<td class="font-weight-bold"><span class="">Totaal ontvangsten</span></td>
																				<td class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalRamingO); ?></span></td>
																				<td class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalBedragO); ?></span></td>
																			</tr>
																			<tr>
																				<td class="font-weight-bold"><span class="">Totaal uitgaven</span></td>
																				<td class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalRamingU); ?></span></td>
																				<td class="text-right text-s"><span class="mr-2"><?php echo currencyConv($totaalBedragU); ?></span></td>
																			</tr>
																			<tr>
																				<td class="font-weight-bold"></td>
																				<?php if(round($totaalRamingO - $totaalRamingU) >= 0.00) {
																					$bedragStyle = 'text-success';
																					} else {
																					$bedragStyle = 'text-danger text-bold';
																					} ?>																	
																				
																				<td class="font-weight-bold text-right text-s border-top-m border-size-s"><span class="<?php echo $bedragStyle ?> mr-2"><?php echo currencyConv($totaalRamingO - $totaalRamingU); ?></span></td>

																				<?php if(round($totaalBedragO - $totaalBedragU) >= 0.00) {
																					$bedragStyle = 'text-success';
																					} else {
																					$bedragStyle = 'text-danger text-bold';
																					} ?>																	
																				<td class="font-weight-bold text-right text-s border-top-m border-size-s"><span class="<?php echo $bedragStyle ?> mr-2"><?php echo currencyConv($totaalBedragO - $totaalBedragU); ?></span></td>
																			</tr>
																			<tr>
																				<td colspan="3">&nbsp;</td>
																			</tr>
																			<?php //if ($reserveExist === true) { ?>
																			<tr>
																				<td class="font-weight-bold"><span class="">Reservefonds dd - 1 januari</span></td>
																				<td class="text-right text-s"><span class="mr-2"></span></td>
																				<td class="text-right text-s">
																					<div class="ml-3 childButtons">
																						<a href="#" title="Reserve wijzigen" data-toggle="modal" data-target="#reserveModal">
																							<i class="fas fa-pencil-alt fa-sm fa-fw mr-2 text-warning"></i>
																						</a>
																					</div>																					
																					<span class="mr-2 font-weight-bold"><?php echo currencyConv($ramingReserve); ?></span>
																				</td>
																			</tr>
																			<tr>
																				<td class="font-weight-bold"><span class="">Reservefonds dd - 31 december</span></td>
																				<td class="text-right text-s"><span class="mr-2"></span></td>
																				<td class="text-right text-s font-weight-bold"><span class="mr-2"><?php echo currencyConv($ramingReserve + $totaalBedragO - $totaalBedragU); ?></span></td>
																			</tr>
																			<?php //} ?>
																		</tbody>
																	</table>
																</div>	
															</div>	
														</div>
													</div>
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

			<?php include $prefix.'includes/footer.php';?>       
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

	<!-- Reserve modal -->
	<div class="modal fade" id="reserveModal" tabindex="-1" role="dialog" aria-labelledby="reserveLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm modal-dialog-centered" role="document">
			<div class="modal-content shadow-lg">

				<!-- Header -->
				<div class="modal-header bg-light border-bottom">
					<h4 class="modal-title text-primary font-weight-bold" id="reserveLabel">
						Wijzig reserve
					</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<form class="user" action="../bin/pages/changeReserve.php" method="post" role="form">
					<div class="modal-body">

						<!-- Reserve Bedrag -->
						<div class="card shadow-sm">
							<div class="card-body">
								<div class="form-group row mb-2">
									<label for="inputReserve" class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
										Reserve
									</label>
									<div class="col-sm-8">
										<input type="text" name="reserveBedrag" value="<?php echo $ramingReserve ?>" class="form-control form-control-sm" id="inputReserve" pattern="^(\d+)?(\.\d{1,2})?$" title="Voer een geldig bedrag in, bijvoorbeeld 123, 123.4, 123.45 of .45" required>
									</div>
								</div>
							</div>
						</div>

					</div>

					<!-- Footer -->
					<div class="modal-footer bg-light">
						<button type="submit" class="btn btn-primary btn-user btn-size" id="reserveSubmit">Opslaan</button>
						<button type="button" class="btn btn-secondary btn-user btn-size" data-dismiss="modal" id="reserveCancel">Annuleren</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<!-- Posten Wijzig Modal -->
	<div class="modal fade" id="postenModal" tabindex="-1" role="dialog" aria-labelledby="postenModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered" role="document">
			<div class="modal-content shadow-lg">
				<div class="modal-header bg-light border-bottom">
					<div class="h4 font-weight-bold mr-3" style="width: 50px;" id="modalHoofdPostRef">
						<!-- Referentie wordt hier ingevuld -->
					</div>
					<div class="h4 flex-grow-1 mr-3" id="modalHoofdPostOmschrijving">
						<!-- Omschrijving wordt hier ingevuld -->
					</div>
					<!-- Post toevoegen button for hoofdpostAndere -->
					<div id="addPostButtonContainer" style="display: none;">
						<button class="btn btn-primary btn-sm" id="addPostButton">
							<i class="fas fa-plus-circle fa-sm fa-fw mr-1"></i>Post toevoegen
						</button>
					</div>

					<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<div class="modal-body">
					<!-- Header for hoofdpostAndere -->
					<div id="hoofdpostAndereHeader" class="p-3 bg-light rounded border mb-2" style="display: none;">
						<div class="d-flex align-items-center mb-2">
							<i class="fas fa-info-circle text-primary mr-2"></i>
							<h6 class="font-weight-bold mb-0 text-dark">Wijzigen van posten</h6>
						</div>
						<ul class="pl-3 mb-0 small text-muted ml-3">
							<li>Wanneer je over een post of subpost gaat, worden de <strong>velden die je kan aanpassen groen gemarkeerd</strong>. Bij de hoofdpost 'Andere' kan je zowel posten als subposten toevoegen.</li>
							<li>Bij <strong>posten</strong> en <strong>subposten</strong> kan je de <strong>referentie</strong>, <strong>omschrijving</strong> en het <strong>begrotingscijfer</strong> wijzigen.</li>
							<li><strong>Posten</strong> en <strong>Subposten</strong> kan je <strong>verwijderen</strong> zolang er nog <strong>geen boekingen</strong> op gebeurd zijn.</li>
							<li>Je kan <strong>nieuwe posten</strong> toevoegen door bovenaan op '<strong>Post toevoegen</strong>' te klikken.</li>
							<li>Je kan <strong>nieuwe subposten</strong> toevoegen door op <strong><i class="fas fa-plus-circle fa-sm fa-fw mr-1"></i></strong>te klikken naast de betreffende post.</li>
						</ul>
					</div>

					<!-- Header for normal posten -->
					<div id="normalPostHeader" class="p-3 bg-light rounded border mb-2" style="display: none;">
						<div class="d-flex align-items-center mb-2">
							<i class="fas fa-info-circle text-primary mr-2"></i>
							<h6 class="font-weight-bold mb-0 text-dark">Wijzigen van posten</h6>
						</div>
						<ul class="pl-3 mb-0 small text-muted ml-3">
							<li>Wanneer je over een post of subpost gaat, worden de <strong>velden die je kan aanpassen groen gemarkeerd</strong>.</li>
							<ul>
								<li>Bij <strong>posten</strong> kan je enkel het <strong>begrotingscijfer</strong> wijzigen (indien er geen subposten zijn).</li>
								<li>Bij <strong>subposten</strong> kan je de <strong>referentie</strong>, <strong>omschrijving</strong> en het <strong>begrotingscijfer</strong> wijzigen.</li>
							</ul>
							<li><strong>Subposten</strong> kan je <strong>verwijderen</strong> zolang er nog <strong>geen boekingen</strong> op gebeurd zijn.</li>
							<li>Je kan <strong>nieuwe subposten</strong> toevoegen door op <strong><i class="fas fa-plus-circle fa-sm fa-fw mr-1"></i></strong>te klikken naast de betreffende post.</li>
						</ul>
					</div>

					<!-- AG Grid Container -->
					<div id="postenGrid" class="ag-theme-alpine" style="width: 100%; height: 500px;"></div>

					<!-- Buttons onder de grid -->
					<div class="mt-3 d-flex justify-content-end">
						<button class="btn btn-primary btn-user btn-size" id="savePostenModal">
							Opslaan
						</button>
						<button class="btn btn-secondary btn-user btn-size ml-2" data-dismiss="modal" id="cancelPostenModal">
							Annuleren
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Toevoegen Post/Subpost Modal -->
	<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content shadow-lg">
				<div class="modal-header bg-light border-bottom">
					<h4 class="modal-title text-primary font-weight-bold" id="postModalLabel">
						Subpost toevoegen
					</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				
				<form id="addPostForm" action="../bin/pages/addPost.php" method="post">
					<div class="modal-body">
						<input type="hidden" name="hoofdPostId" id="inputHoofdpostId">

						<!-- Kies type post -->
						<div id="typePost" class="form-group row mb-2">
							<label for="inputTypePost" class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
								Type post
							</label>
							<div class="col-sm-3">
								<select id="inputTypePost" name="postType" class="form-control form-control-sm">
									<option value="P">Post</option>
									<option value="S">Subpost</option>
								</select>
							</div>
						</div>

						<!-- Parent post dropdown (alleen voor subposts) -->
						<div id="addPostSelect" class="form-group row mb-2">
							<label for="inputPost" class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
								Post
							</label>
							<div class="col-sm-9">
								<select id="inputPost" name="postId" class="form-control form-control-sm" disabled></select>
							</div>
						</div>

						<!-- Omschrijving -->
						<div class="form-group row mb-2">
							<label for="inputOmschrijving" class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
								Omschrijving
							</label>
							<div class="col-sm-9">
								<input type="text" name="postOmschrijving" id="inputOmschrijving" class="form-control form-control-sm" required>
							</div>
						</div>
					</div>
					
					<div class="modal-footer bg-light">
						<button type="submit" class="btn btn-primary">Opslaan</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<?php include $prefix.'includes/modals.php';?>

	<?php include $prefix.'includes/scripts.php';?>
	
	<?php include $prefix.'includes/scriptsVariables.php';?>
	<?php include $prefix.'includes/scriptsGeneral.php';?>

	<!-- AG Grid -->
	<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

	<script type="text/javascript">
	let currentHoofdpostId = null;
	let currentReferentie = null;
	let currentOmschrijving = null;

	function updateInactiveRows(checked) {
		$('table').each(function () {
			const $table = $(this);
			const $inactiveRows = $table.find('tr.text-inactive');
			const $tbodyRows = $table.find('tbody tr');

			if (checked) {
				// Verberg inactieve rijen
				$inactiveRows.hide();
			} else {
				// Toon alles
				$inactiveRows.show();
				$table.find('thead, tfoot').show();
				return;
			}

			// Check of er nog zichtbare rijen zijn in tbody
			const visibleRows = $tbodyRows.filter(':visible').length;

			if (visibleRows === 0) {
				$table.find('thead, tfoot').hide();
			} else {
				$table.find('thead, tfoot').show();
			}
		});
	}

	// Posten Modal functionality
	let postenGridApi;

	// Column definitions for the modal grid
	const postenColumnDefs = [
		{
			headerName: "Acties",
			width: 60,
			cellRenderer: params => {
				const d = params.data;
				if (!d) return "";

				const container = document.createElement("div");
				container.style.display = "flex";
				container.style.alignItems = "center";
				container.style.height = "100%";

				// ➕ Subpost toevoegen (alleen bij actieve posten)
				if (d.type === "post" && d.actief) {
					const addIcon = document.createElement("i");
					addIcon.className = "fa fa-plus text-success";
					addIcon.style.cursor = "pointer";
					addIcon.title = "Subpost toevoegen";

					addIcon.addEventListener("click", () => {
						openSubPostModal(d);
					});

					container.appendChild(addIcon);
				}

				// 🗑 Delete knop
				if (d.verwijderbaar === true) {
					const type = d.type === "post" ? "P" : "S";
					const id = d.type === "post" ? d.postId : d.subpostId;

					const deleteIcon = document.createElement("i");
					deleteIcon.className = "fa fa-trash text-danger";
					deleteIcon.style.cursor = "pointer";
					deleteIcon.style.marginLeft = "13px";

					deleteIcon.addEventListener("click", () => {
						verwijderenPost(type, id, params.node);
					});

					container.appendChild(deleteIcon);
				}

				return container;
			}
		},
		{
			field: "referentie",
			headerName: "Referentie",
			width: 70,
			cellClass: params => params.data.type === "subpost" ? "subpost-indent" : "",
			editable: params => params.data.actief && (params.data.type !== "post" || hoofdpostAndere),
			cellClassRules: {
				"editable-highlight": params => params.data.actief && (params.data.type !== "post" || hoofdpostAndere)
			}
		},
		{
			field: "omschrijving",
			headerName: "Omschrijving",
			flex: 1,
			cellClass: params => params.data.type === "subpost" ? "subpost-indent" : "",
			editable: params => params.data.actief && (params.data.type !== "post" || hoofdpostAndere),
			cellClassRules: {
				"editable-highlight": params => params.data.actief && (params.data.type !== "post" || hoofdpostAndere)
			}
		},
		{
			field: "raming",
			headerName: "Begroting",
			width: 100,
			editable: params => params.data.actief && (params.data.type !== "post" || !params.data.hasSub),
			cellClassRules: {
				"editable-highlight": params => params.data.actief && (params.data.type !== "post" || !params.data.hasSub)
			},
			valueGetter: params => {
				// If it's a post with subposten, show empty string instead of 0.00
				if (params.data.type === "post" && params.data.hasSub) {
					return "";
				}
				return params.data.raming;
			}
		},
		{
			field: "actief",
			headerName: "Actief",
			width: 55,
			cellRenderer: params => {
				const id = `switch_${params.node.id}`;
				const checked = params.value ? "checked" : "";

				setTimeout(() => {
					const el = document.getElementById(id);
					if (!el) return;

					el.onchange = function() {
						const value = this.checked;
						params.data.actief = value;

						if (params.data.type === "post") {
							postenGridApi.forEachNode(node => {
								if (node.data.type === "subpost" && node.data.postId === params.data.postId) {
									node.data.actief = value;
									node.setData(node.data);
								}
							});
						}

						if (params.data.type === "subpost" && value) {
							postenGridApi.forEachNode(node => {
								if (node.data.type === "post" && node.data.postId === params.data.postId) {
									node.data.actief = true;
									node.setData(node.data);
								}
							});
						}

						params.node.setData(params.data);
					};
				});

				return `
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="${id}" ${checked}>
						<label class="custom-control-label" for="${id}"></label>
					</div>
				`;
			}
		}
	];

	// Grid options for the modal
	const postenGridOptions = {
		rowHeight: 28,
		domLayout: 'normal',
		treeData: true,
		singleClickEdit: true,
		stopEditingWhenGridLosesFocus: true,
		getDataPath: function(data) {
			if (data.type === "post") {
				return [data.id];
			} else {
				return [data.parent, data.id];
			}
		},
		getRowNodeId: function(data) {
			return data.id;
		},
		defaultColDef: { 
			filter: "agTextColumnFilter", 
			cellStyle: { color: '#858796' }, 
			suppressMovable: true,
    		lockPosition: true,
			resizable: false, 
			sortable: false,
			singleClickEdit: true,
			stopEditingWhenGridLosesFocus: true,
			suppressMovableColumns: true,
			suppressDragLeaveHidesColumns: true,
			filter: false
		},
		columnDefs: postenColumnDefs,
		rowClassRules: {
			"post-row": params => params.data.type === "post",
			"subpost-row": params => params.data.type === "subpost",
			"text-900": params => params.data.type === "post" && params.data.actief,
			"text-gray-900": params => params.data.type === "subpost" && params.data.actief,
			"text-gray-400": params => !params.data.actief
		}
	};

	// Create grid for modal
	postenGridApi = agGrid.createGrid(document.querySelector("#postenGrid"), postenGridOptions);

	// Open modal and load data
	$(document).on('click', '.btn-wijzig-posten', function(e) {
		e.preventDefault();

		currentHoofdpostId = $(this).data('hoofdpostid');
		currentReferentie = $(this).data('referentie');
		currentOmschrijving = $(this).data('omschrijving');

		$('#modalHoofdPostRef').text(currentReferentie);
		$('#modalHoofdPostOmschrijving').text(currentOmschrijving);

		postenGridApi.setGridOption("rowData", []);
		window.hoofdpostAndere = undefined;

		$('#postenModal').modal('show');
	});

	$('#postenModal').on('shown.bs.modal', function() {

		if(!currentHoofdpostId) return;
		postenGridApi.showLoadingOverlay();

		fetch("../bin/selects/getHoofdPostData.php?hoofdpostId=" + currentHoofdpostId)
			.then(res => res.json())
			.then(hoofdpostData => {
				window.hoofdpostAndere = hoofdpostData.andere === '1';

				// Show/hide the appropriate header based on hoofdpostAndere
				if (window.hoofdpostAndere) {
					$('#hoofdpostAndereHeader').show();
					$('#normalPostHeader').hide();
					$('#addPostButtonContainer').show();
				} else {
					$('#hoofdpostAndereHeader').hide();
					$('#normalPostHeader').show();
					$('#addPostButtonContainer').hide();
				}

				return fetch("../bin/selects/agGetPosten.php?hoofdpostId=" + currentHoofdpostId);
			})
			.then(res => res.json())
			.then(data => {

				data.forEach(r => r.path = r.parent ? [r.parent, r.id] : [r.id]);

				postenGridApi.setGridOption("rowData", data);

			});
	});

	// Save button in modal
	$("#savePostenModal").click(() => {
		// Stop editing and commit all changes
		postenGridApi.stopEditing();
		
		const rows = [];
		postenGridApi.forEachNode(node => rows.push(node.data));
		$.post('../bin/pages/changePosten.php', { rows: JSON.stringify(rows) }, () => {
			location.reload();
		});
	});

	// Delete function for modal
	function verwijderenPost(type, id, node) {
		$.post('../bin/pages/deletePost.php', { type, postId: id }, () => {
			postenGridApi.applyTransaction({
				remove: [node.data]
			});
		});
	}

	// Open subpost modal function
	function openSubPostModal(postData) {
		$("#postModal").modal("show");

		// Subpost type forceren
		$("#inputTypePost").val("S");
		$("#typePost").hide();

		// Parent post automatisch selecteren
		$("#inputPost").empty().append(
			`<option value="${postData.postId}" selected>${postData.referentie} - ${postData.omschrijving}</option>`
		);

		$("#addPostSelect").show();
	}

	// Open post modal function
	function openPostModal(postData) {
		$("#postModal").modal("show");

		// Post type forceren
		$("#inputTypePost").val("P");
		$("#typePost").hide();

		// Geen parent post nodig voor posten
		$("#addPostSelect").hide();
		$("#inputPost").empty();
	}

	// Handle post/subpost form submission
	$('#addPostForm').on('submit', function(e) {
		e.preventDefault();
		const formData = $(this).serialize();
		
		$.post($(this).attr('action'), formData, function(newRow) {
			// Clear the grid and reload all data to ensure consistency
			postenGridApi.showLoadingOverlay();
			
			// Reload the entire grid data
			fetch("../bin/selects/agGetPosten.php?hoofdpostId=" + currentHoofdpostId)
				.then(res => res.json())
				.then(data => {
					data.forEach(r => r.path = r.parent ? [r.parent, r.id] : [r.id]);
					postenGridApi.setGridOption("rowData", data);
				});
			
			// Clear form and close modal
			$('#addPostForm')[0].reset();
			$('#postModal').modal('hide');
		});
	});

	// Handle type select change (Post/Subpost)
	$("select[name='postType']").change(function () {
		var postType = $(this).val();
		var hoofdpostId = $("#inputHoofdpostId").val();

		if(postType === 'S') {
			$("#addPostSelect").show();
			$.getJSON("../bin/selects/getPosten.php", { id: hoofdpostId }, function(data) {
				$("#inputPost").empty();
				$.each(data, function(key, value) {
					$("#inputPost").append('<option value="'+ key +'">'+ value +'</option>');
				});
			});
		} else {
			$("#addPostSelect").hide();
			$("#inputPost").empty();
		}
	});

	// Handle "Post toevoegen" button click
	$(document).on('click', '#addPostButton', function(e) {
		e.preventDefault();
		// Set the hoofdpostId in the hidden input field
		$("#inputHoofdpostId").val(currentHoofdpostId);
		openPostModal(null);
	});

	// Modal close handler - clear immediately when closing starts
	$('#postenModal').on('hide.bs.modal', function () {
		// Clear grid data immediately when modal starts closing
		postenGridApi.setGridOption("rowData", []);
		// Also clear the hoofdpostAndere variable
		window.hoofdpostAndere = undefined;
	});

	$(document).ready(function() {

		// Lees voorkeur uit localStorage
		let showOnlyActive = localStorage.getItem('showOnlyActive');

		// Default = false als nog niet gezet
		showOnlyActive = (showOnlyActive === '1');

		// Zet checkbox
		$('#showOnlyActive').prop('checked', showOnlyActive);

		// Pas tabel aan bij load
		updateInactiveRows(showOnlyActive);

		// Toggle handler
		$('#showOnlyActive').change(function() {
			let checked = $(this).prop('checked');

			// UI aanpassen
			updateInactiveRows(checked);

			// Opslaan in localStorage
			localStorage.setItem('showOnlyActive', checked ? '1' : '0');
		});
	});
	</script>
</body>
</html>