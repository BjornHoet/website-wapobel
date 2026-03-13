<?php
$prefix = '../';

include $prefix.'/bin/init.php';

require_once '../dompdf/autoload.inc.php'; //we've assumed that the dompdf directory is in the same directory as your PHP file. If not, adjust your autoload.inc.php i.e. first line of code accordingly.
// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('defaultFont', 'Helvetica');
if($localhost !== 'X') {
	$options->set('isHtml5ParserEnabled', true);
	$options->set('isRemoteEnabled', true);
//	$options->set('isPhpEnabled', true);
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
$html = $html . '  .text-xl { font-size: 17px !important; }';
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
$html = $html . '  .pl-2 { padding-left: 5px !important; }';
$html = $html . '</style>';
$html = $html . '</head>';
$html = $html . '<body>';
$html = $html . '<img style="height: 25px; position: fixed; top: -31; right: 0; bottom: 0; left: 0; margin: auto;" src="https://www.wapobel.be/img/logo-horizontal-small.png">';
$html = $html . '<header><h3 class="text-center">' . $wateringData['omschrijving'] . ' - Jaaroverzicht ' . $wateringJaar . '</h3></header>';

$posten = getPostenJaaroverzicht($wateringData['wateringId'], $wateringJaar);

$sections = [];

foreach ($posten as $post) {

    // ---------------------------------------------------
    // Fetch hoofdpost + boekingen
    // ---------------------------------------------------
    $hoofdPost = getHoofdPostData($post['hoofdpostId']);
    $boekingenResult = getBoekingenPost(
        $wateringData['wateringId'],
        $wateringJaar,
        $post['postId'],
        $post['subpostId']
    );

    // Convert mysqli_result → array
    $boekingen = [];
    if ($boekingenResult instanceof mysqli_result) {
        while ($row = $boekingenResult->fetch_assoc()) {
            $boekingen[] = $row;
        }
    }

    // Skip posts with no boekingen
    if (empty($boekingen)) {
        continue;
    }

    // ---------------------------------------------------
    // Determine fields for this section
    // ---------------------------------------------------
    $hoofdpostTitel = ($hoofdPost['useKey'] === 'O') ? 'Ontvangsten' : 'Uitgaven';

    if ($post['subpostReferentie'] !== null) {
        $referentie   = $hoofdPost['referentie'] . ' ' . $post['postReferentie'] . $post['subpostReferentie'];
        $omschrijving = $post['subpostOmschrijving'];
        $raming       = currencyConv($post['subpostRaming']);
        $actief       = $post['subpostActief'];
    } else {
        $referentie   = $hoofdPost['referentie'] . ' ' . $post['postReferentie'];
        $omschrijving = $post['postOmschrijving'];
        $raming       = currencyConv($post['postRaming']);
        $actief       = $post['postActief'];
    }

    // Skip inactive posts
    if ($actief !== 'X') {
        continue;
    }

    // ---------------------------------------------------
    // Build the PDF section HTML
    // ---------------------------------------------------
    $section = '';

    $section .= '<hr class="solid">';
    $section .= '<table style="width: 100%"><tr><td style="width: 60%">';
    $section .= '<span class="text-xl"><b>' . $hoofdpostTitel . '</b></span></td><td class="text-right" style="width: 40%">';
    $section .= '<span class="text-l"><b>Toegestane raming</b>: ' . $raming . '</span></td></tr>';

    $section .= '<tr><td colspan="2">&nbsp;</td></tr>';
    $section .= '<tr><td colspan="2"><span class="text-l"><b>Referentie</b>: ' . $referentie . '</span></td></tr>';
    $section .= '<tr><td colspan="2"><span class="text-l"><b>Voorwerp</b>: ' . $omschrijving . '</span></td></tr>';
    $section .= '</table>';

    // Table start
    $section .= '<table class="table-dark" style="margin-top: 20px;" width="100%" cellspacing="0">';
    $section .= '<thead class="text-center"><tr class="thead-dark"><th class="text-m pt-2 pb-2">Datum</th>';

    if ($useNummering === 'X') {
        $section .= '<th class="text-m pt-2 pb-2">Factuurnr</th>';
    }

    $section .= '<th class="text-m pt-2 pb-2">Omschrijving</th><th class="text-m pt-2 pb-2">Bedrag</th></tr></thead>';
    $section .= '<tbody>';

    // Table rows
    $totaal = 0;
    foreach ($boekingen as $boek) {
        $totaal += $boek['bedrag'];

        if ($useNummering === 'X') {
            $section .= '
            <tr>
                <td style="width: 10%">' . sprintf('%02d', $boek['dag']) . '/' . sprintf('%02d', $boek['maand']) . '</td>
                <td style="width: 10%">' . $boek['nummering'] . '</td>
                <td style="width: 65%">' . $boek['omschrijving'] . '</td>
                <td class="text-right" style="width: 15%">' . currencyConv($boek['bedrag']) . '</td>
            </tr>';
        } else {
            $section .= '
            <tr>
                <td style="width: 10%">' . sprintf('%02d', $boek['dag']) . '/' . sprintf('%02d', $boek['maand']) . '</td>
                <td style="width: 75%">' . $boek['omschrijving'] . '</td>
                <td class="text-right" style="width: 15%">' . currencyConv($boek['bedrag']) . '</td>
            </tr>';
        }
    }

    $section .= '</tbody>';

    // Table footer
    $colSpan = ($useNummering === 'X') ? 3 : 2;

	$section .= '
	<tfoot>
		<tr class="thead-dark">
			<th colspan="' . $colSpan . '" 
				class="text-m pt-2 pb-2 pl-2"
				style="text-align: left;">
				TOTAAL
			</th>
			<th class="text-m pt-2 pb-2 text-right">' . currencyConv($totaal) . '</th>
		</tr>
	</tfoot>';

    $section .= '</table>';

    // Save section
    $sections[] = $section;
}

// --------------------------------------------------------------------
 // Output sections with page breaks ONLY between them
// --------------------------------------------------------------------
foreach ($sections as $i => $sec) {
    $html .= $sec;

    if ($i < count($sections) - 1) {
        $html .= '<div class="page_break"></div>';
    }
}

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
$dompdf->stream('Jaaroverzicht ' . $wateringJaar . ' - ' . $wateringData['omschrijving'] . '.pdf',array('Attachment'=>0));
?>