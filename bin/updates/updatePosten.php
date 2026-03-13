<?php
header('Content-Type: application/json');

$prefix = '../../';
require $prefix . 'bin/init.php';
require $prefix . 'bin/database/connect.php';

global $mysqli;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect fields from POST, null if not set
    $postId = $_POST['postId'] ?? null;
    $jaar = $_POST['jaar'] ?? null;
    $hoofdpostId = $_POST['hoofdpostId'] ?? null;
    $referentie = $_POST['referentie'] ?? null;
    $omschrijving = $_POST['omschrijving'] ?? null;
    $raming = $_POST['raming'] ?? null;
    $actief = $_POST['actief'] ?? '';
    $overdrachtPost = $_POST['overdrachtPost'] ?? '';

    if (!$postId) {
        echo json_encode(['success' => false, 'error' => 'Missing postId']);
        exit;
    }

    // Prepare UPDATE statement
    $stmt = $mysqli->prepare("
        UPDATE posten SET 
            jaar = ?, 
            hoofdpostId = ?, 
            referentie = ?, 
            omschrijving = ?, 
            raming = ?, 
            actief = ?, 
            overdrachtPost = ?
        WHERE postId = ?
    ");

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => $mysqli->error]);
        exit;
    }

    // Bind parameters. 's' = string, 'i' = integer, adjust if needed
    $stmt->bind_param(
        "iiisdssi",
        $jaar,
        $hoofdpostId,
        $referentie,
        $omschrijving,
        $raming,
        $actief,
        $overdrachtPost,
        $postId
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
}
?>