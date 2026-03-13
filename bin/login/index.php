<?php
//header("Location: offline.php");

include '../../bin/init.php';

$sessionExpired = '';
if(isset($_COOKIE['session_exp']) && $_COOKIE['session_exp'] === 'X') {
	$sessionExpired = 'X';
	setcookie("session_exp", "", time() - 3600, "/");
	}
else {
	$sessionExpired = '';
	}

$pageTitle = 'Aanmelden';
$prefix = '../../';
?>
<!DOCTYPE html>
<html lang="en">

<?php include '../../includes/head.php';?>

<body class="bg-gradient-light">
    <div class="container">	
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image login-height"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welkom!</h1>
                                    </div>
									<div class="loginErrors small">
										<?php 
											if (isset($_SESSION['outputErrors'])) {
												echo $_SESSION['outputErrors'];
												} 
											if ($sessionExpired === 'X') {
												echo 'De sessie is verlopen. Gelieve opnieuw aan te melden';
												} ?>
									</div>									
                                    <form class="user" action="authenticate.php" autocomplete="on" method="post" role="form">
                                        <div class="form-group">
                                            <input name="userName" autocomplete="username" class="form-control form-control-user" id="inputUserName" aria-describedby="emailHelp" placeholder="Gebruikersnaam..." required>
                                        </div>
                                        <div class="form-group">
                                            <div class="password-wrapper">
												<input type="password" autocomplete="current-password" name="userPassword" class="form-control form-control-user" id="inputPasswordLogin" placeholder="Wachtwoord" required>
												<span toggle="#inputPasswordLogin" class="fa fa-fw fa-eye field-icon toggle-password" style="display: none;"></span>
											</div>
                                        </div>

										<div class="text-center">
											<input class="btn btn-primary btn-user px-5" type="submit" value="Aanmelden" id="submit">
										</div>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.php">Wachtwoord vergeten?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

					<div class="row justify-content-center mb-5">
						<div class="col-xl-10">
							<div class="card shadow h-100 py-3">
								<div class="card-body">
									<div class="row no-gutters align-items-center">
										<div class="col mr-5">
											<h4 class="font-weight-bold text-primary mb-3">Polder of Watering en nog geen account?</h4>
											<p class="mb-3">
												Registreer je en ontdek alle voordelen van <span class="text-bold">Wapobel</span>. <span class="text-bold">Vereenvoudig je boekhouding</span> en behoud het <span class="text-bold">overzicht over je cijfers</span>.  
												Integreer met <span class="text-bold">Billit</span> zodat je je facturen direct kan inboeken in je boekhouding.
											</p>
											<p class="mb-4">
												De bedoeling van Wapobel is om dit proces makkelijk en uniform aan te bieden voor elke <span class="text-bold">Polder en Watering</span>.
											</p>

											<div class="d-flex gap-2">
												<a href="../../meer-info" class="btn btn-primary btn-user auto-w ml-3">Meer info</a>
												<a href="../../registreer" class="btn btn-success btn-user auto-w ml-4">Registreer nu</a>
											</div>
										</div>
										<div class="col-auto">
											<i class="fas fa-user-plus fa-3x text-gray-300"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>

	<?php include $prefix.'includes/scripts.php';?>

	<script src="<?php echo $prefix ?>js/cookies.js" data-cfasync="false"></script>
	<script>
		window.cookieconsent.initialise({
		  "palette": {
			"popup": {
			  "background": "#3c404d",
			  "text": "#d6d6d6"
			},
			"button": {
			  "background": "#4e73df"
			}
		  },
		  "theme": "classic",
		  "type": "opt-in",
		  "content": {
			"message": "Deze website maakt gebruik van functionele en noodzakelijke cookies. Voor meer informatie kan je het Privacybeheer lezen.",
			"dismiss": "Ok",
			"allow": "Accepteer cookies",
			"link": "Privacybeheer",
			"href": "privacy.php"
		  }
		});
	</script>
</body>
</html>