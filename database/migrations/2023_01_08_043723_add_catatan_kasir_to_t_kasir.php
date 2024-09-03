<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('t_kasir', function (Blueprint $table) {
            $table->text('catatan_kasir')->nullable()->after('penarikan_deposit');
        });
    }

    public function down()
    {
        Schema::table('t_kasir', function (Blueprint $table) {
            $table->dropColumn('catatan_kasir');
        });
    }
};
