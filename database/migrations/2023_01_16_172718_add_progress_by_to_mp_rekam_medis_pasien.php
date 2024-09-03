<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mp_rekam_medis_pasien', function (Blueprint $table) {
            $table->string('progress_by')->after('status_apoteker')->nullable();
        });
    }

    public function down()
    {
        Schema::table('mp_rekam_medis_pasien', function (Blueprint $table) {
            $table->dropColumn('progress_by');
        });
    }
};
