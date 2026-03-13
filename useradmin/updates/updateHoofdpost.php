<?php
include '../../bin/init.php';

// Controleer of data aanwezig is
if (!isset($_POST['hoofdpostId'])) {
    exit("Missing hoofdpostId");
}

$hoofdpostId = intval($_POST['hoofdpostId']);
$jaar        = isset($_POST['jaar']) ? intval($_POST['jaar']) : null;
$useKey      = $_POST['useKey'] ?? null;
$typeId      = $_POST['typeId'] ?? null;
$referentie  = $_POST['referentie'] ?? null;
$omschrijving = $_POST['omschrijving'] ?? null;
$omschrijvingBegroting = $_POST['omschrijvingBegroting'] ?? null;
$reserve     = isset($_POST['reserve']) ? intval($_POST['reserve']) : 0;
$andere      = isset($_POST['andere']) ? intval($_POST['andere']) : 0;

// Veiligheid:
if ($jaar === null) exit("Missing jaar");
if ($useKey === null) exit("Missing useKey");
if ($typeId === null) exit("Missing typeId");

// SQL prepare
$sql = "
UPDATE hoofdposten SET
    jaar = ?,
    useKey = ?,
    typeId = ?,
    referentie = ?,
    omschrijving = ?,
    omschrijvingBegroting = ?,
    reserve = ?,
    andere = ?
WHERE hoofdpostId = ?
";

$stmt = $mysqliLogin->prepare($sql);

if (!$stmt) {
    exit("Prepare error: " . $mysqliLogin->error);
}

$stmt->bind_param(
    "isssssiii",
    $jaar,
    $useKey,
    $typeId,
    $referentie,
    $omschrijving,
    $omschrijvingBegroting,
    $reserve,
    $andere,
    $hoofdpostId
);

if (!$stmt->execute()) {
    exit("Execute error: " . $stmt->error);
}

echo "OK";
