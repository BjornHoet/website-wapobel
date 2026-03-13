<!-- Logout Modal -->
	<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h5 class="modal-title text-danger font-weight-bold" id="logoutLabel">
			  Ben je zeker dat je wil afmelden?
			</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <!-- Body -->
		  <div class="modal-body text-center py-4">
			<p class="mb-0">
    			Selecteer <strong>Afmelden</strong> om deze sessie te sluiten.
			</p>
		  </div>

		  <!-- Footer -->
		  <div class="modal-footer bg-light justify-content-center">
			<button type="button"
					class="btn btn-secondary btn-user btn-size"
					data-dismiss="modal">
			  Annuleren
			</button>
			<a href="<?php echo $prefix ?>bin/login/logout.php"
			   class="btn btn-danger btn-user btn-size">
			  Afmelden
			</a>
		  </div>

		</div>
	  </div>
	</div>


    <!-- Profile Modal-->
	<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileLabel" aria-hidden="true">
	  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
		<div class="modal-content shadow-lg">

		  <!-- Header -->
		  <div class="modal-header bg-light border-bottom">
			<h4 class="modal-title text-primary font-weight-bold" id="profileLabel">
			  Wijzig je profiel
			</h4>
			<button type="button" class="close" data-dismiss="modal" aria-label="Sluiten">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>

		  <form action="<?php echo $prefix ?>bin/pages/changeProfile.php"
				method="post"
				role="form">

			<div class="modal-body">

			  <!-- Gebruiker -->
			  <div class="card mb-3">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Gebruiker</h6>
				</div>
				<div class="card-body">
				  <div class="form-group row mb-2">
					<label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold">
					  Gebruikersnaam
					</label>
					<div class="col-sm-4">
					  <input type="text"
							 name="userName"
							 id="userName"
							 class="form-control form-control-sm"
							 value="<?php echo $userName ?>"
							 readonly>
					  <input type="hidden"
							 name="userEmail"
							 value="<?php echo $email ?>">
					</div>
				  </div>

					<div class="form-group row mb-1">
					  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold">Voornaam</label>
					  <div class="col-sm-4">
						<input type="text"
							   name="userFirstName"
							   class="form-control form-control-sm"
							   maxlength="50"
							   value="<?php echo $firstName ?>"
							   required>
					  </div>

					  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold">Achternaam</label>
					  <div class="col-sm-4">
						<input type="text"
							   name="userLastName"
							   class="form-control form-control-sm"
							   maxlength="50"
							   value="<?php echo $lastName ?>"
							   required>
					  </div>
					</div>
				</div>
			  </div>

			  <!-- Instellingen -->
			  <div class="card mb-3">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Instellingen</h6>
				</div>
				<div class="card-body">

				<div class="form-group row mb-1">
				  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold" for="inputKAS">
					Gebruik KAS
				  </label>
				  <div class="col-sm-1 d-flex align-items-center">
					<div class="custom-control custom-switch">
					  <input type="checkbox"
							 class="custom-control-input"
							 id="inputKAS"
							 name="useKAS"
							 <?php echo ($useKAS === 'X' ? 'checked' : '') ?>
							 value="X">
					  <label class="custom-control-label" for="inputKAS"></label>
					</div>
				  </div>
				</div>

				<div class="form-group row mb-1">
				  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold" for="inputNummering">
					Gebruik factuurnrs
				  </label>
				  <div class="col-sm-1 d-flex align-items-center">
					<div class="custom-control custom-switch">
					  <input type="checkbox"
							 class="custom-control-input"
							 id="inputNummering"
							 name="useNummering"
							 <?php echo ($useNummering === 'X' ? 'checked' : '') ?>
							 value="X">
					  <label class="custom-control-label" for="inputNummering"></label>
					</div>
				  </div>

					<label class="col-sm-1 offset-sm-1 col-form-label col-form-label-sm font-weight-bold">
					  Prefix
					</label>
					<div class="col-sm-2">
					  <input type="text"
							 name="userNummeringPrefix"
							 id="inputNummeringPrefix"
							 class="form-control form-control-sm"
							 maxlength="10"
							 pattern="^.*[^0-9]$"
							 title="Het laatste karakter mag geen cijfer zijn."
							 value="<?php echo $nummeringPrefix ?>">
					</div>

					<!-- <label class="col-sm-2 offset-sm-1 col-form-label col-form-label-sm font-weight-bold">
					  Sorteer op
					</label>
					<div class="col-sm-2">
					  <select id="selectSortering"
							  name="userSortering"
							  class="form-control form-control-sm">
						<option value="0" <?php echo ($sortering === '0' ? 'selected' : '') ?>>
						  Op boekingsdatum
						</option>
						<option value="1" <?php echo ($sortering === '1' ? 'selected' : '') ?>>
						  Op factuurnummer
						</option>
					  </select>
					</div> -->
				  </div>
				</div>
			  </div>

			  <!-- Billit -->
			  <div class="card mb-3">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Billit integratie</h6>
				</div>
				<div class="card-body">

				  <div class="form-group row mb-1">
					  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold" for="inputBillit">
						Gebruik Billit
					  </label>
					  <div class="col-sm-1 d-flex align-items-center">
						<div class="custom-control custom-switch">
						  <input type="checkbox"
								 class="custom-control-input"
								 id="inputBillit"
								 name="useBillit"
								 <?php echo ($wateringData['enableBillit'] ? 'checked' : '') ?>
								 value="X"
								 onchange="billitValueChanged()">
						  <label class="custom-control-label" for="inputBillit"></label>
						</div>
					  </div>

					<label class="col-sm-1 offset-sm-1 col-form-label col-form-label-sm font-weight-bold">
					  API key
					</label>
					<div class="col-sm-6">
					  <input type="text"
							 id="inputBillitAPIKey"
							 name="billitAPIKey"
							 class="form-control form-control-sm"
							 autocomplete="api_key"
							 value="<?php echo $wateringData['apiKey'] ?>">
					</div>
				  </div>

				<div class="form-group row mb-1">
				  <label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold" for="inputShowBillit">
					Toon Billitnummering
				  </label>
				  <div class="col-sm-1 d-flex align-items-center">
					<div class="custom-control custom-switch">
					  <input type="checkbox"
							 class="custom-control-input"
							 id="inputShowBillit"
							 name="showBillit"
							 <?php echo ($showBillit === 'X' ? 'checked' : '') ?>
							 value="X">
					  <label class="custom-control-label" for="inputShowBillit"></label>
					</div>
				  </div>
				</div>

				</div>
			  </div>

			  <!-- Wachtwoord -->
			  <div class="card">
				<div class="card-header bg-light">
				  <h6 class="mb-0 font-weight-bold text-gray-900">Wachtwoord</h6>
				</div>
				<div class="card-body">

				  <div class="form-group row mb-1">
					<label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold">
					  Nieuw wachtwoord
					</label>
					<div class="col-sm-4">
						<div class="password-wrapper">
						  <input type="password"
								 name="userPassword"
								 id="inputPasswordLogin"
								 class="form-control form-control-sm"
								 placeholder="Nieuw wachtwoord"
								 onChange="validation();">
						  <span toggle="#inputPasswordLogin" class="fa fa-fw fa-eye field-icon toggle-password" style="display: none;"></span>
						</div>
					</div>

					<label class="col-sm-2 col-form-label col-form-label-sm font-weight-bold">
					  Herhaal wachtwoord
					</label>
					<div class="col-sm-4">
						<div class="password-wrapper">
						  <input type="password"
								 name="checkUserPassword"
								 id="inputCheckPassword"
								 class="form-control form-control-sm"
								 placeholder="Herhaal wachtwoord"
								 onChange="validation();">
						  <span toggle="#inputCheckPassword" class="fa fa-fw fa-eye field-icon toggle-passwordCheck" style="display: none;"></span>
						</div>
					</div>
					
					<div class="col-sm-6 offset-sm-3 mt-2">
					  <div id="passwordFeedback" class="alert text-center font-weight-bold py-2 px-3 d-none">
					  </div>
					</div>				
				  </div>

				</div>
			  </div>
			</div>

			<!-- Footer -->
			<div class="modal-footer bg-light">
			  <button type="submit" id="profileSubmit" class="btn btn-primary">Opslaan</button>
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">
				Annuleren
			  </button>
			</div>

		  </form>
		</div>
	  </div>
	</div>