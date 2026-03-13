<?php
$prefix = '../';
$activeDagboek = '';
$activePost = 'active';
$activeRekening = '';

include $prefix.'/bin/init.php';
$pageTitle = 'Beheer van posten';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

$hoofdPostenOpbrengsten = getHoofdPostenSub('GO', $wateringJaar);
$titelGO = getHoofdPostType('GO', $wateringJaar);
$hoofdPostenBO = getHoofdPostenSub('BO', $wateringJaar);
$titelBO = getHoofdPostType('BO', $wateringJaar);
$hoofdPostenUitgaven = getHoofdPostenSub('GU', $wateringJaar);
$titelGU = getHoofdPostType('GU', $wateringJaar);
$hoofdPostenBU = getHoofdPostenSub('BU', $wateringJaar);
$titelBU = getHoofdPostType('BU', $wateringJaar);
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
					<?php if ($boekjaarOpen === false) { ?>
						<div class="d-sm-flex align-items-center justify-content-center mb-1">
							<div class="alert alert-warning text-center font-weight-bold py-2 px-3">
								<i class="fas fa-lock mr-2"></i>
								Dit boekjaar is afgesloten. Aanpassingen zijn niet meer mogelijk.
								<i class="fas fa-lock ml-2"></i>
							</div>
						</div>
					<?php } ?>

<div class="row">
    <div class="col-xl-12">
        <div class="card shadow mb-4">
            <div class="card-body">

                <!-- Filter / Toggle -->
                <div class="d-flex align-items-center mb-4 p-3 bg-light border rounded">
                    <label for="showOnlyActive" class="mb-0 mr-3 font-weight-bold text-dark">
                        Toon enkel actieve posten
                    </label>

                    <input type="checkbox"
                           id="showOnlyActive"
                           value="X"
                           data-toggle="toggle"
                           data-on="I"
                           data-off="O"
                           data-size="xs">
                </div>

                <!-- GEWONE (Opbrengsten / Uitgaven) -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <?php
                            $totaalRamingO = 0;
                            $totaalBedragO = 0;
                            $titleHeader = $titelGO;
                            $hoofdPosten = $hoofdPostenOpbrengsten;
                            $type = 'GO';
                        ?>
                        <?php include $prefix . 'includes/posten_new.php'; ?>
                    </div>

                    <div class="col-md-6 mb-4">
                        <?php
                            $titleHeader = $titelGU;
                            $hoofdPosten = $hoofdPostenUitgaven;
                            $type = 'GU';
                        ?>
                        <?php include $prefix . 'includes/posten_new.php'; ?>
                    </div>
                </div>

                <!-- BUITENGEWONE (optioneel) -->
                <?php if ($hoofdPostenBO && $hoofdPostenBO->num_rows > 0): ?>
                    <div class="row mt-5">
                        <div class="col-md-6 mb-4">
                            <?php
                                $titleHeader = $titelBO;
                                $hoofdPosten = $hoofdPostenBO;
                                $type = 'BO';
                            ?>
                            <?php include $prefix . 'includes/posten_new.php'; ?>
                        </div>

                        <div class="col-md-6 mb-4">
                            <?php
                                $titleHeader = $titelBU;
                                $hoofdPosten = $hoofdPostenBU;
                                $type = 'BU';
                            ?>
                            <?php include $prefix . 'includes/posten_new.php'; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- TOTAAL overzicht per kolom -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <?php
                            // maak include-bestand of inline: totaal opbrengsten
                            // Dit kan je ook in een include plaatsen; ik laat het hier inline voor eenvoud
                        ?>
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 font-weight-bold">TOTAAL</h6>
                                <div class="text-right">
                                    <div class="small text-muted">Begroting</div>
                                    <div class="font-weight-bold"><?php echo currencyConv($totaalRamingO); ?></div>
                                </div>
                                <div class="text-right ml-3">
                                    <div class="small text-muted">Bedrag</div>
                                    <div class="font-weight-bold"><?php echo currencyConv($totaalBedragO); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 font-weight-bold">TOTAAL</h6>
                                <div class="text-right">
                                    <div class="small text-muted">Begroting</div>
                                    <div class="font-weight-bold"><?php echo currencyConv($totaalRamingU); ?></div>
                                </div>
                                <div class="text-right ml-3">
                                    <div class="small text-muted">Bedrag</div>
                                    <div class="font-weight-bold"><?php echo currencyConv($totaalBedragU); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SALDO kaart -->
                <div class="row mt-5">
                    <div class="col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
                        <div class="card mb-1">
                            <div class="card-body">
                                <?php
                                    // haal reserve op (zoals jij deed)
                                    $ramingReserve = getReserve($wateringData['wateringId'], $wateringJaar);
                                ?>
                                <h6 class="font-weight-bold mb-3">SALDO</h6>

                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:50%"></th>
                                                <th class="text-right" style="width:25%">Begroting</th>
                                                <th class="text-right" style="width:25%">Bedrag</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-weight-bold">Totaal ontvangsten</td>
                                                <td class="text-right"><?php echo currencyConv($totaalRamingO); ?></td>
                                                <td class="text-right"><?php echo currencyConv($totaalBedragO); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="font-weight-bold">Totaal uitgaven</td>
                                                <td class="text-right"><?php echo currencyConv($totaalRamingU); ?></td>
                                                <td class="text-right"><?php echo currencyConv($totaalBedragU); ?></td>
                                            </tr>

                                            <?php
                                                $deltaRaming = round($totaalRamingO - $totaalRamingU, 2);
                                                $deltaBedrag = round($totaalBedragO - $totaalBedragU, 2);
                                            ?>
                                            <tr class="border-top">
                                                <td class="font-weight-bold"></td>
                                                <td class="text-right font-weight-bold <?php echo $deltaRaming >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo currencyConv($deltaRaming); ?>
                                                </td>
                                                <td class="text-right font-weight-bold <?php echo $deltaBedrag >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo currencyConv($deltaBedrag); ?>
                                                </td>
                                            </tr>

                                            <tr><td colspan="3">&nbsp;</td></tr>

                                            <tr>
                                                <td class="font-weight-bold">Reservefonds dd - 1 januari</td>
                                                <td class="text-right">
                                                    <div class="d-inline-block mr-2">
                                                        <a href="#" title="Reserve wijzigen" data-toggle="modal" data-target="#reserveModal">
                                                            <i class="fas fa-pencil-alt fa-sm fa-fw text-warning"></i>
                                                        </a>
                                                    </div>
                                                    <?php echo currencyConv($ramingReserve); ?>
                                                </td>
                                                <td>&nbsp;</td>
                                            </tr>

                                            <tr>
                                                <td class="font-weight-bold">Reservefonds dd - 31 december</td>
                                                <td class="text-right">
                                                    <?php echo currencyConv($ramingReserve + $totaalBedragO - $totaalBedragU); ?>
                                                </td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- card-body -->
        </div> <!-- card -->
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

    <!-- Reserve modal -->
    <div class="modal fade" id="reserveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-primary" id="exampleModalLabel">Wijzig reserve</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
				<form class="user" action="../bin/pages/changeReserve.php" method="post" role="form">
					<div class="modal-body">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="form-group row">
									<div class="col-sm-3 mt-2">
										<label for="inputReserve" class="control-label">Reserve</label>
									</div>
									<div class="col-sm-9">
										<input type="text" name="reserveBedrag" value="<?php echo $ramingReserve ?>" class="form-control form-control-user" id="inputReserve" required>
									</div>
								</div>
							</div>
						</div>					
					</div>
					<div class="modal-footer">
						<input class="btn btn-primary btn-user btn-size" type="submit" value="Opslaan" id="reserveSubmit">
						<input class="btn btn-secondary btn-user btn-size" data-dismiss="modal" value="Annuleren" id="reserveCancel">
					</div>
				</form>
            </div>
        </div>
    </div>	

	<?php include $prefix.'includes/modals.php';?>

	<?php include $prefix.'includes/scripts.php';?>
	
	<?php include $prefix.'includes/scriptsVariables.php';?>
	<?php include $prefix.'includes/scriptsGeneral.php';?>

	<script type="text/javascript">
$(document).ready(function () {

    function applyFilter() {
        if ($("#showOnlyActive").prop("checked")) {
            $("tr.table-row-inactive").fadeOut(300);
        } else {
            $("tr.table-row-inactive").fadeIn(300);
        }
    }

    applyFilter();

    $("#showOnlyActive").change(function () {
        applyFilter();
    });

});

	</script>
</body>
</html>