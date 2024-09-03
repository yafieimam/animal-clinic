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
        Schema::table('t_jurnal', function (Blueprint $table) {
            $table->string('metode_pembayaran')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('nomor_kartu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_jurnal', function (Blueprint $table) {
            $table->dropColumn('metode_pembayaran');
            $table->dropColumn('nama_bank');
            $table->dropColumn('nomor_kartu');
        });
    }
};
