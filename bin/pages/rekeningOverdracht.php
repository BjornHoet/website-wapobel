<?php
include '../init.php';
global $mysqli;

$boekingDatum = $_POST['rekeningDatum'];
$datumArray = explode('/', $boekingDatum);
$dag = $datumArray[0];
$maand = $datumArray[1];
$wateringId = $wateringData['wateringId'];

$rekeningVan = $_POST['rekeningVanNr'];
$rekeningNaar = $_POST['rekeningNaarNr'];
$rekeningNaarBedrag = $_POST['rekeningNaarBedrag'];

$lastBoekingNr = getLastBoekingNr($wateringData['wateringId'], $wateringJaar);
$lastBoekingNr = $lastBoekingNr + 1;

if(isset($_POST['rekeningNaarNr']) && $_POST['rekeningNaarNr'] !== '') {
	$rekeningVanOmsc = getRekening($rekeningVan);
	$rekeningNaarOmsc = getRekening($rekeningNaar);
	$omschrijving = 'Rek. ' . $rekeningVanOmsc['rekening'] . ' à ' . $rekeningNaarOmsc['rekening'];
	
	$boeking = addBoeking($wateringId, $wateringJaar, $maand, $dag, '0', '0', $omschrijving, $lastBoekingNr, 0);
	addBoekingRekBedrag($boeking, $rekeningVanOmsc['rekeningId'], 'U', $rekeningNaarBedrag);
	addBoekingRekBedrag($boeking, $rekeningNaarOmsc['rekeningId'], 'O', $rekeningNaarBedrag);
	}

//header("Location: ../../");
$response = writeBoekingen($_SESSION['wateringId'], $_SESSION['wateringJaar'], $_SESSION['wateringMaand'], $useNummering, $sortering);

exit();
?>