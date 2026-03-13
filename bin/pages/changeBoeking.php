<?php
include '../init.php';

$boekId = $_POST['boekingId'];
$boekingDatum = $_POST['boekingDatum'];
$datumArray = explode('/', $boekingDatum);
$dag = $datumArray[0];
$maand = $datumArray[1];
$hoofdpostId = $_POST['boekingHoofdpost'];
$postId = $_POST['boekingPost'];
$nummering = $_POST['boekingNummering'];
$billitNumber = $_POST['boekingBillitNr'];
$subpostId = '0';
$omschrijving = '';
if(isset($_POST['boekingSubpost']) && $_POST['boekingSubpost'] !== '') {
	$subpostId = $_POST['boekingSubpost'];
	$subpostData = getSubPostData($subpostId);
	$omschrijving = $subpostData['omschrijving'];
	} else {
	$postId = $_POST['boekingPost'];
	$postData = getPostData($postId);
	$omschrijving = $postData['omschrijving'];
	}

$boeking = changeBoeking($boekId, $maand, $dag, $postId, $subpostId, $nummering, $billitNumber);

$response = writeBoekingen($_SESSION['wateringId'], $_SESSION['wateringJaar'], $_SESSION['wateringMaand'], '', '');
//header("Location: ../../");
exit();
?>