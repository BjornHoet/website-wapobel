<?php
include '../../bin/init.php'; // je DB connectie, sessiecheck, etc.
global $mysqliLogin;

// Zorg dat het een POST request is
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Haal alle POST-waarden op
$userId = isset($_POST['userId']) ? (int)$_POST['userId'] : 0;
$userName = $_POST['userName'] ?? '';
$email = $_POST['email'] ?? '';
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$street = $_POST['street'] ?? '';
$houseNumber = $_POST['houseNumber'] ?? '';
$postalCode = $_POST['postalCode'] ?? '';
$city = $_POST['city'] ?? '';
$wapobelDatabase = $_POST['wapobelDatabase'] ?? '';
$useNummering = $_POST['useNummering'] ?? 'Nee';
if ($useNummering === 'Ja') {
    $useNummeringDb = 'X';
} else {
    $useNummeringDb = '';
}
$useKAS = $_POST['useKAS'] ?? 'Nee';
if ($useKAS === 'Ja') {
    $useKASDb = 'X';
} else {
    $useKASDb = '';
}

$active = isset($_POST['active']) ? (int)$_POST['active'] : 0;

// Watering gegevens
$wateringId = isset($_POST['wateringID']) ? (int)$_POST['wateringID'] : 0;
$wateringNaam = $_POST['wateringNaam'] ?? '';
$enableBillit = isset($_POST['enableBillit']) ? (int)$_POST['enableBillit'] : 0;
$apiKey = $_POST['wateringApiKey'] ?? '';

$response = ['success' => false];

$mysqliLogin->begin_transaction();

try {
    // 🔹 Update Users
    $stmt = $mysqliLogin->prepare("
        UPDATE users SET
            userName = ?, email = ?, firstName = ?, lastName = ?,
            street = ?, houseNumber = ?, postalCode = ?, city = ?,
            wapobelDatabase = ?, useNummering = ?, useKAS = ?, active = ?
        WHERE userId = ?
    ");
	$stmt->bind_param(
		"ssssssissssii",
		$userName, $email, $firstName, $lastName,
		$street, $houseNumber, $postalCode, $city,
		$wapobelDatabase, $useNummeringDb, $useKASDb, $active, $userId
	);
	
    $stmt->execute();

    // 🔹 Update Wateringen
    if ($wateringId > 0) {
        $stmt2 = $mysqliLogin->prepare("
            UPDATE wateringen SET
                omschrijving = ?, enableBillit = ?, apiKey = ?
            WHERE wateringId = ?
        ");
        $stmt2->bind_param("sisi", $wateringNaam, $enableBillit, $apiKey, $wateringId);
        $stmt2->execute();
    }

    $mysqliLogin->commit();
    $response['success'] = true;
    $response['message'] = 'Gebruiker succesvol opgeslagen';
} catch (Exception $e) {
    $mysqliLogin->rollback();
    $response['success'] = false;
    $response['message'] = 'Fout bij opslaan: ' . $e->getMessage();
}

$json = writeAdminUsers();

header('Content-Type: application/json');
echo json_encode($response);