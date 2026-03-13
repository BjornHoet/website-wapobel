<?php
$prefix = '../';
$activeLogin = 'active';

include $prefix.'/bin/init.php';
$pageTitle = 'Login log';

if (loggedIn() === false) {
    setcookie('session_exp', 'X', time() + (60), "/"); 
    header("Location: ".$prefix."bin/login");
    die();
}

$json = writeLogonData(); // function to generate JSON for logon table
$dataFile = 'data/logon.json';
?>
<!DOCTYPE html>
<html lang="en">
<?php include $prefix.'includes/head.php';?>
<body id="page-top">

<div id="wrapper">
    <?php include $prefix.'includes/sidebar.php';?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include $prefix.'includes/topbar.php';?>
            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <div class="col-sm-12 d-flex align-items-center pl-3 gap-2">
									<h6 class="m-0 text-black font-weight-bold text-line-height-m mr-2">Zoeken</h6>
									<input type="search" class="form-control form-control-user" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()" style="max-height: 35px;">
								</div>
                            </div>
                        </div>
                        <div id="logonGrid" class="ag-theme-alpine" style="height: 1000px; width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php include $prefix.'includes/footer.php';?>       
    </div>
</div>

<?php include $prefix.'includes/modals.php';?>
<?php include $prefix.'includes/scripts.php';?>
<?php include $prefix.'includes/scriptsVariables.php';?>
<?php include $prefix.'includes/scriptsGeneral.php';?>

<script>
let gridApi;

const columnDefs = [
    { headerName: "VolgNr", field: "volgnr", width: 100, sortable: true, filter: "agNumberColumnFilter" },
    { headerName: "Gebruiker", field: "username", width: 300, sortable: true, filter: true },
    { headerName: "Naam", field: "name", width: 250, sortable: true, filter: true },
    { headerName: "Watering", field: "omschrijving", width: 250, sortable: true, filter: true },
    { headerName: "Datum", field: "datum", width: 120, sortable: true, filter: true },
    { headerName: "Uur", field: "uur", width: 100, sortable: true, filter: true },
    { 
        headerName: "Succes", 
        field: "success", 
        width: 120,
        cellRenderer: params => params.value === 'X'
            ? '<span class="badge bg-success text-white">Ja</span>'
            : '<span class="badge bg-secondary text-white">Nee</span>'
    }
];

const gridOptions = {
    columnDefs,
    pagination: true,
    paginationPageSize: 100,
    defaultColDef: {
        resizable: true,
        sortable: true,
        filter: true
    },
    animateRows: true,
    rowHeight: 28
};

function onFilterTextBoxChanged() {
	gridApi.setGridOption("quickFilterText", document.getElementById("filter-text-box").value,
	);
}

gridApi = agGrid.createGrid(document.querySelector("#logonGrid"), gridOptions);

	// Fetch Remote Data
	fetch("<?php echo $dataFile ?>")
	  .then((response) => response.json())
	  .then((data) => gridApi.setGridOption("rowData", data));		
</script>

</body>
</html>