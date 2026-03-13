<?php
include '../init.php';
global $mysqli;

header('Content-Type: application/json');

if (
    !isset($_POST['rekeningId']) ||
    $_POST['rekeningId'] === ''
) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Geen rekeningId ontvangen'
    ]);
    exit;
}

$rekeningId = (int) $_POST['rekeningId'];

// prepared statement (SQL-injection safe)
$stmt = $mysqli->prepare(
    "DELETE FROM rekeningen WHERE rekeningId = ?"
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Prepare mislukt'
    ]);
    exit;
}

$stmt->bind_param('i', $rekeningId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Verwijderen mislukt'
    ]);
}

$stmt->close();

writeRekeningen($wateringData['wateringId'], $wateringJaar);
writeRekeningenInactief($wateringData['wateringId'], $wateringJaar);

exit;