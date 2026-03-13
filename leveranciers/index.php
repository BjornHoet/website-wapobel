<?php
$prefix = '../';
$activeLeveranciers = 'active';

include $prefix.'/bin/init.php';
$pageTitle = 'Billit leveranciers';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

//$vendors = getVendors('');
$dataFile = $wateringData['wateringId'] . '_vendors.json';
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
                                    <div class="p-3 mb-2 bg-light rounded border">
										<div class="d-flex align-items-center">
											<i class="fas fa-info-circle text-primary mr-2"></i>
											<h6 class="font-weight-bold mb-0 text-dark">Overzicht van je Billit leveranciers</h6>
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-12">
											<div class="form-group row mt-3">
											  <div class="d-flex align-items-center pl-3 gap-2 mb-2">
												<h6 class="m-0 text-black font-weight-bold text-line-height-m mr-2">Zoeken</h6>
												<input type="search" class="form-control" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()" style="min-width: 400px; max-width: 600px; max-height: 35px;">
											  </div>
											</div>
											<div id="myGrid" style="height: 100%; width: 100%;"></div>
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

	<?php include $prefix.'includes/scriptsVariables.php';?>
	<?php include $prefix.'includes/scriptsGeneral.php';?>
	
	<script>
	// Grid API: Access to Grid API methods
	let gridApi;
	
	// Grid Options: Contains all of the grid configurations
	const gridOptions = {
	  // Data to be displayed
	  rowHeight: 28,
	  rowData: [],
	  // Columns to be displayed (Should match rowData properties)
	  columnDefs: [
		{ field: "Name", headerName: "Naam", sort: 'asc' },
		{ field: "Street", headerName: "Straat" },
		{ field: "StreetNumber", headerName: "Huisnummer" },
		{ field: "Zipcode", headerName: "Postcode" },
		{ field: "City", headerName: "Gemeente" },
		{ field: "VATNumber", headerName: "BTW nummer" },
		{ field: "Language", headerName: "Taal" },
	  ],
	  domLayout: 'autoHeight',
	  defaultColDef: {
		  filter: "agTextColumnFilter",
		  cellStyle: { color: '#858796' },
		  wrapText: false,
		  autoHeight: false
		},
	  pagination: true,
	  paginationPageSize: 20,
	  onFirstDataRendered: params => {
		  const allColumnIds = params.api
			.getAllDisplayedColumns()
			.map(col => col.getId());

		  params.api.autoSizeColumns(allColumnIds);
		},
	  // onGridReady: initialFilter,
	  };

	//function initialFilter(){
	//	gridApi.setFilterModel({ status: { type: 'notEqual', filter: 'Inactief' } });
	//}
	
	function onFilterTextBoxChanged() {
		gridApi.setGridOption("quickFilterText", document.getElementById("filter-text-box").value,
		);
	}

	function refreshAll() {
		fetch("../data/<?php echo $dataFile ?>", { cache: "no-store", })
			  .then((response) => response.json())
			  .then((data) => gridApi.setGridOption("rowData", data));
		}
			
	// Create Grid: Create new grid within the #myGrid div, using the Grid Options object
	gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);
	//gridApi.autoSizeAllColumns();

	// Fetch Remote Data
	fetch("../data/<?php echo $dataFile ?>", { cache: "no-store", })
	  .then((response) => response.json())
	  .then((data) => gridApi.setGridOption("rowData", data));			
	</script>
</body>
</html>