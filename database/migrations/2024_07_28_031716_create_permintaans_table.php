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
        Schema::create('permintaans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // $table->unsignedBigInteger('pilihan_id');
            $table->string('no_permintaan');
            $table->date('tgl_request');
            $table->enum('status', ['pending', 'approved by admin', 'approved by supervisor', 'rejected by admin', 'rejected by supervisor']);
            $table->text('ctt_adm')->nullable();
            $table->text('ctt_spv')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permintaans');
    }
};
