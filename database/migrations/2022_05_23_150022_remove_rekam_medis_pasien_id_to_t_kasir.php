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
        Schema::table('t_kasir', function (Blueprint $table) {
            $table->dropColumn(['rekam_medis_pasien_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_kasir', function (Blueprint $table) {
            $table->integer('rekam_medis_pasien_id')->nullable();
        });
    }
};
