<?php
include '../bin/init.php';

$pageTitle = 'Registreer op Wapobel';
$prefix = '../';
?>
<!DOCTYPE html>
<html lang="en">

<?php include $prefix.'includes/head.php';?>

<body class="bg-gradient-light">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
									<div class="text-center mb-5">
										<h1 class="h4 text-gray-900 mb-2 text-bold"><?php echo $pageTitle ?></h1>
										<p class="mb-4"></p>
									</div>
								<?php if (isset($_GET['error']) && $_GET['error'] == 'email'): ?>
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										Beste gebruiker, je bent al geregistreerd met dit e-mailadres.<br>
										Na controle van je gegevens, word je account geactiveerd. Hierna zal je een e-mail krijgen om je wachtwoord in te stellen. Heb je dit al gedaan, kan je gewoon aanmelden met je e-mailadres en gekozen wachtwoord op <a href="https://www.wapobel.be">Wapobel.be</a>
										<button type="button" class="close" data-dismiss="alert" aria-label="Sluiten">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
								<?php endif; ?>
									<div class="text-bold h5 mb-5">Persoonlijke gegevens</div>
									<form class="user" action="register.php" method="post" role="form" onsubmit="return validation()">

										<div class="form-group row">
											<div class="col-sm-3 mt-2">
												<label for="inputEmail" class="control-label">E-mail / gebruikersnaam</label>
											</div>
											<div class="col-sm-6">
												<input type="email" name="userEmail" id="inputEmail" class="form-control form-control-user" pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" aria-describedby="emailHelp" required value="<?php echo isset($_GET['userEmail']) ? htmlspecialchars($_GET['userEmail']) : ''; ?>" maxlength="132">
												<input type="hidden" name="wateringJaar" id="inputWateringJaar" value="2026">
											</div>
										</div>

										<div class="form-group row">
											<div class="col-sm-3 mt-2">
												<label for="inputFirstName" class="control-label">Voornaam</label>
											</div>
											<div class="col-sm-4">
												<input type="text" name="userFirstName" id="inputFirstName" class="form-control form-control-user" required value="<?php echo isset($_GET['userFirstName']) ? htmlspecialchars($_GET['userFirstName']) : ''; ?>" maxlength="50">
											</div>
										</div>

										<div class="form-group row">
											<div class="col-sm-3 mt-2">
												<label for="inputLastName" class="control-label">Achternaam</label>
											</div>
											<div class="col-sm-4">
												<input type="text" name="userLastName" id="inputLastName" class="form-control form-control-user" required value="<?php echo isset($_GET['userLastName']) ? htmlspecialchars($_GET['userLastName']) : ''; ?>" maxlength="50">
											</div>
										</div>

										<hr class="mt-5 mb-5">

										<div class="text-bold h5 mb-5">Gegevens van de watering/polder</div>

										<div class="form-group row">
											<div class="col-sm-3 mt-2">
												<label for="inputWateringPolder" class="control-label">Naam watering/polder</label>
											</div>
											<div class="col-sm-6">
												<input type="text" name="userNameWateringPolder" id="inputWateringPolder" class="form-control form-control-user" required value="<?php echo isset($_GET['userNameWateringPolder']) ? htmlspecialchars($_GET['userNameWateringPolder']) : ''; ?>" maxlength="100">
											</div>
										</div>

										<div class="form-group row">
											<div class="col-sm-3 mt-2">
												<label for="inputStreet" class="control-label">Straat</label>
											</div>
											<div class="col-sm-4">
												<input type="text" name="userStreet" id="inputStreet" class="form-control form-control-user" required value="<?php echo isset($_GET['userStreet']) ? htmlspecialchars($_GET['userStreet']) : ''; ?>" maxlength="80">
											</div>

											<div class="col-sm-2 mt-2">
												<label for="inputHouseNumber" class="control-label">Huisnummer</label>
											</div>
											<div class="col-sm-2">
												<input type="text" name="userHouseNumber" id="inputHouseNumber" class="form-control form-control-user" required value="<?php echo isset($_GET['userHouseNumber']) ? htmlspecialchars($_GET['userHouseNumber']) : ''; ?>" maxlength="10">
											</div>
										</div>

										<div class="form-group row">
											<div class="col-sm-3 mt-2">
												<label for="inputPostalCode" class="control-label">Postcode</label>
											</div>
											<div class="col-sm-2">
												<input type="text" name="userPostalCode" id="inputPostalCode" class="form-control form-control-user" required value="<?php echo isset($_GET['userPostalCode']) ? htmlspecialchars($_GET['userPostalCode']) : ''; ?>" pattern="[0-9]{4}" maxlength="4" inputmode="numeric">
											</div>

											<div class="col-sm-2 mt-2">
												<label for="inputCity" class="control-label">Gemeente</label>
											</div>
											<div class="col-sm-4">
												<input type="text" name="userCity" id="inputCity" class="form-control form-control-user" required value="<?php echo isset($_GET['userCity']) ? htmlspecialchars($_GET['userCity']) : ''; ?>" maxlength="80">
											</div>
										</div>

										<hr class="mt-5 mb-4">
										<div class="text-bold h5 mb-2">Rekeningen watering/polder</div>
										<div class="mb-3">Je kan reeds rekeningen toevoegen die je wil gebruiken voor je dagboek. Deze kan je later ook nog steeds toevoegen en/of wijzigen in het programma.</div>
										<div class="form-group row">
											<div class="col-sm-12">
												<button type="button" class="btn btn-success" onclick="addAccount()">Voeg rekening toe</button>
											</div>
										</div>

										<!-- Container where new account rows will be added -->
										<div id="accountsContainer"></div>
										
										<hr class="mt-5 mb-4">
										<div class="row mb-4 justify-content-center">
											<div class="col-sm-12 text-center">
												<div class="form-check">
													<input type="checkbox" class="form-check-input" id="agreeCheckbox" required>
													<label class="form-check-label" for="agreeCheckbox" style="font-size: 0.9rem;">
														Door te registreren op dit platform ga je akkoord met de 
														<a href="../bin/login/privacy.php" target="_blank">Privacy Policy</a> en de <a href="../meer-info/algemene_voorwaarden.php" target="_blank">Algemene voorwaarden</a>.
													</label>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-sm-2 text-center">
												<a href="https://www.wapobel.be">
													<img src="<?php echo $prefix ?>img/logo-horizontal.png" class="pl-5 pr-3" style="max-width: 200px" />
												</a>
											</div>
											<div class="col-sm-8 text-center">
												<input class="btn btn-primary btn-user" style="font-weight: bold; font-size: 15px; min-width: 175px;" type="submit" value="Registreer" id="submit">
											</div>
											<div class="col-sm-2 text-center mt-3">
												<a href="mailto:info@wapobel.be">info@wapobel.be</a>
											</div>
										</div>
									</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

	<?php include $prefix.'includes/scripts.php';?>

<script>
let accountIndex = 0;

function addAccount() {
    accountIndex++;

    // Create a new div for the account row
    const accountRow = document.createElement("div");
    accountRow.classList.add("form-group", "row");
    accountRow.setAttribute("id", `accountRow${accountIndex}`);
    accountRow.innerHTML = `
        <div class="col-sm-3 mt-2">
            <label for="inputAccountName${accountIndex}" class="control-label">Naam rekening</label>
        </div>
        <div class="col-sm-3">
            <input type="text" name="userAccountName[]" class="form-control form-control-user" placeholder="vb: Zichtrekening Belfius" id="inputAccountName${accountIndex}" required>
        </div>
        <div class="col-sm-2 mt-2">
            <label for="inputAccountNumber${accountIndex}" class="control-label">Rekeningnummer</label>
        </div>
        <div class="col-sm-3">
            <input type="text" name="userAccount[]" 
                class="form-control form-control-user" 
                id="inputAccountNumber${accountIndex}" 
                value="BE"
                required
                pattern="^BE\\d{2}(?:\\s?\\d{4}){3}$"
                title="Voer een geldig rekeningnummer in, zoals: BE00 0000 0000 0000"
                oninput="formatIban(this)">
        </div>
        <div class="col-sm-1 d-flex align-items-center">
            <button type="button" class="btn btn-link p-0 remove-account" data-index="${accountIndex}">
                <i class="fa fa-trash mr-2 text-danger"></i>
            </button>
        </div>
    `;

    // Append the new row to the container
    document.getElementById("accountsContainer").appendChild(accountRow);

    // Add event listener for the remove button
    accountRow.querySelector(".remove-account").addEventListener("click", function (e) {
        e.preventDefault(); // Prevent any default behavior
        removeAccount(this.dataset.index);
    });
}

function removeAccount(index) {
    const row = document.getElementById(`accountRow${index}`);
    if (row) {
        row.remove();
    }
}

function validation() {
    const checkbox = document.getElementById("agreeCheckbox");
    if (!checkbox.checked) {
        alert("Je moet akkoord gaan met de Privacy Policy en de Algemene voorwaarden om te registreren.");
        return false; // Form niet verzenden
    }
    return true; // Form verzenden
}
</script>

</body>
</html>