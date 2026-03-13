<?php
include '../init.php';
global $mysqlLogin;

header('Content-Type: application/json');

$wateringId = $_GET['wateringId'] ?? 0; 

$response = []; // JSON object dat we gaan vullen

if ($wateringId > 0) {
    $query = "SELECT 
                w.wateringId, 
                w.omschrijving, 
                w.enableBillit, 
                w.apiKey, 
                u.userName, 
                u.firstName, 
                u.lastName, 
                u.wapobelDatabase 
              FROM wateringen AS w 
              INNER JOIN users AS u ON u.hoofdWateringId = w.wateringId 
              WHERE w.wateringId = $wateringId";

    $result = $mysqliLogin->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Vul het object 'wateringDetails'
        $response['wateringDetails'] = [
            'wateringId' => $row['wateringId'],
            'omschrijving' => $row['omschrijving'],
            'enableBillit' => $row['enableBillit'],
            'apiKey' => $row['apiKey'],
            'userName' => $row['userName'],
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
            'wapobelDatabase' => $row['wapobelDatabase']
        ];

        // Je kunt hier later andere objecten toevoegen, bv:
        // $response['otherData'] = [...];
    } else {
        $response['wateringDetails'] = null; // geen resultaat gevonden
    }
} else {
    $response['wateringDetails'] = null; // geen geldig ID
}

// Stuur JSON terug
echo json_encode($response);
?>