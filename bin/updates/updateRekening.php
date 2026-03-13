<?php
header('Content-Type: application/json');

$prefix = '../../';
require $prefix . 'bin/init.php';
require $prefix . 'bin/database/connect.php';

global $mysqli;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect fields from POST, null if not set
    $rekeningId = $_POST['rekeningId'] ?? null;
    $wateringId = $_POST['wateringId'] ?? null;
    $jaar = $_POST['jaar'] ?? null;
    $rekening = $_POST['rekening'] ?? null;
    $omschrijving = $_POST['omschrijving'] ?? null;
    $positie = $_POST['positie'] ?? null;
    $overdracht = $_POST['overdracht'] ?? null;
    $actief = $_POST['actief'] ?? '';
    $afgesloten = $_POST['afgesloten'] ?? '';

    if (!$rekeningId) {
        echo json_encode(['success' => false, 'error' => 'Missing rekeningId']);
        exit;
    }

    // Prepare UPDATE statement
    $stmt = $mysqli->prepare("
        UPDATE rekeningen SET
            jaar = ?, 
            rekening = ?, 
            omschrijving = ?, 
            positie = ?, 
            overdracht = ?, 
            actief = ?, 
            afgesloten = ?
        WHERE rekeningId = ? AND wateringId = ?
    ");

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => $mysqli->error]);
        exit;
    }

    // Bind parameters. Adjust types as needed ('s' = string, 'i' = integer)
    $stmt->bind_param(
        "issidssii",
        $jaar,
        $rekening,
        $omschrijving,
        $positie,
        $overdracht,
        $actief,
        $afgesloten,
        $rekeningId,
        $wateringId
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
}
?>
