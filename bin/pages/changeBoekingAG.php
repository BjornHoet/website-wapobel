<?php
include '../init.php';
global $mysqli;
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
	echo json_encode(["status" => "error", "Message" => "No data received"]);
}

$boekId = '';

foreach ($data as $field) {
	$key = $mysqli->real_escape_string($field['key']);
	$value = $mysqli->real_escape_string($field['value']);
	if($key == 'boekId')
		$boekId = $value;

	if($key == 'billitnr') {
		$sql = "UPDATE boekingen SET billitNumber = '".$value."' WHERE boekId='".$boekId."'";
		$result = $mysqli->query($sql);
		}

	if($key == 'nummering') {
		$sql = "UPDATE boekingen SET nummering = '".$value."' WHERE boekId='".$boekId."'";
		$result = $mysqli->query($sql);
		}
	
	if($key == 'omschrijving') {
		$sql = "UPDATE boekingen SET omschrijving = '".$value."' WHERE boekId='".$boekId."'";
		$result = $mysqli->query($sql);
		}
	
	if(substr($key, 0, 3) === 'rek') {
		$rekId = '';
		$useKey = '';
		
		// Split into parts
		$parts = explode("_", $key);
		$rekId  = $parts[1];
		$useKey = $parts[2];
		
		if(is_null($value) || $value == '' || $value == '0' || $value == '0.00' || $value === null) {
			$sql = "DELETE FROM boekingsbedragen WHERE boekId = '".$boekId."'  AND rekeningID = '".$rekId."' AND useKey = '".$useKey."'";
			$result = $mysqli->query($sql);
			} else {
			$sql = "INSERT INTO boekingsbedragen (boekId, rekeningID, useKey, bedrag) VALUES ('".$boekId."', '".$rekId."', '".$useKey."', '".$value."') ON DUPLICATE KEY UPDATE bedrag = VALUES(bedrag)";
			$result = $mysqli->query($sql);
			}
		}
	}

echo json_encode(["status" => "success", "message" => "Data updated"]);
exit();
?>