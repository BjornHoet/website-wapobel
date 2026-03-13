<?php
header('Content-Type: application/json');

$prefix = '../../';
require $prefix . 'bin/init.php';
require $prefix . 'bin/database/connect.php';

global $mysqli;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $wateringId = $_POST['wateringId'] ?? null;
    $jaar = $_POST['jaar'] ?? null;
    $afgesloten = $_POST['afgesloten'] ?? null;

    if (!$wateringId || !$jaar) {
        echo json_encode(['success' => false, 'error' => 'Missing wateringId or jaar']);
        exit;
    }

    // Make sure we sanitize afsluiten if null
    $afgesloten = $afgesloten ?? '';

    $stmt = $mysqli->prepare("UPDATE boekjaren SET afgesloten = ? WHERE wateringId = ? AND jaar = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => $mysqli->error]);
        exit;
    }

    $stmt->bind_param("sis", $afgesloten, $wateringId, $jaar);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
}
?>
