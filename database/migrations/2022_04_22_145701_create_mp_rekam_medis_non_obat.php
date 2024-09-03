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
        Schema::create('mp_rekam_medis_non_obat', function (Blueprint $table) {
            $table->foreignId('rekam_medis_pasien_id')
                ->constrained('mp_rekam_medis_pasien')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('id');
            $table->integer('item_non_obat_id');
            $table->integer('jumlah');
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('mp_rekam_medis_non_obat');
    }
};
