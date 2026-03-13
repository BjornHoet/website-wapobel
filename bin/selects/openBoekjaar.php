<?php
include '../init.php';

openBoekjaar($wateringData['wateringId'], $wateringJaar);

$nieuwBoekjaar = $wateringJaar + 1;
sluitBoekjaar($wateringData['wateringId'], $nieuwBoekjaar);
	
exit();
?>