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
        Schema::table('mk_poli', function (Blueprint $table) {
            $table->string('open_time')->nullable();
            $table->string('close_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mk_poli', function (Blueprint $table) {
            $table->dropColumn(['open_time', 'close_time']);
        });
    }
};
