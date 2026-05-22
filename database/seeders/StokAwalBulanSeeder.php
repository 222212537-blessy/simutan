<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\StokAwalBulan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StokAwalBulanSeeder extends Seeder
{
    public function run()
    {
        // Path ke file CSV yang sudah dipindahkan
        $filePath = database_path('seeders/barangs.csv');

        // Baca file, hapus spasi/baris kosong
        $lines = array_map('trim', file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        
        if (empty($lines)) {
            return; 
        }

        // Ambil dan bersihkan header
        $header = array_map('trim', str_getcsv(array_shift($lines))); 

        foreach ($lines as $line) {
            $row = str_getcsv($line);
            
            if (count($header) !== count($row)) {
                continue; 
            }
            
            $rowData = array_combine($header, $row);

            // 💡 PERBAIKAN FOKUS: Membersihkan dan mengkonversi nilai ke Integer/Numerik
            
            // Konversi qty_awal ke integer (default 0 jika kosong)
            $qtyAwal = empty($rowData['qty_item']) ? 0 : (int)$rowData['qty_item'];
            
            // Konversi harga_total ke integer (default 0 jika kosong)
            $hargaTotal = empty($rowData['harga_total']) ? 0 : (int)$rowData['harga_total']; 

            StokAwalBulan::create([
                'barang_id' => $rowData['id'],
                'qty_awal' => $qtyAwal, // Menggunakan variabel yang sudah dibersihkan
                'harga_total' => $hargaTotal, // Menggunakan variabel yang sudah dibersihkan
                'tahun' => 2025,
                'bulan' => 9,
            ]);
        }
    }
}