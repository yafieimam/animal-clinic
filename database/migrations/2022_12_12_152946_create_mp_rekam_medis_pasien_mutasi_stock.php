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
        Schema::create('mp_rekam_medis_pasien_mutasi_stock', function (Blueprint $table) {
            $table->integer('rekam_medis_pasien_id');
            $table->integer('id');
            $table->integer('fitur_id');
            $table->string('tipe_fitur');
            $table->integer('mutasi_stock_id');
            $table->double('harga_satuan', 20, 2);
            $table->double('qty', 20, 2);
            $table->double('total_harga', 20, 2);
            $table->timestamps();
            $table->primary(['rekam_medis_pasien_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mp_rekam_medis_pasien_mutasi_stock');
    }
};
