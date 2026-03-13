<?php
include '../bin/init.php';

$pageTitle = 'Probleem bij registratie';
$prefix = '../';
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
                                        <h1 class="h4 text-gray-900 mb-4"><?php echo $pageTitle ?></h1>
                                    </div>
								
									<div>
										<p>Er heeft zich een probleem voorgedaan bij het registreren van je gegevens. Neem contact op via e-mail zodat we de registratie manueel kunnen starten.</p>
										<p><a href="mailto:info@wapobel.be">info@wapobel.be</a></p>
										<p class="">Onze excuses voor het ongemak. Het Wapobel team.</p>
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