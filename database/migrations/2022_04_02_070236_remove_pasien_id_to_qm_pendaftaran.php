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
            $table->dropColumn(['pasien_id', 'keluhan']);
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
            $table->integer('pasien_id')->nullable();
            $table->text('keluhan')->nullable();
        });
    }
};
