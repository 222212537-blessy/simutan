<?php

use Illuminate\Database\Migrations\Migration;
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
        DB::statement(
            'ALTER TABLE `notifications` MODIFY `permintaan_id` BIGINT UNSIGNED NULL'
        );
    }

    public function down()
    {
        DB::statement(
            'ALTER TABLE `notifications` MODIFY `permintaan_id` BIGINT UNSIGNED NOT NULL'
        );
    }
};
