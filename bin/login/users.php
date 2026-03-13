<?php
// --- GET ---
// -----------
function userData($userId) {
	$data = array();
	$userId = (int)$userId;
	
	$funcNumArgs = func_num_args();
	$funcGetArgs = func_get_args();
	
	if ($funcNumArgs > 1) {
		unset($funcGetArgs[0]);
		
		$fields = '`' . implode('`, `', $funcGetArgs) . '`';
		
		global $mysqliLogin;
		$result = $mysqliLogin->query("SELECT $fields FROM users WHERE userId = $userId");
		$row = $result->fetch_assoc();
		return $row;
		}
	}

function getUserData($userId) {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM users WHERE userId='$userId'");
	$row = $result->fetch_assoc();
	return $row;
	}

function getUserDataWatering($wateringId) {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM users WHERE hoofdWateringId='$wateringId'");
	$row = $result->fetch_assoc();
	return $row;
	}

function loggedIn() {
	return (isset($_SESSION['userId'])) ? true : false;
}

function checkResetKey($userName, $key) {
	$userName = sanitize($userName);
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM password_reset_temp WHERE email='$userName' AND tempkey='$key'");
	$row = $result->fetch_assoc();
	return $row;
	}
	
function user_exists($userName) {
	$userName = sanitize($userName);
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT COUNT(*) FROM users WHERE userName = '$userName'");
	$row = $result->fetch_row();
	return ($row[0] == 1) ? true : false;
}

function user_exists_mail($userEmail) {
	$userName = sanitize($userEmail);
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT COUNT(*) FROM users WHERE email = '$userEmail'");
	$row = $result->fetch_row();
	return ($row[0] == 1) ? true : false;
}

function user_active($userName) {
	$userName = sanitize($userName);
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT COUNT(*) FROM users WHERE userName = '$userName' AND active = 1");
	$row = $result->fetch_row();
	return ($row[0] == 1) ? true : false;
}

function user_active_mail($userEmail) {
	$userEmail = sanitize($userEmail);
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT COUNT(*) FROM users WHERE email = '$userEmail' AND active = 1");
	$row = $result->fetch_row();
	return ($row[0] == 1) ? true : false;
}

function userIdFromUserName($userName) {
	$userName = sanitize($userName);
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT userId FROM users WHERE userName = '$userName'");
	$row = $result->fetch_row();
	return $row[0];	
}

function getLoginDetails() {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM logon ORDER BY volgnr DESC");
	return $result;
	}

function getUserDetails() {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM users ORDER BY userId");
	return $result;
	}	

function getWateringDetails() {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT * FROM wateringen ORDER BY waterId");
	return $result;
	}

function getLastWateringID() {
	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT MAX(wateringId) AS wateringId FROM wateringen");
	$row = $result->fetch_assoc();
	return $row['wateringId'];
	}
	
function login($userName, $userPassword) {
	$userId = userIdFromUserName($userName);

	$userName = sanitize($userName);
	$userPassword = encryptIt($userPassword);

	global $mysqliLogin;
	$result = $mysqliLogin->query("SELECT COUNT(*) FROM users WHERE userName = '$userName' AND userPassword = '$userPassword'");
	$row = $result->fetch_row();
	return ($row[0] == 1) ? $userId : false;
}

function encryptIt( $q ) {
    $qEncoded = md5( $q );
    return( $qEncoded );
}

function decryptIt( $q ) {
    $cryptKey = 'aDF825qyefvp54DfjDQ47P';
    $qDecoded = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
    return( $qDecoded );
}

function writeAdminUsers() {
	global $mysqliLogin;
	$sql = "SELECT u.userId, u.userPassword, u.userName, u.firstName, u.lastName, u.email, u.city, u.wapobelDatabase, u.useNummering, u.useKAS, u.showBillit, u.showNews, u.active, w.wateringId, w.omschrijving AS wateringNaam, w.enableBillit, w.apiKey FROM users u LEFT JOIN wateringen w ON u.hoofdWateringId = w.wateringId ORDER BY u.hoofdWateringId";
	$result = $mysqliLogin->query($sql);
	
	while($row = $result->fetch_assoc()){
		$json[] = array(
			'userId'          => $row['userId'],
			'userPassword'    => ($row['userPassword'] !== '') ? 'Ja' : 'Nee',
			'userName'        => $row['userName'],
			'firstName'       => $row['firstName'],
			'lastName'        => $row['lastName'],
			'email'           => $row['email'],
			'city'            => $row['city'],
			'wapobelDatabase' => $row['wapobelDatabase'],
			'useNummering'    => ($row['useNummering'] === 'X') ? 'Ja' : 'Nee',
			'useKAS'  		  => ($row['useKAS'] === 'X') ? 'Ja' : 'Nee',
			'showBillit'  	  => ($row['showBillit'] === 'X') ? 'Ja' : 'Nee',
			'active'          => (int)$row['active'],
			'wateringId'      => $row['wateringId'],
			'wateringNaam'    => $row['wateringNaam'],
			'enableBillit'    => (int)$row['enableBillit'],
			'showNews'    	  => (int)$row['showNews'],
			'apiKey'    	  => $row['apiKey']
		);	
	}
	
	$encoded_data = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	$fileLocation = DATA_PATH_ADMIN . 'users.json';
	
	file_put_contents($fileLocation, $encoded_data);
	return json_decode($encoded_data, true);	
}

function writeLogonData() {
    global $mysqliLogin;
	$sql = "SELECT l.volgnr, l.username, CONCAT(u.firstName, ' ', u.lastName) AS name, w.omschrijving, l.datum, l.uur, l.success FROM logon AS l INNER JOIN users AS u ON u.userName = l.username INNER JOIN wateringen AS w ON w.wateringId = u.hoofdWateringId ";
	$sql = $sql	. "WHERE u.userId <> 1 ORDER BY volgnr DESC";
    $result = $mysqliLogin->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    file_put_contents('data/logon.json', json_encode($data));
    return json_encode($data);
}

// --- ADD ---
// -----------
function addWatering($wateringId, $omschrijving) {
	global $mysqliLogin;	
	$sql = "INSERT INTO wateringen (wateringId, userId, omschrijving, enableBillit, apiKey) VALUES ('$wateringId', '1', '$omschrijving', '0', '')";
	
	$result = $mysqliLogin->query($sql);
	return $result;
	}
	
function addUser($email, $firstName, $lastName, $street, $houseNumber, $postalCode, $city, $wateringId) {
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	global $mysqliLogin;	
	//$sql = "INSERT INTO users (userName, userPassword, firstName, lastName, email, wapobelDatabase, street, houseNumber, postalCode, city, hoofdWateringId, useNummering, sortering, active) VALUES ('$email', '', '$firstName', '$lastName', '$email', '', '$street', '$houseNumber', '$postalCode', '$city', '$wateringId', 'X', '0', '0')";

    // Prepare SQL with placeholders
    $sql = "INSERT INTO users (
                userName, userPassword, firstName, lastName, email, wapobelDatabase,
                street, houseNumber, postalCode, city, hoofdWateringId, useNummering, useKAS, sortering, active
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqliLogin->prepare($sql);
    // Example: empty password and wapobelDatabase for now
    $password = ''; 
    $wapobelDatabase = '';
    $useNummering = 'X';
    $useKAS = 'X';
    $sortering = '0';
    $active = '0';

    // Bind parameters (14 placeholders)
    $stmt->bind_param(
        "sssssssssssssii",
        $email, $password, $firstName, $lastName, $email, $wapobelDatabase,
        $street, $houseNumber, $postalCode, $city, $wateringId, $useNummering, $useKAS, $sortering, $active
    );

    // Execute and check result
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
	
    return true;
    }
	
function passwordReset($userEmail, $key, $expDate) {
	$userEmail = sanitize($userEmail);
	global $mysqliLogin;
	$sql = "INSERT INTO password_reset_temp SET email='$userEmail', tempkey='$key', expDate='$expDate';";
	$result = $mysqliLogin->query($sql);
	return '';
	}

function storeLogin($userName, $userPassword, $success) {
	date_default_timezone_set('Europe/Paris');
	$datum = date("d/m/Y");
	$uur = date("H:i:s");
	$userName = sanitize($userName);
	$userPassword = encryptIt($userPassword);
	global $mysqliLogin;
	$sql = "INSERT INTO `logon` (`username`, `password`, `datum`, `uur`, `success`) VALUES ('$userName', '$userPassword', '$datum', '$uur', '$success')";
	$result = $mysqliLogin->query($sql);
	return $result;
	}
	
	
// --- CHANGE ---	
// --------------
function changeProfile($userName, $userFirstName, $userLastName, $userEmail, $useNummering, $nummeringPrefix, $useKAS, $showBillit, $sortering) {
	$userFirstName = sanitize($userFirstName);
	$userLastName = sanitize($userLastName);
	$userEmail = sanitize($userEmail);
	global $mysqliLogin;
	$sql = "UPDATE users SET firstName = '$userFirstName', lastName = '$userLastName', useNummering = '$useNummering', nummeringPrefix = '$nummeringPrefix', useKAS = '$useKAS', showBillit = '$showBillit' WHERE userName = '$userName'";
	$result = $mysqliLogin->query($sql);
	return $result;
	}

function changePassword($userEmail, $userPassword) {
	$userEmail = sanitize($userEmail);
	$userPassword = encryptIt($userPassword);
	global $mysqliLogin;
	$result = $mysqliLogin->query("UPDATE users SET userPassword = '$userPassword' WHERE email = '$userEmail'");
	return $result;
	}


// --- DELETE ---	
// --------------
function deleteTempKey($key) {
	global $mysqliLogin;
	$sql = "DELETE FROM password_reset_temp WHERE tempkey='$key'";
	$result = $mysqliLogin->query($sql);
	return '';
	}

?>