<?php
// --- GET ---
// -----------
function getFactuurDetails($factuurId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM hoofdposten WHERE hoofdpostId='$hoofdpostId'");
	$row = $result->fetch_assoc();
	return $row;
	}

function getFacturenAll($wateringId, $verwerkt) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM facturen WHERE wateringId='$wateringId' AND verwerkt=$verwerkt");
	$facturen = $result->fetch_all(MYSQLI_ASSOC);
	return $facturen;
	}

function getFacturenNotRelevant($wateringId, $notRelevant) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM facturen_nietrel WHERE wateringId='$wateringId' AND verwerkt=$notRelevant");
	$facturen = $result->fetch_all(MYSQLI_ASSOC);
	return $facturen;
	}
	
function getFacturen($wateringId, $jaar, $verwerkt) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM facturen WHERE wateringId='$wateringId' AND jaar='$jaar' AND verwerkt=$verwerkt");
	$facturen = $result->fetch_all(MYSQLI_ASSOC);
	return $facturen;
	}

// --- ADD ---
// -----------
function addFactuur($wateringId, $jaar, $orderID, $verwerkt, $verwerktOp) {
	global $mysqli;
	$sql = "INSERT INTO `facturen` (`wateringId`, `jaar`, `orderID`, `verwerkt`, `verwerktOp`) VALUES ('$wateringId', '$jaar', '$orderID', $verwerkt, '$verwerktOp')";
	$result = $mysqli->query($sql);
	return $result;
	}

function addNotRelevant($wateringId, $jaar, $orderID, $verwerkt, $verwerktOp) {
	global $mysqli;
	$sql = "INSERT INTO `facturen_nietrel` (`wateringId`, `jaar`, `orderID`, `verwerkt`, `verwerktOp`) VALUES ('$wateringId', '$jaar', '$orderID', $verwerkt, '$verwerktOp')";
	$result = $mysqli->query($sql);
	return $result;
	}

// --- CHANGE ---	
// --------------


// --- DELETE ---	
// ---------------
