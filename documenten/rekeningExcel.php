<?php
$prefix = '../';
include $prefix.'/bin/init.php';
require $prefix.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// === CONFIG / DATA ===
$wateringJaar = $wateringJaar;
$wateringNaam = $wateringData['omschrijving'];
$logoPath     = $prefix . 'img/logo-horizontal.png';

$hoofdPostenO = getHoofdPosten('O', $wateringJaar);
$hoofdPostenU = getHoofdPosten('U', $wateringJaar);
$rekeningen   = getRekeningen($wateringData['wateringId'], $wateringJaar, $useKAS, 'X', 'A');
$ramingReserve = getReserve($wateringData['wateringId'], $wateringJaar);

$spreadsheet = new Spreadsheet();
$euroFormat = '"€" #,##0.00;[Red]-"€" #,##0.00';

$skipOverdracht = false;
if(($wateringData['wateringId'] === '1' || $wateringData['wateringId'] === '2' || $wateringData['wateringId'] === '3' || $wateringData['wateringId'] === '5' || $wateringData['wateringId'] === '6') && (int)$wateringJaar < 2026)
	$skipOverdracht = true;

$tabs = [
    ['key'=>'O','title'=>'Ontvangsten','hoofdposten'=>$hoofdPostenO],
    ['key'=>'U','title'=>'Uitgaven','hoofdposten'=>$hoofdPostenU]
];

// Helper: kolomnummer naar letter
function colNumToLetter($num) {
    $letter = '';
    while ($num > 0) {
        $mod = ($num - 1) % 26;
        $letter = chr(65 + $mod) . $letter;
        $num = (int)(($num - $mod) / 26);
    }
    return $letter;
}

// === ONTVANGSTEN & UITGAVEN ===
$sheetTotals = [];
foreach ($tabs as $ti => $tab) {
    $sheet = $ti===0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
    $sheet->setTitle($tab['title']);

    // Header met logo
    $sheet->getRowDimension(1)->setRowHeight(36);
    $sheet->setCellValue('B1', $wateringNaam.' - Rekening '.$tab['title'].' '.$wateringJaar);
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(13);
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('C1','Jaar: '.$wateringJaar);
    $sheet->getStyle('C1')->getFont()->setBold(true)->setSize(11);
    $sheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    if(file_exists($logoPath)){
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(28);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($sheet);
    }

    // Tabellenkop
    $headerRow1 = 3;
    $headerRow2 = 4;
    $row = 5;

    $sheet->setCellValue('A'.$headerRow1,'Referentie'); $sheet->mergeCells('A'.$headerRow1.':A'.$headerRow2);
    $sheet->setCellValue('B'.$headerRow1,'Omschrijving'); $sheet->mergeCells('B'.$headerRow1.':B'.$headerRow2);
    $sheet->setCellValue('C'.$headerRow1,'Bedrag'); $sheet->mergeCells('C'.$headerRow1.':C'.$headerRow2);

    $sheet->getStyle('A'.$headerRow1.':C'.$headerRow2)->applyFromArray([
        'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
        'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
        'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'858796']],
        'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'FFFFFF']]],
    ]);

    $sheet->getRowDimension($headerRow1)->setRowHeight(20);
    $sheet->getRowDimension($headerRow2)->setRowHeight(18);
    $sheet->freezePane('A'.$row);
    $spreadsheet->getDefaultStyle()->getFont()->setSize(9);

    $sheetTotal = 0;
    $hoofdPosten = $tab['hoofdposten'];

    if(empty($hoofdPosten)){
        $sheet->setCellValue('A'.$row,'Geen gegevens');
        $sheet->mergeCells('A'.$row.':C'.$row);
        $row++;
    } else {
        foreach($hoofdPosten as $hoofd){
			$posten = getPostenActief($wateringData['wateringId'],$wateringJaar,$hoofd['hoofdpostId']);
			if(empty($posten)) continue;

			// Hoofdpost rij
			$sheet->setCellValue('A'.$row,$hoofd['referentie'].' '.$hoofd['omschrijving']);
			$sheet->mergeCells('A'.$row.':C'.$row);
			$sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12); // <-- Groter lettertype
			$row++;

			$hoofdTotal = 0;
			foreach($posten as $post){
				$postTotal = getBoekingBedragPost($wateringData['wateringId'],$wateringJaar,$post['postId'],'');
				$subPosten = getSubPostenActief($wateringData['wateringId'],$wateringJaar,$post['postId']);
				if(!empty($subPosten)){
					foreach($subPosten as $sub){
						$postTotal += getBoekingBedragPost($wateringData['wateringId'],$wateringJaar,$post['postId'],$sub['subpostId']);
					}
				}

				// Post rij
				$sheet->setCellValue('A'.$row,$hoofd['referentie'].' '.$post['referentie']);
				$sheet->setCellValue('B'.$row,$post['omschrijving']);
				$sheet->getStyle('A'.$row.':B'.$row)->getFont()->setSize(10); // <-- Iets kleiner dan hoofdpost

				if ($subPosten->num_rows == 0) {
					$sheet->setCellValue('C'.$row,$postTotal);
					$sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
					$sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
				} else {
					$sheet->setCellValue('C'.$row, '');
				}
				$row++;

				// Subposten rij
				if(!empty($subPosten)){
					foreach($subPosten as $sub){
						$sheet->setCellValue('A'.$row,$hoofd['referentie'].' '.$post['referentie'].$sub['referentie']);
						$sheet->setCellValue('B'.$row,'  '.$sub['omschrijving']);
						$sheet->setCellValue('C'.$row,getBoekingBedragPost($wateringData['wateringId'],$wateringJaar,$post['postId'],$sub['subpostId']));
						$sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
						$sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
						$sheet->getStyle('A'.$row.':B'.$row)->getFont()->setSize(9); // <-- Subposten blijven klein
						$row++;
					}
				}

				$hoofdTotal += $postTotal;
			}

			// Hoofdpost Totaal
			$sheet->setCellValue('A'.$row,'TOTAAL');
			$sheet->mergeCells('A'.$row.':B'.$row);
			$sheet->setCellValue('C'.$row,$hoofdTotal);
			$sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
				'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
				'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'858796']],
				'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'FFFFFF']]],
			]);
			$sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
			$sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
			$row+=2;
			$sheetTotal += $hoofdTotal;
        }
    }

    // Totaal tabellen
    $sheet->setCellValue('B'.$row,'Totaal '.strtolower($tab['title']).':');
    $sheet->setCellValue('C'.$row,$sheetTotal);
    $sheet->getStyle('B'.$row)->getFont()->setBold(true);
    $sheet->getStyle('C'.$row)->getFont()->setBold(true);
    $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
    $sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    $sheet->getColumnDimension('A')->setWidth(12);
    $sheet->getColumnDimension('B')->setWidth(85);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getStyle('B')->getAlignment()->setWrapText(true);

    $sheetTotals[$tab['key']] = $sheetTotal; // voor later SALDO
}

// === TOTAAL / SALDO TAB ===
$totaalSheet = $spreadsheet->createSheet();
$totaalSheet->setTitle('SALDO');

$totaalSheet->getRowDimension(1)->setRowHeight(36);
$totaalSheet->setCellValue('B1',$wateringNaam.' Rekening '.$wateringJaar);
$totaalSheet->getStyle('B1')->getFont()->setBold(true)->setSize(13);
$totaalSheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$totaalSheet->setCellValue('C1','Jaar: '.$wateringJaar);
$totaalSheet->getStyle('C1')->getFont()->setBold(true)->setSize(11);
$totaalSheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

if(file_exists($logoPath)){
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setPath($logoPath);
    $drawing->setHeight(28);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($totaalSheet);
}

// Startposities
$row = 4;
$colRekeningenStart = 2; // B
$colRekeningenBedrag = 3; // C
$colSaldoStart = 2; // E

// === REKENINGEN TITEL ===
$totaalSheet->setCellValue(colNumToLetter($colRekeningenStart).$row,'REKENINGEN');
// Merge kolom B en C
$totaalSheet->mergeCells(colNumToLetter($colRekeningenStart).$row.':'.colNumToLetter($colRekeningenStart+1).$row);
$totaalSheet->getStyle(colNumToLetter($colRekeningenStart).$row)->getFont()->setBold(true);
$totaalSheet->getStyle(colNumToLetter($colRekeningenStart).$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$row++;
$row++;

// --- LINKS: REKENINGEN ---
$totaalRekeningBedrag = 0;
foreach($rekeningen as $rekening) {
	if($skipOverdracht)
		$rekeningBedrag = getBoekingBedragRekening($wateringData['wateringId'], $wateringJaar, $rekening['rekeningId']);
	else
		$rekeningBedrag = getBoekingBedragRekening($wateringData['wateringId'], $wateringJaar, $rekening['rekeningId']) + $rekening['overdracht'];

    $totaalRekeningBedrag += $rekeningBedrag;

    $rekeningNaam = ($rekening['rekening'] === 'KAS') 
        ? $rekening['rekening'] 
        : $rekening['rekening'] . ' (' . $rekening['omschrijving'] . ')';

    $totaalSheet->setCellValue(colNumToLetter($colRekeningenStart).$row, $rekeningNaam);
    $totaalSheet->setCellValue(colNumToLetter($colRekeningenBedrag).$row, $rekeningBedrag);
    $row++;
}

// Totaal rekeningen + border-top
$totaalRekeningRow = $row;
$totaalSheet->setCellValue(colNumToLetter($colRekeningenStart).$row, 'Totaal:');
$totaalSheet->setCellValue(colNumToLetter($colRekeningenBedrag).$row, $totaalRekeningBedrag);
$totaalSheet->getStyle(colNumToLetter($colRekeningenStart).$row)->getFont()->setBold(true);
$totaalSheet->getStyle(colNumToLetter($colRekeningenBedrag).$row)->getFont()->setBold(true);
$totaalSheet->getStyle(colNumToLetter($colRekeningenBedrag).$row)
            ->getNumberFormat()->setFormatCode($euroFormat);
$totaalSheet->getStyle(colNumToLetter($colRekeningenBedrag).$row)
            ->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

// Borders rond het hele REKENINGEN-blok
$rekeningStartRow = 4; // eerste rij van de eerste rekening
$rekeningEndRow = $totaalRekeningRow; // rij van Totaal rekeningen
$totaalSheet->getStyle(colNumToLetter($colRekeningenStart).$rekeningStartRow.':'.colNumToLetter($colRekeningenBedrag).$rekeningEndRow)
            ->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
			
$row = $row + 3;

// === SALDO TITEL ===
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart).($row-1),'SALDO');
// Merge kolom B en C
$totaalSheet->mergeCells(colNumToLetter($colSaldoStart).($row-1).':'.colNumToLetter($colSaldoStart+1).($row-1));
$totaalSheet->getStyle(colNumToLetter($colSaldoStart).($row-1))->getFont()->setBold(true);
$totaalSheet->getStyle(colNumToLetter($colSaldoStart).($row-1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$row++;

// Saldo berekenen
$totaalOntvangsten = $sheetTotals['O'];
$totaalUitgaven   = $sheetTotals['U'];
$saldo = $totaalOntvangsten - $totaalUitgaven;

// Totaal Ontvangsten
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart).$row,'Totaal Ontvangsten:');
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart+1).$row,$totaalOntvangsten);
$row++;

// Totaal Uitgaven
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart).$row,'Totaal Uitgaven:');
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart+1).$row,$totaalUitgaven);
$row++;

// Saldo regel met top-border
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart).$row,'Saldo:');
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart+1).$row,$saldo);
$totaalSheet->getStyle(colNumToLetter($colSaldoStart).$row)->getFont()->setBold(true);
$totaalSheet->getStyle(colNumToLetter($colSaldoStart+1).$row)->getFont()->setBold(true);
$totaalSheet->getStyle(colNumToLetter($colSaldoStart+1).$row)
            ->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
$row++;

// Voeg 1 lege lijn in
$row++;

// Reservefonds
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart).$row,'Reservefonds dd - 1 januari');
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart+1).$row,$ramingReserve);
$row++;

$totaalSheet->setCellValue(colNumToLetter($colSaldoStart).$row,'Reservefonds dd - 31 december');
$totaalSheet->setCellValue(colNumToLetter($colSaldoStart+1).$row,$ramingReserve+$saldo);

// Borders rond het SALDO-blok
$saldoStartRow = $row - 7; // eerste rij van Saldo (pas aan indien nodig)
$saldoEndRow = $row;       // laatste rij (Reservefonds dd-31 januari)
$totaalSheet->getStyle(colNumToLetter($colSaldoStart).$saldoStartRow.':'.colNumToLetter($colSaldoStart+1).$saldoEndRow)
            ->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);

// --- Styling kolommen ---
$totaalSheet->getColumnDimension('A')->setWidth(12);
$totaalSheet->getColumnDimension('B')->setWidth(50);
$totaalSheet->getColumnDimension('C')->setWidth(20);
$totaalSheet->getColumnDimension('E')->setWidth(25);
$totaalSheet->getColumnDimension('F')->setWidth(20);

// Formaten en alignment voor alle relevante kolommen
$totaalSheet->getStyle('C4:C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
$totaalSheet->getStyle('F4:F'.$row)->getNumberFormat()->setFormatCode($euroFormat);
$totaalSheet->getStyle('C4:C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$totaalSheet->getStyle('F4:F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

// --- OUTPUT ---
$filename = 'Rekening '.$wateringJaar.' - '.$wateringNaam.'.xlsx';

// Zet eerste tab actief
$spreadsheet->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
