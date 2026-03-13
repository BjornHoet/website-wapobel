<?php
// --- GET ---
// -----------
function getBoekingen($wateringId, $jaar, $maand, $useNummering, $sortering) {
	global $mysqli;
	if ($useNummering === 'X' && $sortering === '1') {
		$result = $mysqli->query("SELECT * FROM boekingen WHERE wateringId='$wateringId' AND jaar='$jaar' AND maand='$maand' ORDER BY CAST( `nummering` AS UNSIGNED), dag, boekId");
		} else {
		$result = $mysqli->query("SELECT * FROM boekingen WHERE wateringId='$wateringId' AND jaar='$jaar' AND maand='$maand' ORDER BY dag, boekId");
		}
	return $result;
	}

function getBoekingenAll($wateringId, $jaar, $useNummering, $sortering) {
	global $mysqli;
	if ($useNummering === 'X' && $sortering === '1') {
		$result = $mysqli->query("SELECT * FROM boekingen WHERE wateringId='$wateringId' AND jaar='$jaar' ORDER BY CAST( `nummering` AS UNSIGNED), dag, boekId");
		} else {
		$result = $mysqli->query("SELECT * FROM boekingen WHERE wateringId='$wateringId' AND jaar='$jaar' ORDER BY dag, boekId");
		}
	return $result;
	}	

function writeBoekingen($wateringId, $jaar, $maand, $useNummering, $sortering) {
	global $mysqli;

	$query = "SELECT b.boekId, b.dag, b.maand, b.nummering, b.omschrijving, b.billitNumber, h.useKey, h.hoofdpostId, h.referentie AS hoofdPostRef, h.omschrijving AS hoofdPostOmschr, p.postId, p.referentie AS postRef, p.omschrijving AS postOmschr, s.subpostId, s.referentie AS subPostRef, s.omschrijving AS subPostOmschr FROM boekingen AS b LEFT OUTER JOIN posten as p on p.postId = b.postId LEFT OUTER JOIN hoofdposten AS h on h.hoofdpostId = p.hoofdpostId LEFT OUTER JOIN subposten AS s on s.subpostId = b.subPostId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND b.maand='$maand'";
	
/* 	if ($useNummering === 'X' && $sortering === '1') {
		$query = $query . " ORDER BY CAST( `nummering` AS UNSIGNED), dag, boekId";
		} else {
 */		$query = $query . " ORDER BY dag, boekId";
/*		} */

	$result = $mysqli->query($query);
	$rekeningen = getRekeningen($wateringId, $jaar, 'X', 'X', 'A');
	$json = [];
	
	if($maand !== '1' && $maand !== '01') {
		$firstPost = '';
	/* 	$firstOmschrijving = '';
		if($maand === '1') {
			$firstPost = 'ONT I 1';
			$postOverdracht = getPostDataOvdrachtPost($wateringId, $jaar);
			$firstOmschrijving = $postOverdracht['omschrijving'];
		} else {
			$firstOmschrijving = 'Overdracht';
		} */
		$firstOmschrijving = 'Overdracht';
		$jsonRow = [
					'boekId' => '',
					'datum' => '01/' . sprintf("%02d", $maand),
					'post' => $firstPost,
					'postDetail' => '',
					'nummering' => '',
					'billitnr' => '',
					'omschrijving' => $firstOmschrijving
				  ];

		foreach ($rekeningen as $rekening) {
			$overdracht = getOverdracht($wateringId, $jaar, $maand, $rekening['rekeningId']);
			$valO = currencyConv($overdracht);
			$valU = '';
				
			$jsonRow['rek_' . $rekening['rekeningId'] . '_O'] = $valO;
			$jsonRow['rek_' . $rekening['rekeningId'] . '_U'] = $valU;

				// Add to totals
			if(!isset($totals['rek_' . $rekening['rekeningId'] . '_O'])) $totals['rek_' . $rekening['rekeningId'] . '_O'] = 0;
			if($overdracht !== '')
				$totals['rek_' . $rekening['rekeningId'] . '_O'] += $overdracht;
			}
		
		$json[] = $jsonRow;
	}
	
	while($row = $result->fetch_assoc()){
		switch($row['useKey']) {
			case 'O':
				$useKeyOmsch = 'ONT';
				$useKeyOmschDetail = 'Ontvangsten';
				break;
			case 'U':
				$useKeyOmsch = 'UIT';
				$useKeyOmschDetail = 'Uitgaven';
				break;
			default:
				$useKeyOmsch = '';
				break;
			}
		if($useKeyOmsch != '') {
			$postDescr = $useKeyOmsch . ' ' . $row['hoofdPostRef'] . ' ' . $row['postRef'] . $row['subPostRef'];
			$postDetail  = '<span style="font-size: 16px;"><strong>' . $useKeyOmschDetail . '</strong></span><br>';
			$postDetail .= '<span style="margin-left: 10px; font-size: 14px;">' . $row['hoofdPostOmschr'] . "</span><br>";
			$postDetail .= '<span style="margin-left: 20px; font-size: 12px;">' . $row['postOmschr'] . "</span>";

			if (!empty($row['subPostOmschr'])) {
				$postDetail .= '<br><span style="margin-left: 30px; font-size: 10px;">' . $row['subPostOmschr'] . "</span>";
				}
			}
		else {
			$postDescr = '';
			$postDetail = '';
			}
	
		$jsonRow = [
					'boekId' => $row['boekId'],
					'datum' => sprintf("%02d", $row['dag']) . '/' . sprintf("%02d", $row['maand']),
					'date' => $jaar . '-' . $row['maand'] . '-' . $row['dag'],
					'post' => $postDescr,
					'postDetail' => $postDetail,
					'hoofdPostId' => $row['hoofdpostId'],
					'postId' => $row['postId'],
					'subPostId' => $row['subpostId'],
					'nummering' => $row['nummering'],
					'billitnr' => $row['billitNumber'],
					'omschrijving' => $row['omschrijving']
				  ];
        
		// Add all rekeningen as extra columns
		$allEmpty = true; // 👈 track if all rek_* fields are empty
		
		// Add all rekeningen as extra columns
        foreach ($rekeningen as $rekening) {
			$boekingsBedragO = getBoekingBedragData($row['boekId'], $rekening['rekeningId'], 'O');
			$boekingsBedragU = getBoekingBedragData($row['boekId'], $rekening['rekeningId'], 'U');
    
	        $valO = $boekingsBedragO['bedrag'] ?? '';
            $valU = $boekingsBedragU['bedrag'] ?? '';
			
            $jsonRow['rek_' . $rekening['rekeningId'] . '_O'] = $valO;
            $jsonRow['rek_' . $rekening['rekeningId'] . '_U'] = $valU;

            // Track if there’s at least one non-empty value
			if ($valO !== '' || $valU !== '') {
				$allEmpty = false;
				}
				
			// Add to totals
            if(!isset($totals['rek_' . $rekening['rekeningId'] . '_O'])) $totals['rek_' . $rekening['rekeningId'] . '_O'] = 0;
            if(!isset($totals['rek_' . $rekening['rekeningId'] . '_U'])) $totals['rek_' . $rekening['rekeningId'] . '_U'] = 0;

            if($valO !== '')
				$totals['rek_' . $rekening['rekeningId'] . '_O'] += $valO;
            if($valU !== '')
				$totals['rek_' . $rekening['rekeningId'] . '_U'] += $valU;
			}
			
			// 👇 Add your flag or attribute here
			$jsonRow['allEmpty'] = $allEmpty;
			$json[] = $jsonRow;
		}
		
    // Add totals row at the end
    $totalsRow = [
        'boekId' => '',
        'datum' => 'TOTAAL',
        'post' => '',
		'postDetail' => '',
        'nummering' => '',
        'billitnr' => '',
        'omschrijving' => ''
    ];

    // Append totals for each rek column
    foreach ($totals as $key => $value) {
        $totalsRow[$key] = ($value == 0) ? currencyConv(round(0, 2)) : currencyConv(round($value, 2));
		}

    $json[] = $totalsRow;		

    $balanceRow = [
        'boekId' => '',
        'datum' => 'Over te dragen',
        'post' => '',
		'postDetail' => '',
        'nummering' => '',
        'billitnr' => '',
        'omschrijving' => ''
    ];

	foreach ($totals as $key => $value) {
		// Only process "_O" keys, then find matching "_U"
		if (str_ends_with($key, '_O')) {
			$rekeningId = substr($key, 0, -2); // remove "_O"
			$keyO = $rekeningId . '_O';
			$keyU = $rekeningId . '_U';

			$o = $totals[$keyO] ?? 0;
			$u = $totals[$keyU] ?? 0;
			$balance = $o - $u;

			$balanceRow[$rekeningId . '_O'] = ($balance == 0) ? '' : currencyConv(round($balance, 2));
			// Optional: leave the _U empty or also repeat the balance
			$balanceRow[$rekeningId . '_U'] = '';
		}
	}

    $json[] = $balanceRow;		

    $rekRow = [
        'boekId' => '',
        'datum' => 'Rekeningtotaal',
        'post' => '',
		'postDetail' => '',
        'nummering' => '',
        'billitnr' => '',
        'omschrijving' => ''
    ];

	foreach ($totals as $key => $value) {
		// Only process "_O" keys, then find matching "_U"
		if (str_ends_with($key, '_O')) {
			$rekeningId = substr($key, 0, -2); // remove "_O"
			$rekeningIdTrim = str_replace('rek_', '', $rekeningId); // verwijdert 'rek_' prefix
			$keyO = $rekeningId . '_O';
			$keyU = $rekeningId . '_U';

			$o = $totals[$keyO] ?? 0;
			$u = $totals[$keyU] ?? 0;
			$balance = $o - $u;
			
			$rekBalance = getOverdracht($wateringId, $jaar, '1', $rekeningIdTrim, true);
			$rekBalance = $rekBalance + $balance;

			$rekRow[$rekeningId . '_O'] = ($rekBalance == 0) ? '' : currencyConv(round($rekBalance, 2));
			// Optional: leave the _U empty or also repeat the balance
			$rekRow[$rekeningId . '_U'] = '';
		}
	}

    $json[] = $rekRow;		
	
	$encoded_data = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	$fileLocation = DATA_PATH . $_SESSION['wateringId'] . '_boekingen.json';
	
	$result = file_put_contents($fileLocation, $encoded_data);
	return json_decode($encoded_data, true);
	}

function getBoekingBedragData($boekId, $rekeningId, $useKey) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM boekingsbedragen WHERE boekId='$boekId' AND rekeningId='$rekeningId' AND useKey='$useKey'");
	$row = $result->fetch_assoc();
	return $row;
	}

function getLastBoekingNr($wateringId, $jaar) {
	global $mysqli;
	// $sql = "SELECT MAX(CAST( `nummering` AS UNSIGNED)) AS max_nr FROM boekingen WHERE wateringId='$wateringId' AND jaar='$jaar'";
	$sql = "SELECT CAST(REGEXP_SUBSTR(nummering, '[0-9]+$') AS UNSIGNED) AS laatsteNummer FROM boekingen WHERE wateringId = '$wateringId' AND jaar = '$jaar' ORDER BY laatsteNummer DESC LIMIT 1;";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	
	return $row['laatsteNummer'];
	}

function getBoekingBedragDataDienstjaar($boekId, $useKey) {
	global $mysqli;
	$result = $mysqli->query("SELECT SUM(bedrag) AS dienstjaarBedrag FROM boekingsbedragen WHERE boekId='$boekId' AND useKey='$useKey'");
	$row = $result->fetch_assoc();
	return $row['dienstjaarBedrag'];
	}

function getBoekingBedragRekening($wateringId, $jaar, $rekeningId) {
	global $mysqli;
	$result = $mysqli->query("SELECT SUM(bedrag) AS bedragO FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND bb.useKey='O' AND bb.rekeningId='$rekeningId'");
	$row = $result->fetch_assoc();
	$bedragO = $row['bedragO'];

	$result = $mysqli->query("SELECT SUM(bedrag) AS bedragU FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND bb.useKey='U' AND bb.rekeningId='$rekeningId'");
	$row = $result->fetch_assoc();
	$bedragU = $row['bedragU'];
	
	$overdracht = getOverdracht($wateringId, $jaar, '01', $rekeningId);
	$bedragO = $bedragO + $overdracht;
	
	$rekeningBedrag = $bedragO - $bedragU;
	
	return $rekeningBedrag;
	}

function getBoekingBedragPost($wateringId, $jaar, $postId, $subPostId) {
	global $mysqli;
	$sql = "SELECT SUM(bedrag) AS bedrag FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND b.postId='$postId' AND b.subPostId='$subPostId'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	return $row['bedrag'];
	}	

function getBoekingenPost($wateringId, $jaar, $postId, $subPostId) {
	global $mysqli;
	$sql = "SELECT b.omschrijving, b.maand, b.dag, b.nummering, bb.bedrag FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND b.postId='$postId' AND b.subPostId='$subPostId' AND bb.bedrag<>'0'";
	$result = $mysqli->query($sql);
	return $result;
	}	
	
function getOverdracht($wateringId, $jaar, $maand, $rekeningId, $userRekening = false) {
	global $mysqli;
    // Init overdracht
    $overdracht = 0;
	
    // Als userRekening true is, voer het oorspronkelijke SQL-stuk uit
    if ($userRekening) {
        $sql = "SELECT overdracht FROM rekeningen WHERE rekeningId='$rekeningId' AND wateringId='$wateringId' AND jaar='$jaar'";
		$result = $mysqli->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $overdracht = $row['overdracht'] ?? 0;
        }
    }
	
	if($maand !== '01' && $maand !== 1) {
		$maand = $maand - 1;
		$sqlOverdracht = "SELECT SUM(bedrag) AS overdrachtBedrag FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND b.maand BETWEEN '1' AND '$maand' AND bb.rekeningId = '$rekeningId' AND bb.useKey = 'O' AND bb.bedrag != 0";
		$resultOverdracht = $mysqli->query($sqlOverdracht);
		$rowOverdracht = $resultOverdracht->fetch_assoc();
		$overdracht = $overdracht + $rowOverdracht['overdrachtBedrag'];

		$sqlOverdrachtUit = "SELECT SUM(bedrag) AS overdrachtBedragUit FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND b.maand BETWEEN '1' AND '$maand' AND bb.rekeningId = '$rekeningId' AND bb.useKey = 'U' AND bb.bedrag != 0";
		$resultOverdrachtUit = $mysqli->query($sqlOverdrachtUit);
		$rowOverdrachtUit = $resultOverdrachtUit->fetch_assoc();
		$overdracht = $overdracht - $rowOverdrachtUit['overdrachtBedragUit'];
	}
	
	return $overdracht;
	}

function getOverdrachtTotaal($wateringId, $jaar) {
	global $mysqli;
	$sql = "SELECT SUM(overdracht) AS overdracht FROM rekeningen WHERE wateringId='$wateringId' AND jaar='$jaar' AND actief = 'X'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	return $row['overdracht'];
	}
	
function getOverdrachtDienstjaar($wateringId, $jaar, $maand) {
	global $mysqli;
	$sql = "SELECT SUM(overdracht) AS overdracht FROM rekeningen WHERE wateringId='$wateringId' AND jaar='$jaar' AND actief = 'X'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$overdracht = $row['overdracht'];

	if($maand !== '01' && $maand !== 1) {
		$maand = $maand - 1;
		$sqlOverdracht = "SELECT SUM(bedrag) AS overdrachtBedrag FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND b.maand BETWEEN '1' AND '$maand' AND bb.useKey = 'O' AND bb.bedrag != 0";
		$resultOverdracht = $mysqli->query($sqlOverdracht);
		$rowOverdracht = $resultOverdracht->fetch_assoc();
		$overdracht = $overdracht + $rowOverdracht['overdrachtBedrag'];

		$sqlOverdrachtUit = "SELECT SUM(bedrag) AS overdrachtBedragUit FROM `boekingsbedragen` AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE b.wateringId='$wateringId' AND b.jaar='$jaar' AND b.maand BETWEEN '1' AND '$maand' AND bb.useKey = 'U' AND bb.bedrag != 0";
		$resultOverdrachtUit = $mysqli->query($sqlOverdrachtUit);
		$rowOverdrachtUit = $resultOverdrachtUit->fetch_assoc();
		$overdracht = $overdracht - $rowOverdrachtUit['overdrachtBedragUit'];
	}
	
	return $overdracht;
	}
	
function getLastMonth($wateringId, $jaar) {
	global $mysqli;
	$sql = "SELECT MAX(maand) AS maand FROM boekingen WHERE wateringId='$wateringId' AND jaar='$jaar'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	return $row['maand'];
	}


function getRecordsToDelete($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM boekingen AS b INNER JOIN boekingsbedragen AS bb ON bb.boekId = b.boekId WHERE b.wateringId = '$wateringId' AND b.jaar = '$jaar' AND bb.bedrag = 0");
	return $result;
	}

// --- ADD ---
// -----------
function addBoeking($wateringId, $jaar, $maand, $dag, $postId, $subpostId, $omschrijving, $nummering, $billitOrderID) {
	global $mysqli;
	
	$stmt = $mysqli->prepare("
    INSERT INTO boekingen (wateringId, jaar, maand, dag, postId, subPostId, nummering, omschrijving, billitNumber)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
	");
	$stmt->execute([$wateringId, $jaar, $maand, $dag, $postId, $subpostId, $nummering, $omschrijving, $billitOrderID]);

	return $mysqli->insert_id;
	}

function addBoekingRek($boekId, $rekeningId, $useKey) {
	global $mysqli;
	$sql = "INSERT INTO `boekingsbedragen` (`boekId`, `rekeningId`, `useKey`, `bedrag`) VALUES ('$boekId', '$rekeningId', '$useKey', '0')";
	
	$result = $mysqli->query($sql);
	return $result;
	}

function addBoekingRekBedrag($boekId, $rekeningId, $useKey, $bedrag) {
	global $mysqli;
	$sql = "INSERT INTO `boekingsbedragen` (`boekId`, `rekeningId`, `useKey`, `bedrag`) VALUES ('$boekId', '$rekeningId', '$useKey', '$bedrag')";

	$result = $mysqli->query($sql);
	return $result;
	}
	
	
// --- CHANGE ---	
// --------------
function changeBoeking($boekId, $maand, $dag, $postId, $subpostId, $nummering, $billitNumber) {
	global $mysqli;
	// $sql = "UPDATE `boekingen` SET `maand` = '$maand', `dag` = '$dag', `postId` = '$postId', `postId` = '$postId', `subPostId` = '$subpostId', `omschrijving` = '$omschrijving', `nummering` = '$nummering', `billitNumber` = '$billitNumber' WHERE boekId = '$boekId'";

    global $mysqli;

    $sql = "UPDATE boekingen SET maand = ?, dag = ?, postId = ?, subPostId = ?, nummering = ?, billitNumber = ? WHERE boekId = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param(
        "iiiisii",
        $maand,
        $dag,
        $postId,
        $subpostId,
        $nummering,
        $billitNumber,
        $boekId
    );

    return $stmt->execute();
	}
	

// --- DELETE ---	
// --------------
function deleteBoeking($boekId) {
	global $mysqli;
	$sql = "DELETE FROM `boekingsbedragen` WHERE boekId = '$boekId'";
	$result = $mysqli->query($sql);

	$sql = "DELETE FROM `boekingen` WHERE boekId = '$boekId'";
	$result = $mysqli->query($sql);

	return $result;
	}	
	
function deleteBoekingsBedrag($boekingId) {
	global $mysqli;
	$sql = "DELETE FROM `boekingsbedragen` WHERE boekingId = '$boekingId'";
	$result = $mysqli->query($sql);

	return $result;
	}		