<?php
include '../init.php';

$boekingDatum = $_POST['boekingDatum'];
$datumArray = explode('/', $boekingDatum);
$dag = $datumArray[0];
$maand = $datumArray[1];
$wateringId = $wateringData['wateringId'];
$hoofdpostId = $_POST['boekingHoofdpost'];
$hoofdPostData = getHoofdPostData($hoofdpostId);
$postId = $_POST['boekingPost'];
$nummering = $_POST['boekingNummering'];
$subpostId = '0';
$omschrijving = $_POST['boekingOmschrijving'];
$rekeningInput = $_POST['boekingRekening'];
$bedrag = $_POST['boekingTotaal'];
$orderID = $_POST['boekingOrderID'];
$orderDate = $_POST['boekingOrderDate'];
$date = new DateTime($orderDate);
$year = $date->format("Y"); // Output: 2025
$datetime = date('Y-m-d H:i:s');
$type = $_POST['boekingType']; // IN | OUT

if(isset($_POST['boekingSubpost']) && $_POST['boekingSubpost'] !== '') {
	$subpostId = $_POST['boekingSubpost'];
	$subpostData = getSubPostData($subpostId);
	} else {
	$postId = $_POST['boekingPost'];
	$postData = getPostData($postId);
	}

$boeking = addBoeking($wateringId, $wateringJaar, $maand, $dag, $postId, $subpostId, $omschrijving, $nummering, $orderID);
addBoekingRekBedrag($boeking, $rekeningInput, $hoofdPostData['useKey'], $bedrag);

addFactuur($wateringId, $year, $orderID, true, $datetime);
//refreshInvoices($wateringId, $wateringJaar);

if ($type === 'IN') {
    $jsonFile = "../../data/{$wateringId}_invoicesIn.json";
} else {
    $jsonFile = "../../data/{$wateringId}_invoicesOut.json";
}
$data = json_decode(file_get_contents($jsonFile), true);

foreach ($data as &$row) {
    if ($row['OrderID'] == $orderID) {
        $row['Invoiced'] = true;
		$row['NotRelevant'] = false;
        break;
    }
}

// veilig schrijven
$fp = fopen($jsonFile, 'c+');
flock($fp, LOCK_EX);
ftruncate($fp, 0);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
flock($fp, LOCK_UN);
fclose($fp);

//header("Location: ../../facturen");
exit();
?>