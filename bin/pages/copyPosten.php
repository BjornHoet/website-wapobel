<?php
include '../init.php';

$posten = getDefaultPosten('2026');
foreach($posten as $post) {
	addPost(7, '2026', $post['hoofdpostId'], $post['referentie'], $post['omschrijving']);
}


exit();
?>