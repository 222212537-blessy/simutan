<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaveMonthlyStock extends Command
{
    protected $signature = 'stock:save-monthly';
    protected $description = 'Simpan stok awal bulan untuk semua barang pada tanggal 1 setiap bulan';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Dapatkan tanggal saat ini (misalnya 1 Januari 2024)
        $currentDate = Carbon::now();
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
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'harga_total' => $barang->harga_total,
                ]);
            } 
        }
    }
}
