<?php
include '../init.php';

$wateringId = $wateringData['wateringId'];
$rekening = $_POST['rekeningNr'];
$omschrijving = $_POST['rekeningOmschrijving'];
$overdracht = $_POST['rekeningOverdracht'];
$maxPositie = getRekeningMaxPositie($wateringId, $wateringJaar);
$positie = $maxPositie + 1;
	
addRekening($wateringId, $wateringJaar, $rekening, $omschrijving, $overdracht, $positie);
	
header("Location: ../../rekeningen/");
exit();
?>