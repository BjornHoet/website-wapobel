<?php
include '../../bin/init.php';
global $databases;

$items = $_POST['items']; // array van { jaar, omschrijving }

if (!is_array($items) || count($items) === 0) {
    echo "Geen items ontvangen";
    exit;
}

$errors = [];

foreach ($databases as $config) {
    $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
    if ($mysqli->connect_error) {
        $errors[] = "Connection failed for {$config['db']}: " . $mysqli->connect_error;
        continue;
    }

    foreach ($items as $item) {
        $stmt = $mysqli->prepare("DELETE FROM hoofdposten WHERE jaar = ? AND omschrijving = ?");
        $stmt->bind_param("is", $item['jaar'], $item['omschrijving']);
        if (!$stmt->execute()) {
            $errors[] = "Fout in {$config['db']} voor {$item['omschrijving']}: " . $stmt->error;
        }
        $stmt->close();
    }

    $mysqli->close();
}

if (!empty($errors)) {
    echo implode("\n", $errors);
} else {
    echo "OK";
}
