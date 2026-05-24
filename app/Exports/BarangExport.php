<?php

namespace App\Exports;

use App\Models\Barang;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\StokAwalBulan;
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

    public function __construct($barang, $tanggal)
    {
        $this->barang = $barang;
        $this->tanggal = Carbon::parse($tanggal);
    }

    public function collection()
    {
        return $this->barang->map(function ($item, $key) {
            $stokAwal = StokAwalBulan::where('barang_id', $item->id)
                ->where('tahun', $this->tanggal->year)
                ->where('bulan', $this->tanggal->month)
                ->first();

            $totalPemasukan = Pemasukan::where('barang_id', $item->id)
                ->whereDate('tanggal', '<=', $this->tanggal)
                ->sum('qty');

            $totalPengeluaran = Pengeluaran::where('barang_id', $item->id)
                ->whereDate('tanggal', '<=', $this->tanggal)
                ->sum('qty');

            $stokPadaTanggal = ($stokAwal ? $stokAwal->qty_awal : 0) + $totalPemasukan - $totalPengeluaran;

            $hargaBeliSatuan = ($item->qty_item > 0) ? $item->harga_total / $item->qty_item : 0;

            return [
                'NO' => $key + 1,
                'Uraian Barang' => $item->nama,
                'Satuan' => $item->satuan,
                'Harga Beli Satuan (Rupiah)' => $hargaBeliSatuan,
                'Total Persediaan Jumlah' => $stokPadaTanggal,
                'Total Persediaan Harga Total (Rupiah)' => $stokPadaTanggal * $hargaBeliSatuan,
                'Barang Rusak Jumlah' => 0,
                'Barang Rusak Harga Total (Rupiah)' => 0,
                'Barang Usang Jumlah' => 0,
                'Barang Usang Harga Total (Rupiah)' => 0,
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
            return $number; // Handle for larger numbers if needed
        }
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
    
        $imagePath = public_path('backend/assets/images/logo-bps.png');
    
        $drawing->setPath($imagePath);
        $drawing->setHeight(90);
        $drawing->setCoordinates('A1');
    
        return [$drawing];
    }

    public function startCell(): string
    {
        return 'A15'; // Starting cell for the data table after the custom text and headers
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

        // Mengambil tanggal yang dipilih dan mengkonversinya ke format yang diinginkan
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

        // Headers with merged cells
        $sheet->mergeCells('A11:A13'); // NO
        $sheet->setCellValue('A11', 'No');
        
        $sheet->mergeCells('B11:B13'); // Uraian Barang
        $sheet->setCellValue('B11', 'Uraian Barang');
        
        $sheet->mergeCells('C11:C13'); // Satuan
        $sheet->setCellValue('C11', 'Satuan');
        
        $sheet->mergeCells('D11:D13'); // Harga Beli Satuan (Rupiah)
        $sheet->setCellValue('D11', 'Harga Beli Satuan (Rupiah)');
        
        $sheet->mergeCells('E11:F11'); // Total Persediaan
        $sheet->setCellValue('E11', 'Total Persediaan');
        $sheet->setCellValue('E12', 'Jumlah');
        $sheet->setCellValue('F12', 'Harga Total (Rupiah)');
        $sheet->mergeCells('E12:E13');
        $sheet->mergeCells('F12:F13');
        
        $sheet->mergeCells('G11:H11'); // Barang Rusak
        $sheet->setCellValue('G11', 'Barang Rusak');
        $sheet->setCellValue('G12', 'Jumlah');
        $sheet->setCellValue('H12', 'Harga Total (Rupiah)');
        $sheet->mergeCells('G12:G13');
        $sheet->mergeCells('H12:H13');
        
        $sheet->mergeCells('I11:J11'); // Barang Usang
        $sheet->setCellValue('I11', 'Barang Usang');
        $sheet->setCellValue('I12', 'Jumlah');
        $sheet->setCellValue('J12', 'Harga Total (Rupiah)');
        $sheet->mergeCells('I12:I13');
        $sheet->mergeCells('J12:J13');
    
        // Add the row below the headers with column descriptions
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
    
        // Style for the headers
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
    
        $sheet->getRowDimension(13)->setRowHeight(20); // Adjust the height as needed
        $sheet->getRowDimension(8)->setRowHeight(50); // Adjust the height as needed
    
        // Adjust column widths
        $sheet->getColumnDimension('A')->setWidth(5);  // NO
        $sheet->getColumnDimension('B')->setWidth(40); // Uraian Barang
        $sheet->getColumnDimension('C')->setWidth(12); // Satuan
        $sheet->getColumnDimension('D')->setWidth(10); // Harga Beli Satuan (Rupiah)
        $sheet->getColumnDimension('E')->setWidth(10); // Total Persediaan Jumlah
        $sheet->getColumnDimension('F')->setWidth(15); // Total Persediaan Harga Total (Rupiah)
        $sheet->getColumnDimension('G')->setWidth(10); // Barang Rusak Jumlah
        $sheet->getColumnDimension('H')->setWidth(15); // Barang Rusak Harga Total (Rupiah)
        $sheet->getColumnDimension('I')->setWidth(10); // Barang Usang Jumlah
        $sheet->getColumnDimension('J')->setWidth(15); // Barang Usang Harga Total (Rupiah)
    
        // Apply styles to the data rows
        $startingRow = 15; // Data starts from row 15 after the header
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
    
        // Center alignment specifically for "No" and "Satuan" columns
        $sheet->getStyle("A$startingRow:A$dataEndRow")->getAlignment()->setHorizontal('center');
        $sheet->getStyle("C$startingRow:C$dataEndRow")->getAlignment()->setHorizontal('center');
    
        // Set the row height for all data rows to 20
        for ($row = $startingRow; $row <= $dataEndRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(30);
        }
    
        // Add the "Jumlah" row
        $sheet->mergeCells("A" . ($dataEndRow + 1) . ":D" . ($dataEndRow + 1)); 
        $sheet->setCellValue("A" . ($dataEndRow + 1), 'Jumlah');
        $sheet->getStyle("A" . ($dataEndRow + 1))->getFont()->setBold(true);
    
        $sheet->setCellValue("E" . ($dataEndRow + 1), '=SUM(E' . $startingRow . ':E' . $dataEndRow . ')');
        $sheet->setCellValue("F" . ($dataEndRow + 1), '=SUM(F' . $startingRow . ':F' . $dataEndRow . ')');
        $sheet->setCellValue("H" . ($dataEndRow + 1), '=SUM(H' . $startingRow . ':H' . $dataEndRow . ')');
        $sheet->setCellValue("J" . ($dataEndRow + 1), '=SUM(J' . $startingRow . ':J' . $dataEndRow . ')');
    
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
                    'rgb' => '9BC2E6', // Same background color as the headers
                ],
            ],
        ]);
    
        // Add the closing statement row
        $sheet->mergeCells("A" . ($dataEndRow + 2) . ":G" . ($dataEndRow + 2)); // Merge for the statement
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

        // Start adding approval information 2 rows after the data ends
        $approvalStartRow = $dataEndRow + 4;

        // Set approval text before the stamp and signature
        $formattedDate = $selectedDate->isoFormat('D MMMM Y'); // Format tanggal dalam bahasa Indonesia

        // Gunakan tanggal tersebut pada cell yang diinginkan
        $sheet->setCellValue("B$approvalStartRow", "Disetujui tanggal, $formattedDate");
        $sheet->setCellValue("B" . ($approvalStartRow + 1), "Kuasa Pengguna Anggaran");
        $sheet->setCellValue("B" . ($approvalStartRow + 2), "Kepala BPS Kota Jakarta Utara");

        // Apply center alignment and Cambria font
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

        // Add the stamp image (centered in column B)
        $drawingStamp = new Drawing();
        $drawingStamp->setPath(public_path('backend/assets/images/stampel-jakut.png')); // Path to your stamp image
        $drawingStamp->setHeight(160); // Adjust height to make it bigger
        $drawingStamp->setCoordinates("B" . ($approvalStartRow + 3)); // Position stamp in column B
        $drawingStamp->setOffsetX(60); // Center the stamp horizontally in the cell
        $drawingStamp->setOffsetY(-40); // Move the stamp up a bit
        $drawingStamp->setWorksheet($sheet);

        // Add the signature image (aligned to the right in column B)
        $drawingSignature1 = new Drawing();
        $drawingSignature1->setPath(public_path('backend/assets/images/users/ttd_1.png')); // Path to the signature image
        $drawingSignature1->setHeight(150); // Increase the height for a bigger signature
        $drawingSignature1->setCoordinates("B" . ($approvalStartRow + 4)); // Position signature in column B
        $drawingSignature1->setOffsetX(150); // Move the signature to the right in the cell
        $drawingSignature1->setOffsetY(-50); // Move the signature up a bit
        $drawingSignature1->setWorksheet($sheet);

        // Set the name and NIP after the stamp and signature
        $sheet->setCellValue("B" . ($approvalStartRow + 8), "Favten Ari Pujiastuti, S.Si, S.ST, M.E");
        $sheet->setCellValue("B" . ($approvalStartRow + 9), "NIP. 197804112000122002");

        // Apply center alignment and Cambria font for name and NIP
        $sheet->getStyle("B" . ($approvalStartRow + 8) . ":B" . ($approvalStartRow + 9))->applyFromArray([
            'font' => [
                'name' => 'Cambria',
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Add another signature block in columns G-J (split into separate lines)
        $sheet->mergeCells("G$approvalStartRow:J" . ($approvalStartRow));
        $sheet->setCellValue("G$approvalStartRow", "Jakarta, $formattedDate");

        $sheet->mergeCells("G" . ($approvalStartRow + 1) . ":J" . ($approvalStartRow + 1));
        $sheet->setCellValue("G" . ($approvalStartRow + 1), "Petugas Pengelola Persediaan,");

        $sheet->mergeCells("G" . ($approvalStartRow + 2) . ":J" . ($approvalStartRow + 2));
        $sheet->setCellValue("G" . ($approvalStartRow + 2), "Staf Subbagian Tata Usaha");

        // Add spacing for the signature and name
        $sheet->mergeCells("G" . ($approvalStartRow + 8) . ":J" . ($approvalStartRow + 8));
        $sheet->setCellValue("G" . ($approvalStartRow + 8), "Juniaty Pardede, A.Md");

        $sheet->mergeCells("G" . ($approvalStartRow + 9) . ":J" . ($approvalStartRow + 9));
        $sheet->setCellValue("G" . ($approvalStartRow + 9), "NIP. 199006302012122004");

        // Apply center alignment and Cambria font for the signature block
        $sheet->getStyle("G$approvalStartRow:J" . ($approvalStartRow + 9))->applyFromArray([
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

        // Add the second signature image (aligned in column G)
        $drawingSignature2 = new Drawing();
        $drawingSignature2->setPath(public_path('backend/assets/images/users/ttd_4.png')); // Path to the second signature image
        $drawingSignature2->setHeight(150); // Increase the height for a bigger signature
        $drawingSignature2->setCoordinates("H" . ($approvalStartRow + 4)); // Position signature in column G
        $drawingSignature2->setOffsetX(20); // Move the signature to the right in the cell
        $drawingSignature2->setOffsetY(-50); // Move the signature up a bit
        $drawingSignature2->setWorksheet($sheet);
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