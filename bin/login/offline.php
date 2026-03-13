<?php
//include '../../bin/init.php';
session_unset();
//session_destroy();
session_write_close();
setcookie(session_name(),'',0,'/');
//session_regenerate_id(true);

$pageTitle = 'In onderhoud';
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
                                        <h1 class="h4 text-gray-900 mb-4">!ONDERHOUD!</h1>
                                    </div>
									<div class="text-center">
										<p>Momenteel zijn we Wapobel aan het updaten. Gelieve later terug te komen.</p>
										<p>Excuses voor het ongemak.</p>
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