<?php
$prefix = '../';
$activeAlgemeen = 'active';

include $prefix.'/bin/init.php';
$pageTitle = 'Wapobel algemeen';

$hoofdposten = getHoofdpostenAllData(); 
$standaardposten = getStandaardpostenAllData();
$types = getTypesAllData();

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
                    <!-- Tabs -->
                    <div class="col-xl-12">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3" id="wateringTabs" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="hoofdposten-tab" data-toggle="tab" href="#hoofdpostenPane" role="tab" aria-controls="hoofdpostenPane" aria-selected="true">
										   Hoofdposten
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="types-tab" data-toggle="tab" 
										   href="#typesPane" role="tab" aria-controls="typesPane" aria-selected="false">
											Types
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" id="standaardposten-tab" data-toggle="tab" 
										   href="#standaardpostenPane" role="tab" aria-controls="standaardpostenPane">
											Standaardposten
										</a>
									</li>									
                                </ul>

                                <div class="tab-content" id="wateringTabsContent">
									<!-- Hoofdposten -->
									<div class="tab-pane fade show active" id="hoofdpostenPane" role="tabpanel" aria-labelledby="hoofdposten-tab">
										<div class="form-inline mb-4">
											<select id="hoofdpostJaarFilter" class="form-control mr-4" style="width:150px;">
												<option value="">Alle jaren</option>
											</select>
											<button class="btn btn-primary mr-4" id="copyHoofdpostenBtn">
												Kopieer naar nieuw jaar
											</button>
											<button id="deleteHoofdposten" class="btn btn-danger">
												Geselecteerde verwijderen
											</button>											
										</div>
										<div id="hoofdpostenGrid" class="ag-theme-alpine" style="height:1000px; width:100%;"></div>
									</div>
									<!-- Types -->
									<div class="tab-pane fade" id="typesPane" role="tabpanel" aria-labelledby="types-tab">
										<div class="form-inline mb-4">
											<select id="typesJaarFilter" class="form-control mr-4" style="width:150px;">
												<option value="">Alle jaren</option>
											</select>
											<button class="btn btn-danger" id="deleteTypes">
												Geselecteerde verwijderen
											</button>
										</div>
										<div id="typesGrid" class="ag-theme-alpine" style="height:1000px; width:100%;"></div>
									</div>								
									<!-- Standaardposten -->
									<div class="tab-pane fade" id="standaardpostenPane" role="tabpanel" aria-labelledby="standaardposten-tab">
										<div class="form-inline mb-4">
											<select id="standaardpostenJaarFilter" class="form-control mr-4" style="width:150px;">
												<option value="">Alle jaren</option>
											</select>
											<button class="btn btn-primary mr-4" id="copyStandaardpostenBtn">
												Kopieer naar nieuw jaar
											</button>
											<button id="deleteStandaardposten" class="btn btn-danger">
												Geselecteerde verwijderen
											</button>
										</div>
										<div id="standaardpostenGrid" class="ag-theme-alpine" style="height:1000px; width:100%;"></div>
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

<?php include $prefix.'includes/modals.php';?>

<div class="modal fade" id="copyYearModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kopieer naar nieuw jaar</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form id="copyYearForm">
            <div class="form-group">
                <label>Huidig jaar</label>
                <input type="text" id="copyFromYear" class="form-control" readonly>
            </div>

            <div class="form-group">
                <label>Nieuw jaar</label>
                <input type="number" id="copyToYear" class="form-control">
            </div>

            <input type="hidden" id="copyType"> <!-- hoofdposten / standaardposten -->
        </form>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Annuleer</button>
        <button class="btn btn-primary" id="confirmCopyYear">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Bevestig verwijderen</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p id="deleteConfirmText">Weet je zeker dat je deze items wilt verwijderen?</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleer</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Verwijderen</button>
      </div>

    </div>
  </div>
</div>

<?php include $prefix.'includes/scripts.php';?>
<?php include $prefix.'includes/scriptsVariables.php';?>
<?php include $prefix.'includes/scriptsGeneral.php';?>

<script>
var hoofdpostenData = <?php echo json_encode($hoofdposten); ?>;
var standaardpostenData = <?php echo json_encode($standaardposten); ?>;
var typesData = <?php echo json_encode($types); ?>;

var hoofdpostenGridApi, standaardpostenGridApi, typesGridApi;
var deleteContext = null;

document.addEventListener('DOMContentLoaded', function () {

    // ----------------------------------------
    // 1. TAB ACTIEF ONTHOUDEN
    // ----------------------------------------
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab', e.target.id);
        resizeGridForTab(e.target.id);
    });

    let activeTab = localStorage.getItem('activeTab');
    if (activeTab) $('#' + activeTab).tab('show');

    // ----------------------------------------
    // 2. GENERIEKE FUNCTIES VOOR DROPDOWN EN FILTER
    // ----------------------------------------
    function populateJaarDropdown(data, selectId) {
        let jaren = [...new Set(data.map(r => r.jaar))].sort((a, b) => b - a);
        let select = document.getElementById(selectId);
        jaren.forEach(jr => {
            let opt = document.createElement("option");
            opt.value = jr;
            opt.textContent = jr;
            select.appendChild(opt);
        });
        if (select.options.length > 1) select.selectedIndex = 1;
    }

    function setupJaarFilter(selectId, gridApi, data) {
        document.getElementById(selectId).addEventListener("change", function () {
            let yr = this.value;
            let filtered = (yr === "") ? data : data.filter(r => r.jaar == yr);
            gridApi.setGridOption("rowData", filtered);
            gridApi.autoSizeAllColumns();
        });
        // trigger default filter
        document.getElementById(selectId).dispatchEvent(new Event("change"));
    }

    // ----------------------------------------
    // 3. HOOFDPOSTEN GRID
    // ----------------------------------------
    populateJaarDropdown(hoofdpostenData, "hoofdpostJaarFilter");
    const hoofdpostColumns = [
        { headerCheckboxSelection: true, checkboxSelection: true, width: 40 },
        { headerName: "ID", field: "hoofdpostId", width: 90 },
        { headerName: "Jaar", field: "jaar", width: 90 },
        {
            headerName: "Use", field: "useKey", width: 140, editable: true,
            cellEditor: "agSelectCellEditor",
            cellEditorParams: { values: ["O","U"] },
            valueFormatter: params => params.value === "O" ? "Opbrengsten" : params.value === "U" ? "Uitgaven" : params.value
        },
        {
            headerName: "Type", field: "typeId", width: 200, editable: true,
            cellEditor: "agSelectCellEditor", cellEditorParams: { values: ["GO","BO","GU","BU"] },
            valueFormatter: params => {
                switch(params.value) {
                    case "GO": return "Gewone opbrengsten";
                    case "BO": return "Buitengewone opbrengsten";
                    case "GU": return "Gewone uitgaven";
                    case "BU": return "Buitengewone uitgaven";
                    default: return params.value;
                }
            }
        },
        { headerName: "Referentie", field: "referentie", width: 100, editable: true },
        { headerName: "Omschrijving", field: "omschrijving", minWidth: 300, editable: true },
        { headerName: "Begroting", field: "omschrijvingBegroting", minWidth: 300, editable: true },
        { headerName: "Reserve", field: "reserve", width: 110, editable: true,
            cellEditor: "agSelectCellEditor", cellEditorParams: { values: ["0","1"] },
            valueFormatter: params => params.value=="1" ? "Ja":"Nee",
            valueParser: params => params.newValue
        },
        { headerName: "Andere", field: "andere", width: 110, editable: true,
            cellEditor: "agSelectCellEditor", cellEditorParams: { values: ["0","1"] },
            valueFormatter: params => params.value=="1" ? "Ja":"Nee",
            valueParser: params => params.newValue
        }
    ];

    hoofdpostenGridApi = agGrid.createGrid(document.getElementById("hoofdpostenGrid"), {
        columnDefs: hoofdpostColumns,
        rowData: hoofdpostenData,
        rowSelection: "multiple",
        singleClickEdit: true,
        rowHeight: 28,
        defaultColDef: { sortable:true, filter:true, resizable:true },
        onCellValueChanged: event => $.post('updates/updateHoofdpost.php', event.data)
    });

    setupJaarFilter("hoofdpostJaarFilter", hoofdpostenGridApi, hoofdpostenData);

    // ----------------------------------------
    // 4. STANDAARDPOSTEN GRID
    // ----------------------------------------
    populateJaarDropdown(standaardpostenData, "standaardpostenJaarFilter");
    const standaardpostenColumns = [
        { headerCheckboxSelection: true, checkboxSelection: true, width: 40 },
        { headerName: "ID", field: "postId", width: 90 },
        { headerName: "Jaar", field: "jaar", width: 100 },
        { headerName: "Hoofdpost", field: "hoofdpostId", width: 150, editable:true },
        { headerName: "Referentie", field: "referentie", width: 120, editable:true },
        { headerName: "Omschrijving", field: "omschrijving", minWidth:300, editable:true }
    ];

    standaardpostenGridApi = agGrid.createGrid(document.getElementById("standaardpostenGrid"), {
        columnDefs: standaardpostenColumns,
        rowData: standaardpostenData,
        rowSelection:"multiple",
        singleClickEdit:true,
        rowHeight:28,
        defaultColDef:{ sortable:true, filter:true, resizable:true },
        onCellValueChanged: event => $.post('updates/updateStandaardpost.php', event.data)
    });

    setupJaarFilter("standaardpostenJaarFilter", standaardpostenGridApi, standaardpostenData);

    // ----------------------------------------
    // 5. TYPES GRID
    // ----------------------------------------
    populateJaarDropdown(typesData, "typesJaarFilter");
    const typesColumns = [
        { headerCheckboxSelection: true, checkboxSelection:true, width:40 },
        { headerName:"Jaar", field:"jaar", width:100 },
        { headerName:"ID", field:"typeId", width:100 },
        { headerName:"Omschrijving", field:"typeOmschrijving", minWidth:300, editable:true },
        { headerName:"Volgorde", field:"volgorde", minWidth:300, editable:true }
    ];

    typesGridApi = agGrid.createGrid(document.getElementById("typesGrid"), {
        columnDefs: typesColumns,
        rowData: typesData,
        rowSelection:"multiple",
        singleClickEdit:true,
        rowHeight:28,
        defaultColDef:{ sortable:true, filter:true, resizable:true },
        onCellValueChanged: event => $.post('updates/updateType.php', event.data)
    });

    setupJaarFilter("typesJaarFilter", typesGridApi, typesData);

    // ----------------------------------------
    // 6. COPY TO NEW YEAR
    // ----------------------------------------
    function openCopyModal(btnId, type) {
        $("#" + btnId).on("click", function(){
            let selectId = type === "hoofdposten" ? "hoofdpostJaarFilter" : "standaardpostenJaarFilter";
            let selectedYear = document.getElementById(selectId).value;
            if(!selectedYear){ alert("Selecteer eerst een jaar om te kopiëren."); return; }

            $("#copyFromYear").val(selectedYear);
            $("#copyToYear").val(parseInt(selectedYear)+1);
            $("#copyType").val(type);
            $("#copyYearModal").modal("show");
        });
    }
    openCopyModal("copyHoofdpostenBtn", "hoofdposten");
    openCopyModal("copyStandaardpostenBtn", "standaardposten");

    $("#confirmCopyYear").on("click", function(){
        let fromYear = $("#copyFromYear").val();
        let toYear = $("#copyToYear").val();
        let type = $("#copyType").val();
        $.post("updates/copyYear.php", { fromYear, toYear, type }, function(res){
            console.log(res);
            $("#copyYearModal").modal("hide");
            location.reload();
        });
    });

    // ----------------------------------------
    // 7. DELETE ITEMS
    // ----------------------------------------
    function setupDelete(buttonId, gridApi, type) {
        document.getElementById(buttonId).addEventListener("click", function(){
            let selected = gridApi.getSelectedRows();
            if(selected.length === 0) return;

            deleteContext = { type, rows: selected };
			document.getElementById("deleteConfirmText").innerHTML =
				"Weet je zeker dat je <strong>" +
				selected.length +
				"</strong> " +
				type.replace(/s$/, "") +
				"(en) wil verwijderen?";
            $("#deleteConfirmModal").modal("show");
        });
    }

    setupDelete("deleteHoofdposten", hoofdpostenGridApi, "hoofdposten");
    setupDelete("deleteStandaardposten", standaardpostenGridApi, "standaardposten");
    setupDelete("deleteTypes", typesGridApi, "types");

    $("#confirmDeleteBtn").on("click", function(){
        if(!deleteContext) return;
        let itemsToDelete = [], url = "";

        switch(deleteContext.type){
            case "hoofdposten":
                deleteContext.rows.forEach(r=>itemsToDelete.push({jaar:r.jaar, omschrijving:r.omschrijving}));
                url="updates/deleteHoofdpostenBatch.php"; break;
            case "standaardposten":
                deleteContext.rows.forEach(r=>itemsToDelete.push({jaar:r.jaar, omschrijving:r.omschrijving}));
                url="updates/deleteStandaardpostenBatch.php"; break;
            case "types":
                deleteContext.rows.forEach(r=>itemsToDelete.push({typeId:r.typeId, jaar:r.jaar, omschrijving:r.omschrijving}));
                url="updates/deleteTypesBatch.php"; break;
        }

        $.post(url, { items: itemsToDelete }, function(res){
            if(res.trim()!=="OK"){ console.error(res); return; }

            if(deleteContext.type==="hoofdposten") hoofdpostenGridApi.applyTransaction({ remove: deleteContext.rows });
            if(deleteContext.type==="standaardposten") standaardpostenGridApi.applyTransaction({ remove: deleteContext.rows });
            if(deleteContext.type==="types") typesGridApi.applyTransaction({ remove: deleteContext.rows });

            $("#deleteConfirmModal").modal("hide");
            deleteContext=null;
            location.reload();
        });
    });

    // ----------------------------------------
    // 8. HULPFUNCTIE: RESIZE GRID BIJ TAB SWITCH
    // ----------------------------------------
    function resizeGridForTab(tabId){
        switch(tabId){
            case "hoofdposten-tab": if(hoofdpostenGridApi) hoofdpostenGridApi.autoSizeAllColumns(); break;
            case "standaardposten-tab": 
                if(standaardpostenGridApi){
                    let yr=document.getElementById("standaardpostenJaarFilter").value;
                    let filtered=(yr==="")?standaardpostenData:standaardpostenData.filter(r=>r.jaar==yr);
                    standaardpostenGridApi.setGridOption("rowData",filtered);
                    standaardpostenGridApi.autoSizeAllColumns();
                } break;
            case "types-tab":
                if(typesGridApi){
                    let yr=document.getElementById("typesJaarFilter").value;
                    let filtered=(yr==="")?typesData:typesData.filter(r=>r.jaar==yr);
                    typesGridApi.setGridOption("rowData",filtered);
                    typesGridApi.autoSizeAllColumns();
                } break;
        }
    }

});

</script>

</body>
</html>
