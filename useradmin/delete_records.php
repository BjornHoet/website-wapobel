<?php
$prefix = '../';
$activeDagboek = '';
$activePost = 'active';
$activeRekening = '';

include $prefix.'/bin/init.php';
$pageTitle = 'Delete records details';

if (loggedIn() === false) {
	setcookie('session_exp', 'X', time() + (60), "/"); 
	header("Location: ".$prefix."bin/login");
	die();
	}

$records = getRecordsToDelete($wateringData['wateringId'], $wateringJaar);
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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-center mb-4">
                        <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle ?></h1>
                    </div>

                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <!-- Card Body -->
                                <div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="row mt-5 mb-3 align-items-center">
												<div class="col-md-5">
													<button class="btn btn-primary btn-sm" id="refresh">Refresh</button>
													<button class="btn btn-primary btn-sm" id="delete">Delete</button>
												</div>
												<div class="col-md-3">
												  <input type="text" class="form-control" placeholder="Search in table..." id="searchField">
												</div>
											</div>
											<div class="text-s" id="root"></div>
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
var data = [
<?php foreach($records as $record) { ?>
    {
        watering: '<?php echo $record['wateringId'] ?>',
        jaar: '<?php echo $record['jaar'] ?>',
        boekingId: '<?php echo $record['boekingId'] ?>',
        post: '<?php echo $record['postId'] ?>',
        subpost: '<?php echo $record['subPostId'] ?>',
        omschrijving: '<?php echo $record['omschrijving'] ?>',
        bedrag: '<?php echo $record['bedrag'] ?>',
    },
<?php } ?>	
]

var columns = {
    watering: 'Watering ID',
    jaar: 'Jaar',
    boekingId: 'Boeking ID',
    post: 'Post',
    subpost: 'SubPost',
    omschrijving: 'Omschrijving',
    bedrag: 'Bedrag',
}
	</script>
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
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/changeJaar.php",
				type: "post",
				data: { jaar: jaar }
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		});	
   
        var table = $('#root').tableSortable({
            data,
            columns,
            searchField: '#searchField',
            responsive: {
                1100: {
                    columns: {
                        omschrijving: 'Omschrijving',
                    },
                },
            },
            rowsPerPage: 25,
            pagination: true,
            tableWillMount: () => {
                console.log('table will mount')
            },
            tableDidMount: () => {
                console.log('table did mount')
            },
            tableWillUpdate: () => console.log('table will update'),
            tableDidUpdate: () => console.log('table did update'),
            tableWillUnmount: () => console.log('table will unmount'),
            tableDidUnmount: () => console.log('table did unmount'),
            onPaginationChange: function(nextPage, setPage) {
                setPage(nextPage);
            }
        });

        $('#changeRows').on('change', function() {
            table.updateRowsPerPage(parseInt($(this).val(), 10));
        })

        $('#rerender').click(function() {
            table.refresh(true);
        })

        $('#distory').click(function() {
            table.distroy();
        })

        $('#refresh').click(function() {
            table.refresh();
        })

        $('#delete').click(function() {
			request = $.ajax({
				url: "<?php echo($prefix);?>bin/selects/deleteRecords.php",
				type: "post"
			});
			// callback handler that will be called on success
			request.done(function (response, textStatus, jqXHR){
				// log a message to the console
				window.location.href = window.location.href;
				location.reload(true);
			});
		})
		
        $('#setPage2').click(function() {
            table.setPage(1);
        })
    </script>	
</body>
</html>