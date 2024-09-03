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
        Schema::table('t_kasir', function (Blueprint $table) {
            $table->string('tarik_deposit')->nullable();
            $table->string('kode_deposit')->nullable();
            $table->integer('owner_id')->nullable();
            $table->double('sisa_pelunasan', 20, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_kasir', function (Blueprint $table) {
            $table->dropColumn(['tarik_deposit', 'kode_deposit', 'owner_id', 'sisa_pelunasan']);
        });
    }
};
