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
        // Drop incorrect foreign key if exists and recreate it to reference `barangs`
        Schema::table('pemasukans', function (Blueprint $table) {
            // Use column-based drop to be resilient to FK naming
            try {
                $table->dropForeign(['barang_id']);
            } catch (\Exception $e) {
                // ignore if it does not exist
            }

            // Recreate correct foreign key referencing `barangs` table
            $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemasukans', function (Blueprint $table) {
            try {
                $table->dropForeign(['barang_id']);
            } catch (\Exception $e) {
                // ignore
            }
            // Optionally recreate the previous foreign key if needed (not implemented)
        });
    }
};
