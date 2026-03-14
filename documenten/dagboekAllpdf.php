<?php
$prefix = '../';

include $prefix.'/bin/init.php';

require_once '../dompdf/autoload.inc.php'; //we've assumed that the dompdf directory is in the same directory as your PHP file. If not, adjust your autoload.inc.php i.e. first line of code accordingly.
// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

$hoofdPosten = getHoofdPostenAll($wateringJaar);
$month = (int)getLastMonth($wateringData['wateringId'], $wateringJaar);

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
$html = $html . '  @page { margin-top: 15px; margin-bottom: 10px; margin-left: 20px; margin-right: 20px; }';
$html = $html . '  body { font-family: Helvetica; font-size: 11px; }';
$html = $html . '  .text-header { font-size: 14px; }';
$html = $html . '  .text-center { text-align: center; }';
$html = $html . '  .text-right { text-align: right; }';
$html = $html . '  .text-left { text-align: left; }';
$html = $html . '  .text-black { color: #000000; }';
$html = $html . '  .table-dark { width: 100%; margin-bottom: 1rem; color: #858796; border: 0px }';
$html = $html . '  .table-dark th, .table-dark td { padding: 0.1rem; vertical-align: top; }';
$html = $html . '  .table-dark thead th { vertical-align: bottom; }';
$html = $html . '  .table-dark tbody + tbody { border-top: 2px solid #e3e6f0; }';
$html = $html . '  .table-dark th, .table-bordered td {  }';
$html = $html . '  .table-dark td, .table-dark th { font-size: 9px;}';
$html = $html . '  .table-dark td { color: #000000; }';
$html = $html . '  .border-right { border-right: 1px solid #e3e6f0; }';
$html = $html . '  .thead-dark { color: #fff; background-color: #5a5c69; border-color: #6c6e7e; }';
$html = $html . '  .table-dark, .table-dark th, .table-dark td { border: 0.5px solid black; border-collapse: collapse; }';
$html = $html . '  .page_break { page-break-before: always; }';
$html = $html . '</style>';
$html = $html . '</head>';
$html = $html . '<body>';

for($maand = 1; $maand <= $month; $maand++) {
$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, $useKAS, 'X', 'A');
$boekingen = getBoekingen($wateringData['wateringId'], $wateringJaar, $maand, $useNummering, $sortering);
$overdrachtDienstJaarMaand_O = 0;
$overdrachtDienstJaarMaand_U = 0;

// Calculate totals for footer rows
$totals = [];
foreach ($rekeningen as $rekening) {
    $totals['rek_' . $rekening['rekeningId'] . '_O'] = 0;
    $totals['rek_' . $rekening['rekeningId'] . '_U'] = 0;
}

// Calculate totals from boekingen
foreach ($boekingen as $boeking) {
    foreach ($rekeningen as $rekening) {
        $boekingsBedragO = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'O');
        $boekingsBedragU = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'U');
        
        if ($boekingsBedragO['bedrag'] !== '' && $boekingsBedragO['bedrag'] !== '0.00') {
            $totals['rek_' . $rekening['rekeningId'] . '_O'] += $boekingsBedragO['bedrag'];
        }
        if ($boekingsBedragU['bedrag'] !== '' && $boekingsBedragU['bedrag'] !== '0.00') {
            $totals['rek_' . $rekening['rekeningId'] . '_U'] += $boekingsBedragU['bedrag'];
        }
    }
}

$aantalRekNoKas = 0;
foreach($rekeningen as $rek) {
	if($rek['rekening'] !== 'KAS') {
		$aantalRekNoKas = $aantalRekNoKas + 1;
		}
	}	

$html = $html . '<table width="100%"><tr><td width="20%"><img height="25px" src="https://www.wapobel.be/img/logo-horizontal-small.png"></td>';
$html = $html . '<td width="60%" class="text-center text-header"><b>' . $wateringData['omschrijving'] . ' - Dag- en Kasboek</b></td>';
$html = $html . '<td width="20%" class="text-right"><span class="text-header"><b>Datum</b>: ' . monthNames[$maand - 1] . ' ' . $wateringJaar . '</span></td></tr></table>';

if($useNummering === 'X') {
	$html = $html . '<table class="table-dark" style="margin-top: 20px;" width="100%" cellspacing="0"><thead class="text-center"><tr class="thead-dark"><th style="width: 3%; border-right: 0.5px solid #e3e6f0;">Datum<br>&nbsp;</th>';
	$html = $html . '<th style="width: 5%; border-right: 0.5px solid #e3e6f0;">Post<br>&nbsp;</th><th style="width: 5%; border-right: 0.5px solid #e3e6f0;">Factuurnr<br>&nbsp;</th><th style="width: 5%; border-right: 0.5px solid #e3e6f0;">Billitnr<br>&nbsp;</th>';
	$html = $html . '<th style="width: 19%; border-right: 0.5px solid #e3e6f0;">Omschrijving<br>&nbsp;</th><th colspan="2" style="border-right: 0.5px solid #e3e6f0;">DIENSTJAAR ' . $wateringJaar . '<br>&nbsp;</th>';
	} else {
	$html = $html . '<table class="table-dark" style="margin-top: 20px;" width="100%" cellspacing="0"><thead class="text-center"><tr class="thead-dark"><th style="width: 4%; border-right: 0.5px solid #e3e6f0;">Datum<br>&nbsp;</th>';
	$html = $html . '<th style="width: 6%; border-right: 0.5px solid #e3e6f0;">Post<br>&nbsp;</th><th style="width: 5%; border-right: 0.5px solid #e3e6f0;">Billitnr<br>&nbsp;</th><th style="width: 22%; border-right: 0.5px solid #e3e6f0;">Omschrijving<br>&nbsp;</th>';
	$html = $html . '<th colspan="2" style="border-right: 0.5px solid #e3e6f0;">DIENSTJAAR ' . $wateringJaar . '<br>&nbsp;</th>';
}

$counter = 0;
$tdClass = 'style="border-right: 0.5px solid #e3e6f0;"';
foreach ($rekeningen as $rekening) {
	if($counter === $aantalRekNoKas) {
		$tdClass = '';
		}
	$counter = $counter + 1;	
	
	${'rek_'.$rekening['rekeningId'].'_O'} = 0;
	${'rek_'.$rekening['rekeningId'].'_U'} = 0;
	
	$html = $html . '<th colspan="2" '. $tdClass . '>' . $rekening['rekening'] . '<br>';
	if($rekening['rekening'] === 'KAS') {
		$html = $html . '&nbsp;'; }
	else { $html = $html . $rekening['omschrijving']; }
		$html = $html . '</th>'; 
	}

if($useNummering === 'X') {
	$colSpanHeading = '5';
	} else {
	$colSpanHeading = '4';
	}

$html = $html . '</tr><tr bgcolor="#dddddd" class="text-xs text-black"><th style="border-bottom: 1px solid #000000;" colspan="' . $colSpanHeading . '"></th>';
$html = $html . '<th style="border-bottom: 1px solid #000000;">Ontvangsten</th><th style="border-bottom: 1px solid #000000;">Uitgaven</th>';

foreach ($rekeningen as $rekening) {
	$html = $html . '<th style="border-bottom: 1px solid #000000;">Ontvangsten</th><th style="border-bottom: 1px solid #000000;">Uitgaven</th>';
	}
$html = $html . '</tr></thead>';
if($maand === 1) {
	$html = $html . '<tbody><tr><td>01/' . sprintf('%02d', $maand) . '</td><td>ONT I 1</td><td></td>';
	if($useNummering === 'X') {
		$html = $html . '<td></td>';
		}
	$html = $html . '<td>Boni saldo van vorige dienstjaren</td>';
}
else {
	$html = $html . '<tbody><tr><td>01/' . sprintf('%02d', $maand) . '</td><td></td><td></td>';
	if($useNummering === 'X') {
		$html = $html . '<td></td>';
		}
	$html = $html . '<td>Overdracht</td>';
}

$overdrachtDienstjaar = getOverdrachtDienstjaar($wateringData['wateringId'], $wateringJaar, $maand);
$overdrachtDienstJaarMaand_O = $overdrachtDienstjaar;
$html = $html . '<td>' . currencyConv($overdrachtDienstjaar) . '</td><td></td>';

$aantalRek = 0;
foreach ($rekeningen as $rekening) { 
	$aantalRek = $aantalRek + 1;
	$overdracht = getOverdracht($wateringData['wateringId'], $wateringJaar, $maand, $rekening['rekeningId']);
	${'rek_'.$rekening['rekeningId'].'_O'} = ${'rek_'.$rekening['rekeningId'].'_O'} + $overdracht;
	$html = $html . '<td>' . currencyConv($overdracht) . '</td><td></td>';
	}

$aantalRek = ($aantalRek * 2) + 2;
$html = $html . '</tr>';

$bgColor = '#ffffff';
foreach ($boekingen as $boeking) { 
	if($bgColor === '#dddddd') {
		$bgColor = '#ffffff'; 
		}
	else {
		$bgColor = '#dddddd'; 
		}
	
	$dienstjaarO = getBoekingBedragDataDienstjaar($boeking['boekId'], 'O');
	if($dienstjaarO === '' or $dienstjaarO === '0.00') {
		$dienstjaarO = '';
		} else {
		$overdrachtDienstJaarMaand_O = $overdrachtDienstJaarMaand_O + $dienstjaarO;
		$dienstjaarO = currencyConv($dienstjaarO);
		}
	$dienstjaarU = getBoekingBedragDataDienstjaar($boeking['boekId'], 'U');
	if($dienstjaarU === '' or $dienstjaarU === '0.00') {
		$dienstjaarU = '';
		} else {
		$overdrachtDienstJaarMaand_U = $overdrachtDienstJaarMaand_U + $dienstjaarU;
		$dienstjaarU = currencyConv($dienstjaarU);
		}
	
	$postData = getPostData($boeking['postId']); 
	$subPostData = getSubPostData($boeking['subPostId']);
	$hoofdPostData = getHoofdPostData($postData['hoofdpostId']);
	$boekingsDatum = $boeking['jaar'] . '-' . sprintf('%02d', $boeking['maand']) . '-' . sprintf('%02d', $boeking['dag']);
	$html = $html . '<tr bgcolor="' . $bgColor . '">';
	$html = $html . '<td>' . sprintf('%02d', $boeking['dag']) . '/' . sprintf('%02d', $boeking['maand']) . '</td>';
	$html = $html . '<td>';
	if($boeking['postId'] !== '0') {
		if($hoofdPostData['useKey'] === 'U') { $html = $html . 'UIT '; } else { $html = $html . 'ONT '; }
		$html = $html . $hoofdPostData['referentie'] . ' ' . $postData['referentie'] . ' ' . $subPostData['referentie'];
		}
		
	$html = $html . '</td>';
	if($useNummering === 'X') {
		$html = $html . '<td>' . $boeking['nummering'] . '</td>';
		}

	$html = $html . '<td>' . $boeking['billitNumber'] . '</td>';	
	$html = $html . '<td>' . $boeking['omschrijving'] . '</td>';
	
	$columnWidth = 68 / $aantalRek; 

	$html = $html . '<td style="width: ' . $columnWidth . '%">' . $dienstjaarO . '</td>';
	$html = $html . '<td style="width: ' . $columnWidth . '%">' . $dienstjaarU . '</td>';

	foreach ($rekeningen as $rekening) {
		$boekingsBedragO = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'O');
		${'rek_'.$rekening['rekeningId'].'_O'} = ${'rek_'.$rekening['rekeningId'].'_O'} + $boekingsBedragO['bedrag'];
		$boekingsBedragU = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'U');
		${'rek_'.$rekening['rekeningId'].'_U'} = ${'rek_'.$rekening['rekeningId'].'_U'} + $boekingsBedragU['bedrag'];

		if($boekingsBedragO['bedrag'] === '' or $boekingsBedragO['bedrag'] === '0.00' or empty($boekingsBedragO['bedrag'])) {
			$oldValO = '';
			} else {
			$oldValO = currencyConv($boekingsBedragO['bedrag']);
			}

		if($boekingsBedragU['bedrag'] === '' or $boekingsBedragU['bedrag'] === '0.00' or empty($boekingsBedragU['bedrag'])) {
			$oldValU = '';
			} else {
			$oldValU = currencyConv($boekingsBedragU['bedrag']);
			}

		$html = $html . '<td style="width: ' . $columnWidth . '%">' . $oldValO . '</td>';
		$html = $html . '<td style="width: ' . $columnWidth . '%">' . $oldValU . '</td>';
		 }
	$html = $html . '</tr>';
	}
	
$html = $html . '</tbody>';

$html = $html . '<tfoot class="text-left"><tr class="thead-dark text-left"><th style="border-bottom: 0.5px solid #e3e6f0; border-right: 0.5px solid #e3e6f0;" class="text-left" colspan="' . $colSpanHeading . '">TOTAAL</th>';
$html = $html . '<th style="border-bottom: 0.5px solid #e3e6f0; border-right: 0.5px solid #e3e6f0;" class="text-left">' . currencyConv($overdrachtDienstJaarMaand_O) . '</th>';
$html = $html . '<th style="border-bottom: 0.5px solid #e3e6f0; border-right: 0.5px solid #e3e6f0;" class="text-left">' . currencyConv($overdrachtDienstJaarMaand_U) . '</th>';

$counter = 0;
$tdClass = 'border-right: 0.5px solid #e3e6f0;';
foreach ($rekeningen as $rekening) {
	if($counter === $aantalRekNoKas) {
		$tdClass = '';
		}
	$counter = $counter + 1;	
	$html = $html . '<th style="border-bottom: 0.5px solid #e3e6f0; border-right: 0.5px solid #e3e6f0;" class=" text-left">' . currencyConv(${'rek_'.$rekening['rekeningId'].'_O'}) . '</th>';
	$html = $html . '<th style="border-bottom: 0.5px solid #e3e6f0; ' . $tdClass . '" class=" text-left">' . currencyConv(${'rek_'.$rekening['rekeningId'].'_U'}) . '</th>';
	}
	
$html = $html . '</tr><tr class="thead-dark text-left"><th style="border-right: 0.5px solid #e3e6f0;" class="text-left" colspan="' . $colSpanHeading . '">OVER TE DRAGEN</th>';
$html = $html . '<th style="border-right: 0.5px solid #e3e6f0;" class=" text-left">' . currencyConv($overdrachtDienstJaarMaand_O - $overdrachtDienstJaarMaand_U) . '</th>';
$html = $html . '<th style="border-right: 0.5px solid #e3e6f0;" class=" text-left"></th>';

$counter = 0;
$tdClass = 'style="border-right: 0.5px solid #e3e6f0;"';
foreach ($rekeningen as $rekening) {
	if($counter === $aantalRekNoKas) {
		$tdClass = '';
		}
	$counter = $counter + 1;	
	$overTeDragen = ${'rek_'.$rekening['rekeningId'].'_O'} - ${'rek_'.$rekening['rekeningId'].'_U'};
	$html = $html . '<th style="border-right: 0.5px solid #e3e6f0;" class=" text-left">' . currencyConv($overTeDragen) . '</th><th ' . $tdClass . '></th>';
	}
$html = $html . '</tr><tr class="thead-dark text-left"><th style="border-right: 0.5px solid #e3e6f0;" class="text-left" colspan="' . $colSpanHeading . '">REKENINGTOTAAL</th>';
$html = $html . '<th style="border-right: 0.5px solid #e3e6f0;" class=" text-left"></th>';
$html = $html . '<th style="border-right: 0.5px solid #e3e6f0;" class=" text-left"></th>';

$counter = 0;
$tdClass = 'style="border-right: 0.5px solid #e3e6f0;"';
foreach ($rekeningen as $rekening) {
	if($counter === $aantalRekNoKas) {
		$tdClass = '';
		}
	$counter = $counter + 1;	
	$rekBalance = getOverdracht($wateringData['wateringId'], $wateringJaar, '1', $rekening['rekeningId'], true);
	$rekBalance = $rekBalance + (${'rek_'.$rekening['rekeningId'].'_O'} - ${'rek_'.$rekening['rekeningId'].'_U'});
	$html = $html . '<th style="border-right: 0.5px solid #e3e6f0;" class=" text-left">' . currencyConv($rekBalance) . '</th><th ' . $tdClass . '></th>';
	}
$html = $html . '</tr></tfoot>';

$html = $html . '</table>';

if ($maand < $month) {
    $html .= '<div class="page_break"></div>';
	}
}

$html = $html . '</body>';
$html = $html . '</html>';

//echo $html;

$dompdf->loadHtml($html);
// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');
// Render the HTML as PDF
$dompdf->render();

$canvas = $dompdf->getCanvas();
$canvas->page_script('
    $current_page = $PAGE_NUM;
    $total_pages = $PAGE_COUNT;
    $font = $fontMetrics->getFont("Helvetica", "normal"); 
    $pdf->text(775.89, 577.64, "pagina $current_page / $total_pages", $font, 8, array(0,0,0));
');

// Output the generated PDF to Browser
$dompdf->stream('Dagboek ' . $wateringJaar . ' - ' . $wateringData['omschrijving'] . '.pdf',array('Attachment'=>0));
?>