<?php
include '../../bin/init.php';

$items = $_POST['items'] ?? null;

if (!is_array($items)) {
    exit("Geen geldige data ontvangen");
}

// Prepared statement voor verwijderen van types per jaar
$stmt = $mysqliLogin->prepare("DELETE FROM types WHERE typeId = ? AND jaar = ?");

foreach ($items as $item) {
    if (!isset($item['typeId'], $item['jaar'])) continue;

    $stmt->bind_param("si", $item['typeId'], $item['jaar']); // typeId = string, jaar = integer
    $stmt->execute();
}

echo "OK";
