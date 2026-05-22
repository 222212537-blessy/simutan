<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaveAwalBulan
{
    public function handle($request, Closure $next)
    {
        // Hanya jalankan logika ini jika pengguna adalah admin
        if (Auth::check() && Auth::user()->role === 'admin') {

            // Path ke file JSON untuk menyimpan jumlah barang di awal bulan
            $jsonFilePath = storage_path('app/awal_bulan_qty.json');

            // Cek apakah data awal bulan sudah disimpan atau belum
            if ($this->shouldSaveAwalBulan($jsonFilePath)) {
                $this->saveAwalBulan();
            }
        }

        return $next($request);
    }

    private function shouldSaveAwalBulan($jsonFilePath)
    {
        Log::info('Memeriksa apakah perlu menyimpan data awal bulan.');

        // Baca file JSON
        if (file_exists($jsonFilePath)) {
            $awalBulanData = json_decode(file_get_contents($jsonFilePath), true);
        } else {
            $awalBulanData = [];
        }

        // Dapatkan bulan saat ini
        $currentMonth = now()->format('Y-m');
        $currentDay = now()->day;

        // Cek apakah sudah ada data untuk bulan ini
        if (!isset($awalBulanData['month']) || $awalBulanData['month'] !== $currentMonth) {
            Log::info('Belum ada data untuk bulan ini. Memeriksa tanggal...');

            if ($currentDay >= 1 && $currentDay <= 5) {
                Log::info("Tanggal saat ini adalah $currentDay, dalam range 1-5.");
                $awalBulanData['month'] = $currentMonth;
                $awalBulanData['save_date'] = now()->startOfMonth()->toDateString();
            } else {
                Log::info("Tanggal saat ini adalah $currentDay, di luar range 1-5. Menyimpan pada tanggal pertama login.");
                $awalBulanData['month'] = $currentMonth;
                $awalBulanData['save_date'] = now()->toDateString();
            }

            file_put_contents($jsonFilePath, json_encode($awalBulanData, JSON_PRETTY_PRINT));
            return true;
        }

        Log::info('Data awal bulan sudah ada untuk bulan ini.');
        return false;
    }

    private function saveAwalBulan()
    {
        // Path ke file Excel
        $filePath = realpath(resource_path('excel/Laporan_Rincian_Persediaan.xlsx'));

        Log::info('Memulai proses saveAwalBulan. Path file: ' . $filePath);

        if ($filePath && file_exists($filePath)) {
            $exporter = new \App\Exports\PemasukanExport();
            $newFilePath = $exporter->export($filePath, true);  // Menyimpan data awal bulan
            Log::info('Data awal bulan telah disimpan ke file: ' . $newFilePath);
        } else {
            Log::error('File tidak ditemukan: ' . $filePath);
        }
    }
}
