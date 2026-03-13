<?php
include '../init.php';

$wateringId = $wateringData['wateringId'];
$rekeningId = $_POST['rekeningId'];
$rekening = $_POST['rekeningNr'];
$omschrijving = $_POST['rekeningOmschrijving'];
$overdracht = $_POST['rekeningOverdracht'];
//$actief = $_POST['rekeningActief'];
$actief = '';

if (isset($_POST['rekeningActief'])) {
	$actief = 'X';
} elseif (isset($_POST['rekeningActiefHidden'])) {
	$actief = $_POST['rekeningActiefHidden'];
} else {
	$actief = $rekeningOrig['actief']; // fallback
}

//$positie = $_POST['rekeningPositie'];

$rekeningOrig = getRekening($rekeningId);

if($actief !== $rekeningOrig['actief']) {
	if($actief === 'X') {
		$maxPositie = getRekeningMaxPositie($wateringId, $wateringJaar);
		$positie = $maxPositie + 1;
		} 
	else {
		$positie = 999;
		}
	}
else {
	$positie = $rekeningOrig['positie'];
}

changeRekening($rekeningId, $rekening, $omschrijving, $positie, $actief, $overdracht);
	
header("Location: ../../rekeningen/");
exit();
?>