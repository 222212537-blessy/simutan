<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

require 'vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = Carbon::create(2026, 1, 1);
$endDate = Carbon::create(2026, 3, 1);

$exporter = new \App\Exports\PemasukanExport();
$filePath = resource_path('excel/Laporan_Rincian_Persediaan.xlsx');
$resultFile = $exporter->export($filePath, $startDate, $endDate);

$spreadsheet = IOFactory::load($resultFile);
$sheet = $spreadsheet->getActiveSheet();
echo "Generated E12 value: " . var_export($sheet->getCell('E12')->getValue(), true) . "\n";
echo "Generated E12 calculated: " . var_export($sheet->getCell('E12')->getCalculatedValue(), true) . "\n";
echo "Generated D12 value: " . var_export($sheet->getCell('D12')->getValue(), true) . "\n";
