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
$maand = (int)$_SESSION['wateringMaand'];
$rekeningen = getRekeningen($wateringData['wateringId'], $wateringJaar, $useKAS, 'X', 'A');
$boekingen = getBoekingen($wateringData['wateringId'], $wateringJaar, $maand, $useNummering, $sortering);

// === CREATE SPREADSHEET ===
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// === HEADER ROW WITH LOGO AND DATE ===
// Set row height
$sheet->getRowDimension(1)->setRowHeight(30);

// Merge cells for title and date
$sheet->mergeCells('B1:F1'); // center title
$sheet->mergeCells('G1:H1'); // right date

// Insert title in center
$sheet->setCellValue('B1', $wateringData['omschrijving'] . ' - Dag- en Kasboek');
$sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('B1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

// Insert date on the right
$sheet->setCellValue('G1', 'Datum: ' . monthNames[$maand-1] . ' ' . $wateringJaar);
$sheet->getStyle('G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('G1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('G1')->getFont()->setBold(true)->setSize(11);

// Insert logo on the left
$drawing = new Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath('../img/logo-horizontal.png'); // use local path; URLs sometimes fail
$drawing->setHeight(45);
$drawing->setCoordinates('A1');
$drawing->setOffsetX(0);
$drawing->setOffsetY(0);
$drawing->setWorksheet($sheet);

// === HEADER ROWS ===
$row = 3; // first header row
$colIndex = 1;

// Base columns
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
$dienstjaarHeader = 'DIENSTJAAR ' . $wateringJaar;
$dienstjaarStart = Coordinate::stringFromColumnIndex($colIndex);
$dienstjaarEnd   = Coordinate::stringFromColumnIndex($colIndex + 1);
$sheet->setCellValue($dienstjaarStart.$row, $dienstjaarHeader);
$sheet->mergeCells($dienstjaarStart.$row.':'.$dienstjaarEnd.$row);
$colIndex += 2;

// Rekening headers (merged)
foreach ($rekeningen as $rekening) {
    $rekeningStart = Coordinate::stringFromColumnIndex($colIndex);
    $rekeningEnd   = Coordinate::stringFromColumnIndex($colIndex + 1);
    $headerText = $rekening['rekening'];
    if ($rekening['rekening'] !== 'KAS') {
        $headerText .= "\n" . $rekening['omschrijving'];
    }
    $sheet->setCellValue($rekeningStart.$row, $headerText);
    $sheet->mergeCells($rekeningStart.$row.':'.$rekeningEnd.$row);
    $colIndex += 2;
}

// Second header row (Ontvangsten / Uitgaven)
$row2 = $row + 1;

// Dienstjaar subheaders
$sheet->setCellValue($dienstjaarStart.$row2, 'Ontvangsten');
$sheet->setCellValue($dienstjaarEnd.$row2, 'Uitgaven');

// Rekening subheaders
$rekeningStartIndex = Coordinate::columnIndexFromString($dienstjaarEnd) + 1;
foreach ($rekeningen as $i => $rekening) {
    $colO = Coordinate::stringFromColumnIndex($rekeningStartIndex + ($i*2));
    $colU = Coordinate::stringFromColumnIndex($rekeningStartIndex + ($i*2 + 1));
    $sheet->setCellValue($colO.$row2, 'Ontvangsten');
    $sheet->setCellValue($colU.$row2, 'Uitgaven');
}

// Style header rows
$lastCol = Coordinate::stringFromColumnIndex($colIndex - 1);
$headerRange = 'A'.$row.':'.$lastCol.$row2;

$sheet->getStyle($headerRange)->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'], // font color white
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'FFFFFF'], // white borders
        ],
    ],		
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '858796'],
    ],
]);

// Row heights
$sheet->getRowDimension($row)->setRowHeight(30);
$sheet->getRowDimension($row2)->setRowHeight(22);

// Freeze header rows
$sheet->freezePane('A'.($row2+1));

// === DATA ROWS ===
$dataRowStart = $row2 + 1;
$row = $dataRowStart;

// === INITIAL OVERDRACHT ROW (Carryover / Boni saldo) ===
$row = $dataRowStart; // first data row after headers
$col = 'A';
// Datum
$sheet->setCellValue($col++.$row, sprintf('01/%02d', $maand));
// Post (leeg)
$col++;

// Factuurnr (indien actief)
if ($useNummering === 'X') $col++;
// Billitnr (nieuw – leeg)
$col++;
// Omschrijving
$sheet->setCellValue($col++.$row, $maand === 1 ? 'Boni saldo van vorige dienstjaren' : 'Overdracht');

// Dienstjaar initial
$overdrachtDienstjaar = getOverdrachtDienstjaar($wateringData['wateringId'], $wateringJaar, $maand);
$sheet->setCellValue($col++.$row, $overdrachtDienstjaar);
$sheet->setCellValue($col++.$row, '');

// Rekening-specific columns
foreach ($rekeningen as $rekening) {
    $overdracht = getOverdracht($wateringData['wateringId'], $wateringJaar, $maand, $rekening['rekeningId']);
    $sheet->setCellValue($col++.$row, $overdracht); // Ontvangsten
    $sheet->setCellValue($col++.$row, ''); // blank for Uitgaven
}

// Apply alternating row color
$fillColor = 'FFFFFF';
$lastColLetter = Coordinate::stringFromColumnIndex($colIndex-1);
$sheet->getStyle('A'.$row.':'.$lastColLetter.$row)
      ->getFill()->setFillType(Fill::FILL_SOLID)
      ->getStartColor()->setRGB($fillColor);

$row++; // move to next row for looping boekingen

// Loop through boekingen
foreach ($boekingen as $boeking) {
    $col = 'A';
    $sheet->setCellValue($col++.$row, sprintf('%02d/%02d', $boeking['dag'], $boeking['maand']));
    $postReferentie = '';
    if ($boeking['postId'] !== '0') {
        $postData = getPostData($boeking['postId']); 
        $subPostData = getSubPostData($boeking['subPostId']);
        $hoofdPostData = getHoofdPostData($postData['hoofdpostId']);
        $postReferentie = ($hoofdPostData['useKey']==='U'?'UIT ':'ONT ')
            .$hoofdPostData['referentie'].' '.$postData['referentie'].' '.$subPostData['referentie'];
    }
    $sheet->setCellValue($col++.$row, $postReferentie);
	if ($useNummering==='X') {
		$sheet->setCellValue($col++.$row, $boeking['nummering']);
	}

	// Nieuwe kolom BILLITNR
	$sheet->setCellValue($col++.$row, $boeking['billitNumber'] ?? '');

	// Omschrijving
	$sheet->setCellValue($col++.$row, $boeking['omschrijving']);

    // Dienstjaar
    $dienstjaarO = getBoekingBedragDataDienstjaar($boeking['boekId'], 'O');
    $dienstjaarU = getBoekingBedragDataDienstjaar($boeking['boekId'], 'U');
    $sheet->setCellValue($col++.$row, $dienstjaarO);
    $sheet->setCellValue($col++.$row, $dienstjaarU);

    // Rekening columns
    foreach ($rekeningen as $rekening) {
        $boekingsBedragO = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'O')['bedrag'];
        $boekingsBedragU = getBoekingBedragData($boeking['boekId'], $rekening['rekeningId'], 'U')['bedrag'];
        $sheet->setCellValue($col++.$row, $boekingsBedragO);
        $sheet->setCellValue($col++.$row, $boekingsBedragU);
    }

    // Alternating row color
    $fillColor = ($row % 2 == 0) ? 'DDDDDD' : 'FFFFFF';
    $colEnd = Coordinate::stringFromColumnIndex($colIndex-1);
    $sheet->getStyle('A'.$row.':'.$colEnd.$row)
          ->getFill()->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB($fillColor);

    $row++;
}

// === TOTAAL row ===
$sheet->setCellValue('A'.$row, 'TOTAAL');
$sheet->getStyle('A'.$row)->getFont()->setBold(true);
$sheet->mergeCells('A'.$row.':E'.$row);

// Sum formulas for dienstjaar columns
$sheet->setCellValue('F'.$row, '=SUM(F'.$dataRowStart.':F'.($row-1).')');
$sheet->setCellValue('G'.$row, '=SUM(G'.$dataRowStart.':G'.($row-1).')');

// Sum formulas for rekening columns
$rekeningColStart = 8; // first rekening column
foreach ($rekeningen as $i=>$rekening) {
    $colO = Coordinate::stringFromColumnIndex($rekeningColStart + ($i*2));
    $colU = Coordinate::stringFromColumnIndex($rekeningColStart + ($i*2+1));
    $sheet->setCellValue($colO.$row, '=SUM('.$colO.$dataRowStart.':'.$colO.($row-1).')');
    $sheet->setCellValue($colU.$row, '=SUM('.$colU.$dataRowStart.':'.$colU.($row-1).')');
}
$row++;

// OVER TE DRAGEN row
$sheet->setCellValue('A'.$row, 'OVER TE DRAGEN');
$sheet->getStyle('A'.$row)->getFont()->setBold(true);
$sheet->mergeCells('A'.$row.':E'.$row);
$sheet->setCellValue('F'.$row, '=F'.($row-1).'-G'.($row-1));
$sheet->setCellValue('G'.$row, '');
foreach ($rekeningen as $i=>$rekening) {
    $colO = Coordinate::stringFromColumnIndex($rekeningColStart + ($i*2));
    $colU = Coordinate::stringFromColumnIndex($rekeningColStart + ($i*2+1));
    $sheet->setCellValue($colO.$row, '='.$colO.($row-1).'-'.$colU.($row-1));
    $sheet->setCellValue($colU.$row, '');
}

// === STYLE DATA ===
$colEnd = isset($colEnd) ? $colEnd : Coordinate::stringFromColumnIndex($colIndex - 1);

// If there are boekingen (data)
if ($row > $dataRowStart) {
    $sheet->getStyle('A'.$dataRowStart.':'.$colEnd.$row)
          ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A'.$dataRowStart.':'.$colEnd.$row)
          ->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A'.$dataRowStart.':'.$colEnd.$row)
          ->getFont()->setSize(9);
} else {
    // No boekingen — display placeholder row
    $sheet->setCellValue('A'.$dataRowStart, 'Geen gegevens beschikbaar voor deze maand.');
    $sheet->mergeCells('A'.$dataRowStart.':'.$colEnd.$dataRowStart);
    $sheet->getStyle('A'.$dataRowStart)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A'.$dataRowStart)->getFont()->setItalic(true)->setSize(10);
}

// === CURRENCY FORMAT ===
$euroFormat = '"€" #,##0.00;[Red]-"€" #,##0.00';
$firstAmountCol = 'F';
$firstIndex = Coordinate::columnIndexFromString($firstAmountCol);
$lastIndex  = Coordinate::columnIndexFromString($colEnd);

for ($ci = $firstIndex; $ci <= $lastIndex; $ci++) {
    $colLetter = Coordinate::stringFromColumnIndex($ci);
    $range = $colLetter.$dataRowStart.':'.$colLetter.$row;
    $sheet->getStyle($range)->getNumberFormat()->setFormatCode($euroFormat);
    $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
}

// === AUTO COLUMN WIDTH for text columns and fixed width for amounts ===
for ($ci = 1; $ci < $firstIndex; $ci++) { // text columns (A → D)
    $colLetter = Coordinate::stringFromColumnIndex($ci);
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
}
for ($ci = $firstIndex; $ci <= $lastIndex; $ci++) { // numeric columns (E → end)
    $colLetter = Coordinate::stringFromColumnIndex($ci);
    $sheet->getColumnDimension($colLetter)->setAutoSize(false);
    $sheet->getColumnDimension($colLetter)->setWidth(18);
}

$lastColLetter = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($col) - 1);

// Apply header-style formatting to TOTAAL and OVER TE DRAGEN rows
$sheet->getStyle('A'.($row-1).':'.$lastColLetter.$row)->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'], // white font
    ],
    'alignment' => [
        'wrapText'   => true,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'FFFFFF'], // white borders
        ],
    ],		
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '858796'], // same dark grey background
    ],
]);

// === OUTPUT TO BROWSER ===
$filename = 'Dagboek ' . $wateringJaar . $maand . ' - ' . $wateringData['omschrijving'] . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
