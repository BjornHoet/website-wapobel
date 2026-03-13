<?php
$prefix = '../';
$activeDagboek = '';
$activePost = '';
$activeRekening = 'active';

include $prefix.'/bin/init.php';
$pageTitle = 'Beheer van rekeningen';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

writeRekeningen($wateringData['wateringId'], $wateringJaar);
writeRekeningenInactief($wateringData['wateringId'], $wateringJaar);
$dataFile = $wateringData['wateringId'] . '_rekeningen.json';
$dataFileInactief = $wateringData['wateringId'] . '_rekeningenInactief.json';
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
									<div class="p-3 mb-4 bg-light rounded border">
										<div class="d-flex align-items-center mb-2">
											<i class="fas fa-info-circle text-primary mr-2"></i>
											<h6 class="font-weight-bold mb-0 text-dark">Beheer van je rekeningen</h6>
										</div>
										<ul class="pl-3 mb-0 small text-muted ml-3">
											<li>Klik op een rekening om deze te <strong>wijzigen</strong>, het <strong>beginbedrag</strong> van dit boekjaar aan te passen of de rekening <strong>(in)actief</strong> te zetten.</li>
											<li><strong>Versleep rekeningen</strong> om hun volgorde aan te passen in je dagboek en de rapporten.</li>
											<li>Klik op de groene <strong>+ knop</strong> om een <strong>nieuwe rekening</strong> toe te voegen.</li>
										</ul>
									</div>

									<!--
									<?php if ($boekjaarOpen === true) { ?>
										<div class="d-flex justify-content-begin mb-4">
											<button type="button"
													class="btn btn-success"
													data-toggle="modal"
													data-target="#addRekeningModal">
												<i class="fas fa-plus mr-1"></i> Voeg rekening toe
											</button>
										</div>
									<?php } ?> -->

									<div class="row">
										<div class="col-md-6">
											<h5 class="text-bold text-black mb-3 mt-3">Actieve rekeningen</h5>
											<div id="myGridRekening" style="height: 500px; width: 100%"></div>
										</div>
										<div class="col-md-6">
											<h5 class="text-bold text-black mb-3 mt-3">Inactieve rekeningen</h5>
											<div id="myGridInactief" style="height: 500px; width: 100%"></div>
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

	<!-- Toevoegen Rekening Modal -->
	<div class="modal fade" id="addRekeningModal" tabindex="-1" role="dialog" aria-labelledby="addRekeningLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="addRekeningLabel">
			  Toevoegen van een rekening
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <form class="user" action="<?php echo $prefix ?>bin/pages/addRekening.php" method="post" role="form">
			<div class="modal-body">

			  <!-- Rekeninginformatie -->
			  <div class="card">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Rekeninginformatie</h6>
				</div>
				<div class="card-body">

				  <!-- Rekeningnummer -->
				  <div class="form-group row mb-2">
					<label for="inputRekeningNr" class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
					  Rekeningnummer
					</label>
					<div class="col-sm-8">
					  <input type="text" name="rekeningNr" class="form-control form-control-sm" id="inputRekeningNr" value="BE" pattern="^BE\d{2}(?:\s?\d{4}){3}$" title="Voer een geldig rekeningnummer in, zoals: BE00 0000 0000 0000" oninput="formatIban(this)" required>
					</div>
				  </div>

				  <!-- Omschrijving -->
				  <div class="form-group row mb-2">
					<label for="inputRekeningOmschrijving" class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
					  Omschrijving
					</label>
					<div class="col-sm-8">
					  <input type="text" name="rekeningOmschrijving" class="form-control form-control-sm" id="inputRekeningOmschrijving" required>
					</div>
				  </div>

				  <!-- Overdracht vorig jaar -->
				  <div class="form-group row mb-2">
					<label for="inputRekeningOverdracht" class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
					  Overdracht vorig jaar
					</label>
					<div class="col-sm-8">
					  <input type="text" name="rekeningOverdracht" class="form-control form-control-sm" id="inputRekeningOverdracht" pattern="^-?(\d+)?(\.\d{1,2})?$" title="Voer een geldig bedrag in, bijvoorbeeld 123, 123.4, 123.45 of .45" required>
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

	<!-- Wijzigen Rekening Modal -->
	<div class="modal fade" id="changeRekeningenModal" tabindex="-1" role="dialog" aria-labelledby="changeRekeningLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="changeRekeningLabel">
			  Wijzigen van een rekening
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <form id="formPatchRekening" role="form">
			<div class="modal-body">

			  <!-- Rekeninginformatie -->
			  <div class="card">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Rekeninginformatie</h6>
				</div>
				<div class="card-body">

				  <!-- Rekeningnummer -->
				  <div class="form-group row mb-2">
					<label for="changeRekeningNr" class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
					  Rekeningnummer
					</label>
					<div class="col-sm-8">
					  <input type="hidden" name="rekeningId" id="changeRekeningId">
					  <input type="text" name="rekeningNr" class="form-control form-control-sm" id="changeRekeningNr" required title="Voer een geldig rekeningnummer in, zoals: BE00 0000 0000 0000" oninput="formatIban(this)">
					</div>
				  </div>

				  <!-- Omschrijving -->
				  <div class="form-group row mb-2">
					<label for="changeRekeningOmschrijving" class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
					  Omschrijving
					</label>
					<div class="col-sm-8">
					  <input type="text" name="rekeningOmschrijving" class="form-control form-control-sm" id="changeRekeningOmschrijving" required>
					</div>
				  </div>

				  <!-- Overdracht vorig jaar -->
				  <div class="form-group row mb-2">
					<label for="changeRekeningOverdracht" class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
					  Overdracht vorig jaar
					</label>
					<div class="col-sm-8">
					  <input type="text" name="rekeningOverdracht" class="form-control form-control-sm" id="changeRekeningOverdracht" pattern="^-?(\d+)?(\.\d{1,2})?$" title="Voer een geldig bedrag in, bijvoorbeeld 123, 123.4, 123.45 of .45" required>
					</div>
				  </div>
				
				  <!-- Actief -->
				  <div class="form-group row mb-1" id="divRekeningActief">
				    <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold" for="changeRekeningActief">
					  Actief
				    </label>
				    <div class="col-sm-1 d-flex align-items-center">
					  <div class="custom-control custom-switch">
					    <input type="checkbox" class="custom-control-input" id="changeRekeningActief" name="rekeningActief" value="X">
					    <label class="custom-control-label" for="changeRekeningActief"></label>
						<input type="hidden" name="rekeningActiefHidden" id="rekeningActiefHidden">
					  </div>
				    </div>
				  </div>
				</div>
			  </div>
			</div>

			<!-- Footer -->
			<div class="modal-footer bg-light justify-content-between" id="changeRekeningFooter">
			  <button type="button"
					  class="btn btn-danger"
					  id="btnVerwijderRekening">
				Verwijder rekening
			  </button>

			  <div>
				<button type="submit" class="btn btn-primary">Opslaan</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
			  </div>
			</div>
		  </form>

		</div>
	  </div>
	</div>


	<!-- Bevestiging Verwijderen Modal -->
	<div class="modal fade" id="confirmDeleteRekeningModal" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">

		  <div class="modal-header bg-danger text-white">
			<h5 class="modal-title">Rekening verwijderen</h5>
			<button type="button" class="close text-white" data-dismiss="modal">
			  <span>&times;</span>
			</button>
		  </div>

		  <div class="modal-body">
			<p class="mb-0">
			  Weet je zeker dat je deze rekening wilt verwijderen?<br>
			  <strong>Deze actie kan niet ongedaan worden gemaakt.</strong>
			</p>
		  </div>

		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">
			  Annuleren
			</button>
			<button type="button" class="btn btn-danger" id="confirmDeleteRekening">
			  Ja, verwijder
			</button>
		  </div>

		</div>
	  </div>
	</div>

	<?php include $prefix.'includes/scripts.php';?>

	<?php include $prefix.'includes/scriptsVariables.php';?>
	<?php include $prefix.'includes/scriptsGeneral.php';?>

	<script>
		var boekJaarOpen = "<?php echo $boekjaarOpen; ?>";
		
		function wijzigenRekening(rekeningId, rekening, omschrijving, overdracht, actief, totaalBedrag) {
			var totaalBedragNumber = parseFloat(totaalBedrag) || 0;

			$("#changeRekeningNr").prop("readonly", false);
			$("#divRekOmschrijving").show();
			$("#divRekeningActief").show();
			$("#changeRekeningNr").attr("pattern", "^BE\\d{2}(?:\\s?\\d{4}){3}$");
			$("#changeRekeningNr").attr("value", "BE");

			// Vul form fields
			$("#changeRekeningId").val(rekeningId);
			$("#changeRekeningNr").val(rekening);
			$("#changeRekeningOmschrijving").val(omschrijving);
			$("#changeRekeningOverdracht").val(overdracht);

			// Reset checkbox & waarschuwing
			$('#changeRekeningActief').prop('checked', actief === 'X');
			$('#rekeningInactiefAlert').remove();

			if (rekening === 'KAS') {
				$("#changeRekeningNr").prop("readonly", true);
				$("#divRekOmschrijving").hide();
				$("#divRekeningActief").hide();
				$("#changeRekeningNr").attr("pattern", "");
				$("#changeRekeningNr").val("KAS");
				$("#changeRekeningOmschrijving").prop("required", false);
			} else {
				$("#changeRekeningOmschrijving").prop("required", true);
			}

			// ⚠️ Waarschuwing + disable checkbox als er een saldo is
			if (totaalBedragNumber > 0) {
				$('#changeRekeningActief').prop('disabled', true);

				var alertHtml =
					'<div id="rekeningInactiefAlert" class="small alert alert-warning d-flex align-items-start mt-2 ml-2 mb-0" role="alert">' +
						'<div class="mr-3" style="font-size: 1.5rem; line-height: 1;">⚠️</div>' +
						'<div>' +
							'<strong>BELANGRIJK</strong><br>' +
							'Deze rekening kan niet op inactief worden gezet omdat er al bedragen op geboekt zijn. Totaalbedrag: ' +
							'<strong>' + formatEuro(totaalBedragNumber) + '</strong>' +
						'</div>' +
					'</div>';
				$('#divRekeningActief').append(alertHtml);
			} else {
				$('#changeRekeningActief').prop('disabled', false);
			}

			// Toon/verberg "Verwijder rekening" knop
			if (actief === 'X') {
				$('#btnVerwijderRekening').hide();
				$('#changeRekeningFooter').removeClass('justify-content-between').addClass('justify-content-end');
			} else {
				$('#btnVerwijderRekening').show();
				$('#changeRekeningFooter').removeClass('justify-content-end').addClass('justify-content-between');
			}

			$('#changeRekeningenModal').modal('show');
		}

		$(".rekeningMove" ).on( "click", function() {
		   var rekId = $(this).attr('rek-id');
		   var rekMove = $(this).attr('rek-move');
		   
			$.post("<?php echo($prefix);?>bin/pages/changeRekeningOrder.php", { rekeningId: rekId, move: rekMove }, function(response) {
				});
			location.reload();
		});
		
/* 		$(document).ready(function(){
		  $('#inputRekeningOverdracht').on('focusout', function() {
			if (!isValid($(this).val().trim())) {
				$(this).addClass('cellError'); // Add error 
				$("#rekeningToevoegenSubmit"). attr("disabled", true);
				return false;
			  } else {
				$(this).removeClass('cellError'); // Remove if it became valid
				$("#rekeningToevoegenSubmit"). attr("disabled", false);
			  }
			});
		});					  

		$(document).ready(function(){
		  $('#changeRekeningOverdracht').on('focusout', function() {
			if (!isValid($(this).val().trim())) {
				$(this).addClass('cellError'); // Add error 
				$("#rekeningWijzigenSubmit"). attr("disabled", true);
				return false;
			  } else {
				$(this).removeClass('cellError'); // Remove if it became valid
				$("#rekeningWijzigenSubmit"). attr("disabled", false);
			  }
			});
		});	 */		

	const rowTooltipText = (data) => {
	  return 'Wijzig rekening';
	};

	// AG Grid		
	const columnDefs = [
		{
		headerName: '',
		field: 'drag',
		width: 70,
		suppressMovable: true,
		pinned: 'left',

		<?php if($boekjaarOpen == true) { ?>
			rowDrag: true,
			headerClass: 'drag-header',
			cellClass: 'drag-cell',
		<?php } ?>

		headerComponent: class {
			init() {
				this.eGui = document.createElement("div");
				this.eGui.style.display = "flex";
				this.eGui.style.alignItems = "center";
				this.eGui.style.justifyContent = "center";
				this.eGui.style.gap = "10px";

				// ↕ icoon (links)
				const dragIcon = document.createElement("span");
				dragIcon.textContent = "↕";
				dragIcon.style.fontSize = "14px";
				dragIcon.style.lineHeight = "1";
				dragIcon.style.cursor = "default";

				this.eGui.appendChild(dragIcon);

				<?php if($boekjaarOpen == true) { ?>
				// + knop (rechts)
				const btn = document.createElement("button");
				btn.className = "btn btn-success btn-sm";
				btn.style.cssText = `
					width: 24px;
					height: 24px;
					padding: 0;
					display: flex;
					justify-content: center;
					align-items: center;
				`;

				// Voeg icon toe
				const icon = document.createElement("i");
				icon.className = "fas fa-plus";
				icon.style.fontSize = "11px";
				btn.appendChild(icon);

				// --- Create tooltip element once ---
				const tooltip = document.createElement("div");
				tooltip.textContent = "Rekening toevoegen";
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

				this.eGui.appendChild(btn);

				btn.addEventListener("click", () => {
					$('#addRekeningModal').modal('show');
				});
				<?php } ?>
			}

			getGui() {
				return this.eGui;
			}
		}
		},
		{ headerName: 'Type', field: 'icon', width: 80,
			cellRenderer: params => {
			  if (!params.value) return '';
			  return `<img src="${params.value}" class="account-icon" />`;
			}
		},
		{ headerName: 'Rekeningnummer', maxWidth: 170, field: 'rekening', flex: 1 },
		{ headerName: 'Naam', field: 'omschrijving', flex: 1 },
		{ headerName: 'Saldo', field: 'overdracht', maxWidth: 170, flex: 1, cellRenderer: (data) => { return formatEuro(data.value); } },
	];

	refreshAll();
		
	function refreshAll() {
		fetch('../data/<?php echo $dataFile ?>')
		  .then(response => response.json())
		  .then(data => {
    // ---- BEGIN SAFE BLOCK SCOPE ----
    {
        let pinnedTop = null;
        let normalRows = [];
		
		<?php if($useKAS === 'X') { ?>
        if (Array.isArray(data) && data.length > 0) {
            pinnedTop = data[0];
            normalRows = data.slice(1);
        }
		<?php } else { ?>
        if (Array.isArray(data) && data.length > 0) {
            normalRows = data.slice(1);
        }		
		<?php } ?>

        const gridOptions = {
            rowData: normalRows,
            columnDefs,
            pinnedTopRowData: pinnedTop ? [pinnedTop] : [],
            rowDragManaged: true,
            animateRows: true,
            rowHeight: 50,
            headerHeight: 50,
            defaultColDef: { 
                sortable: false,
                tooltipValueGetter: (params) => rowTooltipText(params.data)
            },
            getRowClass: params => params.node.rowPinned === 'top' ? 'pinned-row' : null,
            onRowClicked: function(event) {
                if(boekJaarOpen == '1') {
                    wijzigenRekening(
                        event.data.rekeningId,
                        event.data.rekening,
                        event.data.omschrijving,
                        event.data.overdracht,
                        event.data.actief,
						event.data.totaalBedrag
                    );
                }
            },
            onRowDragEnd: params => {
                const allIds = [];
                params.api.forEachNode(node => {
                  if (!node.rowPinned) allIds.push(node.data.rekeningId);
                });
                $.post('<?php echo $prefix ?>bin/pages/changeRekeningOrder.php', 
                       { rows: allIds },
                       function(response) { console.log('Server response:', response); },
                       'json');
            },
			localeText: {
				noRowsToShow: 'Geen rekeningen gevonden'
			},			
        };

        document.getElementById("myGridRekening").innerHTML = '';
        const eGridDiv = document.getElementById('myGridRekening');
        agGrid.createGrid(eGridDiv, gridOptions);
    }
    // ---- END SAFE BLOCK SCOPE ----
		  })
		.catch(err => console.error(err));
		
		fetchInactive();	
		}

	const columnDefsInact = [
		  { headerName: 'Rekeningnummer', maxWidth: 170, field: 'rekening', flex: 1 },
		  { headerName: 'Naam', field: 'omschrijving', flex: 1 },
		  { headerName: 'Saldo', field: 'overdracht', maxWidth: 170, flex: 1, cellRenderer: (data) => { return formatEuro(data.value); } },
		];
		
    const gridOptionsInact = {
        rowHeight: 50,
        headerHeight: 50,		
        columnDefs: columnDefsInact,
		rowData: [],
		onRowClicked: function(event) {
						if(boekJaarOpen == '1') {
							wijzigenRekening(event.data.rekeningId, event.data.rekening, event.data.omschrijving, event.data.overdracht, event.data.actief, event.data.totaalBedrag);
							}
						},
		defaultColDef: {
			resizable: true,
			sortable: false,
			tooltipValueGetter: (params) => rowTooltipText(params.data)
		},
		localeText: {
			noRowsToShow: 'Geen inactieve rekeningen'
		},					
    };

	// Create Grid: Create new grid within the #myGridRekening div, using the Grid Options object
	let gridApiInact = agGrid.createGrid(document.querySelector("#myGridInactief"), gridOptionsInact);
	//gridApi.autoSizeAllColumns();

	// Fetch Remote Data
	fetchInactive();
	
	function fetchInactive() {
	fetch("../data/<?php echo $dataFileInactief ?>")
	  .then((response) => response.json())
	  .then((data) => {
		  // Check if data is valid
		  if (Array.isArray(data) && data.length > 0) {
			  gridApiInact.setGridOption("rowData", data);	
		  } else {
			  gridApiInact.setGridOption("rowData", []);
		  }
	  });
	}

	// Patch rekening
	$('#formPatchRekening').on('submit', function(e) {
		e.preventDefault();
		// Update hidden field voor POST
		$('#rekeningActiefHidden').val($('#changeRekeningActief').is(':checked') ? 'X' : '');
		$.ajax({
			url: '<?php echo $prefix ?>bin/pages/changeRekening.php',
			type: 'POST',
			data: $(this).serialize(),
			success: function(response) {
				$('#changeRekeningenModal').modal('toggle');
				refreshAll();
			}
		});
	});
	
	// Verwijder rekening
	$(function () {

	  // Klik op "Verwijder rekening"
	  $('#btnVerwijderRekening').on('click', function () {
		$('#confirmDeleteRekeningModal').modal('show');
	  });

	  // Bevestiging verwijderen
	  $('#confirmDeleteRekening').on('click', function () {

		const rekeningId = $('#changeRekeningId').val();

		$.ajax({
		  url: '../bin/pages/deleteRekening.php',
		  type: 'POST',
		  data: {
			rekeningId: rekeningId
		  },
		  success: function () {
			$('#confirmDeleteRekeningModal').modal('hide');
			$('#changeRekeningenModal').modal('hide');

			refreshAll();

			console.log('Rekening succesvol verwijderd');
		  },
		  error: function () {
			let message = 'Fout bij verwijderen';

			if (xhr.responseJSON?.message) {
			  message = xhr.responseJSON.message;
			}

			console.log(message);
		  }
		});
	  });

	});
	
	$('#confirmDeleteRekeningModal')
	  .on('show.bs.modal', function () {
		$('#changeRekeningenModal .modal-content')
		  .addClass('modal-dimmed');
	  })
	  .on('hidden.bs.modal', function () {
		$('#changeRekeningenModal .modal-content')
		  .removeClass('modal-dimmed');
	  });	
	</script>
</body>
</html>