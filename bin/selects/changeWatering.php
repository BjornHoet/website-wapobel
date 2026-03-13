<?php
include '../init.php';

$wateringId = $_POST['wateringId'];
$_SESSION['wateringId'] = $wateringId;
$wateringData = getWateringData($wateringId);
$_SESSION['selectedWateringId'] = $wateringId;

$result = getJaren($wateringId);
$jaren = [];
while ($row = $result->fetch_assoc()) {
    $jaren[] = $row['jaar'];
}

// Zorg dat enableBillit een echte boolean is (niet string '0'/'1')
$enableBillit = isset($wateringData['enableBillit']) ? (bool)$wateringData['enableBillit'] : false;

// Bouw de JSON-data op
$data = [
    'jaren' => $jaren,
    'enableBillit' => $enableBillit
];

$_SESSION['wateringJaar'] = $jaren[0];

writeBoekingen($_SESSION['wateringId'], $_SESSION['wateringJaar'], $_SESSION['wateringMaand'], $useNummering, $sortering);

// JSON terugsturen
header('Content-Type: application/json');
echo json_encode($data);
exit();
?>