<?php
include '../init.php';
global $mysqli;

header('Content-Type: application/json');

// read the raw request body
$raw = file_get_contents("php://input");

// decode JSON to PHP associative array
$data = json_decode($raw, true);

// check for JSON parse errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

// get the id
$id = $data['id'] ?? null;

// debug: log or echo what PHP received
if ($id === null) {
    echo json_encode(['error' => 'Missing id', 'received' => $data]);
    exit;
}

deleteBoeking($id);

echo json_encode(["status" => "success", "message" => "Data updated"]);
exit();
?>