<?php
include '../init.php';

$boekingDatum = $_POST['boekingDatum'];
$datumArray = explode('/', $boekingDatum);
$dag = $datumArray[0];
$maand = $datumArray[1];
$wateringId = $wateringData['wateringId'];
$hoofdpostId = $_POST['boekingHoofdpost'];
$postId = $_POST['boekingPost'];
$nummering = $_POST['boekingNummering'];
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

$boeking = addBoeking($wateringId, $wateringJaar, $maand, $dag, $postId, $subpostId, $omschrijving, $nummering, 0);

//header("Location: ../../");
$response = writeBoekingen($_SESSION['wateringId'], $_SESSION['wateringJaar'], $_SESSION['wateringMaand'], $useNummering, $sortering);
exit();
?>