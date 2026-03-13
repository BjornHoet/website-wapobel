<?php
include '../init.php';

if (!isset($_GET['hoofdpostId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'hoofdpostId parameter is required']);
    exit;
}

$hoofdpostId = $_GET['hoofdpostId'];

// Fetch hoofdpost data
$result = getHoofdPostData($hoofdpostId);

if ($result) {
    echo json_encode($result);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Hoofdpost not found']);
}