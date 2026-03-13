<?php
$prefix = '../';
$activeDagboek = '';
$activePost = '';
$activeRekening = '';

include $prefix.'/bin/init.php';
$pageTitle = 'Contact';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}
?>
<!DOCTYPE html>
<html lang="en">

<?php include $prefix.'includes/head.php';?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

		<?php include $prefix.'includes/sidebar.php';?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

				<?php include $prefix.'includes/topbar.php';?>

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Body -->
                                <div class="card-body">
									<div class="row">

									  <!-- Linker kolom: info -->
										<div class="col-md-6 p-4">
										  <h5 class="text-primary font-weight-bold mb-3">
											Contacteer Wapobel
										  </h5>

										  <p class="text-dark mb-3">
											Heb je een vraag of een probleem met het online programma?
											Stuur gerust een e-mail. We proberen je zo snel mogelijk te contacteren.
											Voor dringende problemen mag je altijd bellen.
										  </p>

										  <p class="mb-3">
											<ul>
											    <li><strong>Mail</strong>: <a href="mailto:info@wapobel.be?subject=Wapobel - Ik heb een vraag of een probleem">info@wapobel.be</a></li>
											    <li><strong>Telefoon</strong>: 0498/61.31.81</li>
											</ul>
										  </p>

										  <p class="text-dark mb-4">
											Je kan ook het contactformulier hiernaast invullen.
											Zo beschikken we meteen over alle nodige gegevens.
										  </p>

										  <div class="d-flex align-items-center">
											<img class="logo-sm mr-2" src="<?php echo $prefix ?>img/logo-horizontal.png">
											<span class="text-muted">v2.5</span>
										  </div>
										</div>


									  <!-- Rechter kolom: formulier -->
										<div class="col-md-6 p-4">
										  <h5 class="text-primary font-weight-bold mb-3">
											Contactformulier
										  </h5>

										  <form id="contactForm">
											<div class="mb-4">
											  <div><strong>Gebruiker:</strong> <?php echo $userName ?></div>
											  <div><strong>Naam:</strong> <?php echo $firstName ?> <?php echo $lastName ?></div>
											</div>

											<div class="form-group row">
											  <label class="col-sm-3 col-form-label font-weight-bold">
												Type melding
											  </label>
											  <div class="col-sm-6">
												<select class="form-control" name="contactType" required>
												  <option value="">Kies type</option>
												  <option value="vraag">Vraag of probleem</option>
												  <option value="suggestie">Suggestie ter verbetering</option>
												</select>
											  </div>
											</div>

											<div class="form-group row">
											  <label class="col-sm-3 col-form-label font-weight-bold">
												Toelichting
											  </label>
											  <div class="col-sm-6">
												<textarea id="userMessage" name="contactMessage" rows="4" class="form-control" placeholder="Geef hier extra uitleg..." required></textarea>
											  </div>
											</div>

											<button type="submit" class="btn btn-primary px-4">
											  Verzenden
											</button>

											<div class="mt-3" id="success"></div>
										  </form>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

			<?php include $prefix.'includes/footer.php';?>       
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

	<?php include $prefix.'includes/modals.php';?>

	<?php include $prefix.'includes/scripts.php';?>

	<script>
		$("#selectWatering").change(function() {
			var wateringId = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeWatering.php", { wateringId: wateringId }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});			

		$("#selectJaar").change(function() {
			var jaar = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeJaar.php", { jaar: jaar }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});	

		$("#selectMaand").change(function() {
			var maand = $(this).val();
			$.post("<?php echo($prefix);?>bin/selects/changeMaand.php", { maand: maand }, function(response) {
				// Log the response to the console
				});
			location.reload();
		});	

	$(function() {
    $("#contactForm input,#contactForm textarea").jqBootstrapValidation({
        preventSubmit: true,
        submitError: function($form, event, errors) {
            // additional error messages or events
        },
        submitSuccess: function($form, event) {
            event.preventDefault(); // prevent default submit behaviour
            // get values from FORM
            var message = $("textarea#userMessage").val();
			var type = $("select[name='contactType']").val();
            $.ajax({
                url: "../bin/pages/contactMe.php",
                type: "POST",
                data: {
                        contactType: type,
						contactMessage: message
                },
                cache: false,
                success: function(data) {
					if (data != '') {
						console.log(data);
                    // Fail message
                    $('#success').html("<div class='alert alert-danger'>");
                    $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");
                    $('#success > .alert-danger').append("<strong>Er is iets misgegaan bij het versturen van het bericht. Probleer later opnieuw of stuur een e-mail.");
                    $('#success > .alert-danger').append('</div>');					
					}
					else {
                    // Success message
                    $('#success').html("<div class='alert alert-success'>");
                    $('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");
                    $('#success > .alert-success')
                        .append("<strong>Je bericht is verzonden. We behandelen dit zo snel mogelijk. </strong>");
                    $('#success > .alert-success')
                        .append('</div>');

                    //clear all fields
                    $('#contactForm').trigger("reset");
					}
                },
                error: function(data) {
                    // Fail message
                    $('#success').html("<div class='alert alert-danger'>");
                    $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                        .append("</button>");
                    $('#success > .alert-danger').append("<strong>Er is iets misgegaan bij het versturen van het bericht. Probleer later opnieuw of stuur een e-mail.");
                    $('#success > .alert-danger').append('</div>');
                    //clear all fields
                    $('#contactForm').trigger("reset");
                },
            })
        },
			filter: function() {
				return $(this).is(":visible");
			},
		});

		$("a[data-toggle=\"tab\"]").click(function(e) {
			e.preventDefault();
			$(this).tab("show");
		});
	});


	/*When clicking on Full hide fail/success boxes */
	$('#name').focus(function() {
		$('#success').html('');
	});
	
	</script>
</body>
</html>