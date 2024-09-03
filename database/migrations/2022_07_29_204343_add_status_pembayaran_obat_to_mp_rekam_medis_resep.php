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
        Schema::table('mp_rekam_medis_resep', function (Blueprint $table) {
            $table->string('status_pembayaran_obat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mp_rekam_medis_resep', function (Blueprint $table) {
            $table->dropColumn(['status_pembayaran_obat']);
        });
    }
};
