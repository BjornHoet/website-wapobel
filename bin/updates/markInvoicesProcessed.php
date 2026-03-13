<?php
include '../init.php';

$wateringId = $wateringData['wateringId'];
$datetime = date('Y-m-d H:i:s');

if (!isset($_POST['invoices']) || !is_array($_POST['invoices'])) {
    http_response_code(400);
    exit;
}

$invoices = $_POST['invoices'] ?? [];
$type = $_POST['type']; // IN / OUT

echo $type;

if ($type === 'IN') {
    $jsonFile = "../../data/{$wateringId}_invoicesIn.json";
} else {
    $jsonFile = "../../data/{$wateringId}_invoicesOut.json";
}
$data = json_decode(file_get_contents($jsonFile), true);

foreach ($invoices as $invoice) {
    $orderId   = $invoice['orderId'];
    $orderDate = $invoice['orderDate'];

	$date = new DateTime($orderDate);
	$year = $date->format("Y"); // Output: 2025

	addNotRelevant($wateringId, $year, $orderId, true, $datetime);

	foreach ($data as &$row) {
		if ($row['OrderID'] == $orderId) {
			$row['Invoiced'] = true;
			$row['NotRelevant'] = true;
			break;
		}
	}
}

//refreshInvoices($wateringId, $wateringJaar);

// veilig schrijven
$fp = fopen($jsonFile, 'c+');
flock($fp, LOCK_EX);
ftruncate($fp, 0);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
flock($fp, LOCK_UN);
fclose($fp);

//header("Location: ../../facturen");
exit();