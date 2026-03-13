<?php
include '../init.php';

$lastBoekingNr = getLastBoekingNr($wateringData['wateringId'], $wateringJaar);
$lastBoekingNr = $lastBoekingNr + 1;

echo json_encode($lastBoekingNr);
exit();
?>