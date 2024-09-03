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
            $table->double('total_item_diskon', 20, 2)->default(0);
            $table->double('total_item_non_diskon', 20, 2)->default(0);
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
            $table->dropColumn([
                'total_item_diskon',
                'total_item_non_diskon',
            ]);
        });
    }
};
