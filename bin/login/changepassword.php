<?php
include '../init.php';

if (empty($_POST) === false) {
	$userEmail = $_POST['userEmail'];
	$userPassword = $_POST['userPassword'];
	$key = $_POST['tempkey'];

	if (empty($userEmail) === true || empty($userPassword) === true) {
			$errors[] = 'Gelieve een gebruiker en wachtwoord in te geven';
			} else {
				if (strlen($userPassword) > 32) {
					$errors[] = 'Paswoord is te lang';
					}
	
				$result = changePassword($userEmail, $userPassword);
				
				if ($result === false) {
					$errors[] = 'Er is iets fout gelopen bij het wijzigen van je wachtwoord. Probeer het opnieuw.';
					outputErrors($errors);
					header('Location: index.php');
					exit();
					}
				else {
					$result = deleteTempKey($key);
					
					$errors[] = 'Wachtwoord is gewijzigd? Je kan met je nieuwe wachtwoord inloggen';
					outputErrors($errors);
					header('Location: index.php');
					exit();
					}
				} 
		outputErrors($errors);				
		header('Location: index.php');
		exit();	
		}
	else {
		outputErrors($errors);
		header('Location: index.php');
		exit();		
	}
?>