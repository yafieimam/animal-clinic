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
            $table->integer('pakan')->nullable();
            $table->integer('jenis_grooming')->nullable();
            $table->string('cukur')->nullable();
            $table->string('status_kepulangan')->nullable();
            $table->integer('rekomendasi_tindakan_bedah')->nullable();
            $table->date('rekomendasi_tanggal_bedah')->nullable();
            $table->text('anamnesa')->nullable();
            $table->text('diagnosa')->nullable();
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
            $table->dropColumn([
                'pakan',
                'jenis_grooming',
                'status_kepulangan',
                'cukur',
                'rekomendasi_tindakan_bedah',
                'rekomendasi_tanggal_bedah',
                'anamnesa',
                'diagnosa',
            ]);
        });
    }
};
