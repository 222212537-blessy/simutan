<?php

namespace App\Exports;

use App\Models\Barang;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\StokAwalBulan;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Carbon\Carbon;

class BarangExport implements FromCollection, WithHeadings, WithDrawings, WithCustomStartCell, WithStyles
{
    protected $barang;
    protected $tanggal;
    protected $admin;

    public function __construct($barang, $tanggal, User $admin)
    {
        $this->barang = $barang;
        $this->tanggal = Carbon::parse($tanggal);
        $this->admin = $admin;
    }

    public function collection()
    {
        return $this->barang->map(function ($item, $key) {
            // Kalkulasi stok HISTORIS pada tanggal yang dipilih
            $stokSaatIni = max(0, (int) $item->qty_item);

            $pemasukanSetelah = Pemasukan::where('barang_id', $item->id)
                ->whereDate('tanggal', '>', $this->tanggal)
                ->sum('qty');

            $pengeluaranSetelah = Pengeluaran::where('barang_id', $item->id)
                ->whereDate('tanggal', '>', $this->tanggal)
                ->sum('qty');

            $jumlah = max(0, $stokSaatIni + $pengeluaranSetelah - $pemasukanSetelah);

            if ($jumlah == 0) {
                $hargaBeliSatuan = 0;
                $hargaTotal = 0;
            } else {
                if ($item->qty_item > 0 && $item->harga_total > 0) {
                    $hargaBeliSatuan = $item->harga_total / $item->qty_item;
                } else {
                    $stokAwalFallback = StokAwalBulan::where('barang_id', $item->id)
                        ->where('harga_total', '>', 0)
                        ->where('qty_awal', '>', 0)
                        ->orderByDesc('tahun')
                        ->orderByDesc('bulan')
                        ->first();

                    if ($stokAwalFallback) {
                        $hargaBeliSatuan = $stokAwalFallback->harga_total / $stokAwalFallback->qty_awal;
                    } else {
                        $hargaBeliSatuan = 0;
                    }
                }

                if ($jumlah == $stokSaatIni && $item->harga_total > 0) {
                    $hargaTotal = $item->harga_total;
                } else {
                    $hargaTotal = max(0, round($jumlah * $hargaBeliSatuan));
                }
            }

            return [
                'NO'                                    => $key + 1,
                'Uraian Barang'                         => $item->nama,
                'Satuan'                                => $item->satuan,
                'Harga Beli Satuan (Rupiah)'            => $hargaBeliSatuan,
                'Total Persediaan Jumlah'               => $jumlah,
                'Total Persediaan Harga Total (Rupiah)' => $hargaTotal,
                'Barang Rusak Jumlah'                   => 0,
                'Barang Rusak Harga Total (Rupiah)'     => 0,
                'Barang Usang Jumlah'                   => 0,
                'Barang Usang Harga Total (Rupiah)'     => 0,
            ];
        });
    }

    private function convertNumberToWords($number)
    {
        $words = array(
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas',
            'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas', 'Dua Puluh'
        );

        if ($number <= 20) {
            return $words[$number];
        } elseif ($number < 100) {
            $tens = $words[intval($number / 10)] . ' Puluh';
            $units = $words[$number % 10];
            return $units ? $tens . ' ' . $units : $tens;
        } else {
            return $number; 
        }
    }

    // FUNGSI BARU: Pencari File TTD menggunakan Absolute Path cPanel
    private function getSignaturePath($id)
    {
        $baseDir = '/home/simutanw/public_html/simutan/backend/assets/images/users/';
        $extensions = ['.png', '.PNG', '.jpg', '.JPG', '.jpeg', '.JPEG', '.webp'];
        
        foreach ($extensions as $ext) {
            $path = $baseDir . 'ttd_' . $id . $ext;
            if (file_exists($path)) {
                return $path;
            }
        }
        return null;
    }

    public function headings(): array
    {
        return [];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is the BPS logo');
        
        // PERBAIKAN: Gunakan Absolute Path untuk Logo
        $imagePath = '/home/simutanw/public_html/simutan/backend/assets/images/logo-bps.png';
        
        $drawing->setPath($imagePath);
        $drawing->setHeight(90);
        $drawing->setCoordinates('A1');
        
        return [$drawing];
    }

    public function startCell(): string
    {
        return 'A15'; 
    }

    public function styles(Worksheet $sheet)
    {
        // Logo and header text
        $sheet->mergeCells('B1:F1');
        $sheet->setCellValue('B1', '              Badan Pusat Statistik');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(20);

        $sheet->mergeCells('B2:F2');
        $sheet->setCellValue('B2', '              Kota Jakarta Utara');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(20);

        $sheet->mergeCells('B4:F4');
        $sheet->setCellValue('B4', 'Jl. Berdikari No. 1 Rawa Badak Utara');
        $sheet->getStyle('B4')->getFont()->setSize(12);

        $sheet->mergeCells('B5:F5');
        $sheet->setCellValue('B5', 'Jakarta Utara');
        $sheet->getStyle('B5')->getFont()->setSize(12);

        $sheet->setCellValue('H4', 'Telp. : (021) 22494346');
        $sheet->getStyle('H4')->getFont()->setSize(12);

        $sheet->setCellValue('H5', 'Faks  : (021) 22494346');
        $sheet->getStyle('H5')->getFont()->setSize(12);

        Carbon::setLocale('id');

        // Additional text below the address
        $sheet->mergeCells('A7:J7');
        $sheet->setCellValue('A7', 'BERITA ACARA HASIL OPNAME FISIK (STOCK OPNAME) PERSEDIAAN');
        $sheet->getStyle('A7')->getFont()->setBold(true)->setSize(12)->setUnderline(true)->setName('Cambria');
        $sheet->getStyle('A7')->getAlignment()->setHorizontal('center');

        $selectedDate = $this->tanggal;
        $dayName = $this->getDayName($selectedDate);
        $day = $this->convertNumberToWords($selectedDate->day);
        $monthName = $selectedDate->locale('id')->isoFormat('MMMM');
        $year = 'Dua Ribu ' . $this->convertNumberToWords($selectedDate->year % 1000);
        $dateInWords = "{$dayName} {$day} {$monthName} tahun {$year}";

        $sheet->mergeCells('A8:J8');
        $sheet->setCellValue('A8', "Pada hari ini, {$dateInWords}, kami telah melaksanakan opname fisik saldo barang persediaan Bulan {$monthName} Tahun Anggaran {$selectedDate->year} dengan hasil rincian sebagai berikut:");
        $sheet->getStyle('A8')->getFont()->setSize(12)->setName('Cambria');
        $sheet->getStyle('A8')->getAlignment()->setWrapText(true);

        // Headers
        $sheet->mergeCells('A11:A13'); 
        $sheet->setCellValue('A11', 'No');
        
        $sheet->mergeCells('B11:B13'); 
        $sheet->setCellValue('B11', 'Uraian Barang');
        
        $sheet->mergeCells('C11:C13'); 
        $sheet->setCellValue('C11', 'Satuan');
        
        $sheet->mergeCells('D11:D13'); 
        $sheet->setCellValue('D11', 'Harga Beli Satuan (Rupiah)');
        
        $sheet->mergeCells('E11:F11'); 
        $sheet->setCellValue('E11', 'Total Persediaan');
        $sheet->setCellValue('E12', 'Jumlah');
        $sheet->setCellValue('F12', 'Harga Total (Rupiah)');
        $sheet->mergeCells('E12:E13');
        $sheet->mergeCells('F12:F13');
        
        $sheet->mergeCells('G11:H11'); 
        $sheet->setCellValue('G11', 'Barang Rusak');
        $sheet->setCellValue('G12', 'Jumlah');
        $sheet->setCellValue('H12', 'Harga Total (Rupiah)');
        $sheet->mergeCells('G12:G13');
        $sheet->mergeCells('H12:H13');
        
        $sheet->mergeCells('I11:J11'); 
        $sheet->setCellValue('I11', 'Barang Usang');
        $sheet->setCellValue('I12', 'Jumlah');
        $sheet->setCellValue('J12', 'Harga Total (Rupiah)');
        $sheet->mergeCells('I12:I13');
        $sheet->mergeCells('J12:J13');
    
        $sheet->setCellValue('A14', '(1)');
        $sheet->setCellValue('B14', '(2)');
        $sheet->setCellValue('C14', '(3)');
        $sheet->setCellValue('D14', '(4) = (6) / (5)');
        $sheet->setCellValue('E14', '(5)');
        $sheet->setCellValue('F14', '(6)');
        $sheet->setCellValue('G14', '(7)');
        $sheet->setCellValue('H14', '(8) = (4) x (7)');
        $sheet->setCellValue('I14', '(9)');
        $sheet->setCellValue('J14', '(10) = (4) x (9)');
    
        $sheet->getStyle('A8:J8')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
    
        $sheet->getStyle('A11:J14')->applyFromArray([
            'font' => [
                'bold' => true,
                'name' => 'Cambria',
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '9BC2E6', 
                ],
            ],
        ]);
    
        $sheet->getRowDimension(13)->setRowHeight(20); 
        $sheet->getRowDimension(8)->setRowHeight(50); 
    
        $sheet->getColumnDimension('A')->setWidth(5);  
        $sheet->getColumnDimension('B')->setWidth(40); 
        $sheet->getColumnDimension('C')->setWidth(12); 
        $sheet->getColumnDimension('D')->setWidth(10); 
        $sheet->getColumnDimension('E')->setWidth(10); 
        $sheet->getColumnDimension('F')->setWidth(15); 
        $sheet->getColumnDimension('G')->setWidth(10); 
        $sheet->getColumnDimension('H')->setWidth(15); 
        $sheet->getColumnDimension('I')->setWidth(10); 
        $sheet->getColumnDimension('J')->setWidth(15); 
    
        $startingRow = 15; 
        $dataRowCount = $this->barang->count();
        $dataEndRow = $startingRow + $dataRowCount - 1;
    
        $sheet->getStyle("A$startingRow:J$dataEndRow")->applyFromArray([
            'font' => [
                'name' => 'Cambria',
                'size' => 11,
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    
        $sheet->getStyle("A$startingRow:A$dataEndRow")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C$startingRow:C$dataEndRow")->getAlignment()->setHorizontal('center');
    
        $sheet->getStyle("D$startingRow:D$dataEndRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("E$startingRow:E$dataEndRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F$startingRow:F$dataEndRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("H$startingRow:H$dataEndRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("J$startingRow:J$dataEndRow")->getNumberFormat()->setFormatCode('#,##0');
    
        for ($row = $startingRow; $row <= $dataEndRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(30);
        }
    
        $sheet->mergeCells("A" . ($dataEndRow + 1) . ":D" . ($dataEndRow + 1)); 
        $sheet->setCellValue("A" . ($dataEndRow + 1), 'Jumlah');
        $sheet->getStyle("A" . ($dataEndRow + 1))->getFont()->setBold(true);
    
        $sheet->setCellValue("E" . ($dataEndRow + 1), '=SUM(E' . $startingRow . ':E' . $dataEndRow . ')');
        $sheet->setCellValue("F" . ($dataEndRow + 1), '=SUM(F' . $startingRow . ':F' . $dataEndRow . ')');
        $sheet->setCellValue("H" . ($dataEndRow + 1), '=SUM(H' . $startingRow . ':H' . $dataEndRow . ')');
        $sheet->setCellValue("J" . ($dataEndRow + 1), '=SUM(J' . $startingRow . ':J' . $dataEndRow . ')');
    
        $sheet->getStyle("E" . ($dataEndRow + 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F" . ($dataEndRow + 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("H" . ($dataEndRow + 1))->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("J" . ($dataEndRow + 1))->getNumberFormat()->setFormatCode('#,##0');
    
        $sheet->getStyle("A" . ($dataEndRow + 1) . ":J" . ($dataEndRow + 1))->applyFromArray([
            'font' => [
                'bold' => true,
                'name' => 'Cambria',
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '9BC2E6', 
                ],
            ],
        ]);
    
        $sheet->mergeCells("A" . ($dataEndRow + 2) . ":G" . ($dataEndRow + 2)); 
        $sheet->setCellValue("A" . ($dataEndRow + 2), 'Total Persediaan-Barang Rusak-Barang Usang = (11) - (12) - (13)');
        $sheet->getStyle("A" . ($dataEndRow + 2))->getFont()->setBold(true);
    
        $sheet->setCellValue("H" . ($dataEndRow + 2), '=F' . ($dataEndRow + 1) . '-H' . ($dataEndRow + 1) . '-J' . ($dataEndRow + 1));
    
        $sheet->getStyle("A" . ($dataEndRow + 2) . ":J" . ($dataEndRow + 2))->applyFromArray([
            'font' => [
                'bold' => true,
                'name' => 'Cambria',
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '9BC2E6', 
                ],
            ],
        ]);
    
        $sheet->getRowDimension($dataEndRow + 1)->setRowHeight(40); 
        $sheet->getRowDimension($dataEndRow + 2)->setRowHeight(30);

        $approvalStartRow = $dataEndRow + 4;
        $formattedDate = $selectedDate->isoFormat('D MMMM Y'); 

        $sheet->mergeCells("B$approvalStartRow:D$approvalStartRow");
        $sheet->setCellValue("B$approvalStartRow", "Disetujui tanggal, $formattedDate");
        $sheet->mergeCells("B" . ($approvalStartRow + 1) . ":D" . ($approvalStartRow + 1));
        $sheet->setCellValue("B" . ($approvalStartRow + 1), "Kuasa Pengguna Anggaran");
        $sheet->mergeCells("B" . ($approvalStartRow + 2) . ":D" . ($approvalStartRow + 2));
        $sheet->setCellValue("B" . ($approvalStartRow + 2), "Kepala BPS Kota Jakarta Utara");

        for ($row = $approvalStartRow + 3; $row <= $approvalStartRow + 8; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        $sheet->getStyle("B$approvalStartRow:B" . ($approvalStartRow + 2))->applyFromArray([
            'font' => [
                'name' => 'Cambria',
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // PERBAIKAN: Gunakan Absolute Path untuk Stempel
        $drawingStamp = new Drawing();
        $drawingStamp->setPath('/home/simutanw/public_html/simutan/backend/assets/images/stampel-jakut.png');
        $drawingStamp->setHeight(160);
        $drawingStamp->setCoordinates("B" . ($approvalStartRow + 3)); 
        $drawingStamp->setOffsetX(140); 
        $drawingStamp->setOffsetY(0);
        $drawingStamp->setWorksheet($sheet);

        // PERBAIKAN: TTD Kepala BPS (Asumsi ID 61 berdasarkan script sebelumnya)
        $signaturePath1 = $this->getSignaturePath(61);
        if ($signaturePath1 && is_file($signaturePath1)) {
            $drawingSignature1 = new Drawing();
            $drawingSignature1->setPath($signaturePath1);
            $drawingSignature1->setHeight(150);
            $drawingSignature1->setCoordinates("B" . ($approvalStartRow + 3));
            $drawingSignature1->setOffsetX(140);
            $drawingSignature1->setOffsetY(0);
            $drawingSignature1->setWorksheet($sheet);
        }

        $sheet->mergeCells("B" . ($approvalStartRow + 9) . ":D" . ($approvalStartRow + 9));
        $sheet->setCellValue("B" . ($approvalStartRow + 9), "Theresia Parwati SST, M. I. Kom.");
        $sheet->mergeCells("B" . ($approvalStartRow + 10) . ":D" . ($approvalStartRow + 10));
        $sheet->setCellValue("B" . ($approvalStartRow + 10), "NIP. ");

        $sheet->getStyle("B" . ($approvalStartRow + 9) . ":B" . ($approvalStartRow + 10))->applyFromArray([
            'font' => [
                'name' => 'Cambria',
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getRowDimension($approvalStartRow + 9)->setRowHeight(25);
        $sheet->getRowDimension($approvalStartRow + 10)->setRowHeight(25);

        $sheet->mergeCells("G$approvalStartRow:J" . ($approvalStartRow));
        $sheet->setCellValue("G$approvalStartRow", "Jakarta, $formattedDate");

        $sheet->mergeCells("G" . ($approvalStartRow + 1) . ":J" . ($approvalStartRow + 1));
        $sheet->setCellValue("G" . ($approvalStartRow + 1), "Petugas Pengelola Persediaan,");

        $sheet->mergeCells("G" . ($approvalStartRow + 2) . ":J" . ($approvalStartRow + 2));
        $sheet->setCellValue("G" . ($approvalStartRow + 2), "Staf Subbagian Tata Usaha");

        $sheet->mergeCells("G" . ($approvalStartRow + 9) . ":J" . ($approvalStartRow + 9));
        $sheet->setCellValue("G" . ($approvalStartRow + 9), $this->admin->name);

        $sheet->mergeCells("G" . ($approvalStartRow + 10) . ":J" . ($approvalStartRow + 10));
        $sheet->setCellValue("G" . ($approvalStartRow + 10), "NIP. ");

        $sheet->getStyle("G$approvalStartRow:J" . ($approvalStartRow + 10))->applyFromArray([
            'font' => [
                'name' => 'Cambria',
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // PERBAIKAN: TTD Admin (Petugas Pengelola) menggunakan fungsi getSignaturePath
        if ($this->admin) {
            $signaturePath2 = $this->getSignaturePath($this->admin->id);

            if ($signaturePath2 && is_file($signaturePath2)) {
                $drawingSignature2 = new Drawing();
                $drawingSignature2->setPath($signaturePath2);
                $drawingSignature2->setHeight(150);
                $drawingSignature2->setCoordinates("H" . ($approvalStartRow + 3));
                $drawingSignature2->setOffsetX(60);
                $drawingSignature2->setOffsetY(0);
                $drawingSignature2->setWorksheet($sheet);
            }
        }
    }
    
    private function getDayName($date)
    {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        return $days[$date->format('l')];
    }
}