<?php
$prefix = '../';
$activeUsers = 'active';

include $prefix.'/bin/init.php';
$pageTitle = 'User administration';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

$json = writeAdminUsers();
$dataFile = 'data/users.json';
?>
<!DOCTYPE html>
<html lang="en">

<?php include $prefix.'includes/head.php';?>
<style>
    .ag-cell a {
        pointer-events: all !important;
    }
</style>

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
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Body -->
                                <div class="card-body">
									<div class="col-md-12">
										<div id="fixedMessageContainer"></div>
										<div class="form-group row mt-3">
											<div class="col-sm-3 d-flex align-items-center pl-3 gap-2">
												<h6 class="m-0 text-black font-weight-bold text-line-height-m mr-2">Zoeken</h6>
												<input type="search" class="form-control form-control-user" id="filter-text-box" placeholder="Filter..." oninput="onFilterTextBoxChanged()" style="max-height: 35px;">
											</div>
											<div class="col-sm-3 d-flex">
												<button type="button" class="btn btn-success" onclick="resetNieuws()">Reset Newsflag</button>
											</div>
										</div>
										<div id="userGrid" class="ag-theme-alpine" style="height: 1000px; width: 100%;"></div>
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
	
	<!-- 🔹 Modal voor Gebruiker Bewerken -->
	<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl">
		<div class="modal-content">
		  <div class="modal-header bg-light">
			<h4 class="modal-title text-primary" id="editUserLabel">Gebruiker bewerken</h4>
		  </div>

		  <div class="modal-body">
			<form id="editUserForm">
			  <input type="hidden" id="edit_userId" name="userId">

			  <!-- Gebruiker sectie -->
				<div class="card mb-4 shadow-sm">
				  <div class="card-header bg-primary text-white">
					<strong>Gebruikersgegevens</strong>
				  </div>
				  <div class="card-body">
					<div class="row mb-3">
					  <div class="col-md-6">
						<label class="form-label">Gebruikersnaam</label>
						<input type="text" class="form-control" id="edit_userName" name="userName" required>
					  </div>
					  <div class="col-md-6">
						<label class="form-label">E-mail</label>
						<input type="email" class="form-control" id="edit_email" name="email">
					  </div>
					</div>

					<div class="row mb-3">
					  <div class="col-md-6">
						<label class="form-label">Voornaam</label>
						<input type="text" class="form-control" id="edit_firstName" name="firstName">
					  </div>
					  <div class="col-md-6">
						<label class="form-label">Achternaam</label>
						<input type="text" class="form-control" id="edit_lastName" name="lastName">
					  </div>
					</div>

					<!-- Adresvelden -->
					<div class="row mb-3">
					  <div class="col-md-6">
						<label class="form-label">Straat</label>
						<input type="text" class="form-control" id="edit_street" name="street">
					  </div>
					  <div class="col-md-3">
						<label class="form-label">Huisnummer</label>
						<input type="text" class="form-control" id="edit_houseNumber" name="houseNumber">
					  </div>
					  <div class="col-md-3">
						<label class="form-label">Postcode</label>
						<input type="text" class="form-control" id="edit_postalCode" name="postalCode">
					  </div>
					</div>

					<div class="row mb-3">
					  <div class="col-md-6">
						<label class="form-label">Stad</label>
						<input type="text" class="form-control" id="edit_city" name="city">
					  </div>
						<div class="col-md-6">
						  <label class="form-label">Wapobel Database</label>
						  <select class="form-select form-control w-auto" id="edit_wapobelDatabase" name="wapobelDatabase">
							<option value="">-- Kies database --</option>
							<option value="watering1">watering1</option>
							<option value="watering2">watering2</option>
							<option value="watering3">watering3</option>
							<option value="watering4">watering4</option>
							<option value="watering5">watering5</option>
							<option value="watering6">watering6</option>
							<option value="watering7">watering7</option>
							<option value="watering8">watering8</option>
						  </select>
						</div>
					</div>

					<div class="row mb-3">
					  <div class="col-md-6">
						<label class="form-label">Gebruik factuurnummering</label><br>
						<select class="form-select form-control w-auto" id="edit_useNummering" name="useNummering">
						  <option value="Nee">Nee</option>
						  <option value="Ja">Ja</option>
						</select>
					  </div>
					</div>
					<div class="row mb-3">
					  <div class="col-md-6">
						<label class="form-label">Gebruik KAS</label><br>
						<select class="form-select form-control w-auto" id="edit_useKAS" name="useKAS">
						  <option value="Nee">Nee</option>
						  <option value="Ja">Ja</option>
						</select>
					  </div>
						<div class="col-md-6">
						  <div class="row g-2 align-items-end">

							<!-- Actief -->
							<div class="col-auto d-flex flex-column">
							  <label for="edit_active" class="form-label">Actief</label>
								<select class="form-select form-control" id="edit_active" name="active">
								  <option value="0">Inactief</option>
								  <option value="1">Actief</option>
								</select>
							</div>

							<!-- Jaar -->
							<div class="col-auto flex-column" id="divWaterJaar" style="display:none;">
							  <label for="waterJaar" class="form-label">Jaar</label>
							  <input type="number" class="form-control w-auto" 
									 id="waterJaar" name="waterJaar" 
									 min="1900" max="2100"
									 style="width: 120px;">
							</div>

							<!-- Maak Watering Button -->
							<div class="col-auto d-flex align-items-end">
							  <button type="button" id="btnMaakWatering" class="btn btn-sm btn-success" style="display:none;">
								Maak Watering
							  </button>
							</div>
							<div class="col-auto d-flex align-items-end">
							  <button type="button" id="btnActiveerUser" class="btn btn-sm btn-success" style="display:none;">
								Activeer user
							  </button>
							</div>

						  </div>
						</div>
					</div>
				  </div>
				</div>

			  <!-- Watering sectie -->
			  <div class="card mb-3 shadow-sm">
				<div class="card-header bg-success text-white">
				  <strong>Watering gegevens</strong>
				</div>
				<div class="card-body">
				  <div class="row mb-3">
					<div class="col-md-6">
					  <label class="form-label">Watering ID</label>
					  <input type="text" class="form-control" id="edit_wateringId" name="wateringID" readonly>
					</div>
					<div class="col-md-6">
					  <label class="form-label">Watering naam</label>
					  <input type="text" class="form-control" id="edit_wateringNaam" name="wateringNaam">
					</div>
				  </div>
				  <div class="row mb-3">
					<div class="col-md-6">
					  <label class="form-label">Billit actief</label><br>
					  <select class="form-select form-control w-auto" id="edit_enableBillit" name="enableBillit">
						<option value="0">Nee</option>
						<option value="1">Ja</option>
					  </select>
					</div>
					<div class="col-md-6">
					  <label class="form-label">API Key</label>
					  <input type="text" class="form-control" id="edit_wateringApiKey" name="wateringApiKey">
					</div>
				  </div>

				  <!-- Hier kun je later extra velden van wateringen toevoegen -->
				</div>
			  </div>
			</form>
		  </div>

		  <div class="modal-footer">
		    <button type="button" class="btn btn-primary" id="btnSaveUser">Opslaan</button>
		    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuleren</button>
		  </div>
		</div>
	  </div>
	</div>	

	<!-- Confirm Reset Nieuws Modal -->
	<div class="modal fade" id="confirmToonNieuwsModal" tabindex="-1" role="dialog" aria-labelledby="confirmToonNieuwsLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		  <div class="modal-header bg-success text-white">
			<h5 class="modal-title" id="confirmToonNieuwsLabel">Nieuws opnieuw tonen</h5>
			<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <div class="modal-body">
			Wil je de nieuws sectie resetten voor <strong>alle gebruikers</strong>?
		  </div>

		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">
			  Annuleren
			</button>
			<button type="button" class="btn btn-success" id="btnConfirmToonNieuws">
			  Bevestigen
			</button>
		  </div>
		</div>
	  </div>
	</div>


	<?php include $prefix.'includes/modals.php';?>
	<?php include $prefix.'includes/scripts.php';?>
	<?php include $prefix.'includes/scriptsVariables.php';?>
	<?php include $prefix.'includes/scriptsGeneral.php';?>

<script>
	// Set default: next year
	const now = new Date();
	//const nextYear = now.getFullYear() + 1;
	const nextYear = now.getFullYear();
	document.getElementById('waterJaar').value = nextYear;

	// Grid API: Access to Grid API methods
	let gridApi;
	
	// Kolomdefinities
	const columnDefs = [
		{
			headerName: "",
			field: "actions",
			width: 60,
			cellRenderer: function(params) {
				const editBtn =
					'<a href="#" title="Wijzigen" onclick="editUser(' + params.data.userId + '); event.stopPropagation();">' +
						'<i class="fas fa-edit"></i>' +
					'</a>';

				let deleteBtn = '';
				if (params.data.active == 0) {
					deleteBtn =
						'<a class="ml-2" href="#" title="Verwijderen" onclick="deleteUser(' + params.data.userId + '); event.stopPropagation();">' +
							'<i class="fas fa-trash-alt text-red"></i>' +
						'</a>';
				}

				return '<div class="d-flex justify-content-start gap-2" onclick="event.stopPropagation();">' 
					+ editBtn + deleteBtn + 
				'</div>';
			}
		},	
	  { headerName: "ID", field: "userId", width: 90, sortable: true, filter: "agNumberColumnFilter" },
	  { headerName: "Gebruikersnaam", field: "userName", flex: 1, sortable: true, filter: true },
	  { headerName: "Voornaam", field: "firstName", flex: 1.2, sortable: true, filter: true },
	  { headerName: "Naam", field: "lastName", flex: 1.2, sortable: true, filter: true },
	  { headerName: "E-mail", field: "email", flex: 1.3, sortable: true, filter: true },
	  { headerName: "Database", field: "wapobelDatabase", flex: 1, sortable: true, filter: true },
	  { headerName: "Watering ID", field: "wateringId", flex: 0.5, sortable: true, filter: true },
	  { headerName: "Watering", field: "wateringNaam", flex: 1.2, sortable: true, filter: true },
	  { 
		headerName: "Billit actief", 
		field: "enableBillit",
		width: 130,
		cellRenderer: params => params.value == 1 
		  ? '<span class="badge bg-success text-white">Ja</span>' 
		  : '<span class="badge bg-secondary text-white">Nee</span>'
	  },
	  { 
		headerName: "Factuurnummers", 
		field: "useNummering",
		width: 130,
		cellRenderer: params => params.value == 'Ja' 
		  ? '<span class="badge bg-success text-white">Ja</span>' 
		  : '<span class="badge bg-secondary text-white">Nee</span>'
	  },
	  { 
		headerName: "Gebruik KAS", 
		field: "useKAS",
		width: 130,
		cellRenderer: params => params.value == 'Ja' 
		  ? '<span class="badge bg-success text-white">Ja</span>' 
		  : '<span class="badge bg-secondary text-white">Nee</span>'
	  },
	  { 
		headerName: "Billitnummers", 
		field: "showBillit",
		width: 130,
		cellRenderer: params => params.value == 'Ja' 
		  ? '<span class="badge bg-success text-white">Ja</span>' 
		  : '<span class="badge bg-secondary text-white">Nee</span>'
	  },
	  { 
		headerName: "Wachtwoord gezet", 
		field: "userPassword",
		width: 120,
		cellRenderer: params => params.value == 'Ja' 
		  ? '<span class="badge bg-success text-white">Ja</span>' 
		  : '<span class="badge bg-secondary text-white">Nee</span>'
	  },
	  { 
		headerName: "Actief", 
		field: "active",
		width: 120,
		cellRenderer: params => params.value == 1 
		  ? '<span class="badge bg-success text-white">Actief</span>' 
		  : '<span class="badge bg-danger text-white">Inactief</span>'
	  },
	  { 
		headerName: "News", 
		field: "showNews",
		width: 120,
		cellRenderer: params => params.value == 1 
		  ? '<span class="badge bg-secondary text-white">Niet getoond</span>' 
		  : '<span class="badge bg-success text-white">Getoond</span>'
	  }
	];

	const gridOptions = {
		columnDefs,
		pagination: true,
		paginationPageSize: 50,
		defaultColDef: {
			resizable: true,
			sortable: true,
			filter: true,
		},
		animateRows: true,
		rowHeight: 28,

		// ➜ When clicking a row: Go to wateringDetails.php
		onRowClicked: function(event) {
			if (!event.data || !event.data.wateringId) return;

			window.location.href = "wateringDetails.php?wateringId=" + event.data.wateringId;
		}
	};

	function onFilterTextBoxChanged() {
		gridApi.setGridOption("quickFilterText", document.getElementById("filter-text-box").value,
		);
	}
	
	function refreshAll() {
		fetch("<?php echo $dataFile ?>")
			  .then((response) => response.json())
			  .then((data) => gridApi.setGridOption("rowData", data));
		}

	gridApi = agGrid.createGrid(document.querySelector("#userGrid"), gridOptions);

	// Fetch Remote Data
	fetch("<?php echo $dataFile ?>")
	  .then((response) => response.json())
	  .then((data) => gridApi.setGridOption("rowData", data));		
	  
	// Open modal en vul data in
	function editUser(userId) {
	  // Zoek de gebruiker in de grid data
		fetch('selects/getUserData.php?userId=' + userId)
			.then(res => res.json())
			.then(user => {
				if (user.error) {
					alert(user.error);
					return;
				}

				// Vul Users-velden
				document.getElementById("edit_userId").value = user.userId;
				document.getElementById("edit_userName").value = user.userName || "";
				document.getElementById("edit_email").value = user.email || "";
				document.getElementById("edit_firstName").value = user.firstName || "";
				document.getElementById("edit_lastName").value = user.lastName || "";
				document.getElementById("edit_street").value = user.street || "";
				document.getElementById("edit_houseNumber").value = user.houseNumber || "";
				document.getElementById("edit_postalCode").value = user.postalCode || "";
				document.getElementById("edit_city").value = user.city || "";
				document.getElementById("edit_wapobelDatabase").value = user.wapobelDatabase || "";
				document.getElementById("edit_useNummering").value = user.useNummering || "Nee";
				document.getElementById("edit_useKAS").value = user.useKAS || "Nee";
				document.getElementById("edit_active").value = user.active ? 1 : 0;

				// Vul Watering-velden
				document.getElementById("edit_wateringId").value = user.wateringId || "";
				document.getElementById("edit_wateringNaam").value = user.wateringNaam || "";
				document.getElementById("edit_wateringApiKey").value = user.apiKey || "";
				document.getElementById("edit_enableBillit").value = user.enableBillit ? 1 : 0;

				// Toon modal
				$('#editUserModal').modal('show');
				updateMaakWateringVisibility();
			})
			.catch(err => {
				console.error(err);
				alert("Fout bij ophalen van de gebruiker");
			});
		}
		
	// Toon/verberg Maak Watering knop
	function updateMaakWateringVisibility() {
		const active = document.getElementById("edit_active").value;

		const btn = document.getElementById("btnMaakWatering");
		const btnUser = document.getElementById("btnActiveerUser");
		const divJaar = document.getElementById("divWaterJaar");

		if (active === "0") {
			btn.style.display = "block";
			btnUser.style.display = "block";
			divJaar.style.display = "block";
		} else {
			btn.style.display = "none";
			btnUser.style.display = "none";
			divJaar.style.display = "none";
		}
	}	

	// Opslaan (later koppelbaar aan PHP via AJAX)
	document.getElementById("btnSaveUser").addEventListener("click", () => {
	  const form = document.getElementById("editUserForm");
	  const formData = new FormData(form);

	  fetch('selects/updateUserData.php', {
		  method: 'POST',
		  body: formData
	  })
	  .then(res => res.json())
	  .then(resp => {
		  if (resp.success) {
			  $('#editUserModal').modal('hide');
			  refreshAll(); // herlaad de grid
		  } else {
			  console.log(resp.message);
		  }
	  })
	  .catch(err => {
		  console.error(err);
	  });
	});

	function deleteUser(userId) {
		if (!confirm("Weet je zeker dat je deze gebruiker wilt verwijderen?")) {
			return;
		}

		fetch('selects/deleteUser.php?userId=' + userId, {
			method: 'GET'
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.success) {
				refreshAll(); // herlaad de grid
			} else {
			}
		})
		.catch(err => {
			console.error(err);
			alert("Fout bij verwijderen van de gebruiker");
		});
	}

	document.getElementById("edit_active").addEventListener("change", updateMaakWateringVisibility);
	
	document.getElementById("btnMaakWatering").addEventListener("click", () => {
		const userId = document.getElementById("edit_userId").value;
		const waterJaar = document.getElementById("waterJaar").value;
		$.showLoader({ message: 'De watering wordt aangemaakt…' });

		fetch(`selects/maakWatering.php?userId=${userId}&jaar=${waterJaar}`)
			.then(res => res.json())
			.then(resp => {
				if (resp.success) {
					console.log(resp.message);
					$.hideLoader();

					// Close modal
					$('#editUserModal').modal('hide');

					// Show success message
					showSuccessMessage("De watering werd succesvol aangepast!");

					refreshAll();
				} else {
					console.log(resp.message);
					showErrorMessage(resp.message);
				}
			});
	});

	document.getElementById("btnActiveerUser").addEventListener("click", () => {
		const userId = document.getElementById("edit_userId").value;
		const waterJaar = document.getElementById("waterJaar").value;
		$.showLoader({ message: 'De gebruiker wordt aangemaakt…' });

		fetch(`selects/activeerUser.php?userId=${userId}&jaar=${waterJaar}`)
			.then(res => res.json())
			.then(resp => {
				if (resp.success) {
					console.log(resp.message);
					$.hideLoader();

					// Close modal
					$('#editUserModal').modal('hide');

					// Show success message
					showSuccessMessage("De user werd succesvol aangemaakt!");

					refreshAll();
				} else {
					console.log(resp.message);
					showErrorMessage(resp.message);
				}
			});
	});
	
	function resetNieuws() {
		$('#confirmToonNieuwsModal').modal('show');
	}
	
	document.getElementById('btnConfirmToonNieuws').addEventListener('click', function () {
		$('#confirmToonNieuwsModal').modal('hide');

		$.showLoader({ message: 'Nieuws wordt opnieuw getoond…' });

		$.ajax({
			url: 'selects/resetNews.php',
			type: 'POST',
			dataType: 'json',
			success: function (response) {
				$.hideLoader();

				if (response.success) {
					showSuccessMessage("Nieuws wordt opnieuw getoond.");
					refreshAll();
				} else {
					showErrorMessage(response.message || "Er is iets fout gegaan.");
				}
			},
			error: function () {
				$.hideLoader();
				showErrorMessage("Serverfout bij updaten van nieuws.");
			}
		});
	});
</script>
	
</body>
</html>