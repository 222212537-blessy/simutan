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

        $this->calculateCategorySums($sheet);

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
            // Ambil kode barang dari kolom B
            $kode = $sheet->getCell('B' . $row->getRowIndex())->getValue();
            if ($kode) {
                $kodeStrTrimmed = trim((string)$kode);
                
                // Jika kode memiliki 10 digit, maka ini adalah kelompok_kode
                if (strlen($kodeStrTrimmed) == 10) {
                    $kelompok_kode = $kodeStrTrimmed;
                } elseif (strlen($kodeStrTrimmed) == 6 && isset($kelompok_kode)) {
                    // Jika kode memiliki 6 digit, gabungkan dengan kelompok_kode
                    $kode_barang_full = "{$kelompok_kode}{$kodeStrTrimmed}";

                    // Tambahkan kode barang penuh ke existingBarang
                    $existingBarang[] = $kode_barang_full;

                    // Cari barang di database berdasarkan kode yang digabungkan
                    $barang = DB::table('barangs')->where('kode', $kode_barang_full)->first();

                    if ($barang) {
                        $stokSekarang = $barang->qty_item;

                        // Pemasukan sejak start_date hingga sekarang (termasuk yang melebihi endDate)
                        $pemasukanSetelahStartDate = DB::table('pemasukans')
                            ->where('barang_id', $barang->id)
                            ->where('tanggal', '>=', $startDate)
                            ->sum('qty');

                        // Pengeluaran sejak start_date hingga sekarang (termasuk yang melebihi endDate)
                        $pengeluaranSetelahStartDate = DB::table('pengeluarans')
                            ->where('barang_id', $barang->id)
                            ->where('tanggal', '>=', $startDate)
                            ->sum('qty');

                        $stokSekarang = $barang->qty_item;

                        // Harga Satuan dari stok saat ini
                        $hargaSatuan = 0;
                        if ($barang->qty_item > 0) {
                            $hargaSatuan = $barang->harga_total / $barang->qty_item;
                        }

                        // 1. Hitung Stok Akhir (Nilai Tanggal Akhir)
                        // Mundur dari stokSekarang, batalkan transaksi setelah endDate
                        $pemasukanSetelahEndDate = DB::table('pemasukans')
                            ->where('barang_id', $barang->id)
                            ->where('tanggal', '>', $endDate)
                            ->sum('qty');

                        $pengeluaranSetelahEndDate = DB::table('pengeluarans')
                            ->where('barang_id', $barang->id)
                            ->where('tanggal', '>', $endDate)
                            ->sum('qty');

                        $stokAkhir = $stokSekarang - $pemasukanSetelahEndDate + $pengeluaranSetelahEndDate;
                        if ($stokAkhir < 0) $stokAkhir = 0;
                        $rupiahAkhir = $stokAkhir * $hargaSatuan;

                        // 2. Hitung Mutasi
                        $totalPemasukan = DB::table('pemasukans')
                            ->where('barang_id', $barang->id)
                            ->whereBetween('tanggal', [$startDate, $endDate])
                            ->sum('qty');

                        $totalPengeluaran = DB::table('pengeluarans')
                            ->where('barang_id', $barang->id)
                            ->whereBetween('tanggal', [$startDate, $endDate])
                            ->sum('qty');
                            
                        $mutasiJumlah = $totalPemasukan - $totalPengeluaran;

                        // 3. Hitung Stok Awal (Nilai Tanggal Mulai)
                        // Mundur dari stokAkhir, batalkan mutasi selama periode
                        $stokAwal = $stokAkhir - $totalPemasukan + $totalPengeluaran;
                        if ($stokAwal < 0) $stokAwal = 0;
                        $rupiahAwal = $stokAwal * $hargaSatuan;

                        // Set nilai baru ke dalam sheet Excel
                        $sheet->setCellValue('D' . $rowIndex, $stokAwal); // Stok awal pada kolom D
                        $sheet->setCellValue('E' . $rowIndex, $rupiahAwal); // Rupiah awal pada kolom E
                        $sheet->setCellValue('F' . $rowIndex, $totalPemasukan); // Pemasukan pada kolom F
                        $sheet->setCellValue('G' . $rowIndex, $totalPengeluaran); // Pengeluaran pada kolom G
                        $sheet->setCellValue('H' . $rowIndex, $mutasiJumlah); // Mutasi jumlah
                        $sheet->setCellValue('I' . $rowIndex, $stokAkhir); // Stok akhir pada kolom I
                        $sheet->setCellValue('J' . $rowIndex, $rupiahAkhir); // Rupiah akhir pada kolom J
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

    private function calculateCategorySums($sheet)
    {
        $kategoriRowIndex = null;
        $sums = ['D' => 0, 'E' => 0, 'F' => 0, 'G' => 0, 'H' => 0, 'I' => 0, 'J' => 0];

        $highestRow = $sheet->getHighestRow();
        for ($row = 12; $row <= $highestRow; $row++) {
            $kode = $sheet->getCell('B' . $row)->getValue();
            
            if (strtolower(trim($kode)) == 'jumlah') {
                if ($kategoriRowIndex !== null) {
                    $this->applyCategorySums($sheet, $kategoriRowIndex, $sums);
                }
                break;
            }

            if ($kode) {
                $kodeStr = trim((string)$kode);
                // Jika integer (misal 10 dari 000010), padding agar menjadi 6 digit
                if (is_numeric($kodeStr) && strlen($kodeStr) < 6) {
                    $kodeStr = str_pad($kodeStr, 6, '0', STR_PAD_LEFT);
                }

                if (strlen($kodeStr) == 10) {
                    if ($kategoriRowIndex !== null) {
                        $this->applyCategorySums($sheet, $kategoriRowIndex, $sums);
                    }
                    $kategoriRowIndex = $row;
                    $sums = ['D' => 0, 'E' => 0, 'F' => 0, 'G' => 0, 'H' => 0, 'I' => 0, 'J' => 0];
                } elseif (strlen($kodeStr) == 6 && $kategoriRowIndex !== null) {
                    $sums['D'] += (float)$sheet->getCell('D' . $row)->getCalculatedValue();
                    $sums['E'] += (float)$sheet->getCell('E' . $row)->getCalculatedValue();
                    $sums['F'] += (float)$sheet->getCell('F' . $row)->getCalculatedValue();
                    $sums['G'] += (float)$sheet->getCell('G' . $row)->getCalculatedValue();
                    $sums['H'] += (float)$sheet->getCell('H' . $row)->getCalculatedValue();
                    $sums['I'] += (float)$sheet->getCell('I' . $row)->getCalculatedValue();
                    $sums['J'] += (float)$sheet->getCell('J' . $row)->getCalculatedValue();
                }
            }
        }
    }

    private function applyCategorySums($sheet, $rowIndex, $sums)
    {
        // Unmerge cells yang ada di baris kategori (bawaan dari template lama)
        try {
            $sheet->unmergeCells('D' . $rowIndex . ':E' . $rowIndex);
        } catch (\Exception $e) { }
        try {
            $sheet->unmergeCells('F' . $rowIndex . ':H' . $rowIndex);
        } catch (\Exception $e) { }
        try {
            $sheet->unmergeCells('I' . $rowIndex . ':J' . $rowIndex);
        } catch (\Exception $e) { }

        // Set nilai kosong untuk kolom yang tidak perlu ada isinya di baris kategori
        $emptyColumns = ['D', 'F', 'G', 'H', 'I'];
        foreach ($emptyColumns as $col) {
            $sheet->setCellValue($col . $rowIndex, '');
        }

        // Set jumlah Rupiah untuk kolom Nilai Awal (E) dan Nilai Akhir (J)
        $sumColumns = ['E', 'J'];
        foreach ($sumColumns as $col) {
            $sheet->setCellValue($col . $rowIndex, $sums[$col]);
            $sheet->getStyle($col . $rowIndex)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
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
    
        // Cari posisi baris "Jumlah" terlebih dahulu
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

        // Iterasi barang baru
        foreach ($barangBaru as $barang) {
            $kelompokKode = substr($barang->kode, 0, 10);
            
            // Jika kategori ini belum ada, buat kategori barunya
            if (!isset($kelompokKodeBaris[$kelompokKode])) {
                $sheet->insertNewRowBefore($jumlahRow, 1);
                $sheet->setCellValue('B' . $jumlahRow, $kelompokKode);
                $sheet->setCellValue('C' . $jumlahRow, '');
                
                $sheet->getStyle('B' . $jumlahRow)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);
                $sheet->getStyle('B' . $jumlahRow)->getFont()->setBold(true);
                
                $sheet->getStyle('B' . $jumlahRow . ':J' . $jumlahRow)->applyFromArray([
                    'borders' => [
                        'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                        'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
                    ],
                ]);
                
                // Simpan posisi baris kategori ini
                $kelompokKodeBaris[$kelompokKode] = $jumlahRow;
                
                // Baris Jumlah terdorong turun 1
                $jumlahRow++;
            }
            
            // Sisipkan barang di baris setelah elemen terakhir kelompok kode ini
            $barisBarang = $kelompokKodeBaris[$kelompokKode] + 1;
            $sheet->insertNewRowBefore($barisBarang, 1);
            
            $sheet->setCellValue('B' . $barisBarang, substr($barang->kode, 10));
            $sheet->setCellValue('C' . $barisBarang, $barang->nama);
            
            $stokSekarang = $barang->qty_item;
            $hargaSatuan = 0;
            if ($barang->qty_item > 0) {
                $hargaSatuan = $barang->harga_total / $barang->qty_item;
            }

            // 1. Hitung Stok Akhir
            $pemasukanSetelahEndDate = DB::table('pemasukans')->where('barang_id', $barang->id)->where('tanggal', '>', $endDate)->sum('qty');
            $pengeluaranSetelahEndDate = DB::table('pengeluarans')->where('barang_id', $barang->id)->where('tanggal', '>', $endDate)->sum('qty');
            $stokAkhir = $stokSekarang - $pemasukanSetelahEndDate + $pengeluaranSetelahEndDate;
            if ($stokAkhir < 0) $stokAkhir = 0;
            $rupiahAkhir = $stokAkhir * $hargaSatuan;

            // 2. Hitung Mutasi
            $totalPemasukan = DB::table('pemasukans')->where('barang_id', $barang->id)->whereBetween('tanggal', [$startDate, $endDate])->sum('qty');
            $totalPengeluaran = DB::table('pengeluarans')->where('barang_id', $barang->id)->whereBetween('tanggal', [$startDate, $endDate])->sum('qty');
            $mutasiJumlah = $totalPemasukan - $totalPengeluaran;

            // 3. Hitung Stok Awal
            $stokAwal = $stokAkhir - $totalPemasukan + $totalPengeluaran;
            if ($stokAwal < 0) $stokAwal = 0;
            $rupiahAwal = $stokAwal * $hargaSatuan;
            
            $sheet->setCellValue('D' . $barisBarang, $stokAwal);
            $sheet->setCellValue('E' . $barisBarang, $rupiahAwal);
            $sheet->setCellValue('F' . $barisBarang, $totalPemasukan);
            $sheet->setCellValue('G' . $barisBarang, $totalPengeluaran);
            $sheet->setCellValue('H' . $barisBarang, $mutasiJumlah);
            $sheet->setCellValue('I' . $barisBarang, $stokAkhir);
            $sheet->setCellValue('J' . $barisBarang, $rupiahAkhir);
            
            $sheet->getStyle('B' . $barisBarang . ':J' . $barisBarang)->applyFromArray([
                'borders' => [
                    'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
                'font' => [
                    'bold' => false,
                    'color' => ['argb' => \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK],
                ]
            ]);
            
            // Set rata kanan khusus untuk kolom angka (D sampai J)
            $sheet->getStyle('D' . $barisBarang . ':J' . $barisBarang)->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ]
            ]);
            
            // Perbarui posisi baris terakhir kelompok kode ini
            $kelompokKodeBaris[$kelompokKode] = $barisBarang;
            
            // Pastikan baris "Jumlah" terdorong jika baris yang disisipkan ada di atasnya
            if ($barisBarang <= $jumlahRow) {
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
