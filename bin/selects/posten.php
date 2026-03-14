<?php
// --- GET ---
// -----------
function getTypes($jaar) {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM types WHERE jaar='$jaar' ORDER BY volgorde");
	return $result;
	}

function getDefaultPosten($jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM standaardposten WHERE jaar='$jaar'");
	return $result;
	}

function getHoofdPostData($hoofdpostId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM hoofdposten WHERE hoofdpostId='$hoofdpostId'");
	$row = $result->fetch_assoc();
	return $row;
	}
	
function getHoofdPosten($useKey, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM hoofdposten WHERE useKey='$useKey' AND jaar='$jaar'");
	return $result;
	}

function getHoofdPostenSub($type, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM hoofdposten WHERE typeId='$type' AND jaar='$jaar'");
	return $result;
	}

function getHoofdPostType($type, $jaar) {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT typeOmschrijving FROM types WHERE typeId='$type' AND jaar='$jaar'");
	$row = $result->fetch_assoc();
	return $row['typeOmschrijving'];
	}

function getHoofdPostenAll($jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM hoofdposten WHERE useKey!='R' AND jaar='$jaar'");
	return $result;
	}

function getHoofdPostenActief($jaar, $wateringId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM hoofdposten AS hp WHERE hp.useKey!='R' AND hp.jaar='$jaar' AND EXISTS ( SELECT 1 FROM posten AS p WHERE p.wateringId = '$wateringId' AND p.hoofdpostId = hp.hoofdpostID and p.jaar = '$jaar' AND p.actief = 'X' AND p.overdrachtPost = '' )");
	return $result;
	}

function getPostenAll($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten WHERE wateringId='$wateringId' AND jaar='$jaar' AND actief='X'");
	return $result;
	}

function getPostenAlsoInactive($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten WHERE wateringId='$wateringId' AND jaar='$jaar'");
	return $result;
	}

function getPostenAllData($wateringId) {
	global $mysqli;
	$result = $mysqli->query("SELECT p.postId, p.wateringId, p.jaar, p.hoofdpostId, h.omschrijving AS hoofdpostOmschrijving, p.referentie, CONCAT(h.referentie, ' ', p.referentie) AS referentieTotal, p.omschrijving, p.raming, p.actief, p.overdrachtPost FROM posten AS p INNER JOIN hoofdposten AS h ON p.hoofdpostId = h.hoofdpostId WHERE p.wateringId = '$wateringId' ORDER BY p.jaar DESC, p.postId ASC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;

		$subposten = getSubpostenAllData($wateringId, $row['postId']);
		while ($rowSubpost = $subposten->fetch_assoc()) {
			$rows[] = $rowSubpost;
		}
    }
		
    return $rows;
	}

function getHoofdPostIdForKey($jaar, $useKey, $typeId, $referentie) {
	global $mysqli;
	$result = $mysqli->query("SELECT hoofdpostId FROM hoofdposten WHERE jaar='$jaar' AND useKey='$useKey' AND typeId='$typeId' AND referentie='$referentie'");
	$row = $result->fetch_assoc();
	return $row['hoofdpostId'];
	}	

function getBoekjarenAllData($wateringId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM boekjaren WHERE wateringId = '$wateringId' ORDER BY jaar DESC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
		
    return $rows;
	}

function getSubpostenAllData($wateringId, $postId) {
	global $mysqli;
	$sql = "SELECT sp.subpostId AS postId, sp.wateringId, sp.jaar, p.hoofdpostId, h.omschrijving AS hoofdpostOmschrijving, sp.referentie, CONCAT(SPACE(3), h.referentie, ' ', p.referentie, ' ', sp.referentie) AS referentieTotal, CONCAT(SPACE(3), sp.omschrijving) AS omschrijving, sp.raming, sp.actief, '' as overdrachtPost" .
			" FROM subposten AS sp INNER JOIN posten AS p ON p.postId = sp.postId INNER JOIN hoofdposten AS h ON h.hoofdpostId = p.hoofdpostId" .			
			" WHERE sp.wateringId = '$wateringId' AND sp.postId = '$postId' ORDER BY sp.jaar DESC, sp.subpostId ASC";
	$result = $mysqli->query($sql);

    return $result;
	}

function getHoofdpostenAllData() {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM hoofdposten AS h ORDER BY h.jaar DESC, h.hoofdpostId ASC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
		
    return $rows;	
}

function getTypesAllData() {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM types AS t ORDER BY t.jaar DESC, t.volgorde ASC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
		
    return $rows;	
}

function getStandaardpostenAllData() {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM standaardposten AS h ORDER BY h.jaar DESC, h.hoofdpostId ASC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
		
    return $rows;	
}

function getPosten($wateringId, $jaar, $hoofdpostId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten WHERE wateringId='$wateringId' AND jaar='$jaar' AND hoofdpostId='$hoofdpostId' ORDER BY postId");
	return $result;
	}

function getPostenActief($wateringId, $jaar, $hoofdpostId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten WHERE wateringId='$wateringId' AND jaar='$jaar' AND hoofdpostId='$hoofdpostId' AND actief='X'");
	return $result;
	}

function getPostenActiefGeenOverdracht($wateringId, $jaar, $hoofdpostId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten WHERE wateringId='$wateringId' AND jaar='$jaar' AND hoofdpostId='$hoofdpostId' AND actief='X' AND overdrachtPost = ''");
	return $result;
	}

function getPostenJaaroverzicht($wateringId, $jaar) {
	global $mysqli;
	$sql = "SELECT p.postId, p.referentie AS postReferentie, p.omschrijving AS postOmschrijving, p.raming AS postRaming, p.actief AS postActief, p.hoofdpostId AS hoofdpostId, sp.subpostId, sp.referentie AS subpostReferentie, sp.omschrijving AS subpostOmschrijving, sp.raming AS subpostRaming, sp.actief AS subpostActief FROM `posten` AS p LEFT OUTER JOIN `subposten` AS sp ON sp.postId = p.postId WHERE p.wateringId='$wateringId' AND p.jaar='$jaar' ORDER BY p.hoofdpostId, p.referentie, sp.referentie";
	$result = $mysqli->query($sql);
	return $result;
	}

function getPostData($postId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten WHERE postId='$postId'");
	$row = $result->fetch_assoc();
	return $row;
	}	

function getPostDataOvdrachtPost($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten WHERE wateringId='$wateringId' AND jaar='$jaar' AND overdrachtPost='X'");
	$row = $result->fetch_assoc();
	return $row;
	}		

function getPostenUseKey($wateringId, $jaar, $useKey) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM posten p INNER JOIN hoofdposten h on h.hoofdpostId=p.hoofdpostId WHERE p.wateringId = '$wateringId' AND p.jaar = '$jaar' AND h.useKey = '$useKey' ORDER BY p.hoofdpostId, p.postId");
	return $result;
	}	

function getNextPostId($wateringId, $jaar, $hoofdpostId) {
	global $mysqli;
	$sql = "SELECT MAX(referentie) AS max_post FROM posten WHERE wateringId='$wateringId' AND jaar='$jaar' AND hoofdpostId='$hoofdpostId'";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	return $row['max_post'];
	}

function getSubPosten($wateringId, $jaar, $postId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM subposten WHERE wateringId='$wateringId' AND jaar='$jaar' AND postId='$postId'");
	return $result;
	}

function getSubPostenActief($wateringId, $jaar, $postId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM subposten WHERE wateringId='$wateringId' AND jaar='$jaar' AND postId='$postId' AND actief='X'");
	return $result;
	}

function getSubPostData($subPostId) {
	global $mysqli;
	$result = $mysqli->query("SELECT * FROM subposten WHERE subpostId='$subPostId'");
	$row = $result->fetch_assoc();
	return $row;
	}		

function getNextSubPostId($wateringId, $jaar, $postId) {
	global $mysqli;
	$result = $mysqli->query("SELECT MAX(s.referentie) AS max_post FROM subposten AS s WHERE s.wateringId='$wateringId' AND s.jaar='$jaar' AND s.postId='$postId'");
	$row = $result->fetch_assoc();
	return $row['max_post'];
	}
	
function getReserve($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT raming AS raming FROM reserve WHERE wateringId='$wateringId' AND jaar='$jaar'");
	$row = $result->fetch_assoc();
	return $row['raming'];
	}

function getOpbrengsten($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT SUM(bedrag) AS opbrengsten FROM boekingsbedragen AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE bb.useKey = 'O' AND wateringId='$wateringId' AND jaar='$jaar'");
	$row = $result->fetch_assoc();
	return $row['opbrengsten'];
	}

function getUitgaven($wateringId, $jaar) {
	global $mysqli;
	$result = $mysqli->query("SELECT SUM(bedrag) AS uitgaven FROM boekingsbedragen AS bb INNER JOIN boekingen AS b ON b.boekId = bb.boekId WHERE bb.useKey = 'U' AND wateringId='$wateringId' AND jaar='$jaar'");
	$row = $result->fetch_assoc();
	return $row['uitgaven'];
	}

// --- ADD ---
// -----------
function addDefaultPost($jaar, $hoofdpostId, $referentie, $omschrijving) {
	global $mysqli;

	$stmt = $mysqli->prepare("INSERT INTO `standaardposten` (`jaar`, `hoofdpostId`, `referentie`, `omschrijving`) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("iiss", $jaar, $hoofdpostId, $referentie, $omschrijving);
	$result = $stmt->execute();
	$stmt->close();

	return $result;	
	}

function addPost($wateringId, $jaar, $hoofdpostId, $referentie, $omschrijving) {
	global $mysqli;

	$stmt = $mysqli->prepare("INSERT INTO `posten` (`wateringId`, `jaar`, `hoofdpostId`, `referentie`, `omschrijving`, `raming`, `actief`, `overdrachtPost`) VALUES (?, ?, ?, ?, ?, 0, 'X', '')");
	$stmt->bind_param("iiiss", $wateringId, $jaar, $hoofdpostId, $referentie, $omschrijving);
	$result = $stmt->execute();
	$stmt->close();

	return $result;
	}

function addPostBoekjaar($wateringId, $jaar, $hoofdpostId, $referentie, $omschrijving, $raming, $actief, $overdrachtPost) {
	global $mysqli;

	$stmt = $mysqli->prepare("INSERT INTO `posten` (`wateringId`, `jaar`, `hoofdpostId`, `referentie`, `omschrijving`, `raming`, `actief`, `overdrachtPost`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("iiissdss", $wateringId, $jaar, $hoofdpostId, $referentie, $omschrijving, $raming, $actief, $overdrachtPost);
	$stmt->execute();
	$stmt->close();

	return $mysqli->insert_id;
	}

function addSubPost($wateringId, $jaar, $postId, $referentie, $omschrijving) {
	global $mysqli;

	$stmt = $mysqli->prepare("INSERT INTO `subposten` (`wateringId`, `jaar`, `postId`, `referentie`, `omschrijving`, `raming`, `actief`) VALUES (?, ?, ?, ?, ?, 0, 'X')");
	$stmt->bind_param("iiiss", $wateringId, $jaar, $postId, $referentie, $omschrijving);
	$result = $stmt->execute();
	$stmt->close();

	return $result;
	}

function addSubPostBoekjaar($wateringId, $jaar, $postId, $referentie, $omschrijving, $raming, $actief) {
	global $mysqli;

	$stmt = $mysqli->prepare("INSERT INTO `subposten` (`wateringId`, `jaar`, `postId`, `referentie`, `omschrijving`, `raming`, `actief`) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("iiissds", $wateringId, $jaar, $postId, $referentie, $omschrijving, $raming, $actief);
	$result = $stmt->execute();
	$stmt->close();

	return $result;
	}

// --- CHANGE ---	
// --------------
function changePost($postId, $referentie, $omschrijving, $raming, $actief) {
	global $mysqli;

	$stmt = $mysqli->prepare("UPDATE `posten` SET `referentie` = ?, `omschrijving` = ?, `raming` = ?, `actief` = ? WHERE `postId` = ?");
	$stmt->bind_param("ssdsi", $referentie, $omschrijving, $raming, $actief, $postId);
	$result = $stmt->execute();
	$stmt->close();

	return $result;	
	}

function changePostRef($postId, $referentie) {
	global $mysqli;
	
	// Debug: Log the values being passed
	error_log("changePostRef: postId=$postId, referentie=$referentie");
	
	$stmt = $mysqli->prepare("UPDATE `posten` SET `referentie` = ? WHERE `postId` = ?");
	$stmt->bind_param("si", $referentie, $postId);
	$result = $stmt->execute();
	$stmt->close();
	
	return $result;
	}

function changeSubPost($subpostId, $referentie, $omschrijving, $raming, $actief) {
	global $mysqli;

	$stmt = $mysqli->prepare("UPDATE `subposten` SET `referentie` = ?, `omschrijving` = ?, `raming` = ?, `actief` = ? WHERE `subpostId` = ?");
	$stmt->bind_param("ssdsi", $referentie, $omschrijving, $raming, $actief, $subpostId);

	$result = $stmt->execute();
	$stmt->close();

	return $result;	
	}
	
function changeReserve($wateringId, $jaar, $raming) {
	global $mysqli;
	$sql = "UPDATE `reserve` SET `raming` = '$raming' WHERE `reserve`.`wateringId` = '$wateringId' AND `reserve`.`jaar` = '$jaar'";
	$result = $mysqli->query($sql);
	
	return $result;
	}

function deletePost($postId) {
	global $mysqli;
	$sql = "DELETE FROM `posten` WHERE postId = '$postId'";
	$result = $mysqli->query($sql);

	return $result;
}
	
function deleteSubpost($subpostId) {
	global $mysqli;
	$sql = "DELETE FROM `subposten` WHERE subpostId = '$subpostId'";
	$result = $mysqli->query($sql);

	return $result;
}