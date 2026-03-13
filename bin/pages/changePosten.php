<?php
include '../init.php';

$category = '';
$referentie = '';
$omschrijving = '';
$raming = '';
$actief = '';

foreach ($_POST as $key => $value) {
	$category = substr($key, 0, 1);
	$postId = substr($key, 6);
	$fieldCat = substr($key, 2, 3);
	
	switch ($fieldCat) {
		case 'ref':
			$referentie = $value;
			break;
		case 'oms':
			$omschrijving = $value;
			break;
		case 'ram':
			$raming = $value;
//			$raming = str_replace(".","", $raming);
//			$raming = str_replace(",",".", $raming);
			break;
		case 'act':
			$actief = $value;
			break;
		case 'end':
			$doPost = 'X';
		}
	
	if($doPost === 'X') {
		if($category === 'p') {
			changePost($postId, $referentie, $omschrijving, $raming, $actief);
			}
		else {
			changeSubPost($postId, $referentie, $omschrijving, $raming, $actief);
			}
		
		//echo $postId.'-'.$referentie.'-'.$omschrijving.'-'.$raming.'-'.$actief.'-';
		//echo '<br>';

		$category = '';
		$referentie = '';
		$omschrijving = '';
		$raming = '';
		$actief = '';
		$doPost = '';
		}
	}

header("Location: ../../posten/");
exit();
?>