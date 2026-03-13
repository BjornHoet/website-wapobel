<?php
include '../init.php';

$wateringId = $wateringData['wateringId'];
$rekeningId = $_POST['rekeningId'];
$move = $_POST['move'];

if($move === 'D')
	$order = 'A';
else
	$order = 'D';
	
$rekeningen = getRekeningen($wateringId, $wateringJaar, '', 'X', $order);
$rekening = getRekening($rekeningId);

$next = '';
$positie = '';
foreach($rekeningen as $rek) {
	if($next === 'X') {
		$next = '';
		if($move === 'D') {
			$positie = $rek['positie'] - 1;
			changeRekening($rek['rekeningId'], $rek['rekening'], $rek['omschrijving'], $positie, 'X');
			}
		if($move === 'U') {
			$positie = $rek['positie'] + 1;
			changeRekening($rek['rekeningId'], $rek['rekening'], $rek['omschrijving'], $positie, 'X');
			}
		}

	if($rek['rekeningId'] === $rekeningId) {
		$next = 'X';
		if($move === 'D') {
			$positie = $rek['positie'] + 1;
			changeRekening($rek['rekeningId'], $rek['rekening'], $rek['omschrijving'], $positie, 'X');
			}
		if($move === 'U') {
			$positie = $rek['positie'] - 1;
			changeRekening($rek['rekeningId'], $rek['rekening'], $rek['omschrijving'], $positie, 'X');
			}
		}
	}
	
header("Location: ../../rekeningen/");
exit();
?>