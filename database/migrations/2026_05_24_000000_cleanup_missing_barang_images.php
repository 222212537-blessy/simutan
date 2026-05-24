<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Reset foto_barang yang file-nya tidak ada
        $barangs = Barang::whereNotNull('foto_barang')->get();
        
        foreach ($barangs as $barang) {
            $path = 'backend/assets/images/barang/' . basename($barang->foto_barang);
            
            if (!Storage::disk('public')->exists($path)) {
                $barang->update(['foto_barang' => null]);
                echo "Reset: {$barang->kode} - {$barang->nama}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tidak ada yang perlu di-reverse
    }
};
