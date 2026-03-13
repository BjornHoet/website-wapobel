<?php
include '../../bin/init.php';
global $mysqliLogin;   // SOURCE DB

$from = $_POST['fromYear'];
$to   = $_POST['toYear'];
$type = $_POST['type'];

//
// ---- COPY HOOFDPOSTEN (naar ALLE databases) ----
//
if ($type === 'hoofdposten') {

    // kopieer hoofdposten
    foreach ($databases as $config) {
        $mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
        if ($mysqli->connect_error) {
            echo "Connection failed for {$config['db']}: " . $mysqli->connect_error . "<br>";
            continue;
        }

        $stmt = $mysqliLogin->prepare("SELECT * FROM hoofdposten WHERE jaar = ?");
        $stmt->bind_param("i", $from);
        $stmt->execute();
        $rows = $stmt->get_result();

        while ($r = $rows->fetch_assoc()) {
            $stmtInsert = $mysqli->prepare("
                INSERT INTO hoofdposten 
                (jaar, useKey, typeId, referentie, omschrijving, omschrijvingBegroting, reserve, andere)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
			echo $r['useKey'];
			echo $r['typeId'];
             $stmtInsert->bind_param(
                "isssssii",
                $to,
                $r['useKey'],
                $r['typeId'],
                $r['referentie'],
                $r['omschrijving'],
                $r['omschrijvingBegroting'],
                $r['reserve'],
                $r['andere']
            );
            $stmtInsert->execute(); 
        }
    }

    // -------------------------------------------
    // EXTRA: KOPIEER TYPES enkel in $mysqliLogin
    // -------------------------------------------
    $stmt = $mysqliLogin->prepare("SELECT typeId, typeOmschrijving, volgorde FROM types WHERE jaar = ?");
    $stmt->bind_param("i", $from);
    $stmt->execute();
    $resultTypes = $stmt->get_result();

    while ($r = $resultTypes->fetch_assoc()) {
        $stmtInsert = $mysqliLogin->prepare("
            INSERT INTO types (typeId, jaar, typeOmschrijving, volgorde)
            VALUES (?, ?, ?, ?)
        ");
        $stmtInsert->bind_param(
            "sisi",
            $r['typeId'],
            $to,
            $r['typeOmschrijving'],
            $r['volgorde']
        );
        $stmtInsert->execute();
    }

    $stmt->close();
}


//
// ---- COPY STANDAARDPOSTEN (alleen in source DB) ----
//
if ($type === 'standaardposten') {

    $result = $mysqliLogin->prepare("SELECT hoofdpostId, referentie, omschrijving 
                                     FROM standaardposten WHERE jaar = ?");
    $result->bind_param("i", $from);
    $result->execute();
    $rows = $result->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $mysqliLogin->prepare("
        INSERT INTO standaardposten
        (jaar, hoofdpostId, referentie, omschrijving)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($rows as $r) {
        $stmt->bind_param(
            "iiss",
            $to,
            $r['hoofdpostId'],
            $r['referentie'],
            $r['omschrijving']
        );
        $stmt->execute();
    }

    $stmt->close();
}

echo "OK";
