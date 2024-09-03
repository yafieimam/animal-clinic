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
        Schema::table('qm_pendaftaran_pasien', function (Blueprint $table) {
            $table->integer('dokter_periksa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qm_pendaftaran_pasien', function (Blueprint $table) {
            $table->dropColumn(['dokter_periksa']);
        });
    }
};
