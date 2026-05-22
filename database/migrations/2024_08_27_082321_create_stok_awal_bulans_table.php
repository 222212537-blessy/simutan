<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_awal_bulans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->integer('qty_awal'); // Stok awal bulan
            $table->year('tahun'); // Tahun
            $table->tinyInteger('bulan'); // Bulan (1-12)
            $table->timestamps();
            $table->integer('harga_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stok_awal_bulans');
    }
};
