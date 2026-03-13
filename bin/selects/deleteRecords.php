<?php
include '../init.php';

$records = getRecordsToDelete($wateringData['wateringId'], $wateringJaar);

foreach($records as $record) { 
	deleteBoekingsBedrag($record['boekingId']);
	}

exit();
?>