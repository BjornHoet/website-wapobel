<?php
// bin/deletes/deleteWateringDetails.php
header('Content-Type: application/json');

$prefix = '../../'; // adjust if needed
require $prefix . 'bin/init.php';
require $prefix . 'bin/database/connect.php';

global $mysqli;

$rows = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : [];
$type = isset($_POST['type']) ? $_POST['type'] : '';

if (!$rows || !is_array($rows) || !$type) {
    http_response_code(400);
    echo json_encode(['error' => 'No rows or type provided']);
    exit;
}

switch ($type) {
    case 'posten':
        $stmt = $mysqli->prepare("DELETE FROM posten WHERE postId = ?");
        foreach ($rows as $row) {
            $stmt->bind_param("i", $row['postId']);
            $stmt->execute();
        }
        break;

    case 'rekeningen':
        $stmt = $mysqli->prepare("DELETE FROM rekeningen WHERE rekeningId = ?");
        foreach ($rows as $row) {
            $stmt->bind_param("i", $row['rekeningId']);
            $stmt->execute();
        }
        break;

    case 'boekjaren':
        $stmt = $mysqli->prepare("DELETE FROM boekjaren WHERE wateringId = ? AND jaar = ?");
        foreach ($rows as $row) {
            $stmt->bind_param("ii", $row['wateringId'], $row['jaar']);
            $stmt->execute();
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown type']);
        exit;
}

echo json_encode(['success' => true]);
