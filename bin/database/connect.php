<?php
$connect_error = 'Sorry, er is een probleem om connectie te maken met de server';
if (loggedIn() === true) {
	$userData = getUserDataWatering($_SESSION['selectedWateringId']);
	$database = 'wapobel_be' . $userData['wapobelDatabase'];
	$password = $userData['wapobelDatabase'] . 'Wapobel123!';

	$mysqli = new mysqli("localhost", "root", "", $database) or die($connect_error);
	
/* 	$userData = getUserDataWatering($_SESSION['selectedWateringId']);
	$database = 'wapobel_be' . $userData['wapobelDatabase'];
	$password = $userData['wapobelDatabase'] . 'Wapobel123!';

	$mysqli = new mysqli("wapobel.be.mysql", $database, $password, $database) or die($connect_error); */	
}
?>