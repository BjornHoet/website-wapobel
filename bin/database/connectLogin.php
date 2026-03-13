<?php
$connect_error = 'Sorry, er is een probleem om connectie te maken met de server';
$mysqliLogin = new mysqli("localhost", "root", "", "wapobel_belogin") or die($connect_error);
?>