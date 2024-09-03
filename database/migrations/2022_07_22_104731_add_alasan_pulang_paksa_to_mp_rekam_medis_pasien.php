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
        Schema::table('mp_rekam_medis_pasien', function (Blueprint $table) {
            $table->text('alasan_pulang_paksa')->nullable();
            $table->string('upload_pulang_paksa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mp_rekam_medis_pasien', function (Blueprint $table) {
            $table->dropColumn('alasan_pulang_paksa');
            $table->dropColumn('upload_pulang_paksa');
        });
    }
};
