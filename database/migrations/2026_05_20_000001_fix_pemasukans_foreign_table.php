<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $databaseName = DB::getDatabaseName();
        $foreignKeys = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'pemasukans')
            ->where('COLUMN_NAME', 'barang_id')
            ->whereNotNull('CONSTRAINT_NAME')
            ->where('CONSTRAINT_NAME', '<>', 'PRIMARY')
            ->pluck('CONSTRAINT_NAME')
            ->unique();

        foreach ($foreignKeys as $foreignKey) {
            DB::statement('ALTER TABLE `pemasukans` DROP FOREIGN KEY `' . str_replace('`', '``', $foreignKey) . '`');
        }

        $hasCorrectForeignKey = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'pemasukans')
            ->where('COLUMN_NAME', 'barang_id')
            ->where('REFERENCED_TABLE_NAME', 'barangs')
            ->exists();

        if (!$hasCorrectForeignKey) {
            Schema::table('pemasukans', function (Blueprint $table) {
                $table->foreign('barang_id')->references('id')->on('barangs')->onDelete('cascade');
            });
        }
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
