<?php
// --- GET ---
// -----------
function getRekeningen($wateringId, $jaar, $kas, $actief, $order) {
	global $mysqli;
	
	if($kas === 'X') {
		$sql = "SELECT * FROM rekeningen WHERE wateringId='$wateringId' AND jaar='$jaar'";
	} else {
		$sql = "SELECT * FROM rekeningen WHERE wateringId='$wateringId' AND jaar='$jaar' AND rekening <> 'KAS'";
	}
		
	if($actief === 'X') {
		$sql = $sql . " AND actief = 'X'";
	}
	
	$sql = $sql . ' ORDER BY positie';
	
	if($order === 'D') {
		$sql = $sql . ' DESC';
		}
		
	$result = $mysqli->query($sql);
	return $result;
	}

function getRekeningenAll($wateringId) {
	global $mysqli;
	
	$sql = "SELECT * FROM rekeningen WHERE wateringId='$wateringId' ORDER BY jaar DESC, positie ASC";
		
	$result = $mysqli->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
	}

function writeRekeningen($wateringId, $jaar) {
	global $mysqli;
	$sql = "SELECT r.rekeningId, r.wateringId, r.jaar, r.rekening, r.omschrijving, r.positie, r.overdracht, r.actief, r.afgesloten, COALESCE(SUM(b.bedrag),0) AS totaalBedrag FROM rekeningen AS r LEFT OUTER JOIN boekingsbedragen AS b ON b.rekeningId = r.rekeningId WHERE wateringId='$wateringId' AND jaar='$jaar' AND actief='X' GROUP BY r.rekeningId, r.wateringId, r.jaar, r.rekening, r.omschrijving, r.positie, r.overdracht, r.actief, r.afgesloten
ORDER BY r.positie;";
	$result = $mysqli->query($sql);
	
	while($row = $result->fetch_assoc()){
		if($row['rekening'] == 'KAS')
			$icon = '../img/icon-kas.png';
		else
			$icon = '../img/icon-bankkaart.png';
		
		$json[] = array(
					'icon' => $icon,
					'rekeningId' => $row['rekeningId'],
					'rekening' => $row['rekening'],
					'omschrijving' => $row['omschrijving'],
					'positie' => $row['positie'],
					'overdracht' => $row['overdracht'],
					'actief' => $row['actief'],
					'totaalBedrag' => $row['totaalBedrag']
				  );		
		}
	
	$encoded_data = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	$fileLocation = '../data/' . $_SESSION['wateringId'] . '_rekeningen.json';
	
	file_put_contents($fileLocation, $encoded_data);
	return json_decode($encoded_data, true);
	}
	
function writeRekeningenInactief($wateringId, $jaar) {
	global $mysqli;
	$sql = "SELECT * FROM rekeningen WHERE wateringId='$wateringId' AND jaar='$jaar' AND actief='' ORDER BY positie";
	$result = $mysqli->query($sql);
	
	while($row = $result->fetch_assoc()){
		if($row['rekening'] == 'KAS')
			$icon = '../img/icon-kas.png';
		else
			$icon = '../img/icon-bankkaart.png';
		
		$json[] = array(
					'icon' => $icon,
					'rekeningId' => $row['rekeningId'],
					'rekening' => $row['rekening'],
					'omschrijving' => $row['omschrijving'],
					'positie' => $row['positie'],
					'overdracht' => $row['overdracht'],
					'actief' => $row['actief']
				  );		
		}
	
	$encoded_data = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	$fileLocation = DATA_PATH . $_SESSION['wateringId'] . '_rekeningenInactief.json';
	
	file_put_contents($fileLocation, $encoded_data);
	return json_decode($encoded_data, true);
	}	

function getRekening($rekeningId) {
	global $mysqli;
	$sql = "SELECT * FROM rekeningen WHERE rekeningid='$rekeningId'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	return $row;
	}

function getRekeningMaxPositie($wateringId, $jaar) {
	global $mysqli;
	$sql = "SELECT MAX(positie) AS maxPositie FROM rekeningen WHERE wateringId='$wateringId' AND jaar='$jaar' AND positie!='999'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	return $row['maxPositie'];
	}
	
function boekjaarAfgesloten($wateringId, $jaar) {
	global $mysqli;
	$sql = "SELECT afgesloten FROM rekeningen WHERE wateringId='$wateringId' AND jaar='$jaar' AND rekening='KAS'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	return $row['afgesloten'];
	}
	

// --- ADD ---
// -----------
function addRekening($wateringId, $jaar, $rekening, $omschrijving, $overdracht, $positie) {
	global $mysqli;
	$sql = "INSERT INTO `rekeningen` (`wateringId`, `jaar`, `rekening`, `omschrijving`, `overdracht`, `positie`, `actief`, `afgesloten`) VALUES ('$wateringId', '$jaar', '$rekening', '$omschrijving', '$overdracht', '$positie', 'X', '')";

	$result = $mysqli->query($sql);
	return $result;
	}

function addRekeningGlobal($wateringId, $jaar, $rekening, $omschrijving, $overdracht, $positie) {
	global $mysqliLogin;
	$sql = "INSERT INTO `rekeningen` (`wateringId`, `jaar`, `rekening`, `omschrijving`, `overdracht`, `positie`, `actief`, `afgesloten`) VALUES ('$wateringId', '$jaar', '$rekening', '$omschrijving', '0', '$positie', 'X', '')";
	$result = $mysqliLogin->query($sql);
	return $result;
	}
	
// --- CHANGE ---	
// --------------
function changeRekening($rekeningId, $rekening, $omschrijving, $positie, $actief, $overdracht) {
	global $mysqli;
	$sql = "UPDATE `rekeningen` SET `rekening` = '$rekening', `omschrijving` = '$omschrijving', `overdracht` = '$overdracht', `positie` = '$positie', `actief` = '$actief' WHERE rekeningId = '$rekeningId'";
	
	$result = $mysqli->query($sql);
	return $result;
	}

function changeRekeningOrder($rekeningId, $positie) {
	global $mysqli;
	$sql = "UPDATE `rekeningen` SET `positie` = '$positie' WHERE rekeningId = '$rekeningId'";
	
	$result = $mysqli->query($sql);
	return $result;
	}
	
function verwijderRekeningen($wateringId, $jaar) {
	global $mysqli;
	$sql = "DELETE FROM `rekeningen` WHERE wateringId='$wateringId' AND jaar='$jaar'";
	$result = $mysqli->query($sql);

	return $result;	
	}

function changeOverdracht($wateringId, $nieuwJaar, $rekening, $overdracht) {
	global $mysqli;
	$sql = "UPDATE `rekeningen` SET `overdracht` = '$overdracht' WHERE wateringId='$wateringId' AND jaar='$nieuwJaar' AND rekening = '$rekening'";
	
	$result = $mysqli->query($sql);
	return $result;	
	}