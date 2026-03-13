<?php
session_start();

if (isset($_POST['showOnlyActive'])) {
    $_SESSION['showOnlyActive'] = $_POST['showOnlyActive'] == 1 ? true : false;
}
?>