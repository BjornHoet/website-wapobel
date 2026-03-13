<?php
$prefix = '../';

include $prefix.'/bin/init.php';

require_once '../dompdf/autoload.inc.php'; //we've assumed that the dompdf directory is in the same directory as your PHP file. If not, adjust your autoload.inc.php i.e. first line of code accordingly.
// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

$hoofdPosten = getHoofdPostenAll($wateringJaar);
$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, $useKAS, 'X', 'A');
$skipOverdracht = false;
if(($wateringData['wateringId'] === '1' || $wateringData['wateringId'] === '2' || $wateringData['wateringId'] === '3' || $wateringData['wateringId'] === '5' || $wateringData['wateringId'] === '6') && (int)$wateringJaar < 2026)
	$skipOverdracht = true;

$month = date('m');

$options = new Options();
$options->set('defaultFont', 'Helvetica');
if($localhost !== 'X') {
	$options->set('isHtml5ParserEnabled', true);
	$options->set('isRemoteEnabled', true);
	$options->set('isPhpEnabled', true);
}
$dompdf = new Dompdf($options);

$html = '';
$html = $html . '<html>';
$html = $html . '<head>';
$html = $html . '<style>';
$html = $html . '  @page { margin-top: 70px; margin-bottom: 50px; margin-left: 40px; margin-right: 40px; }';
$html = $html . '  body { font-family: Helvetica; font-size: 11px; }';
$html = $html . '  header { position: fixed; top: -50px; left: 0px; right: 0px; height: 50px; }';
$html = $html . '  footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 50px; }';
$html = $html . '  .text-header { font-size: 14px; }';
$html = $html . '  .text-center { text-align: center; }';
$html = $html . '  .text-right { text-align: right; }';
$html = $html . '  .text-left { text-align: left; }';
$html = $html . '  .text-l { font-size: 15px !important; }';
$html = $html . '  .text-m { font-size: 12px !important; }';
$html = $html . '  .text-s { font-size: 11px !important; }';
$html = $html . '  .text-u { text-transform: uppercase !important; }';
$html = $html . '  .text-bold { font-weight: bold !important; }';
$html = $html . '  .text-black { color: #000000 !important; }';
$html = $html . '  .table-dark { width: 100%; margin-bottom: 1rem; color: #858796; border: 0px }';
$html = $html . '  .table-dark th, .table-dark td { padding: 0.1rem; vertical-align: top; border-top: 1px solid #e3e6f0; }';
$html = $html . '  .table-dark thead th { vertical-align: bottom; border-bottom: 2px solid #e3e6f0; }';
$html = $html . '  .table-dark tbody + tbody { border-top: 2px solid #e3e6f0; }';
$html = $html . '  .table-dark th, .table-bordered td { border: 1px solid #e3e6f0; }';
$html = $html . '  .table-dark td, .table-dark th { font-size: 12px;}';
$html = $html . '  .table-dark td { color: #000000; border: 1px solid #e3e6f0; }';
$html = $html . '  .thead-dark { color: #fff; background-color: #5a5c69; border-color: #6c6e7e; }';
$html = $html . '  .page_break { page-break-before: always; }';
$html = $html . '  .page-break-avoid { page-break-inside: avoid; }';
$html = $html . '  .pt-2 { padding-top: 5px !important; }';
$html = $html . '  .pb-2 { padding-bottom: 5px !important; }';
$html = $html . '  .footer .page-number:after { content: counter(page); }';
$html = $html . '</style>';
$html = $html . '</head>';
$html = $html . '<body>';
$html = $html . '<img style="height: 25px; position: fixed; top: -31; right: 0; bottom: 0; left: 0; margin: auto;" src="https://www.wapobel.be/img/logo-horizontal-small.png">';
$html = $html . '<header><h3 class="text-center">' . $wateringData['omschrijving'] . ' - Rekening ' . $wateringJaar . '</h3></header>';

$hoofdPostenOpbrengsten = getHoofdPosten('O', $wateringJaar);
$hoofdPostenUitgaven = getHoofdPosten('U', $wateringJaar);
$totaalRamingO = 0;
$totaalBedragO = 0;

$ramingReserve = getReserve($wateringData['wateringId'], $wateringJaar);

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
		$html = $html . '<table class="table-dark page-break-avoid" style="margin-top: 20px;" width="100%" cellspacing="0">';
		$html = $html . '<thead class="text-center"><tr class="thead-dark"><th class="text-l text-u pt-2 pb-2" colspan="3">' . $hoofdPostOpbrengst['referentie'] . ' ' . $hoofdPostOpbrengst['omschrijving'] . '</th></tr></thead>';
		$html = $html . '<tbody>';

		// Posten
		foreach ($posten as $post) {
			$subPostThere = '';
			/* if($hoofdPostOpbrengst['referentie'] === 'I' && $post['referentie'] === '1')
				$totaalPost = getOverdrachtTotaal($wateringData['wateringId'], $wateringJaar);
			else */
			$totaalPost = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], '');
		
			$subPosten = getSubPostenActief($wateringData['wateringId'], $wateringJaar, $post['postId']);
			
			foreach ($subPosten as $subPost) {
				$subPostThere = "X";
				break;
				}
			
			$html = $html . '<tr><td class="text-m" style="width: 8%">' . $hoofdPostOpbrengst['referentie'] . ' ' . $post['referentie'] . '</td><td class="text-m" style="width: 80%">' . $post['omschrijving'] . '</td>';
			if($subPostThere === '') {
				$html = $html . '<td class="text-m text-right" style="width: 12%">' . currencyConv($totaalPost) . '</td></tr>';
				}
			else {
				$html = $html . '<td class="text-m text-right" style="width: 12%">&nbsp;</td></tr>';
				}
			foreach ($subPosten as $subPost) {
				$totaalPost = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], $subPost['subpostId']);
				$html = $html . '<tr><td style="padding-left: 15px; width: 8%">' . $hoofdPostOpbrengst['referentie'] . ' ' . $post['referentie'] . $subPost['referentie'] . '</td><td style="padding-left: 15px; width: 80%">' . $subPost['omschrijving'] . '</td><td class="text-m text-right" style="width: 12%">' . currencyConv($totaalPost) . '</td></tr>';
				$totaalHoofdPost = $totaalHoofdPost + $totaalPost;
				$totaalPost = 0;
			}
			
			$totaalHoofdPost = $totaalHoofdPost + $totaalPost;
			$totaalPost = 0;
		}

		$html = $html . '</tbody>';
		$html = $html . '<tfoot class="text-left"><tr class="thead-dark text-left"><th class="text-m text-left" colspan="2">TOTAAL</th><th class="text-m text-right">' . currencyConv($totaalHoofdPost) . '</th>';
		$html = $html . '</tr></tfoot>';

		$html = $html . '</table>';
		}
	
	$totaalOpbrengsten = $totaalOpbrengsten + $totaalHoofdPost;
	}
	
$html = $html . '<hr class="solid">';
$html = $html . '<table class="" style="margin-top: 20px;" width="100%" cellspacing="0"><tr><td class="text-bold text-m" style="width: 85%">TOTAAL ONTVANGSTEN:</td><td class="text-right text-bold text-m" style="width: 15%">' . currencyConv($totaalOpbrengsten) . '</td></tr></table>';

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
		$html = $html . '<table class="table-dark page-break-avoid" style="margin-top: 20px;" width="100%" cellspacing="0">';
		$html = $html . '<thead class="text-center"><tr class="thead-dark"><th class="text-l text-u pt-2 pb-2" colspan="3">' . $hoofdPostUitgaaf['referentie'] . ' ' . $hoofdPostUitgaaf['omschrijving'] . '</th></tr></thead>';
		$html = $html . '<tbody>';

		// Posten
		foreach ($posten as $post) {
			$subPostThere = '';
			$totaalPost = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], '');
			
			$subPosten = getSubPostenActief($wateringData['wateringId'], $wateringJaar, $post['postId']);

			foreach ($subPosten as $subPost) {
				$subPostThere = "X";
				break;
				}

			$html = $html . '<tr><td class="text-m" style="width: 8%">' . $hoofdPostUitgaaf['referentie'] . ' ' . $post['referentie'] . '</td><td class="text-m" style="width: 77%">' . $post['omschrijving'] . '</td>';
			if($subPostThere === '') {
				$html = $html . '<td class="text-m text-right" style="width: 15%">' . currencyConv($totaalPost) . '</td></tr>';
				}
			else {
				$html = $html . '<td class="text-m text-right" style="width: 15%">&nbsp;</td></tr>';
				}

			foreach ($subPosten as $subPost) {
				$totaalPost = getBoekingBedragPost($wateringData['wateringId'], $wateringJaar, $post['postId'], $subPost['subpostId']);
				$html = $html . '<tr><td style="padding-left: 15px; width: 8%">' . $hoofdPostUitgaaf['referentie'] . ' ' . $post['referentie'] . $subPost['referentie'] . '</td><td style="padding-left: 15px; width: 77%">' . $subPost['omschrijving'] . '</td><td class="text-m text-right" style="width: 15%">' . currencyConv($totaalPost) . '</td></tr>';
				$totaalHoofdPost = $totaalHoofdPost + $totaalPost;
				$totaalPost = 0;
			}
			
			$totaalHoofdPost = $totaalHoofdPost + $totaalPost;
			$totaalPost = 0;
		}

		$html = $html . '</tbody>';
		$html = $html . '<tfoot class="text-left"><tr class="thead-dark text-left"><th class="text-m text-left" colspan="2">TOTAAL</th><th class="text-m text-right">' . currencyConv($totaalHoofdPost) . '</th>';
		$html = $html . '</tr></tfoot>';

		$html = $html . '</table>';
		}
	
	$totaalUitgaven = $totaalUitgaven + $totaalHoofdPost;
	}
	
$html = $html . '<hr class="solid">';
$html = $html . '<table class="" style="margin-top: 20px;" width="100%" cellspacing="0"><tr><td class="text-bold text-m" style="width: 85%">TOTAAL UITGAVEN:</td><td class="text-right text-bold text-m" style="width: 15%">' . currencyConv($totaalUitgaven) . '</td></tr></table>';

$html = $html . '<br><br>';

$html = $html . '<table class="page-break-avoid" style="width: 100%"><tr><td style="width: 60%; margin-left: 10px; margin-right: 10px;">';

$html = $html . '<table class="page-break-avoid" style="border: 1.5px solid; margin-top: 20px; border-radius: 0.35rem;" width="100%" cellspacing="0">';
$html = $html . '<tr><td class="text-center text-m text-bold" colspan="2" style="padding: 5px; vertical-align:top;">REKENINGEN</td></tr>';

$totaalRekeningBedrag = 0;
foreach($rekeningen as $rekening) {
	$rekeningBedrag = 0;
	if($skipOverdracht)
		$rekeningBedrag = getBoekingBedragRekening($wateringData['wateringId'], $wateringJaar, $rekening['rekeningId']);
	else
		$rekeningBedrag = getBoekingBedragRekening($wateringData['wateringId'], $wateringJaar, $rekening['rekeningId']) + $rekening['overdracht'];
	$totaalRekeningBedrag = $totaalRekeningBedrag + $rekeningBedrag;
	if($rekening['rekening'] === 'KAS')
		$html = $html . '<tr><td class="text-left text-s" style="width: 70%; padding: 5px;">' . $rekening['rekening'] . '</td><td class="text-right text-s padding: 10px;" style="width: 30%; padding: 5px;">' . currencyConv($rekeningBedrag) . '</td></tr>';
	else	
		$html = $html . '<tr><td class="text-left text-s" style="width: 70%; padding: 5px;">' . $rekening['rekening'] . ' (' . $rekening['omschrijving'] . ')</td><td class="text-right text-s padding: 10px;" style="width: 30%; padding: 5px;">' . currencyConv($rekeningBedrag) . '</td></tr>';
	}
$html = $html . '<tr><td class="text-bold text-s" style="width: 70%; padding: 5px;">&nbsp;</td><td class="text-right text-bold text-s padding: 10px;" style="width: 30%; padding: 5px;"></td></tr>';
$html = $html . '<tr><td class="text-left text-bold text-m" style="width: 70%; padding: 5px;">Totaal:</td><td class="text-right text-bold text-m padding: 10px;" style="width: 30%; padding: 5px;">' . currencyConv($totaalRekeningBedrag) . '</td></tr>';
$html = $html . '</table>';

$html = $html . '</td><td style="width: 40%; margin-left: 10px; margin-right: 10px; vertical-align:top">';
$html = $html . '<table class="page-break-avoid" style="border: 1.5px solid; margin-top: 20px; border-radius: 0.35rem;" width="100%" cellspacing="0">';
$html = $html . '<tr><td class="text-center text-m text-bold" colspan="2" style="padding: 5px; vertical-align:top;">SALDO</td></tr>';
$html = $html . '<tr><td class="text-right text-s" style="width: 60%; padding: 5px;">Totaal Ontvangsten:</td><td class="text-right text-s padding: 10px;" style="width: 40%; padding: 5px;">' . currencyConv($totaalOpbrengsten) . '</td></tr>';
$html = $html . '<tr><td class="text-right text-s" style="width: 60%; padding: 5px;">Totaal Uitgaven:</td><td class="text-right text-s padding: 10px;" style="width: 40%; padding: 5px;">' . currencyConv($totaalUitgaven) . '</td></tr>';
/* $html = $html . '<tr><td class="text-bold text-s" style="width: 60%; padding: 5px;">&nbsp;</td><td class="text-right text-bold text-s padding: 10px;" style="width: 40%; padding: 5px;"></td></tr>';
$html = $html . '<tr><td class="text-right text-bold text-m" style="width: 60%; padding: 5px;">Saldo:</td><td class="text-right text-bold text-m" style="width: 40%; padding: 5px;">' . currencyConv($totaalOpbrengsten - $totaalUitgaven) . '</td></tr>'; */
$html = $html . '<tr><td class="text-right text-bold text-m" style="width: 85%; padding: 5px;"></td><td class="text-right text-bold text-m" style="width: 15%; padding: 5px; border-top: 1px solid;">' . currencyConv($totaalOpbrengsten - $totaalUitgaven) . '</td></tr>';

$html = $html . '<tr><td class="text-bold text-m" style="width: 85%; padding: 5px;">&nbsp;</td><td class="text-right text-bold text-m padding: 10px;" style="width: 15%; padding: 5px;"></td></tr>';
$html = $html . '<tr><td class="text-right text-m" style="width: 85%; padding: 5px;">Reservefonds dd - 1 januari</td><td class="text-right text-m" style="width: 15%; padding: 5px;">' . currencyConv($ramingReserve) . '</td></tr>';
$html = $html . '<tr><td class="text-right text-m" style="width: 85%; padding: 5px;">Reservefonds dd - 31 december</td><td class="text-right text-m" style="width: 15%; padding: 5px;">' . currencyConv($ramingReserve + $totaalOpbrengsten - $totaalUitgaven) . '</td></tr>';

$html = $html . '</table>';
$html = $html . '</td></tr></table>';
$html = $html . '</body>';
$html = $html . '</html>';

//echo $html;

$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation

$dompdf->setPaper('A4', 'portrait');
// Render the HTML as PDF
$dompdf->render();

$canvas = $dompdf->getCanvas();
$canvas->page_script('
    $current_page = $PAGE_NUM;
    $total_pages = $PAGE_COUNT;
    $font = $fontMetrics->getFont("Helvetica", "normal"); 
    $pdf->text(505.64, 800.89, "pagina $current_page / $total_pages", $font, 10, array(0,0,0));
');

// Output the generated PDF to Browser
$dompdf->stream('Rekening ' . $wateringJaar . ' - ' . $wateringData['omschrijving'] . '.pdf',array('Attachment'=>0));
?>