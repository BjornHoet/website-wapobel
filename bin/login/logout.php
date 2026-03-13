<?php
include '../../bin/init.php';

$pageTitle = 'Succesvol afgemeld';
$prefix = '../../';

//databaseBackup();

session_unset();
session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
session_regenerate_id(true);
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
										<h1 class="h4 text-gray-900 mb-2">Je bent succesvol afgemeld</h1>
										<p class="mb-4"></p>
									</div>
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