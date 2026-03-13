<?php
include '../init.php';

if (empty($_POST) === false) {
	$userName = $_POST['userName'];
	$userPassword = $_POST['userPassword'];
	if (empty($userName) === true || empty($userPassword) === true) {
			$errors[] = 'Gelieve een gebruiker en wachtwoord in te geven';
			} else {
			if (user_exists($userName) === false) {
				storeLogin($userName, $userPassword, '');
				$errors[] = 'Deze gebruiker bestaat niet';
			} else 
			if (user_active($userName) === false) {
				storeLogin($userName, $userPassword, '');
				$errors[] = 'Deze gebruiker is niet actief';
			} else {
				if (strlen($userPassword) > 32) {
					$errors[] = 'Paswoord is te lang';
					}
	
				$login = login($userName, $userPassword);
					if ($login === false) {
						storeLogin($userName, $userPassword, '');
						$errors[] = 'Wachtwoord is niet correct';
						}
					else {
						storeLogin($userName, $userPassword, 'X');
						$_SESSION['userId'] = $login;
						$errors[] = '';
						outputErrors($errors);

						// GET Billit data
						$userData = getUserData($_SESSION['userId']);
						$_SESSION['selectedWateringId'] = $userData['hoofdWateringId'];
						require '../database/connect.php';

						// databaseBackup();
						/* $wateringData = getWateringData($userData['hoofdWateringId']);
						
						if($wateringData['enableBillit'] == true) {
							$apiKey = $wateringData['apiKey'];
							refreshBillit($userData['hoofdWateringId']);
							} */

						header('Location: ../../index.php');
						exit();
						}
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