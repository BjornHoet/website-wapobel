<?php
	include '../init.php';

	$wateringId = $wateringData['wateringId'];

	global $mysqli;
	
	$sql = "SELECT * FROM subposten WHERE wateringId = '$wateringId' AND jaar = '$wateringJaar' AND postId = '".$_GET['id']."' AND actief = 'X'"; 
	$result = $mysqli->query($sql);

	$json = [];
	while($row = $result->fetch_assoc()){
        $json[$row['subpostId']] = $row['referentie'] . '. ' . $row['omschrijving'];
	}
	
	echo json_encode($json);
?>