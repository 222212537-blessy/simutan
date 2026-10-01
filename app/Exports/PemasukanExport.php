<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Pastikan Model User dipanggil
use Carbon\Carbon;

class PemasukanExport
{
    public function export($filePath, $startDate, $endDate)
    {
        // 1. BUAT SPREADSHEET BARU DARI NOL
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Rincian Persediaan');

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // 2. SET UP HEADER (Kop Laporan)
        $sheet->setCellValue('B1', 'UAPB');
        $sheet->setCellValue('C1', 'BADAN PUSAT STATISTIK');
        $sheet->setCellValue('B2', 'UAPPB-E1');
        $sheet->setCellValue('C2', 'BADAN PUSAT STATISTIK');
        $sheet->setCellValue('B3', 'UAPPB-W');
        $sheet->setCellValue('C3', 'SEKRETARIAT UTAMA BADAN PUSAT STATISTIK');

        $sheet->mergeCells('A4:J4');
        $sheet->setCellValue('A4', 'LAPORAN RINCIAN BARANG PERSEDIAAN');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A5:J5');
        $sheet->setCellValue('A5', 'UNTUK PERIODE YANG BERAKHIR TANGGAL ' . $end->format('d-m-Y'));
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A6:J6');
        $sheet->setCellValue('A6', 'TAHUN ANGGARAN : ' . $end->format('Y'));
        $sheet->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('B7:F7');
        $sheet->setCellValue('B7', 'NAMA UAKPB : BADAN PUSAT STATISTIK JAKARTA UTARA');

        $sheet->mergeCells('B8:F8');
        $sheet->setCellValue('B8', 'KODE UAKPB : 054.01.0100.539170');

        // 3. SET UP HEADER TABEL (Baris 10 & 11)
        $sheet->mergeCells('B10:B11'); $sheet->setCellValue('B10', 'Kode');
        $sheet->mergeCells('C10:C11'); $sheet->setCellValue('C10', 'Uraian');

        $sheet->mergeCells('D10:E10'); 
        $sheet->setCellValue('D10', "Nilai\n" . $start->format('d-m-Y'));
        $sheet->getStyle('D10')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('D11', 'Jumlah'); $sheet->setCellValue('E11', 'Rupiah');

        $sheet->mergeCells('F10:H10'); 
        $sheet->setCellValue('F10', 'Mutasi');
        $sheet->setCellValue('F11', 'Masuk'); $sheet->setCellValue('G11', 'Keluar'); $sheet->setCellValue('H11', 'Jumlah');

        $sheet->mergeCells('I10:J10'); 
        $sheet->setCellValue('I10', "Nilai\n" . $end->format('d-m-Y'));
        $sheet->getStyle('I10')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('I11', 'Jumlah'); $sheet->setCellValue('J11', 'Rupiah');

        $sheet->getStyle('B10:J11')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // 4. AMBIL DATA DARI DATABASE & LAKUKAN KALKULASI
        $barangs = DB::table('barangs')->orderBy('kode')->get();
        
        $grouped = [];
        foreach($barangs as $b) {
            $kel = substr($b->kode, 0, 10);
            $grouped[$kel][] = $b;
        }

        $row = 12;
        $grandTotalAwalRp = 0;
        $grandTotalAkhirRp = 0;

        foreach($grouped as $kelompokKode => $items) {
            $sumAwalRupiah = 0;
            $sumAkhirRupiah = 0;
            $itemData = [];
            
            foreach($items as $barang) {
                $stokSekarang = $barang->qty_item;
                $hargaSatuan = ($barang->qty_item > 0) ? $barang->harga_total / $barang->qty_item : 0;

                $pemasukanSetelahEndDate = DB::table('pemasukans')->where('barang_id', $barang->id)->where('tanggal', '>', $endDate)->sum('qty');
                $pengeluaranSetelahEndDate = DB::table('pengeluarans')->where('barang_id', $barang->id)->where('tanggal', '>', $endDate)->sum('qty');

                $stokAkhir = max(0, $stokSekarang - $pemasukanSetelahEndDate + $pengeluaranSetelahEndDate);
                $rupiahAkhir = $stokAkhir * $hargaSatuan;

                $totalPemasukan = DB::table('pemasukans')->where('barang_id', $barang->id)->whereBetween('tanggal', [$startDate, $endDate])->sum('qty');
                $totalPengeluaran = DB::table('pengeluarans')->where('barang_id', $barang->id)->whereBetween('tanggal', [$startDate, $endDate])->sum('qty');
                $mutasiJumlah = $totalPemasukan - $totalPengeluaran;

                $stokAwal = max(0, $stokAkhir - $totalPemasukan + $totalPengeluaran);
                $rupiahAwal = $stokAwal * $hargaSatuan;
                
                $itemData[] = [
                    'kode' => substr($barang->kode, 10), 
                    'nama' => $barang->nama,
                    'awal_qty' => $stokAwal,
                    'awal_rp' => $rupiahAwal,
                    'in' => $totalPemasukan,
                    'out' => $totalPengeluaran,
                    'mutasi' => $mutasiJumlah,
                    'akhir_qty' => $stokAkhir,
                    'akhir_rp' => $rupiahAkhir
                ];
                
                $sumAwalRupiah += $rupiahAwal;
                $sumAkhirRupiah += $rupiahAkhir;
            }
            
            // Cetak Baris Kategori
            $sheet->setCellValue('B' . $row, $kelompokKode);
            $sheet->setCellValue('C' . $row, $this->getKategoriName($kelompokKode));
            $sheet->setCellValue('E' . $row, $sumAwalRupiah);
            $sheet->setCellValue('J' . $row, $sumAkhirRupiah);
            
            $sheet->getStyle("B$row:J$row")->getFont()->setBold(true)->getColor()->setARGB('FF0000FF'); 
            $row++;
            
            // Cetak Baris Item Barang
            foreach($itemData as $item) {
                $sheet->setCellValue('B' . $row, $item['kode']);
                $sheet->setCellValue('C' . $row, $item['nama']);
                $sheet->setCellValue('D' . $row, $item['awal_qty']);
                $sheet->setCellValue('E' . $row, $item['awal_rp']);
                $sheet->setCellValue('F' . $row, $item['in']);
                $sheet->setCellValue('G' . $row, $item['out']);
                $sheet->setCellValue('H' . $row, $item['mutasi']);
                $sheet->setCellValue('I' . $row, $item['akhir_qty']);
                $sheet->setCellValue('J' . $row, $item['akhir_rp']);
                $row++;
            }

            $grandTotalAwalRp += $sumAwalRupiah;
            $grandTotalAkhirRp += $sumAkhirRupiah;
        }

        $dataEndRow = $row - 1;

        // Styling Border & Format Angka Tabel Data
        $sheet->getStyle("B12:J$dataEndRow")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle("D12:J$dataEndRow")->getNumberFormat()->setFormatCode('#,##0');

        // 5. BUAT FOOTER "JUMLAH"
        $jumlahRow = $dataEndRow + 1;
        $sheet->mergeCells("B$jumlahRow:D$jumlahRow");
        $sheet->setCellValue("B$jumlahRow", 'Jumlah');
        $sheet->setCellValue("E$jumlahRow", $grandTotalAwalRp);
        $sheet->setCellValue("J$jumlahRow", $grandTotalAkhirRp);
        
        $sheet->getStyle("B$jumlahRow:J$jumlahRow")->applyFromArray([
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], // Pusatkan teks 'Jumlah'
        ]);
        $sheet->getStyle("E$jumlahRow:J$jumlahRow")->getNumberFormat()->setFormatCode('#,##0');

        // 6. TAMBAHKAN KETERANGAN (Diberi jarak 1 baris kosong agar lebih lega)
        $ketRow = $jumlahRow + 2;
        $sheet->setCellValue("B$ketRow", "Keterangan :");
        
        // Memperlebar area keterangan agar tidak bertabrakan dengan TTD
        $sheet->mergeCells("B".($ketRow+1).":C".($ketRow+1));
        $sheet->setCellValue("B".($ketRow+1), "1. Persediaan Senilai");
        $sheet->mergeCells("F".($ketRow+1).":G".($ketRow+1));
        $sheet->setCellValue("F".($ketRow+1), ",- dalam kondisi rusak");
        
        $sheet->mergeCells("B".($ketRow+2).":C".($ketRow+2));
        $sheet->setCellValue("B".($ketRow+2), "2. Persediaan Senilai");
        $sheet->mergeCells("F".($ketRow+2).":G".($ketRow+2));
        $sheet->setCellValue("F".($ketRow+2), ",- dalam kondisi usang");

        // 7. TAMBAHKAN BAGIAN TTD (Kunci berdasarkan Role)
        $sigRow = $ketRow + 3; // Turunkan sedikit posisi persetujuan

        // Bagian Kiri (Kuasa Pengguna Barang - Digeser ke kolom D, E, F)
        $sheet->mergeCells("D$sigRow:F$sigRow");
        $sheet->setCellValue("D$sigRow", "Disetujui Tanggal :");
        $sheet->mergeCells("D".($sigRow+1).":F".($sigRow+1));
        $sheet->setCellValue("D".($sigRow+1), "Kuasa Pengguna Barang");
        $sheet->mergeCells("D".($sigRow+6).":F".($sigRow+6));
        $sheet->setCellValue("D".($sigRow+6), "Theresia Parwati SST, M. I. Kom.");

        // Bagian Kanan (Petugas Pengelola Persediaan - Digeser ke kolom H, I, J)
        $sheet->mergeCells("H$sigRow:J$sigRow");
        $sheet->setCellValue("H$sigRow", "Jakarta, " . $end->format('d-m-Y'));
        $sheet->mergeCells("H".($sigRow+1).":J".($sigRow+1));
        $sheet->setCellValue("H".($sigRow+1), "Petugas Pengelola Persediaan");
        $sheet->mergeCells("H".($sigRow+6).":J".($sigRow+6));
        
        $adminUser = User::where('role', 'admin')->first(); 
        $adminName = $adminUser ? $adminUser->name : "Manisha Elok Sholikhati"; 
        $adminId = $adminUser ? $adminUser->id : 51; 

        $sheet->setCellValue("H".($sigRow+6), $adminName);

        // Pusatkan teks TTD untuk kedua kolom
        $sheet->getStyle("D$sigRow:J".($sigRow+6))->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Perlebar baris kosong tempat gambar TTD agar TTD tidak menabrak teks nama
        $sheet->getRowDimension($sigRow+2)->setRowHeight(20);
        $sheet->getRowDimension($sigRow+3)->setRowHeight(20);
        $sheet->getRowDimension($sigRow+4)->setRowHeight(20);
        $sheet->getRowDimension($sigRow+5)->setRowHeight(20);

        // GAMBAR TTD KEPALA BPS (Diatur Offset agar posisinya tepat di tengah kolom E)
        $ttdKuasaPath = $this->getSignaturePath(61); 
        if ($ttdKuasaPath && is_file($ttdKuasaPath)) {
            $draw1 = new Drawing();
            $draw1->setPath($ttdKuasaPath);
            $draw1->setHeight(80); // Ukuran TTD sedikit dikecilkan agar proporsional
            $draw1->setCoordinates("E".($sigRow+2)); // Posisi jangkar di kolom E
            $draw1->setOffsetX(15); // Geser perlahan ke tengah
            $draw1->setOffsetY(10); // Geser ke bawah menjauhi judul
            $draw1->setWorksheet($sheet);
        }

        // GAMBAR TTD ADMIN (Diatur Offset agar posisinya tepat di tengah kolom I)
        $ttdPetugasPath = $this->getSignaturePath($adminId);
        if ($ttdPetugasPath && is_file($ttdPetugasPath)) {
            $draw2 = new Drawing();
            $draw2->setPath($ttdPetugasPath);
            $draw2->setHeight(80); // Ukuran TTD sedikit dikecilkan agar proporsional
            $draw2->setCoordinates("I".($sigRow+2)); // Posisi jangkar di kolom I
            $draw2->setOffsetX(15); // Geser perlahan ke tengah
            $draw2->setOffsetY(10); // Geser ke bawah menjauhi judul
            $draw2->setWorksheet($sheet);
        }

        // 8. ATUR LEBAR KOLOM AGAR RAPI (Disempurnakan)
        $sheet->getColumnDimension('A')->setWidth(3);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(12); // Diperlebar
        $sheet->getColumnDimension('E')->setWidth(15); // Diperlebar untuk ruang TTD
        $sheet->getColumnDimension('F')->setWidth(12); // Diperlebar
        $sheet->getColumnDimension('G')->setWidth(12); // Diperlebar
        $sheet->getColumnDimension('H')->setWidth(12); // Diperlebar
        $sheet->getColumnDimension('I')->setWidth(15); // Diperlebar untuk ruang TTD
        $sheet->getColumnDimension('J')->setWidth(15); // Diperlebar

        // 9. SIMPAN FILE
        $fileName = 'Laporan_Rincian_Persediaan_' . now()->format('Ymd_His') . '.xlsx';
        $directory = storage_path('app/excel');
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $tempFilePath = $directory . '/' . $fileName;
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempFilePath);
        
        return $tempFilePath;
    }

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

    private function getKategoriName($kode) {
        $kategori = [
            '1010301001' => 'ALAT TULIS',
            '1010301002' => 'KERTAS DAN COVER',
            '1010301003' => 'BAHAN CETAK',
            '1010302001' => 'BARANG CETAKAN',
            '1010303001' => 'ALAT RUMAH TANGGA',
            '1010304001' => 'BAHAN OBA T/ALAT KESEHATAN',
            '1010399999' => 'PERSEDIAAN LAINNYA'
        ];
        return $kategori[$kode] ?? '';
    }
}