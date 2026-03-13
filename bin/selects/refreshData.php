<?php
include '../init.php';

$response = writeBoekingen($_SESSION['wateringId'], $_SESSION['wateringJaar'], $_SESSION['wateringMaand'], $useNummering, $sortering);
echo $response;

exit();
?>