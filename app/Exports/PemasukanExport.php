<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PemasukanExport
{
    public function export($filePath, $startDate, $endDate)
    {
        // Muat spreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Update header dan data inventory
        $this->updateHeader($sheet, $startDate, $endDate);
        $this->updateInventoryFromExcel($spreadsheet, $sheet, $startDate, $endDate);

        // Simpan perubahan ke file sementara
        $tempFilePath = storage_path('app/excel/Laporan_Rincian_Persediaan_' . now()->format('Ymd_His') . '.xlsx');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFilePath);
        
        return $tempFilePath;
    }

    private function updateHeader($sheet, $startDate, $endDate)
    {
        // Ubah teks pada sel A5
        $sheet->setCellValue('A5', 'UNTUK PERIODE YANG BERAKHIR TANGGAL ' . Carbon::parse($endDate)->format('d-m-Y'));

        // Ubah teks pada sel A6 hanya menampilkan tahun
        $sheet->setCellValue('A6', 'TAHUN ANGGARAN : ' . Carbon::parse($endDate)->format('Y'));

        // Ubah teks pada kolom D10 menjadi "Nilai" di baris pertama dan "$startDate" di baris kedua
        $sheet->setCellValue('D10', "Nilai" . PHP_EOL . Carbon::parse($startDate)->format('d-m-Y'));

        // Atur format sel agar mendukung pemisah baris (wrap text)
        $sheet->getStyle('D10')->getAlignment()->setWrapText(true);

        // Ubah teks pada kolom I10 menjadi "Nilai $endDate"
        $sheet->setCellValue('I10', "Nilai" . PHP_EOL . Carbon::parse($endDate)->format('d-m-Y'));
        // Atur format sel agar mendukung pemisah baris (wrap text)
        $sheet->getStyle('I10')->getAlignment()->setWrapText(true);
    }

    private function updateInventoryFromExcel($spreadsheet, $sheet, $startDate, $endDate)
    {
        $rowIndex = 12; // Baris data mulai dari baris ke-12
        $existingBarang = []; // Array untuk menyimpan kode barang yang sudah ada di Excel
        $kelompok_kode = null;

        foreach ($sheet->getRowIterator($rowIndex) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Ambil kode barang dari kolom B (index 1)
            $kode = $rowData[1];

            // Gabungkan kelompok kode dengan kode barang jika ada
            if ($kode) {
                if (strlen($kode) == 10) {
                    // Jika kode memiliki 10 digit, maka ini adalah kelompok_kode
                    $kelompok_kode = $kode;
                } elseif (strlen($kode) == 6 && isset($kelompok_kode)) {
                    // Jika kode memiliki 6 digit, gabungkan dengan kelompok_kode
                    $kode_barang_full = "{$kelompok_kode}{$kode}";

                    // Tambahkan kode barang penuh ke existingBarang
                    $existingBarang[] = $kode_barang_full;

                    // Cari barang di database berdasarkan kode yang digabungkan
                    $barang = DB::table('barangs')->where('kode', $kode_barang_full)->first();

                    if ($barang) {
                        // Hitung stok awal pada start_date
                        $stokAwal = DB::table('stok_awal_bulans')
                            ->where('barang_id', $barang->id)
                            ->where('tahun', Carbon::parse($startDate)->year)
                            ->where('bulan', Carbon::parse($startDate)->month)
                            ->value('qty_awal');

                        // Hitung pemasukan di antara start_date dan endDate
                        $totalPemasukan = DB::table('pemasukans')
                            ->where('barang_id', $barang->id)
                            ->whereBetween('tanggal', [$startDate, $endDate])
                            ->sum('qty');

                        // Hitung pengeluaran di antara start_date dan endDate
                        $totalPengeluaran = DB::table('pengeluarans')
                            ->where('barang_id', $barang->id)
                            ->whereBetween('tanggal', [$startDate, $endDate])
                            ->sum('qty');

                        // Hitung stok akhir
                        $stokAkhir = $stokAwal + $totalPemasukan - $totalPengeluaran;

                        // Set nilai baru ke dalam sheet Excel
                        $sheet->setCellValue('D' . $rowIndex, $stokAwal); // Stok awal pada kolom D
                        $sheet->setCellValue('F' . $rowIndex, $totalPemasukan); // Pemasukan pada kolom F
                        $sheet->setCellValue('G' . $rowIndex, -$totalPengeluaran); // Pengeluaran pada kolom G (negatif)
                        $sheet->setCellValue('H' . $rowIndex, "=IF(F$rowIndex+G$rowIndex<0, F$rowIndex+G$rowIndex, F$rowIndex+G$rowIndex)");
                        $sheet->setCellValue('I' . $rowIndex, $stokAkhir); // Stok akhir pada kolom I
                    } else {
                        Log::warning("Barang tidak ditemukan untuk kode: $kode_barang_full pada baris $rowIndex");
                    }
                }
            }

            $rowIndex++;
        }
        // Simpan existingBarang untuk digunakan pada sheet baru
        $this->createNewSheetWithExistingAndNewBarang($spreadsheet, $existingBarang, $startDate, $endDate);
        // Loop dari baris 580 dan cari "Jakarta" di kolom G
        $row = 587;
        while (true) {
            $currentValue = $sheet->getCell('G' . $row)->getValue();
            
            // Jika menemukan "Jakarta", ubah nilainya
            if (strpos($currentValue, 'Jakarta') !== false) {
                $newValue = "Jakarta, " . Carbon::parse($endDate)->format('d-m-Y');
                $sheet->setCellValue('G' . $row, $newValue);
                break; // Keluar dari loop setelah mengganti nilai
            }

            $row++;
            
            // Tambahkan batasan untuk menghindari loop tanpa akhir
            if ($row > $sheet->getHighestRow()) {
                // Log::warning("Teks 'Jakarta' tidak ditemukan dari baris 580 ke atas.");
                break;
            }
        }
    }
    private function createNewSheetWithExistingAndNewBarang($spreadsheet, $existingBarang, $startDate, $endDate)
    {
        // Buat sheet baru untuk barang baru
        $newSheet = $spreadsheet->createSheet();
        $newSheet->setTitle('Barang Baru');
        
        // Tambahkan header di sheet baru
        $newSheet->setCellValue('A1', 'Kode Barang Baru');
        $newSheet->setCellValue('B1', 'Nama Barang Baru');
        
        $rowIndex = 2; // Mulai dari baris kedua untuk memasukkan data barang baru
        
        // Ambil semua barang dari database yang tidak ada di Excel
        $barangBaru = DB::table('barangs')
            ->whereNotIn('kode', $existingBarang)
            ->get();
    
        // Inisialisasi array untuk menyimpan baris terakhir dari setiap kelompok kode
        $kelompokKodeBaris = [];
    
        // Iterasi melalui sheet untuk menemukan baris terakhir dari setiap kelompok kode
        $sheet = $spreadsheet->getActiveSheet();
        foreach ($sheet->getRowIterator(12) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
    
            $kode = $rowData[1];
            if ($kode && strlen($kode) == 10) {
                // Jika kode adalah kelompok kode (10 digit), simpan barisnya
                $kelompok_kode = $kode;
                $kelompokKodeBaris[$kelompok_kode] = $row->getRowIndex();
            } elseif ($kode && strlen($kode) == 6 && isset($kelompok_kode)) {
                // Jika kode adalah barang (6 digit) dalam kelompok kode, perbarui baris terakhir kelompok kode
                $kelompokKodeBaris[$kelompok_kode] = $row->getRowIndex();
            }
        }
    
        // Masukkan barang baru yang kelompok kodenya sudah ada
        foreach ($barangBaru as $barang) {
            $kelompokKode = substr($barang->kode, 0, 10); // Dapatkan 10 digit pertama sebagai kelompok kode
    
            if (isset($kelompokKodeBaris[$kelompokKode])) {
                // Jika kelompok kode sudah ada, tambahkan barang baru di bawahnya
                $barisKelompok = $kelompokKodeBaris[$kelompokKode] + 1;
                $sheet->insertNewRowBefore($barisKelompok, 1);
                $sheet->setCellValue('B' . $barisKelompok, substr($barang->kode, 10)); // Masukkan 6 digit terakhir sebagai kode barang
                $sheet->setCellValue('C' . $barisKelompok, $barang->nama); // Masukkan nama barang
    
                // Hitung stok awal, pemasukan, pengeluaran, dan stok akhir
                $stokAwal = DB::table('stok_awal_bulans')
                    ->where('barang_id', $barang->id)
                    ->where('tahun', Carbon::parse($startDate)->year)
                    ->where('bulan', Carbon::parse($startDate)->month)
                    ->value('qty_awal');
    
                $totalPemasukan = DB::table('pemasukans')
                    ->where('barang_id', $barang->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('qty');
    
                $totalPengeluaran = DB::table('pengeluarans')
                    ->where('barang_id', $barang->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('qty');
    
                $stokAkhir = $stokAwal + $totalPemasukan - $totalPengeluaran;
    
                // Masukkan nilai stok awal, pemasukan, pengeluaran, dan stok akhir ke dalam sheet Excel
                $sheet->setCellValue('D' . $barisKelompok, $stokAwal); // Stok awal pada kolom D
                $sheet->setCellValue('F' . $barisKelompok, $totalPemasukan); // Pemasukan pada kolom F
                $sheet->setCellValue('G' . $barisKelompok, -$totalPengeluaran); // Pengeluaran pada kolom G (negatif)
                $sheet->setCellValue('H' . $barisKelompok, "=F$barisKelompok+G$barisKelompok"); // Rumus untuk kolom H
                $sheet->setCellValue('I' . $barisKelompok, $stokAkhir); // Stok akhir pada kolom I
    
                // Tambahkan border tebal (thick) hanya pada outline (atas, bawah, kanan, kiri)
                $sheet->getStyle('B' . $barisKelompok . ':I' . $barisKelompok)->applyFromArray([
                    'borders' => [
                        'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                    ],
                ]);
    
                // Perbarui posisi terakhir kelompok kode
                $kelompokKodeBaris[$kelompokKode] = $barisKelompok;
            }
        }
    
        // Setelah semua barang yang sesuai kelompok kodenya ditambahkan, cari posisi terbaru dari baris "Jumlah"
        $jumlahRow = null;
        for ($row = 581; $row <= $sheet->getHighestRow(); $row++) {
            $cellValue = $sheet->getCell('B' . $row)->getValue();
            if (strtolower(trim($cellValue)) == 'jumlah') {
                $jumlahRow = $row;
                break;
            }
        }
    
        // Pastikan jumlahRow ditemukan
        if ($jumlahRow === null) {
            throw new \Exception('Tidak dapat menemukan baris dengan nilai "jumlah" di kolom B.');
        }
    
        // Sisipkan kelompok kode baru dan barang-barang baru sebelum "Jumlah"
        foreach ($barangBaru as $barang) {
            $kelompokKode = substr($barang->kode, 0, 10); // Dapatkan 10 digit pertama sebagai kelompok kode
    
            if (!isset($kelompokKodeBaris[$kelompokKode])) {
                // Tambahkan kelompok kode baru sebelum "Jumlah"
                $sheet->insertNewRowBefore($jumlahRow, 1);
                $sheet->setCellValue('B' . $jumlahRow, $kelompokKode); // Masukkan kelompok kode di kolom B
                $sheet->setCellValue('C' . $jumlahRow, ''); // Kosongkan kolom C
    
                // Terapkan format warna biru pada kelompok kode
                $sheet->getStyle('B' . $jumlahRow)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                $sheet->getStyle('B' . $jumlahRow)->getFont()->setBold(true); // Tambahkan tebal pada teks
    
                // Tambahkan border tebal (thick) hanya pada outline (atas, bawah, kanan, kiri)
                $sheet->getStyle('B' . $jumlahRow . ':I' . $jumlahRow)->applyFromArray([
                    'borders' => [
                        'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                    ],
                ]);
    
                // Update jumlahRow agar barang berikutnya disisipkan di bawah kelompok kode ini
                $jumlahRow++;
    
                // Masukkan barang baru di bawah kelompok kode baru
                $sheet->insertNewRowBefore($jumlahRow, 1);
                $sheet->setCellValue('B' . $jumlahRow, substr($barang->kode, 10)); // Masukkan 6 digit terakhir sebagai kode barang
                $sheet->setCellValue('C' . $jumlahRow, $barang->nama); // Masukkan nama barang
    
                // Hitung stok awal, pemasukan, pengeluaran, dan stok akhir
                $stokAwal = DB::table('stok_awal_bulans')
                    ->where('barang_id', $barang->id)
                    ->where('tahun', Carbon::parse($startDate)->year)
                    ->where('bulan', Carbon::parse($startDate)->month)
                    ->value('qty_awal');
    
                $totalPemasukan = DB::table('pemasukans')
                    ->where('barang_id', $barang->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('qty');
    
                $totalPengeluaran = DB::table('pengeluarans')
                    ->where('barang_id', $barang->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('qty');
    
                $stokAkhir = $stokAwal + $totalPemasukan - $totalPengeluaran;
    
                // Masukkan nilai stok awal, pemasukan, pengeluaran, dan stok akhir ke dalam sheet Excel
                $sheet->setCellValue('D' . $jumlahRow, $stokAwal); // Stok awal pada kolom D
                $sheet->setCellValue('F' . $jumlahRow, $totalPemasukan); // Pemasukan pada kolom F
                $sheet->setCellValue('G' . $jumlahRow, -$totalPengeluaran); // Pengeluaran pada kolom G (negatif)
                $sheet->setCellValue('H' . $jumlahRow, "=F$jumlahRow+G$jumlahRow"); // Rumus untuk kolom H
                $sheet->setCellValue('I' . $jumlahRow, $stokAkhir); // Stok akhir pada kolom I
    
                // Tambahkan border tebal (thick) hanya pada outline (atas, bawah, kanan, kiri)
                $sheet->getStyle('B' . $jumlahRow . ':I' . $jumlahRow)->applyFromArray([
                    'borders' => [
                        'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                    ],
                ]);
                // Update jumlahRow agar barang berikutnya disisipkan di bawahnya
                $jumlahRow++;
            }
        }
    
        // Juga tambahkan barang baru ke sheet "Barang Baru" untuk pelacakan tambahan
        foreach ($barangBaru as $barang) {
            $newSheet->setCellValue('A' . $rowIndex, $barang->kode);
            $newSheet->setCellValue('B' . $rowIndex, $barang->nama);
            $rowIndex++;
        }
    }    
}
