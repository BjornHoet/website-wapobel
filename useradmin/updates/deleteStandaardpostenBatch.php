<?php
include '../../bin/init.php';

// Verwacht een array van items met 'jaar' en 'omschrijving'
$items = $_POST['items'];

if (!is_array($items)) exit;

// Loop over alle items
foreach ($items as $item) {
    $jaar = $item['jaar'];
    $omschrijving = $item['omschrijving'];

    // DELETE statement gebaseerd op jaar + omschrijving
    $stmt = $mysqliLogin->prepare("DELETE FROM standaardposten WHERE jaar = ? AND omschrijving = ?");
    $stmt->bind_param("is", $jaar, $omschrijving);
    $stmt->execute();
}

echo "OK";
