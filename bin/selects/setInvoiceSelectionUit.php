<?php
include '../init.php';

if (isset($_POST['selected'])) {
    $_SESSION['invoice_filterUit'] = $_POST['selected'];
	refreshInvoices($wateringData['wateringId'], $wateringJaar);
}

exit();
?>