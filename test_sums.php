<?php
require 'vendor/autoload.php';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('resources/excel/Laporan_Rincian_Persediaan.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$mergedCells = $sheet->getMergeCells();
echo "Merged cells:\n";
foreach ($mergedCells as $merged) {
    if (strpos($merged, '12') !== false) {
        echo $merged . "\n";
    }
}
