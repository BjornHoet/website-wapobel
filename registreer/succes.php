<?php
include '../bin/init.php';

$pageTitle = 'Registratie gelukt';
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
										<p>Je gegevens zijn succesvol doorgestuurd.</p>
										<p>We bekijken je aanvraag en van zodra je gebruiker is aangemaakt sturen we een e-mail om je wachtwoord in te stellen.</p>
										<p class="text-black">Tot snel. Het Wapobel team.</p>
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