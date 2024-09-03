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
        Schema::create('t_kasir_pembayaran', function (Blueprint $table) {
            $table->integer('kasir_id');
            $table->integer('id');
            $table->string('ref');
            $table->double('nilai_pembayaran', 20, 2);
            $table->text('keterangan')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
            $table->primary(['kasir_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_kasir_pembayaran');
    }
};
