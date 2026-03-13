    <!-- Logout Modal-->
	<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content border-0 shadow-lg">

				<div class="modal-header border-bottom-0 pb-0">
					<h5 class="modal-title text-dark font-weight-bold" id="logoutModalLabel">
						Afmelden bevestigen
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body text-muted">
					Weet je zeker dat je wil afmelden?  
					Klik <strong>Afmelden</strong> om deze sessie te beëindigen.
				</div>

				<div class="modal-footer border-top-0 pt-0">
					<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
						Annuleren
					</button>

					<a href="<?php echo $prefix ?>bin/login/logout.php"
					   class="btn btn-danger">
						Afmelden
					</a>
				</div>

			</div>
		</div>
	</div>

	<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content border-0 shadow-lg">

				<div class="modal-header border-bottom-0 pb-0">
					<h5 class="modal-title font-weight-bold text-dark" id="profileModalLabel">
						Wijzig je profiel
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<form class="user" action="<?php echo $prefix ?>bin/pages/changeProfile.php" method="post" role="form">
					<div class="modal-body">

						<!-- Gebruiker -->
						<div class="bg-light rounded border p-3 mb-4">
							<h6 class="font-weight-bold text-dark mb-3">Gebruiker</h6>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label" for="userName">Gebruikersnaam</label>
								<div class="col-sm-8">
									<input type="text" readonly
										   class="form-control"
										   id="userName"
										   name="userName"
										   value="<?php echo $userName ?>">
									<input type="hidden" name="userEmail" value="<?php echo $email ?>">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label" for="inputFirstName">Voornaam</label>
								<div class="col-sm-8">
									<input type="text"
										   class="form-control"
										   id="inputFirstName"
										   name="userFirstName"
										   value="<?php echo $firstName ?>"
										   required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label" for="inputLastName">Achternaam</label>
								<div class="col-sm-8">
									<input type="text"
										   class="form-control"
										   id="inputLastName"
										   name="userLastName"
										   value="<?php echo $lastName ?>"
										   required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Gebruik KAS</label>
								<div class="col-sm-8 d-flex align-items-center">
									<input type="checkbox"
										   id="inputKAS"
										   name="useKAS"
										   value="X"
										   <?php echo $useKAS === 'X' ? 'checked' : '' ?>
										   data-toggle="toggle"
										   data-on="I"
										   data-off="O"
										   data-size="xs">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Gebruik factuurnummers</label>
								<div class="col-sm-8 d-flex align-items-center">
									<input type="checkbox"
										   id="inputNummering"
										   name="useNummering"
										   value="X"
										   <?php echo $useNummering === 'X' ? 'checked' : '' ?>
										   data-toggle="toggle"
										   data-on="I"
										   data-off="O"
										   data-size="xs"
										   onchange="valueChanged()">
								</div>
							</div>

							<div id="inputSortering"
								 class="form-group row"
								 <?php echo $useNummering === '' ? 'style="display:none;"' : '' ?>>
								<label class="col-sm-4 col-form-label">Sorteer op</label>
								<div class="col-sm-8">
									<select id="selectSortering"
											name="userSortering"
											class="form-control">
										<option value="0" <?php echo $sortering == '0' ? 'selected' : '' ?>>Op boekingsdatum</option>
										<option value="1" <?php echo $sortering == '1' ? 'selected' : '' ?>>Op factuurnummer</option>
									</select>
								</div>
							</div>
						</div>

						<!-- Billit -->
						<div class="bg-light rounded border p-3 mb-4">
							<h6 class="font-weight-bold text-dark mb-3">Billit</h6>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label" for="inputBillit">Integreer Billit</label>
								<div class="col-sm-8 d-flex align-items-center">
									<input type="checkbox"
										   id="inputBillit"
										   name="useBillit"
										   value="X"
										   <?php echo $wateringData['enableBillit'] ? 'checked' : '' ?>
										   data-toggle="toggle"
										   data-on="I"
										   data-off="O"
										   data-size="xs"
										   onchange="billitValueChanged()">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label" for="inputBillitAPIKey">Billit API key</label>
								<div class="col-sm-8">
									<input type="text"
										   class="form-control"
										   id="inputBillitAPIKey"
										   name="billitAPIKey"
										   autocomplete="api_key"
										   placeholder="API key"
										   value="<?php echo $wateringData['apiKey'] ?>">
								</div>
							</div>
						</div>

						<!-- Wachtwoord -->
						<div class="bg-light rounded border p-3 mb-4">
							<h6 class="font-weight-bold text-dark mb-3">Wachtwoord</h6>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label" for="inputPasswordLogin">Nieuw wachtwoord</label>
								<div class="col-sm-8">
									<div class="password-wrapper">
										<input type="password"
											   class="form-control"
											   id="inputPasswordLogin"
											   name="userPassword"
											   placeholder="Nieuw wachtwoord">
										<span toggle="#inputPasswordLogin"
											  class="fa fa-fw fa-eye field-icon toggle-password"
											  style="display:none;"></span>
									</div>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-4 col-form-label" for="inputCheckPassword">Herhaal wachtwoord</label>
								<div class="col-sm-8">
									<div class="password-wrapper">
										<input type="password"
											   class="form-control"
											   id="inputCheckPassword"
											   name="checkUserPassword"
											   placeholder="Herhaal wachtwoord"
											   onChange="validation();">
										<span toggle="#inputCheckPassword"
											  class="fa fa-fw fa-eye field-icon toggle-passwordCheck"
											  style="display:none;"></span>
									</div>
									<div class="mt-3">
										<div id="passwordFeedback"
											 class="alert text-center font-weight-bold py-2 px-3 d-none"
											 role="alert"></div>
									</div>
								</div>
							</div>
						</div>

					</div>

					<div class="modal-footer border-top-0 pt-0">
						<button type="submit" class="btn btn-primary">Opslaan</button>
						<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuleren</button>
					</div>

				</form>

			</div>
		</div>
	</div>