<?php
include '../init.php';

$wateringId = $wateringData['wateringId'];
$ramingReserve = getReserve($wateringId, $wateringJaar);
if($ramingReserve == '')
    $ramingReserve = 0;

$opbrengsten = getOpbrengsten($wateringId, $wateringJaar);
$uitgaven = getUitgaven($wateringId, $wateringJaar);
																		
$nieuweReserve = $ramingReserve + $opbrengsten - $uitgaven;

$nieuwJaar = $wateringJaar + 1;
$nieuwJaarBestaat = checkNieuwJaarBestaat($wateringId, $nieuwJaar);

if($nieuwJaarBestaat === false) {
	openNieuwJaar($wateringId, $nieuwJaar);
	openReserve($wateringId, $nieuwJaar, $nieuweReserve);
	$posten = getPostenAlsoInactive($wateringId, $wateringJaar);
	foreach($posten as $post) {
		$subPosten = getSubPosten($wateringId, $wateringJaar, $post['postId']);
		$hoofdPost = getHoofdPostData($post['hoofdpostId']);
		$newHoofdPostId = getHoofdPostIdForKey($nieuwJaar, $hoofdPost['useKey'], $hoofdPost['typeId'], $hoofdPost['referentie']);
		
		echo $post['postId'] . ' - ' . $post['hoofdpostId'] . ' - ' . $newHoofdPostId;
		$nieuwePostId = addPostBoekjaar($wateringId, $nieuwJaar, $newHoofdPostId, $post['referentie'], $post['omschrijving'], $post['raming'], $post['actief'], $post['overdrachtPost']);
		
		foreach($subPosten as $subPost) {
			addSubPostBoekjaar($wateringId, $nieuwJaar, $nieuwePostId, $subPost['referentie'], $subPost['omschrijving'], $subPost['raming'], $subPost['actief']);
			}
		}

	$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, 'X', 'X', 'A');	
	foreach($rekeningen as $rekening) {
		$overdracht = getOverdracht($wateringId, $wateringJaar, 13, $rekening['rekeningId']);
		addRekening($wateringId, $nieuwJaar, $rekening['rekening'], $rekening['omschrijving'], $overdracht, $rekening['positie']);
		}
	}
else {
	$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, 'X', 'X', 'A');	
	foreach($rekeningen as $rekening) {
		$overdracht = getOverdracht($wateringId, $wateringJaar, 13, $rekening['rekeningId']);
		changeOverdracht($wateringId, $nieuwJaar, $rekening['rekening'], $overdracht);
		}
	updateReserve($wateringId, $nieuwJaar, $nieuweReserve);
	}

openBoekjaar($wateringData['wateringId'], $nieuwJaar);
sluitBoekjaar($wateringId, $wateringJaar);


$_SESSION['wateringJaar'] = $nieuwJaar;
$_SESSION['wateringMaand'] = 1;

exit();
?>