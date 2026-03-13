<?php
include '../../bin/init.php';

$pageTitle = 'Wachtwoord vergeten';
$prefix = '../../';
?>
<!DOCTYPE html>
<html lang="en">

<?php include $prefix.'includes/head.php';?>

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
										<h1 class="h4 text-gray-900 mb-2">Wachtwoord vergeten?</h1>
										<p class="mb-4">Dit kan gebeuren!<br>
										Vul je e-mail adres en we sturen je een mail om je wachtwoord opnieuw in te stellen.</p>
									</div>
									<div class="loginErrors small">
										<?php 
											if (isset($_SESSION['outputErrors'])) {
												echo $_SESSION['outputErrors'];
												} ?>
									</div>										
                                    <form class="user" action="resetpassword.php" method="post" role="form">
                                        <div class="form-group">
                                            <input type="email" name="userEmail" class="form-control form-control-user" id="inputEmail" aria-describedby="emailHelp" placeholder="Vul je e-mail adres in..." required>
                                        </div>
										<div class="text-center">
											<input class="btn btn-primary btn-user px-5" type="submit" value="Wachtwoord opnieuw instellen" id="submit">
										</div>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="index.php">Terug naar het aanmeldscherm</a>
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

</body>
</html>