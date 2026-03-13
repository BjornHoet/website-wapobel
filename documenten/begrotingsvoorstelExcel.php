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

$spreadsheet = new Spreadsheet();

$tabs = [
    ['key' => 'O', 'title' => 'Ontvangsten', 'hoofdposten' => $hoofdPostenO],
    ['key' => 'U', 'title' => 'Uitgaven',    'hoofdposten' => $hoofdPostenU],
];

$euroFormat = '"€" #,##0.00;[Red]-"€" #,##0.00';

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
foreach ($tabs as $ti => $tab) {
    $sheet = $ti === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
    $sheet->setTitle($tab['title']);

    // Header met logo, titel, jaar
    $sheet->getRowDimension(1)->setRowHeight(36);

    $sheet->setCellValue('B1', $wateringNaam . ' - Begrotingsvoorstel - ' . $tab['title'] . ' ' . $wateringJaar);
    $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(13);
    $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('C1', 'Jaar: ' . $wateringJaar);
    $sheet->getStyle('C1')->getFont()->setBold(true)->setSize(11);
    $sheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    if (file_exists($logoPath)) {
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

    $sheet->setCellValue('A'.$headerRow1, 'Referentie');
    $sheet->mergeCells('A'.$headerRow1.':A'.$headerRow2);
    $sheet->setCellValue('B'.$headerRow1, 'Omschrijving');
    $sheet->mergeCells('B'.$headerRow1.':B'.$headerRow2);
    $sheet->setCellValue('C'.$headerRow1, 'Bedrag');
    $sheet->mergeCells('C'.$headerRow1.':C'.$headerRow2);

    $sheet->getStyle('A'.$headerRow1.':C'.$headerRow2)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '858796']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
    ]);

    $sheet->getRowDimension($headerRow1)->setRowHeight(20);
    $sheet->getRowDimension($headerRow2)->setRowHeight(18);
    $sheet->freezePane('A' . $row);

    $spreadsheet->getDefaultStyle()->getFont()->setSize(9);

    $sheetTotal = 0;
    $hoofdPosten = $tab['hoofdposten'];

    if (empty($hoofdPosten)) {
        $sheet->setCellValue('A'.$row, 'Geen gegevens');
        $sheet->mergeCells('A'.$row.':C'.$row);
        $row++;
    } else {
        foreach ($hoofdPosten as $hoofd) {
            $posten = getPostenActief($wateringData['wateringId'], $wateringJaar, $hoofd['hoofdpostId']);
            if (empty($posten)) continue;

            // Hoofdpost
            $sheet->setCellValue('A'.$row, $hoofd['referentie'] . ' ' . $hoofd['omschrijving']);
            $sheet->mergeCells('A'.$row.':C'.$row);
            $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(12); // Groter voor hoofdposten
            $row++;

            $hoofdTotal = 0;

            foreach ($posten as $post) {
                $postTotal = $post['raming'];
                $subPosten = getSubPostenActief($wateringData['wateringId'], $wateringJaar, $post['postId']);
                if (!empty($subPosten)) {
                    foreach ($subPosten as $sub) {
                        $postTotal += $sub['raming'];
                    }
                }

                // Post
                $sheet->setCellValue('A'.$row, $hoofd['referentie'] . ' ' . $post['referentie']);
                $sheet->setCellValue('B'.$row, $post['omschrijving']);
                $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setSize(10); // Groter dan subposten

                // Alleen tonen als geen subposten
                if ($subPosten->num_rows == 0) {
					if ($postTotal != 0) {
						$sheet->setCellValue('C'.$row, $postTotal);
						$sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
						$sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
					} else {
						$sheet->setCellValue('C'.$row, '');
					}
				} else {
					$sheet->setCellValue('C'.$row, '');
				}
                $row++;

                // Subposten
                if (!empty($subPosten)) {
                    foreach ($subPosten as $sub) {
                        $sheet->setCellValue('A'.$row, $hoofd['referentie'] . ' ' . $post['referentie'] . $sub['referentie']);
                        $sheet->setCellValue('B'.$row, '  ' . $sub['omschrijving']);
						if ($sub['raming'] != 0) {
							$sheet->setCellValue('C'.$row, $sub['raming']);
						} else {
							$sheet->setCellValue('C'.$row, '');
						}
                        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setSize(9); // Kleiner voor subposten
                        $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
                        $sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $row++;
                    }
                }

                $hoofdTotal += $postTotal;
            }

            // Hoofdpost totaal
            $sheet->setCellValue('A'.$row, 'TOTAAL');
            $sheet->mergeCells('A'.$row.':B'.$row);
            $sheet->setCellValue('C'.$row, $hoofdTotal);
            $sheet->getStyle('A'.$row.':C'.$row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '858796']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
            ]);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
            $sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
            $sheetTotal += $hoofdTotal;
            $row++;
        }
    }

    $sheet->setCellValue('B'.$row, 'Totaal ' . strtolower($tab['title']) . ':');
    $sheet->setCellValue('C'.$row, $sheetTotal);
    $sheet->getStyle('B'.$row)->getFont()->setBold(true);
    $sheet->getStyle('C'.$row)->getFont()->setBold(true);
    $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode($euroFormat);
    $sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    // Kolombreedtes instellen
    $sheet->getColumnDimension('A')->setWidth(12);
    $sheet->getColumnDimension('B')->setWidth(85);
    $sheet->getColumnDimension('C')->setWidth(20);

    // Tekstterugloop voor kolom B
    $sheet->getStyle('B')->getAlignment()->setWrapText(true);

    // Print instellingen voor 1 pagina breed
    $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
}

// === Derde tabblad: TOTAAL / SALDO ===
$totaalSheet = $spreadsheet->createSheet();
$totaalSheet->setTitle('SALDO');

// Header met logo, titel, jaar
$totaalSheet->getRowDimension(1)->setRowHeight(36);
$totaalSheet->setCellValue('B1', $wateringNaam . ' Begrotingsvoorstel - Saldo ' . $wateringJaar);
$totaalSheet->getStyle('B1')->getFont()->setBold(true)->setSize(13);
$totaalSheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$totaalSheet->setCellValue('C1', 'Jaar: ' . $wateringJaar);
$totaalSheet->getStyle('C1')->getFont()->setBold(true)->setSize(11);
$totaalSheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

if (file_exists($logoPath)) {
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setPath($logoPath);
    $drawing->setHeight(28);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($totaalSheet);
}

// Bepaal totalen
$totaalOntvangsten = $spreadsheet->getSheetByName('Ontvangsten')->getCell('C'.$spreadsheet->getSheetByName('Ontvangsten')->getHighestDataRow())->getCalculatedValue();
$totaalUitgaven   = $spreadsheet->getSheetByName('Uitgaven')->getCell('C'.$spreadsheet->getSheetByName('Uitgaven')->getHighestDataRow())->getCalculatedValue();
$saldo = $totaalOntvangsten - $totaalUitgaven;

// Tabellenweergave
$startRow = 4;
$totaalSheet->setCellValue('B'.$startRow, 'Totaal Ontvangsten:');
$totaalSheet->setCellValue('C'.$startRow, $totaalOntvangsten);
$totaalSheet->setCellValue('B'.($startRow+1), 'Totaal Uitgaven:');
$totaalSheet->setCellValue('C'.($startRow+1), $totaalUitgaven);
$totaalSheet->setCellValue('B'.($startRow+2), 'Saldo');
$totaalSheet->setCellValue('C'.($startRow+2), $saldo);

// Opmaak
$totaalSheet->getStyle('B'.$startRow.':B'.($startRow+2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$totaalSheet->getStyle('C'.$startRow.':C'.($startRow+2))->getNumberFormat()->setFormatCode($euroFormat);
$totaalSheet->getStyle('C'.($startRow+2))->getFont()->setBold(true);
$totaalSheet->getStyle('C'.($startRow+2))->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

$totaalSheet->getStyle('B'.$startRow.':C'.($startRow+2))->applyFromArray([
    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
]);

$totaalSheet->getRowDimension($startRow+2)->setRowHeight(25);

// Kolombreedtes en printinstellingen
$totaalSheet->getColumnDimension('A')->setWidth(12);
$totaalSheet->getColumnDimension('B')->setWidth(85);
$totaalSheet->getColumnDimension('C')->setWidth(20);
$totaalSheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);

// Zet eerste tab actief
$spreadsheet->setActiveSheetIndex(0);

// === OUTPUT ===
$filename = 'Begroting '.$wateringJaar.' - '.$wateringNaam.'.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
