<?php
include '../init.php';

if (empty($_POST) === false) {
	$userName = $_POST['userName'];
	$userFirstName = $_POST['userFirstName'];
	$userLastName = $_POST['userLastName'];
	$userEmail = $_POST['userEmail'];
	$userPassword = $_POST['userPassword'];
	$useNummering = $_POST['useNummering'];
	$nummeringPrefix = $_POST['userNummeringPrefix'];
	$useKAS = $_POST['useKAS'];
	$showBillit = $_POST['showBillit'];
	$sortering = $_POST['userSortering'];
	
	$useBillit = $_POST['useBillit'];
	$apiKey = $_POST['billitAPIKey'];

	if($useBillit == 'X')
		$useBillit = '1';
	else {
		$useBillit = '0';
		}
		
	$result = changeProfile($userName, $userFirstName, $userLastName, '', $useNummering, $nummeringPrefix, $useKAS, $showBillit, 0);
	$result = changeWatering($wateringData['wateringId'], $useBillit, $apiKey);
	
	if($useBillit == true) {
		refreshBillit($wateringData['wateringId']);
		}
	
	if (!empty($userPassword)) {
		$resultPass = changePassword($userEmail, $userPassword);
		header('Location: ../../index.php');
		exit();
		}
	
	if ($result === false) {
		$errors[] = 'Er is iets fout gelopen bij het wijzigen van je wachtwoord. Probeer het opnieuw.';
		outputErrors($errors);
		header('Location: ../../index.php');
		exit();
		}
	else {
		$result = deleteTempKey($key);
		
		$errors[] = 'Wachtwoord is gewijzigd? Je kan met je nieuwe wachtwoord inloggen';
		outputErrors($errors);
		header('Location: ../../index.php');
		exit();
		}
	} else {
		outputErrors($errors);
		header('Location: ../../index.php');
		exit();		
	}
?>