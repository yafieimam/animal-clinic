<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSexToMpPasien extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mp_pasien', function (Blueprint $table) {
            $table->enum('sex', ['JANTAN', 'BETINA'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mp_pasien', function (Blueprint $table) {
            $table->dropColumn('sex');
        });
    }
}
