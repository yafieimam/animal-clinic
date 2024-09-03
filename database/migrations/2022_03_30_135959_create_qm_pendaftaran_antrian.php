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
        Schema::create('qm_pendaftaran_antrian', function (Blueprint $table) {
            $table->integer('pendaftaran_id');
            $table->integer('id');
            $table->string('kode_antrian');
            $table->integer('poli_id');
            $table->string('status')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->primary(['pendaftaran_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qm_pendaftaran_antrian');
    }
};
