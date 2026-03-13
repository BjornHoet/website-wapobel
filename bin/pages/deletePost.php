<?php
include '../init.php';
global $mysqli;

if(isset($_POST['postId']) && $_POST['postId'] !== '') {
	if($_POST['type'] === 'P') {
		$result = deletePost($_POST['postId']);
		}
	if($_POST['type'] === 'S') {
		$result = deleteSubpost($_POST['postId']);
		}
	}

exit();
?>