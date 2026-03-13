<?php
$prefix = '../';
$activeUsers = 'active';

include $prefix.'bin/init.php';
$pageTitle = 'Watering Details';

$wateringId = $_GET['wateringId'];
$_SESSION['selectedWateringId'] = $wateringId;
require $prefix.'bin/database/connect.php';

$rekeningen = getRekeningenAll($wateringId);
$posten = getPostenAllData($wateringId);
$boekjaren = getBoekjarenAllData($wateringId);

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

            <div class="container-fluid">
                <div class="row">
                    <!-- Watering Details -->
                    <div class="col-xl-12">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div id="wateringDetails"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="col-xl-12">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3" id="wateringTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="posten-tab" data-toggle="tab" href="#postenPane" role="tab" aria-controls="postenPane" aria-selected="true">
                                            Posten
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="rekeningen-tab" data-toggle="tab" href="#rekeningenPane" role="tab" aria-controls="rekeningenPane" aria-selected="false">
                                            Rekeningen
                                        </a>
                                    </li>
									<li class="nav-item">
										<a class="nav-link" id="boekjaren-tab" data-toggle="tab" href="#boekjarenPane" role="tab" aria-controls="boekjarenPane" aria-selected="false">
											Boekjaren
										</a>
									</li>
                                </ul>

                                <div class="tab-content" id="wateringTabsContent">
                                    <!-- Posten -->
                                    <div class="tab-pane fade show active" id="postenPane" role="tabpanel" aria-labelledby="posten-tab">
										<div class="d-flex align-items-center mb-2" style="gap: 10px;">
											<select id="postJaarFilter" class="form-control mb-2" style="width:150px;">
												<option value="">Alle jaren</option>
											</select>

											<button id="deletePostenBtn" class="btn btn-danger btn-sm ml-4 mb-2">Verwijderen</button>
										</div>
										<div id="postenGrid" class="ag-theme-alpine" style="height:800px; width:100%;"></div>
                                    </div>
									
                                    <!-- Rekeningen -->
                                    <div class="tab-pane fade" id="rekeningenPane" role="tabpanel" aria-labelledby="rekeningen-tab">
										<div class="d-flex align-items-center mb-2" style="gap: 10px;">
											<select id="jaarFilter" class="form-control mb-2" style="width:150px;">
												<option value="">Alle jaren</option>
											</select>
											<button id="deleteRekeningenBtn" class="btn btn-danger btn-sm ml-4 mb-2">Verwijderen</button>
										</div>
                                        <div id="rekeningGrid" class="ag-theme-alpine" style="height:800px; width:100%;"></div>
                                    </div>

									<!-- Boekjaren -->
									<div class="tab-pane fade" id="boekjarenPane" role="tabpanel" aria-labelledby="boekjaren-tab">
										<div class="d-flex align-items-center mb-2" style="gap: 10px;">
											<select id="boekjaarFilter" class="form-control mb-2" style="width:150px;">
												<option value="">Alle jaren</option>
											</select>
											<button id="deleteBoekjarenBtn" class="btn btn-danger btn-sm ml-4 mb-2">Verwijderen</button>
										</div>
										<div id="boekjarenGrid" class="ag-theme-alpine" style="height:800px; width:100%;"></div>
									</div>									
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php include $prefix.'includes/footer.php';?>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteConfirmModalLabel">Bevestig Verwijderen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Weet je zeker dat je de geselecteerde items wilt verwijderen?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Verwijderen</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
      </div>
    </div>
  </div>
</div>

<?php include $prefix.'includes/modals.php';?>
<?php include $prefix.'includes/scripts.php';?>
<?php include $prefix.'includes/scriptsVariables.php';?>
<?php include $prefix.'includes/scriptsGeneral.php';?>

<script>
var rekeningenData = <?php echo json_encode($rekeningen); ?>;
var postenData = <?php echo json_encode($posten); ?>;
var boekjarenData = <?php echo json_encode($boekjaren); ?>;

document.addEventListener('DOMContentLoaded', function() {
    // -------------------------------
    // 1. Load watering details
    // -------------------------------
    var wateringId = new URLSearchParams(window.location.search).get('wateringId');
    $.ajax({
        url: '<?php echo $prefix; ?>bin/selects/wateringDetails.php',
        method: 'GET',
        data: { wateringId: wateringId },
        dataType: 'json',
        success: function(data) {
            var details = data.wateringDetails;
            if(details) {
                var html = '<div class="row mb-3">';
                html += '<div class="col-md-3"><strong>Omschrijving:</strong><br>' + details.omschrijving + '</div>';
                html += '<div class="col-md-3"><strong>Watering ID:</strong><br>' + details.wateringId + '</div>';
                html += '<div class="col-md-3"><strong>Enable Billit:</strong><br>' + (details.enableBillit==1?'Ja':'Nee') + '</div>';
                html += '<div class="col-md-3"><strong>API Key:</strong><br>' + details.apiKey + '</div></div>';

                html += '<div class="row">';
                html += '<div class="col-md-3"><strong>Gebruikersnaam:</strong><br>' + details.userName + '</div>';
                html += '<div class="col-md-3"><strong>Voornaam:</strong><br>' + details.firstName + '</div>';
                html += '<div class="col-md-3"><strong>Achternaam:</strong><br>' + details.lastName + '</div>';
                html += '<div class="col-md-3"><strong>Database:</strong><br>' + details.wapobelDatabase + '</div></div>';

                $('#wateringDetails').html(html);
            } else {
                $('#wateringDetails').html('<p class="text-danger">Geen details gevonden</p>');
            }
        }
    });
	

    // -------------------------------
    // 1. YEAR DROPDOWNS
    // -------------------------------
	function populateJaarDropdown(data, elementId, autoSelectNewest = true) {
		let jaren = [...new Set(data.map(r => r.jaar))].sort((a, b) => b - a); // descending
		let select = document.getElementById(elementId);

		jaren.forEach(j => {
			let opt = document.createElement('option');
			opt.value = j;
			opt.textContent = j;
			select.appendChild(opt);
		});

		// Auto-select newest only if enabled
		if (autoSelectNewest && select.options.length > 1) {
			select.selectedIndex = 1;
		}
	}

	populateJaarDropdown(rekeningenData, 'jaarFilter');
	populateJaarDropdown(postenData, 'postJaarFilter');
	populateJaarDropdown(boekjarenData, 'boekjaarFilter', false);


    // -------------------------------
    // 2. REKENINGEN GRID
    // -------------------------------
    const rekeningColumns = [
		{ headerName: "", checkboxSelection: true, headerCheckboxSelection: true, width: 50 },
        { headerName: "Rekening ID", field: "rekeningId", editable: false, width: 100 },
        { headerName: "Watering ID", field: "wateringId", editable: false, width: 100 },
        { headerName: "Jaar", field: "jaar", editable: true, width: 100 },
        { headerName: "Rekening", field: "rekening", editable: true },
        { headerName: "Omschrijving", field: "omschrijving", minWidth: 300, editable: true },
        { headerName: "Positie", field: "positie", editable: true, width: 100 },
        { headerName: "Overdracht", field: "overdracht", editable: true },
        { headerName: "Actief", field: "actief", editable: true, width: 100, cellEditor: "agSelectCellEditor", cellEditorParams: { values: ["","X"] }},
        { headerName: "Afgesloten", field: "afgesloten", width: 100, editable: true, cellEditor: "agSelectCellEditor", cellEditorParams: { values: ["","X"] }}
    ];

    const rekeningGridOptions = {
		rowSelection: "multiple",
        columnDefs: rekeningColumns,
        rowData: rekeningenData,
        singleClickEdit: true,
        rowHeight: 28,
        defaultColDef: { sortable: true, filter: true, resizable: true },
		onCellValueChanged: function(event) {
			$.ajax({
				url: '<?php echo $prefix; ?>bin/updates/updateRekening.php',
				method: 'POST',
				data: event.data,
				dataType: 'json', // ensures response is parsed automatically
				success: function(res) {
					if (res.success) {
					} else {
						console.error('Error updating Posten:', res.error);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX error:', error);
				}
			});
		}
    };

    const rekeningGridDiv = document.querySelector('#rekeningGrid');
    const rekeningGridApi = agGrid.createGrid(rekeningGridDiv, rekeningGridOptions);

    // Filter by year
    document.getElementById('jaarFilter').addEventListener('change', function(){
        let year = this.value;
        let filtered = year === "" ? rekeningenData : rekeningenData.filter(r => r.jaar == year);
        rekeningGridApi.setGridOption("rowData", filtered);
    });


    // -------------------------------
    // 3. POSTEN GRID
    // -------------------------------
    const postenColumns = [
		{ headerName: "", checkboxSelection: true, headerCheckboxSelection: true, width: 50 },
        { headerName: "Post ID", field: "postId", editable: false, width: 100 },
        { headerName: "Watering ID", field: "wateringId", editable: false, width: 100 },
        { headerName: "Jaar", field: "jaar", editable: true, width: 100 },
        { headerName: "Hoofdpost ID", field: "hoofdpostId", editable: true, width: 100 },
        { headerName: "Omschrijving", field: "hoofdpostOmschrijving", minWidth: 300 },
		{ headerName: "Ref", field: "referentieTotal", width: 100, editable: false },
		{ headerName: "Referentie", field: "referentie", width: 100, editable: true },
        { headerName: "Omschrijving", field: "omschrijving", minWidth: 400, editable: true },
        { headerName: "Raming", field: "raming", editable: true },
        { headerName: "Actief", field: "actief", width: 100, editable: true, cellEditor: "agSelectCellEditor", cellEditorParams: { values:["","X"] }},
        { headerName: "Overdracht Post", field: "overdrachtPost", width: 100, editable: true, cellEditor: "agSelectCellEditor", cellEditorParams: { values:["","X"] }}
    ];

    const postenGridOptions = {
		rowSelection: "multiple",
        columnDefs: postenColumns,
        rowData: postenData,
        singleClickEdit: true,
        rowHeight: 28,
        defaultColDef: { sortable: true, filter: true, resizable: true },
		onCellValueChanged: function(event) {
			$.ajax({
				url: '<?php echo $prefix; ?>bin/updates/updatePosten.php',
				method: 'POST',
				data: event.data,
				dataType: 'json', // ensures response is parsed automatically
				success: function(res) {
					if (res.success) {
					} else {
						console.error('Error updating Posten:', res.error);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX error:', error);
				}
			});
		}
    };

    const postenGridDiv = document.querySelector('#postenGrid');
    const postenGridApi = agGrid.createGrid(postenGridDiv, postenGridOptions);

    // Filter Posten by year
    document.getElementById('postJaarFilter').addEventListener('change', function() {
        let year = this.value;
        let filtered = year === "" ? postenData : postenData.filter(r => r.jaar == year);
        postenGridApi.setGridOption("rowData", filtered);
    });


	// -------------------------------
	// 4. BOEKJAREN GRID
	// -------------------------------
	const boekjaarColumns = [
		{ headerName: "", checkboxSelection: true, headerCheckboxSelection: true, width: 50 },
		{ headerName: "Watering ID", field: "wateringId", editable: false, width:100 },
		{ headerName: "Jaar", field: "jaar", editable: false, width:100 },
		{ headerName: "Afgesloten", field: "afgesloten", width:100,
		  editable: true,
		  cellEditor: "agSelectCellEditor",
		  cellEditorParams: { values:["","X"] }
		}
	];

	const boekjarenGridOptions = {
		rowSelection: "multiple",
		columnDefs: boekjaarColumns,
		rowData: boekjarenData,
		singleClickEdit: true,
		rowHeight: 28,
		defaultColDef: { sortable: true, filter: true, resizable: true },
		onCellValueChanged: function(event) {
			$.ajax({
				url: '<?php echo $prefix; ?>bin/updates/updateBoekjaar.php',
				method: 'POST',
				data: event.data,
				dataType: 'json', // ensures response is parsed automatically
				success: function(res) {
					if (res.success) {
					} else {
						console.error('Error updating Boekjaar:', res.error);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX error:', error);
				}
			});
		}
	};

	const boekjarenGridDiv = document.querySelector('#boekjarenGrid');
	const boekjarenGridApi = agGrid.createGrid(boekjarenGridDiv, boekjarenGridOptions);

	// Filter by year
	document.getElementById('boekjaarFilter').addEventListener('change', function(){
		let year = this.value;
		let filtered = year === "" ? boekjarenData : boekjarenData.filter(r => r.jaar == year);
		boekjarenGridApi.setGridOption("rowData", filtered);
	});

    // -------------------------------
    // 5. Resize grids when switching tabs
    // -------------------------------
    const tabEl = document.querySelectorAll('button[data-bs-toggle="tab"]');
	tabEl.forEach(tab => {
		tab.addEventListener('shown.bs.tab', function() {
			rekeningGridApi.sizeColumnsToFit();
			postenGridApi.sizeColumnsToFit();
			boekjarenGridApi.sizeColumnsToFit();
		});
	});

    // ------------------------------------------------------
    // 6. NOW trigger the filter (AFTER grid exists)
    // ------------------------------------------------------
    document.getElementById("jaarFilter").dispatchEvent(new Event("change"));
    document.getElementById("postJaarFilter").dispatchEvent(new Event("change"));


    // ------------------------------------------------------
    // 7.  DELETE ROWS
    // ------------------------------------------------------

	let rowsToDelete = [];
	let gridToDeleteFrom = null;

	document.getElementById('deletePostenBtn').addEventListener('click', function() {
		rowsToDelete = postenGridApi.getSelectedRows();
		gridToDeleteFrom = postenGridApi;
		gridType = 'posten';   // pass type

		if (rowsToDelete.length === 0) return;

		$('#deleteConfirmModal').modal('show');
	});

	document.getElementById('deleteRekeningenBtn').addEventListener('click', function() {
		rowsToDelete = rekeningGridApi.getSelectedRows();
		gridToDeleteFrom = rekeningGridApi;
		gridType = 'rekeningen';

		if (rowsToDelete.length === 0) return;

		$('#deleteConfirmModal').modal('show');
	});

	document.getElementById('deleteBoekjarenBtn').addEventListener('click', function() {
		rowsToDelete = boekjarenGridApi.getSelectedRows();
		gridToDeleteFrom = boekjarenGridApi;
		gridType = 'boekjaren';

		if (rowsToDelete.length === 0) return;

		$('#deleteConfirmModal').modal('show');
	});
	
	document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
		if (!rowsToDelete || rowsToDelete.length === 0) return;

		$.ajax({
			url: '<?php echo $prefix; ?>bin/deletes/deleteWateringDetails.php',
			method: 'POST',
			data: {
				rows: JSON.stringify(rowsToDelete),
				type: gridType
			},
			dataType: 'json', // Ensure jQuery parses it automatically
			success: function(res) {
				if (res.success) {
					// Remove rows from grid
					gridToDeleteFrom.applyTransaction({ remove: rowsToDelete });
					$('#deleteConfirmModal').modal('hide');
				} else {
					console.log(res.error);
				}
			},
			error: function(xhr, status, error) {
				console.error(xhr.responseText);
			}
		});
	});
	
});
</script>

</body>
</html>
