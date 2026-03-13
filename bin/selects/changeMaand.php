<?php
include '../init.php';

$maand = $_POST['maand'];
$_SESSION['wateringMaand'] = $maand;

$response = writeBoekingen($_SESSION['wateringId'], $_SESSION['wateringJaar'], $_SESSION['wateringMaand'], $useNummering, $sortering);

exit();
?>