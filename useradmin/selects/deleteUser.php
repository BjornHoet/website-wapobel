<?php
include '../../bin/init.php'; // DB connectie, sessiecheck, etc.
global $mysqliLogin;

header('Content-Type: application/json');

if (!isset($_GET['userId'])) {
    echo json_encode(['success' => false, 'message' => 'userId ontbreekt']);
    exit;
}

$userId = (int)$_GET['userId'];
$userData = getUserData($userId);

try {
    // Je kan hier eventueel eerst checken of de user bestaat
    $stmt = $mysqliLogin->prepare("DELETE FROM users WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
	
    $stmt2 = $mysqliLogin->prepare("DELETE FROM wateringen WHERE wateringId = ?");
    $stmt2->bind_param("i", $userData['hoofdWateringId']);
    $stmt2->execute();

    $stmt3 = $mysqliLogin->prepare("DELETE FROM rekeningen WHERE wateringId = ?");
    $stmt3->bind_param("i", $userData['hoofdWateringId']);
    $stmt3->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Gebruiker succesvol verwijderd']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gebruiker niet gevonden of al verwijderd']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Fout bij verwijderen: ' . $e->getMessage()]);
}

$json = writeAdminUsers();
