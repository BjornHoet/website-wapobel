<?php
include '../../bin/init.php';

$pageTitle = 'Wachtwoord opnieuw instellen';
$prefix = '../../';

if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"]=="reset") && !isset($_POST["action"])) {
  $key = $_GET["key"];
  $email = $_GET["email"];
  $curDate = date("Y-m-d H:i:s");

  $row = checkResetKey($email, $key);
  $error .= '<p>De link die je gebruikt is niet correct. Ofwel is de link niet meer geldig, ofwel heb je deze code al gebruikt om je wachtwoord opnieuw in te stellen.';
  $error .= 'Indien je opnieuw je wachtwoord wenst in te stellen, klik dan op onderstaande knop.</p><p><a href="forgot-password.php" class="btn btn-primary btn-user btn-block">Wachtwoord vergeten</a></p>';
  if ($row==""){
	} 
  else {
    $expDate = $row['expDate'];
	if ($expDate >= $curDate) 
		$error = ''; 
		}
	}
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
										<h1 class="h4 text-gray-900 mb-2">Wachtwoord opnieuw instellen</h1>
										<p class="mb-4"></p>
									</div>
									<?php if ($error !== '') { ?>
										<div class="loginErrors small">
											<?php 
												echo $error;
											?>
										</div>
									<?php } else { ?>
										<form class="user" action="changepassword.php" autocomplete="on" method="post" role="form" onsubmit="return validation()">
											<div class="form-group">
												<input type="hidden" name="userEmail" id="userEmail" autocomplete="username" value="<?php echo $email;?>">
												<input type="hidden" name="tempkey" id="tempkey" value="<?php echo $key;?>">
												
												<div class="password-wrapper">
													<input type="password" name="userPassword" autocomplete="new-password" class="form-control form-control-user" id="inputPasswordLogin" placeholder="Nieuw wachtwoord" required>
													<span toggle="#inputPasswordLogin" class="fa fa-fw fa-eye field-icon toggle-password" style="display: none;"></span>
												</div>
											</div>
											<div class="form-group">
												<div class="password-wrapper">
													<input type="password" name="checkUserPassword" class="form-control form-control-user" id="inputCheckPassword" placeholder="Herhaal nieuw wachtwoord" onChange="validation();" required>
													<span toggle="#inputCheckPassword" class="fa fa-fw fa-eye field-icon toggle-passwordCheck" style="display: none;"></span>
												</div>
											</div>
											<div class="mt-3">
												<div id="passwordFeedback" class="alert text-center font-weight-bold py-2 px-3 d-none" role="alert"></div>
											</div>

											<div class="text-center">
												<input class="btn btn-primary btn-user px-5" type="submit" value="Nieuw wachtwoord instellen" id="profileSubmit" disabled>
											</div>
										</form>
									<?php } ?>
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