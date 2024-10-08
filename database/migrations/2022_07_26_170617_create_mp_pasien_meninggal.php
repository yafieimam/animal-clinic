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
        Schema::create('mp_pasien_meninggal', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('pasien_id');
            $table->enum('meninggal_saat', ['Rawat Inap', 'Bedah']);
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('mp_pasien_meninggal');
    }
};
