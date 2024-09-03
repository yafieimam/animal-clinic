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
        Schema::table('qm_pendaftaran', function (Blueprint $table) {
            $table->boolean('status_pickup')->nullable();
            $table->boolean('status_owner')->nullable();
            $table->integer('request_dokter')->nullable();
            $table->integer('poli_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qm_pendaftaran', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'status_pickup',
                    'status_owner',
                    'request_dokter',
                    'poli_id',
                ]
            );
        });
    }
};
