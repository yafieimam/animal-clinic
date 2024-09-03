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
        Schema::create('t_deposit', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('kode');
            $table->integer('owner_id');
            $table->double('nilai_deposit', 20, 2);
            $table->double('sisa_deposit', 20, 2);
            $table->text('keterangan')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_deposit');
    }
};
