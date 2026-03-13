<?php
include '../init.php';

$jaar = $_POST['jaar'];
$_SESSION['wateringJaar'] = $jaar;

$response = writeBoekingen($_SESSION['wateringId'], $_SESSION['wateringJaar'], $_SESSION['wateringMaand'], $useNummering, $sortering);

exit();
?>