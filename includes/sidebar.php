<?php 
  $wateringen = getWateringen($_SESSION['userId']);
  $jaren = getJaren($wateringData['wateringId']);
?>
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo $prefix ?>index.php">
                <div class="sidebar-brand-text mx-3"><img class="logo-sm" src="<?php echo $prefix ?>img/logo-horizontal-white.png" ></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">
			<h6 class="mt-3 mb-3 text-center text-white text-bold"><span id="wateringOmschrijving"><?php echo $wateringData['omschrijving'] ?></span></h6>

            <!-- Heading -->
            <div class="sidebar-heading mt-3">
                Navigatie
            </div>

            <!-- Nav Item - Dagboek -->
            <li class="nav-item <?php echo $activeDagboek ?>">
                <a class="nav-link" href="<?php echo $prefix ?>index.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Dagboek</span></a>
            </li>

            <!-- Nav Item - Posten -->
            <li class="nav-item <?php echo $activePost ?>">
                <a class="nav-link collapsed" href="<?php echo $prefix ?>posten/">
                    <i class="fas fa-fw fa-calendar"></i>
                    <span>Posten</span>
                </a>
            </li>

            <!-- Nav Item - Rekeningnummers -->
            <li class="nav-item <?php echo $activeRekening ?>">
                <a class="nav-link collapsed" href="<?php echo $prefix ?>rekeningen/">
                    <i class="fas fa-fw fa-list"></i>
                    <span>Rekeningen</span>
                </a>
            </li>

			<div id="billitEnabled">
				<!-- Divider -->
				<hr class="sidebar-divider my-0">

				<!-- Heading -->
				<div class="sidebar-heading mt-3">
					Billit
				</div>

				<!-- Nav Item - Klanten -->
				<li class="nav-item <?php echo $activeKlanten ?>">
					<a class="nav-link collapsed" href="<?php echo $prefix ?>klanten/">
						<i class="fas fa-fw fa-users"></i>
						<span>Klanten</span>
					</a>
				</li>

				<!-- Nav Item - Leveranciers -->
				<li class="nav-item <?php echo $activeLeveranciers ?>">
					<a class="nav-link collapsed" href="<?php echo $prefix ?>leveranciers/">
						<i class="fas fa-fw fa-address-book"></i>
						<span>Leveranciers</span>
					</a>
				</li>

				<!-- Nav Item - Facturen -->
				<li class="nav-item <?php echo $activeFacturen ?>">
					<a class="nav-link collapsed" href="<?php echo $prefix ?>facturen/">
						<i class="fas fa-fw fa-file-invoice"></i>
						<span>Facturen</span>
					</a>
				</li>

				<div class="sidebar-heading mb-3 mt-2 text-center">
				  <button type="button" id="verversBillit" class="btn btn-warning btn-sm">
					<i class="fas fa-redo fa-sm fa-fw"></i> Ververs Billit data
				  </button>
				</div>
			</div>
			
            <!-- Divider -->
            <hr class="sidebar-divider">

            <?php if($userData['userId'] === '1') { ?>
			<div class="sidebar-heading mb-3">
                Watering
				<select id="selectWatering" class="mt-2 form-control form-select text-xs">
				<?php foreach ($wateringen as $watering) { 
					if($watering['wateringId'] === $wateringData['wateringId']) {
						$wateringSelected = 'selected="selected"';
						}
					else {
						$wateringSelected = ''; 
						}?>
					<option value="<?php echo $watering['wateringId'] ?>" <?php echo $wateringSelected ?>><?php echo $watering['omschrijving'] ?></option>
				<?php } ?>
				</select>				
            </div>
			<?php } ?>
            <div class="sidebar-heading mb-3">
                Jaar
				<select id="selectJaar" class="mt-2 form-control form-select text-xs">
				<?php foreach ($jaren as $jaar) {	
					if($jaar['jaar'] === $wateringJaar) {				
						$jaarSelected = 'selected="selected"';
						}
					else {
						$jaarSelected = ''; 
						}?>
					<option value="<?php echo $jaar['jaar'] ?>" <?php echo $jaarSelected ?>><?php echo $jaar['jaar'] ?></option>
				<?php } ?>
				</select>					
            </div>
            <div class="sidebar-heading mb-3">
                Maand
				<select id="selectMaand" class="mt-2 form-control form-select text-xs">
					<?php for($i=1; $i<=12; $i++) { 
						$maand = $i - 1;
						if($i === (int)$wateringMaand) {
							$monthSelected = 'selected="selected"';
							} else {
							$monthSelected = '';
							} ?>
						<option value="<?php echo $i ?>" <?php echo $monthSelected ?>><?php echo monthNames[$maand]; ?></option>
					<?php } ?>
				</select>					
            </div>

            <?php if($userData['userId'] === '1') { ?>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                User Admin
            </div>
            <!-- Nav Item - Pages Collapse Menu -->
				<li class="nav-item <?php echo $activeUsers ?>">
					<a class="nav-link collapsed" href="<?php echo $prefix ?>useradmin/users.php">
						<i class="fas fa-fw fa-address-book"></i>
						<span>Gebruikersadministratie</span>
					</a>
				</li>
				<li class="nav-item <?php echo $activeAlgemeen ?>">
					<a class="nav-link collapsed" href="<?php echo $prefix ?>useradmin/algemeen.php">
						<i class="fas fa-fw fa-sliders-h"></i>
						<span>Wapobel algemeen</span>
					</a>
				</li>
				<li class="nav-item <?php echo $activeLogin ?>">
					<a class="nav-link collapsed" href="<?php echo $prefix ?>useradmin/login.php">
						<i class="fas fa-fw fa-user"></i>
						<span>Login details</span>
					</a>
				</li>


			<?php } ?>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Documenten
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
			<li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
					aria-expanded="true" aria-controls="collapsePages">
					<i class="fas fa-fw fa-folder"></i>
					<span>Documenten</span>
				</a>
				<div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
					<div class="bg-white py-2 collapse-inner rounded">

						<!-- Dagboek submenu -->
						<a class="collapse-item collapsed" href="#" data-toggle="collapse" data-target="#collapseDagboek"
							aria-expanded="true" aria-controls="collapseDagboek">
							Dagboek
						</a>
						<div id="collapseDagboek" class="collapse">
							<a class="collapse-item pl-4" href="<?php echo $prefix ?>documenten/dagboekAllxls.php" target="_blank">Download in Excel</a>
							<a class="collapse-item pl-4" href="<?php echo $prefix ?>documenten/dagboekAllpdf.php" target="_blank">Download in PDF</a>
						</div>

						<!-- Begrotingsvoorstel submenu -->
						<a class="collapse-item collapsed" href="#" data-toggle="collapse" data-target="#collapseBegrotingsvoorstel"
							aria-expanded="true" aria-controls="collapseBegrotingsvoorstel">
							Begrotingsvoorstel
						</a>
						<div id="collapseBegrotingsvoorstel" class="collapse">
							<a class="collapse-item pl-4" href="<?php echo $prefix ?>documenten/begrotingsvoorstelExcel.php" target="_blank">Download in Excel</a>
							<a class="collapse-item pl-4" href="<?php echo $prefix ?>documenten/begrotingsvoorstelPDF.php" target="_blank">Download in PDF</a>
						</div>

						<!-- Rekening submenu -->
						<a class="collapse-item collapsed" href="#" data-toggle="collapse" data-target="#collapseRekening"
							aria-expanded="true" aria-controls="collapseRekening">
							Rekening
						</a>
						<div id="collapseRekening" class="collapse">
							<a class="collapse-item pl-4" href="<?php echo $prefix ?>documenten/rekeningExcel.php" target="_blank">Download in Excel</a>
							<a class="collapse-item pl-4" href="<?php echo $prefix ?>documenten/rekeningPDF.php" target="_blank">Download in PDF</a>
						</div>

						<!-- Andere documenten -->
						<a class="collapse-item" href="<?php echo $prefix ?>documenten/jaaroverzicht.php" target="_blank">Jaaroverzicht per post</a>
						<div class="border-top-s border-size-lg"></div>
						<a class="collapse-item" href="<?php echo $prefix ?>documenten/posten.php" target="_blank">Overzicht posten</a>
						<a class="collapse-item" href="<?php echo $prefix ?>documenten/Wapobel - Gebruikershandleiding.pdf" target="_blank">Gebruikershandleiding</a>

					</div>
				</div>
			</li>


			
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
			
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->		