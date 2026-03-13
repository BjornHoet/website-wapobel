<?php
include '../../bin/init.php';
global $mysqliLogin;

header('Content-Type: application/json');

try {
    $query = "UPDATE users SET showNews = 1";

    if (!$mysqliLogin->query($query)) {
        throw new Exception($mysqliLogin->error);
    }

    echo json_encode([
        'success' => true
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$json = writeAdminUsers();
exit;