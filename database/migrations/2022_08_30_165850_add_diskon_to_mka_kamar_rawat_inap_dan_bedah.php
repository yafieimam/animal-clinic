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
        Schema::table('mka_kamar_rawat_inap_dan_bedah', function (Blueprint $table) {
            $table->string('diskon')->default('false');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mka_kamar_rawat_inap_dan_bedah', function (Blueprint $table) {
            $table->dropColumn('diskon');
        });
    }
};
