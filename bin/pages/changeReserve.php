<?php
include '../init.php';

$wateringId = $wateringData['wateringId'];
$raming = $_POST['reserveBedrag'];

$reserve = changeReserve($wateringId, $wateringJaar, $raming);

header("Location: ../../posten");
exit();
?>