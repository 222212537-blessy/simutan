<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Barang;
use Illuminate\Support\Facades\Storage;

class CleanupMissingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barang:cleanup-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membersihkan referensi gambar barang yang file-nya tidak ada di storage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $barangs = Barang::whereNotNull('foto_barang')->get();
        $missingCount = 0;
        $deletedCount = 0;

        foreach ($barangs as $barang) {
            $path = 'backend/assets/images/barang/' . basename($barang->foto_barang);
            
            if (!Storage::disk('public')->exists($path)) {
                $this->warn("❌ Barang '{$barang->nama}' (Kode: {$barang->kode}) - Gambar tidak ditemukan: {$barang->foto_barang}");
                $missingCount++;

                // Opsi: Hapus referensi gambar dari database
                // $barang->update(['foto_barang' => null]);
                // $deletedCount++;
            } else {
                $this->info("✅ Barang '{$barang->nama}' (Kode: {$barang->kode}) - Gambar OK");
            }
        }

        $this->info("\n📊 Ringkasan:");
        $this->info("Total barang dengan gambar yang hilang: {$missingCount}");
        $this->info("Total referensi yang dihapus: {$deletedCount}");

        return Command::SUCCESS;
    }
}
