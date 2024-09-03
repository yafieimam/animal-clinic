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
        Schema::table('t_kasir_pembayaran', function (Blueprint $table) {
            $table->string('jenis_pembayaran')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('nomor_kartu')->nullable();
            $table->string('nomor_transaksi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_kasir_pembayaran', function (Blueprint $table) {
            $table->dropColumn(['jenis_pembayaran', 'nama_bank', 'nomor_kartu', 'nomor_transaksi']);
        });
    }
};
