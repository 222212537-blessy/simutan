<?php
require 'vendor/autoload.php';
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('resources/excel/Laporan_Rincian_Persediaan.xlsx');
$sheet = $spreadsheet->getActiveSheet();
$val = $sheet->getCell('B12')->getValue();
echo 'Value: ' . $val . ', Type: ' . gettype($val) . ', Length: ' . strlen((string)$val) . "\n";
