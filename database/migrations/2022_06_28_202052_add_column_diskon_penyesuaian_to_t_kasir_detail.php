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
        Schema::table('t_kasir_detail', function (Blueprint $table) {
            $table->double('bruto', 20, 2)->default(0);
            $table->double('diskon_penyesuaian', 20, 2)->default(0);
            $table->double('nilai_diskon_penyesuaian', 20, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_kasir_detail', function (Blueprint $table) {
            $table->dropColumn([
                'bruto',
                'diskon_penyesuaian',
                'nilai_diskon_penyesuaian',
            ]);
        });
    }
};
