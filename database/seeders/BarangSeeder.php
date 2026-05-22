<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run()
    {
        // ... (Logika pembacaan CSV yang sudah benar) ...
        $filePath = database_path('seeders/barangs.csv');
        $lines = array_map('trim', file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        if (empty($lines)) {
            return; 
        }
        $header = array_map('trim', str_getcsv(array_shift($lines))); 

        // 4. Proses sisa baris data
        foreach ($lines as $line) {
            $row = str_getcsv($line);
            
            if (count($header) !== count($row)) {
                continue; 
            }
            
            $rowData = array_combine($header, $row);

            // Bersihkan data dan konversi ke integer
            // Menggunakan (int) untuk memastikan nilai adalah INTEGER (WAJIB untuk qty_item)
            $qty_item = empty($rowData['qty_item']) ? 0 : (int)$rowData['qty_item']; 
            $harga_total = empty($rowData['harga_total']) ? 0 : (int)$rowData['harga_total']; 

            // Simpan data ke database
            Barang::create([
                'kode' => $rowData['kode'],
                'nama' => $rowData['nama'],
                'kelompok_id' => $rowData['kelompok_id'],
                'kategori_id' => $rowData['kategori_id'],
                
                // 💡 PERBAIKAN: Ganti string literal 'qty_item' dengan variabel $qty_item
                'qty_item' => $qty_item, 
                
                'satuan' => $rowData['satuan'],
                'foto_barang' => $rowData['foto_barang'],
                'created_at' => now(),
                'updated_at' => now(),
                'harga_total' => $harga_total,
            ]);
        }
    }
}