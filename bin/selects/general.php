<?php
function getWateringen($userId) {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM wateringen WHERE userId='$userId'");
	return $result;
	}

function getWateringData($wateringId) {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM wateringen WHERE wateringId='$wateringId'");
	$row = $result->fetch_assoc();
	return $row;
	}
	
function getJaren($wateringId) {
	global $mysqli;
	$result = $mysqli->query("SELECT jaar FROM boekjaren WHERE wateringId='$wateringId' GROUP BY jaar ORDER BY jaar DESC");
	return $result;
	}
	
function currencyConv($amount) {
	$result = "€ " . number_format($amount, 2, ",", ".");
	return $result;
	}
	
function currencyConvNoEuro($amount) {
	$result = number_format($amount, 2, ",", ".");
	return $result;
	}	
	
function boekJaarOpen($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT afgesloten FROM boekjaren WHERE wateringId='$wateringId' AND jaar='$jaar'");
	$row = $result->fetch_assoc();
	if($row['afgesloten'] === 'X')
		return false;
	else
		return true;
	}

function checkNieuwJaarBestaat($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT jaar FROM boekjaren WHERE wateringId='$wateringId' AND jaar='$jaar'");
	$row = $result->fetch_assoc();
	if($row['jaar'] !== null and $row['jaar'] !== '')
		return true;
	else
		return false;
	}

function openNieuwJaar($wateringId, $nieuwjaar) {
	global $mysqli;
	$sql = "INSERT INTO `boekjaren` (`wateringId`, `jaar`, `afgesloten`) VALUES ('$wateringId', '$nieuwjaar', '')";
	$result = $mysqli->query($sql);
	return $result;
	}

function openReserve($wateringId, $nieuwjaar, $raming) {
	global $mysqli;
	$sql = "INSERT INTO `reserve` (`wateringId`, `jaar`, `raming`) VALUES ('$wateringId', '$nieuwjaar', '$raming')";
	$result = $mysqli->query($sql);
	return $result;
	}

function updateReserve($wateringId, $nieuwjaar, $raming) {
	global $mysqli;
	$sql = "UPDATE `reserve` SET `raming` = '$raming' WHERE wateringId = '$wateringId' AND jaar = '$nieuwjaar'";
	$result = $mysqli->query($sql);
	return $result;
	}

	
function openBoekjaar($wateringId, $jaar) {
	global $mysqli;
	$sql = "UPDATE `boekjaren` SET `afgesloten` = '' WHERE wateringId = '$wateringId' AND jaar = '$jaar'";
	
	$result = $mysqli->query($sql);
	return $result;	
	}
	
function sluitBoekjaar($wateringId, $jaar) {
	global $mysqli;
	$sql = "UPDATE `boekjaren` SET `afgesloten` = 'X' WHERE wateringId = '$wateringId' AND jaar = '$jaar'";
	
	$result = $mysqli->query($sql);
	return $result;	
	}	

function changeWatering($wateringId, $useBillit, $apiKey) {
	global $mysqliLogin;
	$sql = "UPDATE `wateringen` SET `enableBillit` = '$useBillit', `apiKey` = '$apiKey' WHERE wateringId = '$wateringId'";
	
	$result = $mysqliLogin->query($sql);
	return $result;	
	}	
	
function getOpenBoekjaar($wateringId) {
	global $mysqli;
	$result = $mysqli->query("SELECT jaar FROM boekjaren WHERE wateringId='$wateringId' AND afgesloten=''");
	$row = $result->fetch_assoc();
	return $row['jaar'];	
	}
	
function checkReserveExists($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT jaar FROM reserve WHERE wateringId='$wateringId' AND jaar='$jaar'");
	$row = $result->fetch_assoc();
	if($row['jaar'] !== null and $row['jaar'] !== '')
		return true;
	else
		return false;
	}	