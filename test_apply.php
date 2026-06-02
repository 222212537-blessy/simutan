<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;

require 'vendor/autoload.php';

$spreadsheet = IOFactory::load('resources/excel/Laporan_Rincian_Persediaan.xlsx');
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('E12', 12345.67);
$sheet->getStyle('E12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('test_apply.xlsx');
echo "Saved test_apply.xlsx\n";
