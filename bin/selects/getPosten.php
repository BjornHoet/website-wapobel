<?php
	include '../init.php';

	$wateringId = $wateringData['wateringId'];

	global $mysqli;
	
	$sql = "SELECT * FROM posten WHERE wateringId = '$wateringId' AND jaar = '$wateringJaar' AND hoofdpostId = '".$_GET['id']."' AND actief = 'X'"; 
	$result = $mysqli->query($sql);

	$json = [];
	while($row = $result->fetch_assoc()){
		if($row['overdrachtPost'] !== 'X')
			$json[$row['postId']] = $row['referentie'] . '. ' . $row['omschrijving'];
	}
	
	echo json_encode($json);
?>