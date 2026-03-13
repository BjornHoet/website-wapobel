<?php
include '../init.php';

if (isset($_POST['selected'])) {
    $_SESSION['invoice_filterIn'] = $_POST['selected'];
	refreshInvoices($wateringData['wateringId'], $wateringJaar);
}

exit();
?>