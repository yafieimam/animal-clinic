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
        Schema::table('mo_rule_resep_racikan', function (Blueprint $table) {
            $table->dropColumn('binatang_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mo_rule_resep_racikan', function (Blueprint $table) {
            $table->integer('binatang_id')->nullable();
        });
    }
};
