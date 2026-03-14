<?php
$prefix = '../';
include $prefix.'/bin/init.php';
require $prefix.'/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// === INPUT DATA ===
$hoofdPosten = getHoofdPostenAll($wateringJaar);
$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, $useKAS, 'X', 'A');
$month = (int)getLastMonth($wateringData['wateringId'], $wateringJaar);

// Calculate totals for footer rows
$totals = [];
foreach ($rekeningen as $rekening) {
    $totals['rek_' . $rekening['rekeningId'] . '_O'] = 0;
    $totals['rek_' . $rekening['rekeningId'] . '_U'] = 0;
}

// Create spreadsheet
$spreadsheet = new Spreadsheet();

// Define month names
$monthNames = ['Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'];

// === Loop through months ===
for ($maand = 1; $maand <= $month; $maand++) {

    // Create new sheet per month
    if ($maand == 1) {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($monthNames[$maand-1]);
    } else {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($monthNames[$maand-1]);
    }

    $boekingen = getBoekingen($wateringData['wateringId'], $wateringJaar, $maand, $useNummering, $sortering);

    // === HEADER ROW WITH LOGO AND DATE ===
    $sheet->getRowDimension(1)->setRowHeight(30);
    $sheet->mergeCells('B1:F1'); // title
    $sheet->mergeCells('G1:H1'); // date

    $sheet->setCellValue('B1', $wateringData['omschrijving'].' - Dag- en Kasboek');
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

    $sheet->setCellValue('G1', 'Datum: '.$monthNames[$maand-1].' '.$wateringJaar);
    $sheet->getStyle('G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle('G1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('G1')->getFont()->setBold(true)->setSize(11);

    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setDescription('Logo');
    $drawing->setPath('../img/logo-horizontal.png');
    $drawing->setHeight(45);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($sheet);

    // === HEADER ROWS ===
    $row = 3; // first header row
    $colIndex = 1;

	if ($useNummering === 'X') {
		$baseHeaders = ['Datum', 'Post', 'Factuurnr', 'Billitnr', 'Omschrijving'];
		$colSpanHeading = 5;
	} else {
		$baseHeaders = ['Datum', 'Post', 'Billitnr', 'Omschrijving'];
		$colSpanHeading = 4;
	}

    // First header row
    foreach ($baseHeaders as $header) {
        $colLetter = Coordinate::stringFromColumnIndex($colIndex);
        $sheet->setCellValue($colLetter.$row, $header);
        $sheet->mergeCells($colLetter.$row.':'.$colLetter.($row+1));
        $colIndex++;
    }

    // Dienstjaar header
    $dienstjaarHeader = 'DIENSTJAAR '.$wateringJaar;
    $dienstjaarStart = Coordinate::stringFromColumnIndex($colIndex);
    $dienstjaarEnd   = Coordinate::stringFromColumnIndex($colIndex + 1);
    $sheet->setCellValue($dienstjaarStart.$row, $dienstjaarHeader);
    $sheet->mergeCells($dienstjaarStart.$row.':'.$dienstjaarEnd.$row);
    $colIndex += 2;

    // Rekening headers
    foreach ($rekeningen as $rekening) {
        $rekeningStart = Coordinate::stringFromColumnIndex($colIndex);
        $rekeningEnd   = Coordinate::stringFromColumnIndex($colIndex + 1);
        $headerText = $rekening['rekening'];
        if ($rekening['rekening'] !== 'KAS') {
            $headerText .= "\n".$rekening['omschrijving'];
        }
        $sheet->setCellValue($rekeningStart.$row, $headerText);
        $sheet->mergeCells($rekeningStart.$row.':'.$rekeningEnd.$row);
        $colIndex += 2;
    }

    // Second header row
    $row2 = $row+1;
    $sheet->setCellValue($dienstjaarStart.$row2,'Ontvangsten');
    $sheet->setCellValue($dienstjaarEnd.$row2,'Uitgaven');
    $rekeningStartIndex = Coordinate::columnIndexFromString($dienstjaarEnd)+1;
    foreach ($rekeningen as $i => $rekening) {
        $colO = Coordinate::stringFromColumnIndex($rekeningStartIndex+($i*2));
        $colU = Coordinate::stringFromColumnIndex($rekeningStartIndex+($i*2+1));
        $sheet->setCellValue($colO.$row2,'Ontvangsten');
        $sheet->setCellValue($colU.$row2,'Uitgaven');
    }

    // Style header
    $lastCol = Coordinate::stringFromColumnIndex($colIndex-1);
    $headerRange = 'A'.$row.':'.$lastCol.$row2;
    $sheet->getStyle($headerRange)->applyFromArray([
        'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
        'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
        'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'FFFFFF']]],
        'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'858796']]
    ]);
    $sheet->getRowDimension($row)->setRowHeight(30);
    $sheet->getRowDimension($row2)->setRowHeight(22);
    $sheet->freezePane('A'.($row2+1));

    // === DATA ROWS ===
    $dataRowStart = $row2+1;
    $row = $dataRowStart;

    // Initial overdracht row
	$col = 'A';
	// Datum
	$sheet->setCellValue($col++.$row, sprintf('01/%02d', $maand));
	// Post
	$col++;
	// Factuurnr (indien aanwezig)
	if ($useNummering === 'X') {
		$col++;
	}
	// Billitnr (NIEUWE KOLUM)  ← deze ontbrak
	$col++;
	// Omschrijving
	$sheet->setCellValue($col++.$row, $maand === 1 ? 'Boni saldo van vorige dienstjaren' : 'Overdracht');
    $overdrachtDienstjaar = getOverdrachtDienstjaar($wateringData['wateringId'],$wateringJaar,$maand);
    $sheet->setCellValue($col++.$row,$overdrachtDienstjaar);
    $sheet->setCellValue($col++.$row,'');
    foreach ($rekeningen as $rekening) {
        $overdracht = getOverdracht($wateringData['wateringId'],$wateringJaar,$maand,$rekening['rekeningId']);
        $sheet->setCellValue($col++.$row,$overdracht);
        $sheet->setCellValue($col++.$row,'');
    }
    $fillColor='FFFFFF';
    $lastColLetter = Coordinate::stringFromColumnIndex($colIndex-1);
    $sheet->getStyle('A'.$row.':'.$lastColLetter.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
    $row++;

    // Loop boekingen
    foreach ($boekingen as $boeking) {
        $col = 'A';
        $sheet->setCellValue($col++.$row, sprintf('%02d/%02d',$boeking['dag'],$boeking['maand']));
        $postReferentie='';
        if ($boeking['postId']!=='0') {
            $postData=getPostData($boeking['postId']);
            $subPostData=getSubPostData($boeking['subPostId']);
            $hoofdPostData=getHoofdPostData($postData['hoofdpostId']);
            $postReferentie=($hoofdPostData['useKey']==='U'?'UIT ':'ONT ').$hoofdPostData['referentie'].' '.$postData['referentie'].' '.$subPostData['referentie'];
        }
		$sheet->setCellValue($col++.$row, $postReferentie);

		if ($useNummering === 'X') {
			// Factuurnr
			$sheet->setCellValue($col++.$row, $boeking['nummering']);
		}

		// Billitnr (nieuwe kolom)
		$sheet->setCellValue($col++.$row, $boeking['billitNumber']);

		// Omschrijving
		$sheet->setCellValue($col++.$row, $boeking['omschrijving']);

        $dienstjaarO=getBoekingBedragDataDienstjaar($boeking['boekId'],'O');
        $dienstjaarU=getBoekingBedragDataDienstjaar($boeking['boekId'],'U');
        $sheet->setCellValue($col++.$row,$dienstjaarO);
        $sheet->setCellValue($col++.$row,$dienstjaarU);

        foreach ($rekeningen as $rekening) {
            $boekingsBedragO = getBoekingBedragData($boeking['boekId'],$rekening['rekeningId'],'O')['bedrag'];
            $boekingsBedragU = getBoekingBedragData($boeking['boekId'],$rekening['rekeningId'],'U')['bedrag'];
            $sheet->setCellValue($col++.$row,$boekingsBedragO);
            $sheet->setCellValue($col++.$row,$boekingsBedragU);
        }

        $fillColor=($row%2==0)?'DDDDDD':'FFFFFF';
        $colEnd=Coordinate::stringFromColumnIndex($colIndex-1);
        $sheet->getStyle('A'.$row.':'.$colEnd.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
        $row++;
    }

    // === TOTAAL row ===
    $sheet->setCellValue('A'.$row,'TOTAAL');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true);
    $sheet->mergeCells('A'.$row.':E'.$row);

	$ontvangCol = 'F';
	$uitgaveCol = 'G';
	$sheet->setCellValue($ontvangCol.$row,'=SUM('.$ontvangCol.$dataRowStart.':'.$ontvangCol.($row-1).')');
	$sheet->setCellValue($uitgaveCol.$row,'=SUM('.$uitgaveCol.$dataRowStart.':'.$uitgaveCol.($row-1).')');

    $rekeningColStart = 8;
    foreach ($rekeningen as $i=>$rekening) {
        $colO=Coordinate::stringFromColumnIndex($rekeningColStart+($i*2));
        $colU=Coordinate::stringFromColumnIndex($rekeningColStart+($i*2+1));
        $sheet->setCellValue($colO.$row,'=SUM('.$colO.$dataRowStart.':'.$colO.($row-1).')');
        $sheet->setCellValue($colU.$row,'=SUM('.$colU.$dataRowStart.':'.$colU.($row-1).')');
    }
    $row++;

    // OVER TE DRAGEN row
    $sheet->setCellValue('A'.$row,'OVER TE DRAGEN');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true);
    $sheet->mergeCells('A'.$row.':E'.$row);
    $sheet->setCellValue($ontvangCol.$row,'='.$ontvangCol.($row-1).'-'.$uitgaveCol.($row-1));
    $sheet->setCellValue('G'.$row,'');
    foreach ($rekeningen as $i=>$rekening) {
        $colO=Coordinate::stringFromColumnIndex($rekeningColStart+($i*2));
        $colU=Coordinate::stringFromColumnIndex($rekeningColStart+($i*2+1));
        $sheet->setCellValue($colO.$row,'='.$colO.($row-1).'-'.$colU.($row-1));
        $sheet->setCellValue($colU.$row,'');
    }
    $row++;

    // REKENINGTOTAAL row
    $sheet->setCellValue('A'.$row,'REKENINGTOTAAL');
    $sheet->getStyle('A'.$row)->getFont()->setBold(true);
    $sheet->mergeCells('A'.$row.':E'.$row);
    $sheet->setCellValue($ontvangCol.$row,'');
    $sheet->setCellValue('G'.$row,'');
    foreach ($rekeningen as $i=>$rekening) {
        $colO=Coordinate::stringFromColumnIndex($rekeningColStart+($i*2));
        $colU=Coordinate::stringFromColumnIndex($rekeningColStart+($i*2+1));
        // Calculate rekening balance: opening balance + (Ontvangsten - Uitgaven)
        $rekBalance = getOverdracht($wateringData['wateringId'], $wateringJaar, '1', $rekening['rekeningId'], true);
        $ontvangsten = $totals['rek_' . $rekening['rekeningId'] . '_O'];
        $uitgaven = $totals['rek_' . $rekening['rekeningId'] . '_U'];
        
        // Calculate total for this rekening
        $rekeningTotaal = $rekBalance + ($ontvangsten - $uitgaven);
        $sheet->setCellValue($colO.$row, $rekeningTotaal);
        $sheet->setCellValue($colU.$row,'');
    }

    // Style data and currency
    $lastColLetter = Coordinate::stringFromColumnIndex($colIndex-1);
    $sheet->getStyle('A'.$dataRowStart.':'.$lastColLetter.$row)
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A'.$dataRowStart.':'.$lastColLetter.$row)
        ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A'.$dataRowStart.':'.$lastColLetter.$row)
        ->getFont()->setSize(9);

    $euroFormat='"€" #,##0.00;[Red]-"€" #,##0.00';
    $firstAmountCol='F';
    $firstIndex=Coordinate::columnIndexFromString($firstAmountCol);
    $lastIndex=Coordinate::columnIndexFromString($lastColLetter);

    for ($ci=$firstIndex;$ci<=$lastIndex;$ci++) {
        $colLetter=Coordinate::stringFromColumnIndex($ci);
        $range=$colLetter.$dataRowStart.':'.$colLetter.$row;
        $sheet->getStyle($range)->getNumberFormat()->setFormatCode($euroFormat);
        $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
    }

    // Column widths
    for ($ci=1;$ci<$firstIndex;$ci++) {
        $colLetter=Coordinate::stringFromColumnIndex($ci);
        $sheet->getColumnDimension($colLetter)->setAutoSize(true);
    }
    for ($ci=$firstIndex;$ci<=$lastIndex;$ci++) {
        $colLetter=Coordinate::stringFromColumnIndex($ci);
        $sheet->getColumnDimension($colLetter)->setAutoSize(false);
        $sheet->getColumnDimension($colLetter)->setWidth(18);
    }

    // Style TOTAAL and OVER TE DRAGEN rows
    $sheet->getStyle('A'.($row-1).':'.$lastColLetter.$row)->applyFromArray([
        'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
        'alignment'=>['wrapText'=>true],
        'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'FFFFFF']]],
        'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'858796']]
    ]);
}

// === OUTPUT TO BROWSER ===
$filename = 'Dagboek ' . $wateringJaar . ' - ' . $wateringData['omschrijving'] . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
