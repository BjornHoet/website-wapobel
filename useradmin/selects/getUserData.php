<?php
include '../../bin/init.php'; // jouw database connectie, etc.
global $mysqliLogin;

if (!isset($_GET['userId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'userId ontbreekt']);
    exit;
}

$userId = (int)$_GET['userId'];

// Haal gebruiker en watering op
$sql = "SELECT u.userId, u.userName, u.firstName, u.lastName, u.email, u.street, u.houseNumber, u.postalCode, u.city, u.wapobelDatabase, u.active, u.useNummering, u.useKAS, w.wateringId, w.omschrijving AS wateringNaam, w.enableBillit, w.apiKey FROM users u LEFT JOIN wateringen w ON u.hoofdWateringId = w.wateringId WHERE u.userId = ? LIMIT 1";

$stmt = $mysqliLogin->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Gebruiker niet gevonden']);
    exit;
}

$data = $result->fetch_assoc();

// Converteer useNummering naar Ja/Nee
$data['useNummering'] = ($data['useNummering'] === 'X') ? 'Ja' : 'Nee';
$data['useKAS'] = ($data['useKAS'] === 'X') ? 'Ja' : 'Nee';

header('Content-Type: application/json');
echo json_encode($data);