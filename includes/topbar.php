                <!-- Loader -->
				<!-- <div id="loader" class="center"></div> -->
				
				<!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">					
					<div class="d-sm-flex align-items-end justify-content-end ml-3">
                        <h1 class="h4 mb-0 text-primary text-bold"><?php echo $pageTitle ?></h1>
                    </div>

									
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $firstName ?> <?php echo $lastName ?></span>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profiel
                                </a>
                                <a class="dropdown-item" href="<?php echo $prefix ?>bin/login/privacy.php" target="_blank">
                                    <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Privacybeheer
                                </a>
                                <a class="dropdown-item" href="<?php echo $prefix ?>meer-info/algemene_voorwaarden.php" target="_blank">
                                    <i class="fas fa-file-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Algemene voorwaarden
                                </a>
                                <a class="dropdown-item" href="<?php echo $prefix ?>contact/">
                                    <i class="fas fa-envelope fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Contact
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Afmelden
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->