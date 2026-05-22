<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaveMonthlyStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dapatkan tanggal saat ini dengan timezone yang tepat
        $currentDate = Carbon::now('Asia/Jakarta');
        $year = $currentDate->year;
        $month = $currentDate->month;

        // Ambil semua barang dari tabel barang
        $barangs = DB::table('barangs')->get();

        foreach ($barangs as $barang) {
            // Periksa apakah stok awal sudah ada untuk bulan ini
            $existingStock = DB::table('stok_awal_bulans')
                ->where('barang_id', $barang->id)
                ->where('tahun', $year)
                ->where('bulan', $month)
                ->first();

            if (!$existingStock) {
                // Jika stok awal belum ada, simpan stok awal bulan ini
                DB::table('stok_awal_bulans')->insert([
                    'barang_id' => $barang->id,
                    'qty_awal' => $barang->qty_item, // Ambil qty saat ini sebagai stok awal bulan
                    'tahun' => $year,
                    'bulan' => $month,
                    'created_at' => Carbon::now('Asia/Jakarta'),
                    'updated_at' => Carbon::now('Asia/Jakarta'),
                    'harga_total' => $barang->harga_total,
                ]);
            }
        }
    }
}
