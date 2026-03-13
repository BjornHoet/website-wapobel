<?php
$prefix = '../';

include $prefix.'/bin/init.php';

require_once '../dompdf/autoload.inc.php'; //we've assumed that the dompdf directory is in the same directory as your PHP file. If not, adjust your autoload.inc.php i.e. first line of code accordingly.
// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

$hoofdPosten = getHoofdPostenAll($wateringJaar);
$month = date('m');

$options = new Options();
$options->set('defaultFont', 'Helvetica');
if($localhost !== 'X') {
	$options->set('isHtml5ParserEnabled', true);
	$options->set('isRemoteEnabled', true);
}
$dompdf = new Dompdf($options);

$html = '';
$html = $html . '<html>';
$html = $html . '<head>';
$html = $html . '<style>';
$html = $html . '  @page { margin-top: 70px; margin-bottom: 50px; margin-left: 40px; margin-right: 40px; }';
$html = $html . '  body { font-family: Helvetica; font-size: 10px; }';
$html = $html . '  header { position: fixed; top: -50px; left: 0px; right: 0px; height: 50px; }';
$html = $html . '  footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
$html = $html . '  .text-header { font-size: 13px; }';
$html = $html . '  .text-center { text-align: center; }';
$html = $html . '  .text-right { text-align: right; }';
$html = $html . '  .text-left { text-align: left; }';
$html = $html . '  .text-l { font-size: 13px !important; }';
$html = $html . '  .text-m { font-size: 12px !important; }';
$html = $html . '  .text-u { text-transform: uppercase !important; }';
$html = $html . '  .text-bold { font-weight: bold !important; }';
$html = $html . '  .text-black { color: #000000 !important; }';
$html = $html . '  .table-dark { width: 100%; margin-bottom: 1rem; color: #858796; border: 0px }';
$html = $html . '  .table-dark th, .table-dark td { padding: 0.05rem; vertical-align: top; border-top: 1px solid #e3e6f0; }';
$html = $html . '  .table-dark thead th { vertical-align: bottom; border-bottom: 2px solid #e3e6f0; }';
$html = $html . '  .table-dark tbody + tbody { border-top: 2px solid #e3e6f0; }';
$html = $html . '  .table-dark th, .table-bordered td { border: 1px solid #e3e6f0; }';
$html = $html . '  .table-dark td, .table-dark th { font-size: 10px;}';
$html = $html . '  .table-dark td { color: #000000; border: 1px solid #e3e6f0; }';
$html = $html . '  .thead-dark { color: #fff; background-color: #5a5c69; border-color: #6c6e7e; }';
$html = $html . '  .page_break { page-break-before: always; }';
$html = $html . '  .page-break-avoid { page-break-inside: avoid; }';
$html = $html . '  .pt-1 { padding-top: 2px !important; }';
$html = $html . '  .pt-1 { padding-top: 2px !important; }';
$html = $html . '  .pt-2 { padding-top: 5px !important; }';
$html = $html . '  .pb-2 { padding-bottom: 5px !important; }';
$html = $html . '</style>';
$html = $html . '</head>';
$html = $html . '<body>';
$html = $html . '<img style="height: 25px; position: fixed; top: -31; right: 0; bottom: 0; left: 0; margin: auto;" src="https://www.wapobel.be/img/logo-horizontal-small.png">';
$html = $html . '<header><h3 class="text-center">' . $wateringData['omschrijving'] . ' - Overzicht posten ' . $wateringJaar . '</h3></header>';

$hoofdPostenOpbrengsten = getHoofdPosten('O', $wateringJaar);
$hoofdPostenUitgaven = getHoofdPosten('U', $wateringJaar);
$totaalRamingO = 0;
$totaalBedragO = 0;

$html = $html . '<hr class="solid">';
$html = $html . '<h2>ONTVANGSTEN</h2>';

// Hoofdposten Ontvangsten
$totaalOpbrengsten = 0;
foreach ($hoofdPostenOpbrengsten as $hoofdPostOpbrengst) { 
	$totaalHoofdPost = 0;
	$posten = getPostenActief($wateringData['wateringId'], $wateringJaar, $hoofdPostOpbrengst['hoofdpostId']);

	$postExists = '';
	foreach($posten as $post) {
		$postExists = 'X';
		break;
		}

	if($postExists === 'X') {	
		$html = $html . '<table class="table-dark page-break-avoid" width="100%" cellspacing="0">';
		$html = $html . '<thead class="text-center"><tr class="thead-dark"><th class="text-l text-u pt-1 pb-1" colspan="2">' . $hoofdPostOpbrengst['referentie'] . ' ' . $hoofdPostOpbrengst['omschrijving'] . '</th></tr></thead>';
		$html = $html . '<tbody>';

		// Posten
		foreach ($posten as $post) {
			$totaalPost = 0;
			$totaalPost = $post['raming'];
			
			$subPosten = getSubPostenActief($wateringData['wateringId'], $wateringJaar, $post['postId']);
			$subPostThere = "";
			foreach ($subPosten as $subPost) {
				$subPostThere = "X";
				$totaalPost = $totaalPost + $subPost['raming'];
				}

			$html = $html . '<tr><td class="text-m" style="width: 6%">' . $hoofdPostOpbrengst['referentie'] . ' ' . $post['referentie'] . '</td><td class="text-m" style="width: 77%">' . $post['omschrijving'] . '</td></tr>';

			foreach ($subPosten as $subPost) {
				$html = $html . '<tr><td style="padding-left: 15px; width: 6%">' . $hoofdPostOpbrengst['referentie'] . ' ' . $post['referentie'] . $subPost['referentie'] . '</td><td style="padding-left: 15px; width: 77%">' . $subPost['omschrijving'] . '</td></tr>';
			}
			
			$totaalHoofdPost = $totaalHoofdPost + $totaalPost;
		}

		$html = $html . '</tbody>';
		$html = $html . '</table>';
		}
	
	$totaalOpbrengsten = $totaalOpbrengsten + $totaalHoofdPost;
	}
	
$html = $html . '<div class="page_break"></div>';

$html = $html . '<hr class="solid">';
$html = $html . '<h2>UITGAVEN</h2>';

// Hoofdposten Uitgaven
$totaalUitgaven = 0;
foreach ($hoofdPostenUitgaven as $hoofdPostUitgaaf) { 
	$totaalHoofdPost = 0;
	$posten = getPostenActief($wateringData['wateringId'], $wateringJaar, $hoofdPostUitgaaf['hoofdpostId']);

	$postExists = '';
	foreach($posten as $post) {
		$postExists = 'X';
		break;
		}

	if($postExists === 'X') {	
		$html = $html . '<table class="table-dark page-break-avoid" width="100%" cellspacing="0">';
		$html = $html . '<thead class="text-center"><tr class="thead-dark"><th class="text-l text-u pt-1 pb-1" colspan="2">' . $hoofdPostUitgaaf['referentie'] . ' ' . $hoofdPostUitgaaf['omschrijving'] . '</th></tr></thead>';
		$html = $html . '<tbody>';

		// Posten
		foreach ($posten as $post) {
			$totaalPost = 0;
			$totaalPost = $post['raming'];
			
			$subPosten = getSubPostenActief($wateringData['wateringId'], $wateringJaar, $post['postId']);
			$subPostThere = "";
			foreach ($subPosten as $subPost) {
				$subPostThere = "X";
				$totaalPost = $totaalPost + $subPost['raming'];
				}

			$html = $html . '<tr><td class="text-m" style="width: 6%">' . $hoofdPostUitgaaf['referentie'] . ' ' . $post['referentie'] . '</td><td class="text-m" style="width: 77%">' . $post['omschrijving'] . '</td></tr>';

			foreach ($subPosten as $subPost) {
				$html = $html . '<tr><td style="padding-left: 15px; width: 6%">' . $hoofdPostUitgaaf['referentie'] . ' ' . $post['referentie'] . $subPost['referentie'] . '</td><td style="padding-left: 15px; width: 77%">' . $subPost['omschrijving'] . '</td></tr>';
			}
			
			$totaalHoofdPost = $totaalHoofdPost + $totaalPost;
		}

		$html = $html . '</tbody>';
		$html = $html . '</table>';
		}
	
	$totaalUitgaven = $totaalUitgaven + $totaalHoofdPost;
	}
	
$html = $html . '</body>';
$html = $html . '</html>';

//echo $html;

$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');
// Render the HTML as PDF
$dompdf->render();
// Output the generated PDF to Browser
$dompdf->stream('Overzicht posten ' . $wateringJaar . ' - ' . $wateringData['omschrijving'] . '.pdf',array('Attachment'=>0));
?>