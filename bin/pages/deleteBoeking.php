<?php
include '../init.php';
global $mysqli;

$boekId = $_GET['boekId'];
if(isset($_GET['boekId']) && $_GET['boekId'] !== '') {
	deleteBoeking($boekId);
	}

header("Location: ../../");
exit();
?>