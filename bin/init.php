<?php
/* $timeout = 2;

//Set the maxlifetime of the session
ini_set( "session.gc_maxlifetime", $timeout );
//Set the cookie lifetime of the session
ini_set( "session.cookie_lifetime", $timeout );

session_start();
$s_name = session_name();

//Check the session exists or not
$sessionExpired = '';

if(isset( $_COOKIE[ $s_name ] )) {
	echo 'cookie set';
    setcookie( $s_name, $_COOKIE[ $s_name ], time() + $timeout, '/' );
}
else {
	echo 'cookie not set';
} */

//Start a new session
session_start();

$duration = 3600;
$localhost = '';

$apiEndpoint = 'https://api.billit.be/v1/';
if($localhost === 'X')
	$apiEndpoint = 'https://api.sandbox.billit.be/v1/';

$apiClients = 'parties';
$apiInvoices = 'orders';
$apiCosts = 'financialTransactions';

// Database configurations
if($localhost === 'X') {
	$databases = [
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_belogin'],
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_bewatering1'],
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_bewatering2'],
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_bewatering3'],
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_bewatering4'],
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_bewatering5'],
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_bewatering6'],
		['host'=>'localhost','user'=>'root','pass'=>'','db'=>'wapobel_bewatering7'],
	];
}
else {
	$databases = [
		['host'=>'wapobel.be.mysql','user'=>'wapobel_belogin','pass'=>'wapobelLogin123!','db'=>'wapobel_belogin'],
		['host'=>'wapobel.be.mysql','user'=>'wapobel_bewatering1','pass'=>'watering1Wapobel123!','db'=>'wapobel_bewatering1'],
		['host'=>'wapobel.be.mysql','user'=>'wapobel_bewatering2','pass'=>'watering2Wapobel123!','db'=>'wapobel_bewatering2'],
		['host'=>'wapobel.be.mysql','user'=>'wapobel_bewatering3','pass'=>'watering3Wapobel123!','db'=>'wapobel_bewatering3'],
		['host'=>'wapobel.be.mysql','user'=>'wapobel_bewatering4','pass'=>'watering4Wapobel123!','db'=>'wapobel_bewatering4'],
		['host'=>'wapobel.be.mysql','user'=>'wapobel_bewatering5','pass'=>'watering5Wapobel123!','db'=>'wapobel_bewatering5'],
		['host'=>'wapobel.be.mysql','user'=>'wapobel_bewatering6','pass'=>'watering6Wapobel123!','db'=>'wapobel_bewatering6'],
		['host'=>'wapobel.be.mysql','user'=>'wapobel_bewatering7','pass'=>'watering7Wapobel123!','db'=>'wapobel_bewatering7'],
	];
}

//Read the request time of the user
$time = $_SERVER['REQUEST_TIME'];

//Check the user's session exist or not
if (isset($_SESSION['LAST_ACTIVITY']) &&
   ($time - $_SESSION['LAST_ACTIVITY']) > $duration) {
    //Unset the session variables
    session_unset();
    //Destroy the session
    session_destroy();
    //Start another new session
    session_start();
    setcookie('session_exp', 'X', time() + (60), "/");     
	}
else {
	}

//Set the time of the user's last activity

$_SESSION['LAST_ACTIVITY'] = $time;

setlocale(LC_MONETARY, 'nl_BE');
error_reporting(2);

// Define data path
define('DATA_PATH', __DIR__ . '/../data/');
define('DATA_PATH_ADMIN', __DIR__ . '/../useradmin/data/');

require 'database/connectLogin.php';
require 'login/users.php';
//$userData = getUserData($_SESSION['userId']);
//$database = 'wapobel_be' . $userData['wapobelDatabase'];
require 'database/connect.php';
require 'functions/general.php';
require 'functions/selects.php';
require 'selects/posten.php';
require 'selects/rekeningen.php';
require 'selects/boekingen.php';
require 'selects/general.php';
require 'selects/facturen.php';
require 'api/api.php';

$title = "Wapobel";
$errors = array();
const monthNames = ["Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December"];  

if (isset($_SESSION['invoice_filterIn'])) {
    $selectedValueIn = $_SESSION['invoice_filterIn'];
	}
else {
	$_SESSION['invoice_filterIn'] = 'P3M';
	$selectedValueIn = $_SESSION['invoice_filterIn'];
}
if (isset($_SESSION['invoice_filterUit'])) {
    $selectedValueUit = $_SESSION['invoice_filterUit'];
	}
else {
	$_SESSION['invoice_filterUit'] = 'P3M';
	$selectedValueUit = $_SESSION['invoice_filterUit'];	
}


if (loggedIn() === true) {
	$userData = getUserData($_SESSION['userId']);

	$userName = $userData['userName'];
	$firstName = $userData['firstName'];
	$lastName = $userData['lastName'];
	$email = $userData['email'];
	$useNummering = $userData['useNummering'];
	$useKAS = $userData['useKAS'];
	$showBillit = $userData['showBillit'];
	$nummeringPrefix = $userData['nummeringPrefix'];
	$showNews = $userData['showNews'];
	$sortering = $userData['sortering'];

	if (!isset($_SESSION['wateringId'])) {
		$_SESSION['wateringId'] = $userData['hoofdWateringId'];
	}
	$wateringData = getWateringData($_SESSION['wateringId']);
	$apiKey = $wateringData['apiKey'];
	
	$openBoekjaar = getOpenBoekjaar($wateringData['wateringId']);
	
	if (!isset($_SESSION['wateringJaar'])) {
		//$_SESSION['wateringJaar'] = date("Y");
		$_SESSION['wateringJaar'] = $openBoekjaar;
		}
	$wateringJaar = $_SESSION['wateringJaar'];

	if (!isset($_SESSION['wateringMaand'])) {
		if($openBoekjaar === date("Y"))
			$_SESSION['wateringMaand'] = date("m");
		else {
			if($openBoekjaar > date("Y"))
				$_SESSION['wateringMaand'] = '01';
			else
				$_SESSION['wateringMaand'] = '12';
			}
		}
	$wateringMaand = $_SESSION['wateringMaand'];

	$maandSel = $wateringMaand - 1;

	$boekjaarAfgesloten = boekjaarAfgesloten($wateringData['wateringId'], $wateringJaar);
	$boekjaarOpen = boekJaarOpen($wateringData['wateringId'], $wateringJaar);
	}

if (!empty($_SESSION['api_error'])) {
    $apiError = $_SESSION['api_error'];
    unset($_SESSION['api_error']); // optional, clear after use
}	
?>