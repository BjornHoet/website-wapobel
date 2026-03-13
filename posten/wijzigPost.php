<?php
$prefix = '../';
$activeDagboek = '';
$activePost = 'active';
$activeRekening = '';

$hoofdpostId = $_GET["hoofdpostId"];

include $prefix.'/bin/init.php';

$pageTitle = 'Wijzig posten';
$hoofdPostData = getHoofdPostData($hoofdpostId);

if (!loggedIn()) {
    header("Location: ".$prefix."bin/login");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include $prefix.'includes/head.php'; ?>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <?php include $prefix.'includes/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include $prefix.'includes/topbar.php'; ?>

            <!-- Begin Page Content -->
            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="d-sm-flex mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle; ?></h1>
                </div>

                <div class="row">
                    <div class="col-xl-9">

                        <div class="card shadow mb-4">
                            <div class="card-body">

								<!-- Header: Referentie en Omschrijving -->
								<div class="d-flex align-items-center mb-3">
									<div class="font-weight-bold mr-3" style="width: 50px;">
										<?php echo $hoofdPostData['referentie']; ?>
									</div>
									<div class="flex-grow-1 mr-3">
										<?php echo $hoofdPostData['omschrijving']; ?>
									</div>
									<div style="display:none;">
										<a href="#" data-toggle="modal" data-target="#postModal" class="btn-add-post" 
											data-hoofdpostid="<?php echo $hoofdpostId; ?>"
											data-referentie="<?php echo $hoofdPostData['referentie']; ?>"
											data-omschrijving="<?php echo $hoofdPostData['omschrijving']; ?>"
											data-andere="<?php echo $hoofdPostData['andere']; ?>">
											<button class="btn btn-success">
												<?php echo ($hoofdPostData['andere'] == 1) ? 'Post/Subpost toevoegen' : 'Subpost toevoegen'; ?>
											</button>
										</a>
									</div>
								</div>

								<!-- AG Grid Container -->
								<div id="myGrid" class="ag-theme-alpine" style="width: 100%; height: 500px;"></div>

								<!-- Buttons onder de grid -->
								<div class="mt-3">
									<button class="btn btn-primary btn-user btn-size" id="savePosten">
										Opslaan
									</button>
									<a href="../posten/" class="ml-1 btn btn-secondary btn-user btn-size">
										Annuleren
									</a>
								</div>

								<!-- Modal: Toevoegen Post/Subpost -->
								<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
										<div class="modal-content shadow-lg">
											<div class="modal-header bg-light border-bottom">
												<h4 class="modal-title text-primary font-weight-bold" id="postModalLabel">
													Toevoegen van post
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
															<select id="inputPost" name="postId" class="form-control form-control-sm"></select>
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

                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->

                    </div> <!-- /.col-xl-9 -->
                </div> <!-- /.row -->

            </div> <!-- /.container-fluid -->

        </div> <!-- /#content -->

        <?php include $prefix.'includes/footer.php'; ?>

    </div> <!-- /#content-wrapper -->

</div> <!-- /#wrapper -->

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<?php include $prefix.'includes/modals.php'; ?>
<?php include $prefix.'includes/scripts.php'; ?>

<!-- AG Grid -->
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

<script>
let gridApi;
const hoofdpostAndere = <?php echo $hoofdPostData['andere'] ? 'true' : 'false'; ?>;

// Column definitions
const columnDefs = [
	{
		headerName: "Acties",
		width: 120,
		cellRenderer: params => {

			const d = params.data;
			if (!d) return "";

			const container = document.createElement("div");
			container.style.display = "flex";
			container.style.alignItems = "center"; // verticaal centreren
			container.style.height = "100%";

			// ➕ Subpost toevoegen (alleen bij posten)
			if (d.type === "post") {
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

				// 🔹 Inspringen naast de toevoegen knop
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
        width: 120,
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
        width: 120,
        editable: params => params.data.actief && (params.data.type !== "post" || !params.data.hasSub),
        cellClassRules: {
            "editable-highlight": params => params.data.actief && (params.data.type !== "post" || !params.data.hasSub)
        }
    },
    {
        field: "actief",
        headerName: "Actief",
        width: 100,
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
                        gridApi.forEachNode(node => {
                            if (node.data.type === "subpost" && node.data.postId === params.data.postId) {
                                node.data.actief = value;
                                node.setData(node.data);
                            }
                        });
                    }

                    if (params.data.type === "subpost" && value) {
                        gridApi.forEachNode(node => {
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

// Grid options
const gridOptions = {
    rowHeight: 28,
    domLayout: 'normal',
    treeData: true,
    singleClickEdit: true,
    getDataPath: function(data) {
        if (data.type === "post") {
            return [data.id];          // root nodes
        } else {
            return [data.parent, data.id];  // subpost nodes
        }
    },
    getRowNodeId: function(data) {
        return data.id;   // ID moet uniek zijn
    },
    defaultColDef: { filter: "agTextColumnFilter", cellStyle: { color: '#858796' }, resizable: true, sortable: true },
    columnDefs: columnDefs,
    rowClassRules: {
        "post-row": params => params.data.type === "post",
        "subpost-row": params => params.data.type === "subpost",
        "text-gray-800": params => params.data.type === "post" && params.data.actief,
        "text-gray-700": params => params.data.type === "subpost" && params.data.actief,
        "text-inactive": params => !params.data.actief
    }
};

// Create grid
gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);

// Fetch initial data
fetch("../bin/selects/agGetPosten.php?hoofdpostId=<?php echo $hoofdpostId ?>")
    .then(res => res.json())
    .then(data => {
		data.forEach(r => r.path = r.parent ? [r.parent, r.id] : [r.id]);
		gridApi.setGridOption("rowData", data);
    });

// Delete row
document.addEventListener("click", e => {

    if (e.target.classList.contains("deletePost")) {
        verwijderenPost("P", e.target.dataset.id, e.target.dataset.rowid);
    }

    if (e.target.classList.contains("deleteSubPost")) {
        verwijderenPost("S", e.target.dataset.id, e.target.dataset.rowid);
    }

});

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

function verwijderenPost(type, id, node) {

    $.post('../bin/pages/deletePost.php', { type, postId: id }, () => {

        gridApi.applyTransaction({
            remove: [node.data]
        });

    });

}

// Save button
$("#savePosten").click(() => {
    const rows = [];
    gridApi.forEachNode(node => rows.push(node.data));
    $.post('../bin/pages/changePosten.php', { rows: JSON.stringify(rows) }, () => location.reload());
});

// --- Toevoegen Post/Subpost via modal ---
// --- Open modal ---
function toevoegenPost(hoofdpostId, referentie, omschrijving, andere) {
    // Show modal
    $("#postModal").modal("show");

    // Vul header en hidden input
    $("#toevoegenPostHoofdPost").html(referentie + '<span class="ml-3">' + omschrijving + '</span>');
    $("#inputHoofdpostId").val(hoofdpostId);

    if (andere == '1') {
        // Laat gebruiker kiezen tussen Post of Subpost
        $("#inputTypePost").val('P');
        $("#typePost").show();
        $("#addPost").hide();
    } else {
        // Alleen Subpost mogelijk
        $("#inputTypePost").val('S');
        $("#typePost").hide();
        $("#addPost").show();

        // Vul parent post select via AJAX
        $.getJSON("../bin/selects/getPosten.php", { id: hoofdpostId }, function(data){
            $("#inputPost").empty();
            $.each(data, function(key, value){
                $("#inputPost").append('<option value="'+ key +'">'+ value +'</option>');
            });
        });
    }
}

// --- Type select change (Post/Subpost) ---
$("select[name='postType']").change(function () {
    var postType = $(this).val();
    var hoofdpostId = $("#inputHoofdpostId").val();

    if(postType === 'S') {
        $("#addPost").show();
        $.getJSON("../bin/selects/getPosten.php", { id: hoofdpostId }, function(data) {
            $("#inputPost").empty();
            $.each(data, function(key, value) {
                $("#inputPost").append('<option value="'+ key +'">'+ value +'</option>');
            });
        });
    } else {
        $("#addPost").hide();
        $("#inputPost").empty();
    }
});

$("#addPostForm").on("submit", function(e) {
    e.preventDefault();
    const formData = $(this).serialize();

	$.post($(this).attr("action"), formData, function(newRow) {
        // Zorg dat alle bestaande nodes een path hebben
        gridApi.forEachNode(node => {
            if(!node.data.path) {
                node.data.path = node.data.parent ? [node.data.parent, node.data.id] : [node.data.id];
            }
        });

        // Parent ophalen
        let parentNode = null;
        if(newRow.parent) {
            parentNode = gridApi.getRowNode(newRow.parent);
            if(!parentNode) {
                gridApi.forEachNode(node => {
                    if(node.data.id === newRow.parent) parentNode = node;
                });
            }
        }

        // Path instellen
        if(parentNode) {
			let counter = 0;
            newRow.path = parentNode.data.path.concat([newRow.id]);

			gridApi.forEachNode(node => {
				if(node.data.parent === parentNode.data.id) counter++;
			});
			counter ++;

            // **Belangrijk:** voeg nu toe als child van parent
            gridApi.applyTransaction({
                add: [newRow],
                addIndex: parentNode.childIndex + counter // childIndex kan undefined zijn bij lege parent, dan valt het terug op add onderaan parent
            });
        } else {
            newRow.path = [newRow.id];
            console.log("Geen parent, pad:", newRow.path);
            gridApi.applyTransaction({ add: [newRow] });
        }

		// 🔹 FORM LEGEN
        $("#addPostForm")[0].reset();

        // 🔹 dropdown leegmaken (optioneel maar proper)
        $("#inputPost").empty();

        $("#postModal").modal("hide");

		gridApi.refreshClientSideRowModel('sort');
    });
});

$(document).ready(function() {
    $(".btn-add-post").click(function(e) {
        e.preventDefault(); // voorkom page jump

        let hoofdpostId = $(this).data("hoofdpostid");
        let referentie = $(this).data("referentie");
        let omschrijving = $(this).data("omschrijving");
        let andere = $(this).data("andere");

        toevoegenPost(hoofdpostId, referentie, omschrijving, andere);
    });
});
</script>

</body>
</html>