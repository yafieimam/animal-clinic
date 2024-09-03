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
        Schema::table('qm_pendaftaran_pasien_anamnesa', function (Blueprint $table) {
            $table->dropColumn(['status']);
            $table->string('ya');
            $table->string('tidak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('qm_pendaftaran_pasien_anamnesa', function (Blueprint $table) {
            $table->boolean('status');
            $table->dropColumn(['ya', 'tidak']);
        });
    }
};
