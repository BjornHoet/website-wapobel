<?php
include '../init.php';

$wateringId = $wateringData['wateringId'];
header('Content-Type: application/json');

$rows = $_POST['rows'] ?? [];
if (!is_array($rows)) {
    echo json_encode(['status'=>'error', 'message'=>'No rows received']);
    exit;
}

$orderIndex = 1;
foreach ($rows as $id) {
	changeRekeningOrder($id, $orderIndex);
	$orderIndex = $orderIndex + 1;
	}

exit();
?>