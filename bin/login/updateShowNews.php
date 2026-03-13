<?php
include '../init.php';
global $mysqliLogin;

$userId = $_SESSION['userId']; // of jouw field

$stmt = $mysqliLogin->prepare("UPDATE users SET showNews = 0 WHERE userId = ?");
$ok = $stmt->execute([$userId]);

if ($ok) {
    echo "OK";
} else {
    echo "ERROR";
}